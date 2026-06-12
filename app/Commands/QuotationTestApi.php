<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\QuotationEngine;
use App\Libraries\QuotationGate;

/**
 * Teste OFFLINE da camada de API do simulador (Fase 2).
 *
 * Exercita parseInput -> preview -> quote -> buildLeadData/publicDossier SEM
 * tocar no banco nem disparar o webhook do CRM/Meta (não chama addSimLead).
 * Inclui a checagem-chave do gate: o payload de PREVIEW não pode conter os
 * valores em R$ (prêmio mensal, reserva aos 65, etc.).
 *
 * Uso:  php spark quotation:test-api
 */
class QuotationTestApi extends BaseCommand
{
    protected $group       = 'Quotation';
    protected $name        = 'quotation:test-api';
    protected $description = 'Testa parse/preview/unlock-assembly do simulador offline (sem DB/CRM) + checagem do gate.';

    private int $fails = 0;

    public function run(array $params)
    {
        CLI::write('== Teste da API do Simulador (Fase 2, offline) ==', 'cyan');
        CLI::newLine();

        // Simula o POST do front-end.
        $post = [
            'idade'         => '28',
            'sexo'          => 'M',
            'estrategia'    => 'WL10',
            'capital_vida'  => '150.000,00',   // testa o parser pt-BR
            'capital_dg_plus' => '200000',
            'name'          => 'Lead Teste',
            'email'         => 'lead.teste@example.com',
            'phone'         => '11999998888',
            'utm_source'    => 'teste',
        ];

        $input = QuotationGate::parseInput($post);
        $this->assert('parseInput: 2 coberturas', count($input['coberturas']) === 2);
        $this->assert('parseInput: capital_vida pt-BR = 150000', $this->capOf($input, 'vida') === 150000.0);
        $this->assert('parseInput: idade=28/sexo=M/WL10',
            $input['idade'] === 28 && $input['sexo'] === 'M' && $input['estrategia'] === 'WL10');
        $this->assert('parseInput: cliente NÃO injeta ipca/pay_years',
            !isset($input['ipca']) && !isset($input['pay_years']));

        $engine  = new QuotationEngine();
        $preview = $engine->preview($input);
        $dossier = $engine->quote($input);

        // --- Estrutura do preview ---
        $this->assert('preview: tem séries indexadas', isset($preview['pago_idx'], $preview['reserva_idx']));
        $this->assert('preview: séries do mesmo tamanho',
            count($preview['labels']) === count($preview['pago_idx'])
            && count($preview['labels']) === count($preview['reserva_idx']));
        $this->assert('preview: break-even presente', $preview['breakeven_ano'] !== null);

        // --- CHECAGEM DO GATE: preview não pode vazar R$ ---
        $premio = $dossier['premio'];
        $dst    = $dossier['destaques'];
        $json   = json_encode($preview);
        $vazou = [];
        foreach ([
            'mensal_bruto'   => $premio['mensal_bruto'],
            'bruto_anual'    => $premio['bruto_anual'],
            'reserva_aos_65' => $dst['reserva_aos_65'],
            'reserva_final'  => $dst['reserva_final'],
            'pago_total'     => $dst['pago_total'],
        ] as $nome => $val) {
            if ($val === null) continue;
            // procura o inteiro do valor (ex.: 457, 318279) no JSON do preview
            $needle = (string) (int) $val;
            if (strlen($needle) >= 3 && strpos($json, $needle) !== false) {
                $vazou[] = "$nome($needle)";
            }
        }
        $this->assert('GATE: preview não vaza R$ ' . (empty($vazou) ? '' : '-> VAZOU: ' . implode(',', $vazou)), empty($vazou));

        // --- Dossiê em R$ (o que o unlock retorna) ---
        $this->assert('quote: prêmio mensal > 0', $premio['mensal_bruto'] > 0);
        $this->assert('quote: reserva aos 65 > 0', ($dst['reserva_aos_65'] ?? 0) > 0);

        $pub = QuotationGate::publicDossier($dossier);
        $this->assert('publicDossier: tem serie_rs alinhada',
            isset($pub['serie_rs']) &&
            count($pub['serie_rs']['labels']) === count($pub['serie_rs']['pago']) &&
            count($pub['serie_rs']['labels']) === count($pub['serie_rs']['reserva']));

        // --- Assembly do lead (sem gravar) ---
        $lead = QuotationGate::buildLeadData($input, $dossier, [
            'name' => $post['name'], 'email' => $post['email'], 'phone' => $post['phone'],
        ], $post);
        $sim = json_decode($lead['sim_data'] ?? '', true);
        $this->assert('buildLeadData: sim_data é JSON válido c/ dossiê',
            is_array($sim) && ($sim['tipo'] ?? '') === 'seguro_resgatavel' && isset($sim['projecao']));
        $this->assert('buildLeadData: meta_value == prêmio mensal',
            abs(((float) $lead['meta_value']) - $premio['mensal_bruto']) < 0.01);
        $this->assert('buildLeadData: observations preenchida c/ disclaimer',
            !empty($lead['observations']) && strpos($lead['observations'], 'SUSEP') !== false);
        $this->assert('buildLeadData: utm_source repassado', ($lead['utm_source'] ?? '') === 'teste');

        // --- Eco do contrato p/ o front-end ---
        CLI::newLine();
        CLI::write('Contrato de saída (amostra):', 'cyan');
        CLI::write('  preview.multiplo_final = ' . $preview['multiplo_final'] . 'x | break-even idade ' . $preview['breakeven_idade'], 'white');
        CLI::write('  dossie.premio.mensal_bruto = R$ ' . number_format($premio['mensal_bruto'], 2, ',', '.'), 'white');
        CLI::write('  dossie.destaques.reserva_aos_65 = R$ ' . number_format($dst['reserva_aos_65'], 2, ',', '.'), 'white');

        CLI::newLine();
        if ($this->fails === 0) {
            CLI::write('Fase 2: TODOS OS CHECKS OK.', 'green');
        } else {
            CLI::write("Fase 2: {$this->fails} FALHA(S).", 'red');
        }
    }

    private function capOf(array $input, string $tipo): float
    {
        foreach ($input['coberturas'] as $c) {
            if ($c['tipo'] === $tipo) return (float) $c['capital'];
        }
        return -1.0;
    }

    private function assert(string $label, bool $cond): void
    {
        CLI::write(($cond ? '  PASS  ' : '  FAIL  ') . $label, $cond ? 'green' : 'red');
        if (!$cond) {
            $this->fails++;
        }
    }
}
