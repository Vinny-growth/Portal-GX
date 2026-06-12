<?php

namespace App\Libraries;

use App\Models\ActuarialRateModel;
use App\Models\ReserveFactorModel;
use InvalidArgumentException;

/**
 * Motor atuarial do Simulador de Seguro de Vida Resgatável.
 *
 * Replica EXATAMENTE a planilha "Dinamica seg resgatavel.xlsx" (ver memória
 * project_actuarial_sheet_rules):
 *
 *   Prêmio (por cobertura):
 *     líquido_anual = base × taxa(idade,sexo)         (base = capital/1000;
 *                                                       renda hospitalar = capital)
 *     bruto_anual   = líquido_anual × 1,0038           (IOF 0,38%)
 *     mensal        = bruto_anual × 0,09               (fracionamento)
 *
 *   Projeção (idades idade+1 .. 100; o ano 1 da planilha = idade+1):
 *     capital(i)      = capital × (1+ipca)^(i-1)
 *     aporte_mensal(i)= wl_mensal × (1+ipca)^(i-1)
 *     aporte_anual(i) = aporte_mensal(i) × 12 × 0,97352   (só i ≤ pay_years)
 *     reserva(i)      = capital(i) × fator(idade, sexo, ano=i+1)
 *
 * A reserva (linha verde) e o aporte acumulado (linha vermelha) usam a
 * cobertura de VIDA (WL). Os demais riders entram só no prêmio total.
 */
class QuotationEngine
{
    public const IPCA_PADRAO   = 0.055;   // 5,5% a.a.
    public const IOF           = 0.0038;  // 0,38%
    public const FRAC_MENSAL   = 0.09;    // mensal = bruto_anual × 0,09
    public const APORTE_FATOR  = 0.97352; // aporte_anual = mensal × 12 × 0,97352
    public const IDADE_FINAL   = 100;
    public const IDADE_REFERIDA = 65;

    /** tipo de cobertura => coluna de taxa em actuarial_rates. */
    private const COLUNA_TAXA = [
        'dg_plus'          => 'dg_plus_taxa',
        'dg_basico'        => 'dg_basico_taxa',
        'invalidez'        => 'invalidez_taxa',
        'renda_hospitalar' => 'renda_hospitalar_taxa',
        'morte_acidental'  => 'morte_acidental_taxa',
    ];

    /** coberturas cujo prêmio usa capital cheio (sem /1000). */
    private const CAPITAL_CHEIO = ['renda_hospitalar'];

    private ActuarialRateModel $rates;
    private ReserveFactorModel $factors;

    public function __construct(?ActuarialRateModel $rates = null, ?ReserveFactorModel $factors = null)
    {
        $this->rates   = $rates   ?? new ActuarialRateModel();
        $this->factors = $factors ?? new ReserveFactorModel();
    }

    /** Cotação completa (server-side). Retorna o dossiê em R$. */
    public function quote(array $input): array
    {
        $in = $this->normalizeInput($input);

        $rateRow = $this->rates->getRate($in['idade'], $in['sexo']);
        if ($rateRow === null) {
            throw new InvalidArgumentException("Sem taxas para idade {$in['idade']} / sexo {$in['sexo']}.");
        }

        $premio    = $this->priceBasePremium($in, $rateRow);
        // projeção usa o mensal WL em precisão cheia (não arredondado) p/ casar com a planilha
        $projecao  = $this->projectLongTerm($in, $premio['mensal_bruto_raw']);
        $destaques = $this->buildDestaques($projecao, $in);

        return [
            'input'     => $in,
            'premio'    => $premio,
            'projecao'  => $projecao,
            'destaques' => $destaques,
            'params'    => [
                'ipca'         => $in['ipca'],
                'iof'          => self::IOF,
                'frac_mensal'  => self::FRAC_MENSAL,
                'aporte_fator' => self::APORTE_FATOR,
                'idade_final'  => $in['idade_final'],
                'pay_years'    => $in['pay_years'],
                'rates_source' => $rateRow['source'] ?? null,
            ],
        ];
    }

    public function preview(array $input): array
    {
        return $this->toPreview($this->quote($input));
    }

    // ── Bloco A: Prêmio ──────────────────────────────────────────────────────

    private function priceBasePremium(array $in, array $rateRow): array
    {
        $breakdown = [];
        $wl = null;
        $wlMensalRaw = 0.0;
        $riders = [];

        foreach ($in['coberturas'] as $cob) {
            $coluna = $this->resolveColunaTaxa($cob['tipo'], $in['estrategia']);
            $taxa   = isset($rateRow[$coluna]) ? (float) $rateRow[$coluna] : null;
            if ($taxa === null || $taxa <= 0) {
                continue;
            }
            $base    = in_array($cob['tipo'], self::CAPITAL_CHEIO, true) ? $cob['capital'] : ($cob['capital'] / 1000);
            $liquido = $base * $taxa;
            $bruto   = $liquido * (1 + self::IOF);
            $mensal  = $bruto * self::FRAC_MENSAL;

            if ($cob['tipo'] === 'vida') {
                $wlMensalRaw = $mensal; // precisão cheia p/ a projeção
            }

            $item = [
                'tipo'          => $cob['tipo'],
                'capital'       => round($cob['capital'], 2),
                'taxa'          => $taxa,
                'liquido_anual' => round($liquido, 2),
                'bruto_anual'   => round($bruto, 2),
                'mensal'        => round($mensal, 2),
            ];
            $breakdown[] = $item;

            if ($cob['tipo'] === 'vida') {
                $wl = $item;
            } else {
                $riders[] = $item;
            }
        }

        if ($wl === null) {
            throw new InvalidArgumentException('Cobertura de vida (WL) é obrigatória e precisa de taxa cadastrada.');
        }

        $totalMensal = $wl['mensal'];
        foreach ($riders as $r) {
            $totalMensal += $r['mensal'];
        }

        return [
            // KPI principal e driver da projeção = WL (forma a reserva)
            'mensal_bruto'             => $wl['mensal'],
            'mensal_bruto_raw'         => $wlMensalRaw,
            'bruto_anual'              => $wl['bruto_anual'],
            'liquido_anual'            => $wl['liquido_anual'],
            'fracionamento'            => self::FRAC_MENSAL,
            'riders'                   => $riders,
            'total_mensal_com_riders'  => round($totalMensal, 2),
            'breakdown'                => $breakdown,
        ];
    }

    private function resolveColunaTaxa(string $tipo, string $estrategia): string
    {
        if ($tipo === 'vida') {
            return $estrategia === 'WL20' ? 'wl20_taxa' : 'wl10_taxa';
        }
        return self::COLUNA_TAXA[$tipo] ?? '__inexistente__';
    }

    // ── Bloco B: Projeção (idade+1 .. 100) ───────────────────────────────────

    private function projectLongTerm(array $in, float $wlMensalBase): array
    {
        $capitalBase = 0.0;
        foreach ($in['coberturas'] as $cob) {
            if ($cob['tipo'] === 'vida') { $capitalBase = (float) $cob['capital']; break; }
        }

        $fatorMap = $this->factors->getFactorMapForAge($in['idade'], $in['sexo']); // 1 query
        $ipca     = $in['ipca'];
        $totalAnos = $in['idade_final'] - $in['idade']; // ages idade+1 .. 100

        $projecao = [];
        $pagoAcum = 0.0;

        for ($i = 1; $i <= $totalAnos; $i++) {
            $idadeNoAno = $in['idade'] + $i;          // começa em idade+1 (= ano 1 da planilha)
            $ano        = $i + 1;                     // linha da matriz / ano de vigência
            $infl       = pow(1 + $ipca, $i - 1);

            $capital = $capitalBase * $infl;

            $aporteMensal = $wlMensalBase * $infl;
            $aporteAnual  = ($i <= $in['pay_years']) ? ($aporteMensal * 12 * self::APORTE_FATOR) : 0.0;
            $pagoAcum    += $aporteAnual;

            $fator   = $fatorMap[$ano] ?? 0.0;
            $reserva = $capital * $fator;

            $projecao[] = [
                'ano'             => $ano,
                'idade'           => $idadeNoAno,
                'capital_vigente' => round($capital, 2),
                'aporte_mensal'   => round($aporteMensal, 2),
                'aporte_anual'    => round($aporteAnual, 2),
                'pago_acum'       => round($pagoAcum, 2),
                'fator'           => $fator,
                'reserva'         => round($reserva, 2),
            ];
        }

        return $projecao;
    }

    private function buildDestaques(array $projecao, array $in): array
    {
        $breakevenAno = null; $breakevenIdade = null; $reservaAos65 = null;

        foreach ($projecao as $p) {
            if ($breakevenAno === null && $p['pago_acum'] > 0 && $p['reserva'] >= $p['pago_acum']) {
                $breakevenAno = $p['ano']; $breakevenIdade = $p['idade'];
            }
            if ($p['idade'] === self::IDADE_REFERIDA) {
                $reservaAos65 = $p['reserva'];
            }
        }

        $ultimo    = end($projecao) ?: ['reserva' => 0];
        $pagoTotal = 0.0;
        foreach ($projecao as $p) { $pagoTotal = max($pagoTotal, $p['pago_acum']); }
        $reservaFinal = $ultimo['reserva'];
        $multiplo = $pagoTotal > 0 ? round($reservaFinal / $pagoTotal, 2) : null;

        return [
            'breakeven_ano'   => $breakevenAno,
            'breakeven_idade' => $breakevenIdade,
            'reserva_aos_65'  => $reservaAos65,
            'reserva_final'   => round($reservaFinal, 2),
            'pago_total'      => round($pagoTotal, 2),
            'quitacao_ano'    => $in['pay_years'],
            'multiplo_final'  => $multiplo,
        ];
    }

    // ── Bloco C: Payload seguro (sem R$) ──────────────────────────────────────

    public function toPreview(array $dossier): array
    {
        $proj = $dossier['projecao'];
        $maxVal = 0.0;
        foreach ($proj as $p) { $maxVal = max($maxVal, $p['pago_acum'], $p['reserva']); }
        $maxVal = $maxVal > 0 ? $maxVal : 1.0;

        $labels = $pagoIdx = $reservaIdx = [];
        foreach ($proj as $p) {
            $labels[]     = $p['idade'];
            $pagoIdx[]    = round($p['pago_acum'] / $maxVal * 100, 2);
            $reservaIdx[] = round($p['reserva']   / $maxVal * 100, 2);
        }

        $d = $dossier['destaques'];
        return [
            'labels'          => $labels,
            'pago_idx'        => $pagoIdx,
            'reserva_idx'     => $reservaIdx,
            'breakeven_ano'   => $d['breakeven_ano'],
            'breakeven_idade' => $d['breakeven_idade'],
            'quitacao_ano'    => $d['quitacao_ano'],
            'multiplo_final'  => $d['multiplo_final'],
            'idade_inicial'   => $dossier['input']['idade'],
            'idade_final'     => $dossier['input']['idade_final'],
            'estrategia'      => $dossier['input']['estrategia'],
        ];
    }

    // ── Normalização ──────────────────────────────────────────────────────────

    private function normalizeInput(array $input): array
    {
        $idade = (int) ($input['idade'] ?? 0);
        if ($idade < 14 || $idade > 65) {
            throw new InvalidArgumentException("Idade fora da faixa 14-65: {$idade}");
        }
        $sexo = strtoupper((string) ($input['sexo'] ?? 'M'));
        if (!in_array($sexo, ['M', 'F'], true)) {
            throw new InvalidArgumentException("Sexo inválido: {$sexo}");
        }
        $estrategia = strtoupper((string) ($input['estrategia'] ?? 'WL10'));
        if (!in_array($estrategia, ['WL10', 'WL20'], true)) {
            throw new InvalidArgumentException("Estratégia inválida: {$estrategia}");
        }

        $coberturas = [];
        foreach (($input['coberturas'] ?? []) as $cob) {
            $tipo    = (string) ($cob['tipo'] ?? '');
            $capital = (float) ($cob['capital'] ?? 0);
            if ($tipo === '' || $capital <= 0) { continue; }
            $coberturas[] = ['tipo' => $tipo, 'capital' => $capital];
        }
        if (empty($coberturas)) {
            throw new InvalidArgumentException('Nenhuma cobertura válida informada.');
        }

        $payDefault = $estrategia === 'WL20' ? 20 : 10;

        return [
            'idade'       => $idade,
            'sexo'        => $sexo,
            'estrategia'  => $estrategia,
            'coberturas'  => $coberturas,
            'ipca'        => isset($input['ipca']) ? (float) $input['ipca'] : self::IPCA_PADRAO,
            'pay_years'   => isset($input['pay_years']) ? (int) $input['pay_years'] : $payDefault,
            'idade_final' => isset($input['idade_final']) ? (int) $input['idade_final'] : self::IDADE_FINAL,
        ];
    }
}
