<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ActuarialRateModel;
use App\Models\ReserveFactorModel;
use App\Libraries\QuotationEngine;

/**
 * Valida a camada de dados + o QuotationEngine contra os pontos âncora do PRD.
 *
 * - Âncoras DURAS (PASS/FAIL): taxas (M/28) e fatores de resgate (28/4, 28/5).
 * - RECONCILIAÇÃO (INFO/WARN): a indexação do IPCA no loop. O PRD mostra
 *   ano 4 -> capital 166.953,75 e reserva 2.500,96; aqui comparamos com a saída
 *   do motor e sugerimos o inflation_offset que casaria — a definição real só
 *   sai quando o CSV/planilha chegar.
 *
 * Uso:  php spark quotation:validate
 */
class QuotationValidate extends BaseCommand
{
    protected $group       = 'Quotation';
    protected $name        = 'quotation:validate';
    protected $description = 'Confere actuarial_rates, reserve_factors e o QuotationEngine contra as âncoras do PRD.';
    protected $usage       = 'quotation:validate';

    private int $fails = 0;

    public function run(array $params)
    {
        CLI::write('== Validação do Motor Atuarial GX (Fase 1) ==', 'cyan');
        CLI::newLine();

        $this->checkTablesPopulated();
        $this->checkRateAnchors();
        $this->checkFactorAnchors();
        $this->checkEngineAndReconcile();

        CLI::newLine();
        if ($this->fails === 0) {
            CLI::write('Âncoras duras: TODAS OK.', 'green');
        } else {
            CLI::write("Âncoras duras: {$this->fails} FALHA(S). Rode o seeder: php spark db:seed ActuarialDemoSeeder", 'red');
        }
    }

    private function checkTablesPopulated(): void
    {
        $rates   = (new ActuarialRateModel())->countAllResults();
        $factors = (new ReserveFactorModel())->countAllResults();
        CLI::write("actuarial_rates: {$rates} linhas | reserve_factors: {$factors} linhas", 'yellow');

        if ($rates === 0 || $factors === 0) {
            CLI::write('Tabelas vazias. Rode as migrations e o seeder primeiro:', 'red');
            CLI::write('  php spark migrate', 'white');
            CLI::write('  php spark db:seed ActuarialDemoSeeder', 'white');
            $this->fails++;
        }
        CLI::newLine();
    }

    private function checkRateAnchors(): void
    {
        CLI::write('-- Taxas (Homem, 28) --', 'cyan');
        $row = (new ActuarialRateModel())->getRate(28, 'M');

        if ($row === null) {
            $this->fail('linha M/28 inexistente');
            return;
        }
        $this->assertNear('wl10_taxa', (float) $row['wl10_taxa'], 34.67);
        $this->assertNear('dg_plus_taxa', (float) $row['dg_plus_taxa'], 1.32);
        CLI::newLine();
    }

    private function checkFactorAnchors(): void
    {
        CLI::write('-- Fatores de resgate (idade 28, masc) --', 'cyan');
        $m = new ReserveFactorModel();
        $this->assertNear('fator ano 4', (float) $m->getFactor(28, 'M', 4), 0.01498, 0.000005);
        $this->assertNear('fator ano 5', (float) $m->getFactor(28, 'M', 5), 0.03786, 0.000005);
        $this->assertNear('fator ano 38 (idade65)', (float) $m->getFactor(28, 'M', 38), 0.67340, 0.000005);
        $this->assertNear('fator fem ano 4', (float) $m->getFactor(28, 'F', 4), 0.01329, 0.000005);
        CLI::newLine();
    }

    private function checkEngineAndReconcile(): void
    {
        CLI::write('-- QuotationEngine vs planilha (M/28, WL10, cap 150k + DG 200k) --', 'cyan');

        $engine = new QuotationEngine();
        $dossier = $engine->quote([
            'idade'      => 28,
            'sexo'       => 'M',
            'estrategia' => 'WL10',
            'coberturas' => [
                ['tipo' => 'vida',    'capital' => 150000],
                ['tipo' => 'dg_plus', 'capital' => 200000],
            ],
        ]);

        $premio = $dossier['premio'];
        $d = $dossier['destaques'];

        // WL: bruto anual 5.220,26 ; mensal 469,82 (planilha)
        $this->assertNear('WL bruto anual', $premio['bruto_anual'], 5220.26, 0.5);
        $this->assertNear('WL mensal', $premio['mensal_bruto'], 469.82, 0.5);

        // DG Plus rider mensal 23,85 (planilha)
        $dg = null;
        foreach ($premio['riders'] as $r) { if ($r['tipo'] === 'dg_plus') { $dg = $r; } }
        $this->assertNear('DG Plus mensal', $dg['mensal'] ?? 0, 23.85, 0.2);

        // Projeção ano 4 (idade 31): capital 166.953,75 / reserva 2.500,97 ; aporte ano1 5.488,59
        $ano4 = null; $ano2 = null;
        foreach ($dossier['projecao'] as $p) {
            if ($p['ano'] === 4) { $ano4 = $p; }
            if ($p['ano'] === 2) { $ano2 = $p; }
        }
        $this->assertNear('capital ano4', $ano4['capital_vigente'] ?? 0, 166953.75, 1.0);
        $this->assertNear('reserva ano4', $ano4['reserva'] ?? 0, 2500.97, 1.0);
        $this->assertNear('aporte anual (ano1)', $ano2['aporte_anual'] ?? 0, 5488.59, 1.0); // ano2 = primeira linha (i=1)
        $this->assertNear('reserva aos 65', $d['reserva_aos_65'] ?? 0, 694149.34, 50.0);

        CLI::newLine();
        CLI::write(sprintf(
            'Resumo: WL mensal R$ %s | total c/ riders R$ %s | break-even idade %s (ano %s) | reserva 65 R$ %s | final R$ %s',
            number_format($premio['mensal_bruto'], 2, ',', '.'),
            number_format($premio['total_mensal_com_riders'], 2, ',', '.'),
            $d['breakeven_idade'] ?? '—', $d['breakeven_ano'] ?? '—',
            number_format($d['reserva_aos_65'] ?? 0, 2, ',', '.'),
            number_format($d['reserva_final'], 2, ',', '.')
        ), 'white');
        CLI::write('Fonte dos dados: ' . ($dossier['params']['rates_source'] ?? '—'), 'yellow');
    }

    private function assertNear(string $label, float $got, float $expected, float $tol = 0.01): void
    {
        if (abs($got - $expected) <= $tol) {
            CLI::write(sprintf('  PASS  %-14s = %s', $label, $got), 'green');
        } else {
            CLI::write(sprintf('  FAIL  %-14s = %s (esperado %s)', $label, $got, $expected), 'red');
            $this->fails++;
        }
    }

    private function fail(string $msg): void
    {
        CLI::write('  FAIL  ' . $msg, 'red');
        $this->fails++;
    }
}
