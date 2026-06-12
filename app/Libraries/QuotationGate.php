<?php

namespace App\Libraries;

/**
 * Cola entre a requisição HTTP e o QuotationEngine.
 *
 * Mantém o ApiController fino e — principalmente — torna o GATE testável
 * offline (sem HTTP, sem banco, sem webhook):
 *
 *   parseInput()     request POST  -> input validado do engine (whitelist)
 *   publicDossier()  dossiê em R$  -> subconjunto que o cliente recebe no unlock
 *   buildLeadData()  dossiê        -> payload p/ SimLeadModel::addSimLead (dossiê do corretor)
 *
 * Regra do gate: o cliente só controla idade/sexo/estratégia/capitais.
 * IPCA, IOF, pay_years, idade_final e offset são SEMPRE do servidor — o cliente
 * não pode torcer a projeção. E nada em R$ trafega no preview.
 */
class QuotationGate
{
    /** Coberturas opcionais aceitas e o campo de capital correspondente. */
    private const CAPITAL_FIELDS = [
        'dg_plus'          => 'capital_dg_plus',
        'dg_basico'        => 'capital_dg_basico',
        'invalidez'        => 'capital_invalidez',
        'renda_hospitalar' => 'capital_renda_hospitalar',
    ];

    /** Custo de inventário sobre o patrimônio: 6% advogado + 2% cartório. */
    public const INVENTARIO_RATE = 0.08;
    public const UF_PADRAO = 'SP';

    /**
     * ITCMD por UF (causa mortis) — valores de referência (máximo/efetivo),
     * a validar com a área tributária. Vários estados são progressivos (até 8%).
     * Fonte única, espelhada no front via QuotationGate::itcmdMap().
     * @var array<string, array{0:string,1:float}>  UF => [nome, alíquota]
     */
    private const UF_ITCMD = [
        'AC' => ['Acre', 0.08],               'AL' => ['Alagoas', 0.04],
        'AP' => ['Amapá', 0.04],              'AM' => ['Amazonas', 0.02],
        'BA' => ['Bahia', 0.08],              'CE' => ['Ceará', 0.08],
        'DF' => ['Distrito Federal', 0.06],   'ES' => ['Espírito Santo', 0.04],
        'GO' => ['Goiás', 0.08],              'MA' => ['Maranhão', 0.07],
        'MT' => ['Mato Grosso', 0.08],        'MS' => ['Mato Grosso do Sul', 0.06],
        'MG' => ['Minas Gerais', 0.05],       'PA' => ['Pará', 0.06],
        'PB' => ['Paraíba', 0.08],            'PR' => ['Paraná', 0.04],
        'PE' => ['Pernambuco', 0.08],         'PI' => ['Piauí', 0.06],
        'RJ' => ['Rio de Janeiro', 0.08],     'RN' => ['Rio Grande do Norte', 0.06],
        'RS' => ['Rio Grande do Sul', 0.06],  'RO' => ['Rondônia', 0.04],
        'RR' => ['Roraima', 0.06],            'SC' => ['Santa Catarina', 0.08],
        'SP' => ['São Paulo', 0.04],          'SE' => ['Sergipe', 0.08],
        'TO' => ['Tocantins', 0.08],
    ];

    /** [UF => alíquota ITCMD] — para o cálculo ao vivo no front. */
    public static function itcmdMap(): array
    {
        $map = [];
        foreach (self::UF_ITCMD as $uf => $row) {
            $map[$uf] = $row[1];
        }
        return $map;
    }

    /** Lista ordenada por nome: [ ['uf','nome','itcmd'], ... ] para o <select>. */
    public static function ufList(): array
    {
        $out = [];
        foreach (self::UF_ITCMD as $uf => $row) {
            $out[] = ['uf' => $uf, 'nome' => $row[0], 'itcmd' => $row[1]];
        }
        usort($out, fn($a, $b) => strcmp($a['nome'], $b['nome']));
        return $out;
    }

    public static function itcmdFor(string $uf): float
    {
        $uf = strtoupper(trim($uf));
        return self::UF_ITCMD[$uf][1] ?? self::UF_ITCMD[self::UF_PADRAO][1];
    }

    /** ITCMD(UF) + inventário — fração total da liquidez sucessória. */
    public static function successionRate(string $uf): float
    {
        return self::itcmdFor($uf) + self::INVENTARIO_RATE;
    }

    /** Monta o input do engine a partir do POST, em whitelist. */
    public static function parseInput(array $post): array
    {
        $coberturas = [];

        $capitalVida = self::money($post['capital_vida'] ?? 0);
        if ($capitalVida > 0) {
            $coberturas[] = ['tipo' => 'vida', 'capital' => $capitalVida];
        }

        foreach (self::CAPITAL_FIELDS as $tipo => $campo) {
            $cap = self::money($post[$campo] ?? 0);
            if ($cap > 0) {
                $coberturas[] = ['tipo' => $tipo, 'capital' => $cap];
            }
        }

        return [
            'idade'      => (int) ($post['idade'] ?? 0),
            'sexo'       => strtoupper((string) ($post['sexo'] ?? 'M')),
            'estrategia' => strtoupper((string) ($post['estrategia'] ?? 'WL10')),
            'coberturas' => $coberturas,
            // IPCA/IOF/pay_years/offset NÃO vêm do cliente — defaults do engine.
        ];
    }

    /**
     * Subconjunto em R$ devolvido ao cliente APÓS o unlock (gravar lead).
     * Inclui a série em R$ para o front trocar o gráfico indexado pelo real.
     */
    public static function publicDossier(array $dossier): array
    {
        $labels = [];
        $pago   = [];
        $reserva = [];
        foreach ($dossier['projecao'] as $p) {
            $labels[]  = $p['idade'];
            $pago[]    = $p['pago_acum'];
            $reserva[] = $p['reserva'];
        }

        return [
            'premio'    => $dossier['premio'],
            'destaques' => $dossier['destaques'],
            'serie_rs'  => [
                'labels'  => $labels,
                'pago'    => $pago,
                'reserva' => $reserva,
            ],
            'params'    => $dossier['params'],
        ];
    }

    /**
     * Payload para SimLeadModel::addSimLead — inclui o dossiê completo em
     * sim_data (JSON) e um resumo legível em observations para o corretor.
     *
     * @param array $contact ['name','email','phone','phone_country']
     * @param array $passthrough campos de tracking vindos do POST (utm_*, landing_page, event_id...)
     */
    public static function buildLeadData(array $input, array $dossier, array $contact, array $passthrough = []): array
    {
        $premio    = $dossier['premio'];
        $destaques = $dossier['destaques'];

        $estr = $input['estrategia'];
        $origem = 'Simulador Seguro Resgatável - ' . $estr;

        $perfil = self::buildPerfil($passthrough);

        $simData = json_encode([
            'tipo'      => 'seguro_resgatavel',
            'input'     => $dossier['input'],
            'perfil'    => $perfil,
            'premio'    => $premio,
            'destaques' => $destaques,
            'params'    => $dossier['params'],
            'projecao'  => $dossier['projecao'],
        ], JSON_UNESCAPED_UNICODE);

        $base = [
            'name'                 => $contact['name'] ?? null,
            'email'                => $contact['email'] ?? null,
            'phone'                => $contact['phone'] ?? null,
            'phone_country'        => $contact['phone_country'] ?? null,
            'sim_data'             => $simData,
            'observations'         => self::buildObservations($input, $dossier, $perfil),
            'origem'               => $origem,
            'origin'               => $origem,
            'meta_content_name'    => 'Simulador Seguro Resgatável',
            'meta_content_category'=> 'Seguro de Vida Resgatável',
            'meta_value'           => $premio['mensal_bruto'],
            'meta_currency'        => 'BRL',
        ];

        // tracking / utm / event_id passam direto se vierem.
        foreach (['landing_page', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'event_id', 'company', 'message'] as $k) {
            if (isset($passthrough[$k]) && $passthrough[$k] !== '') {
                $base[$k] = $passthrough[$k];
            }
        }

        return $base;
    }

    /** Diagnóstico financeiro do lead (qualificação do corretor). */
    private static function buildPerfil(array $p): array
    {
        $objetivos = [
            'protecao_familiar'  => 'Proteger a renda da família',
            'reserva_patrimonio' => 'Construir reserva resgatável',
            'sucessao'           => 'Planejar sucessão com liquidez',
            'quitar_dividas'     => 'Quitar dívidas em imprevisto',
            'aposentadoria'      => 'Complementar aposentadoria',
        ];
        $obj = (string) ($p['objetivo'] ?? '');
        $imob = isset($p['patrimonio_imobiliario']) ? self::money($p['patrimonio_imobiliario']) : 0.0;
        $fin  = isset($p['patrimonio_financeiro']) ? self::money($p['patrimonio_financeiro']) : 0.0;
        $total = $imob + $fin;
        // Liquidez sucessória = (ITCMD do estado + inventário 10%) sobre o patrimônio.
        $uf = strtoupper(trim((string) ($p['estado'] ?? self::UF_PADRAO)));
        $itcmd = self::itcmdFor($uf);
        $custoSucessao = round($total * ($itcmd + self::INVENTARIO_RATE), 2);

        return [
            'dependentes'             => isset($p['dependentes']) ? (int) $p['dependentes'] : null,
            'filhos'                  => isset($p['filhos']) ? (int) $p['filhos'] : null,
            'renda_mensal'            => isset($p['renda_mensal']) ? self::money($p['renda_mensal']) : null,
            'estado'                  => $uf,
            'itcmd_rate'              => $itcmd,
            'patrimonio_imobiliario'  => $imob,
            'patrimonio_financeiro'   => $fin,
            'patrimonio_total'        => $total,
            'custo_sucessao_estimado' => $custoSucessao,
            'dividas'                 => isset($p['dividas']) ? self::money($p['dividas']) : null,
            'objetivo'                => $obj,
            'objetivo_label'          => $objetivos[$obj] ?? ($obj !== '' ? $obj : null),
        ];
    }

    /** Resumo legível para o corretor (quebra de objeção pronta). */
    private static function buildObservations(array $input, array $dossier, array $perfil = []): string
    {
        $p = $dossier['premio'];
        $d = $dossier['destaques'];

        $caps = [];
        foreach ($input['coberturas'] as $c) {
            $caps[] = $c['tipo'] . ' R$ ' . self::fmt($c['capital']);
        }

        $diagLinha = '';
        if (!empty($perfil['renda_mensal']) || !empty($perfil['patrimonio_total']) || !empty($perfil['dividas']) || !empty($perfil['dependentes']) || !empty($perfil['filhos'])) {
            $diagLinha = sprintf('Diagnóstico: renda R$ %s/mês, %s, patrimônio imobiliário R$ %s + financeiro R$ %s (sucessão estimada R$ %s | ITCMD %s%% + inventário %d%%), dívidas R$ %s, %d dependente(s), %d filho(s)%s.',
                self::fmt((float) ($perfil['renda_mensal'] ?? 0)),
                'estado ' . ($perfil['estado'] ?? '—'),
                self::fmt((float) ($perfil['patrimonio_imobiliario'] ?? 0)),
                self::fmt((float) ($perfil['patrimonio_financeiro'] ?? 0)),
                self::fmt((float) ($perfil['custo_sucessao_estimado'] ?? 0)),
                rtrim(rtrim(number_format(((float) ($perfil['itcmd_rate'] ?? 0)) * 100, 1, ',', ''), '0'), ','),
                (int) round(self::INVENTARIO_RATE * 100),
                self::fmt((float) ($perfil['dividas'] ?? 0)),
                (int) ($perfil['dependentes'] ?? 0),
                (int) ($perfil['filhos'] ?? 0),
                !empty($perfil['objetivo_label']) ? ' | objetivo: ' . $perfil['objetivo_label'] : '');
        }

        $linhas = [
            sprintf('Perfil: %s, %d anos, estratégia %s (quitação em %d anos).',
                $input['sexo'] === 'F' ? 'feminino' : 'masculino', $input['idade'], $input['estrategia'], $d['quitacao_ano']),
            $diagLinha,
            'Coberturas: ' . implode(' | ', $caps) . '.',
            sprintf('Prêmio mensal (c/ IOF): R$ %s | anual bruto: R$ %s.',
                self::fmt($p['mensal_bruto']), self::fmt($p['bruto_anual'])),
            sprintf('Reserva projetada aos 65: R$ %s | reserva final: R$ %s.',
                $d['reserva_aos_65'] !== null ? self::fmt($d['reserva_aos_65']) : '—', self::fmt($d['reserva_final'])),
            $d['breakeven_ano'] !== null
                ? sprintf('Break-even no ano %d (idade %d): a reserva ultrapassa o total pago.', $d['breakeven_ano'], $d['breakeven_idade'])
                : 'Break-even não atingido na projeção.',
        ];

        return implode(' ', array_filter($linhas))
            . ' [valores projetados (IPCA estimado), não garantidos; sujeitos às condições da apólice/SUSEP]';
    }

    private static function money($v): float
    {
        if (is_numeric($v)) {
            return (float) $v;
        }
        // aceita "150.000,00" / "150000" / "R$ 150.000"
        $s = preg_replace('/[^0-9,.-]/', '', (string) $v);
        $s = str_replace('.', '', $s);
        $s = str_replace(',', '.', $s);
        return is_numeric($s) ? (float) $s : 0.0;
    }

    private static function fmt(float $v): string
    {
        return number_format($v, 2, ',', '.');
    }
}
