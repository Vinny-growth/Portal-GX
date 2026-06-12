<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Popula actuarial_rates e reserve_factors com os dados REAIS extraídos da
 * planilha "Dinamica seg resgatavel.xlsx" (app/Database/data/sheet_actuarial.json).
 *
 * Substitui o ActuarialDemoSeeder (sintético). Grava source='sheet' e remove
 * qualquer linha sintética anterior.
 *
 * Uso:  php spark db:seed ActuarialSheetSeeder
 */
class ActuarialSheetSeeder extends Seeder
{
    public function run()
    {
        $path = APPPATH . 'Database/data/sheet_actuarial.json';
        if (!is_file($path)) {
            throw new \RuntimeException("JSON da planilha não encontrado: {$path}");
        }
        $json = json_decode(file_get_contents($path), true);
        if (!is_array($json) || empty($json['rates']) || empty($json['reserve'])) {
            throw new \RuntimeException('JSON da planilha inválido ou incompleto.');
        }

        $fracMensal = (float) ($json['meta']['frac_mensal'] ?? 0.09);

        // limpa dados antigos (sintéticos ou sheet) para idempotência
        $this->db->table('actuarial_rates')->truncate();
        $this->db->table('reserve_factors')->truncate();

        $this->seedRates($json['rates'], $fracMensal);
        $this->seedReserve($json['reserve']);
    }

    private function num($v): ?float
    {
        return is_numeric($v) ? (float) $v : null;
    }

    private function seedRates(array $rates, float $fracMensal): void
    {
        $rows = [];
        foreach ($rates as $r) {
            $idade = (int) $r['idade'];
            $map = [
                'M' => ['wl10' => 'wl10_m', 'wl20' => 'wl20_m', 'dgp' => 'dgplus_m', 'dgb' => 'dgbasico_m', 'inv' => 'inval_m', 'rh' => 'rendahosp_m', 'mac' => 'morte_m'],
                'F' => ['wl10' => 'wl10_f', 'wl20' => 'wl20_f', 'dgp' => 'dgplus_f', 'dgb' => 'dgbasico_f', 'inv' => 'inval_f', 'rh' => 'rendahosp_f', 'mac' => 'morte_f'],
            ];
            foreach ($map as $sexo => $k) {
                $rows[] = [
                    'idade' => $idade,
                    'sexo' => $sexo,
                    'wl10_taxa' => $this->num($r[$k['wl10']] ?? null),
                    'wl20_taxa' => $this->num($r[$k['wl20']] ?? null),
                    'dg_plus_taxa' => $this->num($r[$k['dgp']] ?? null),
                    'dg_basico_taxa' => $this->num($r[$k['dgb']] ?? null),
                    'invalidez_taxa' => $this->num($r[$k['inv']] ?? null),
                    'renda_hospitalar_taxa' => $this->num($r[$k['rh']] ?? null),
                    'morte_acidental_taxa' => $this->num($r[$k['mac']] ?? null),
                    'frac_fator' => $fracMensal,
                    'source' => 'sheet',
                ];
            }
        }
        $this->db->table('actuarial_rates')->insertBatch($rows);
    }

    private function seedReserve(array $reserve): void
    {
        $batch = [];
        foreach (['M', 'F'] as $sexo) {
            $block = $reserve[$sexo] ?? [];
            foreach ($block as $idadeKey => $fatores) {
                $idade = (int) $idadeKey;
                // $fatores é lista 0-based: índice 0 => ano 1, índice k => ano k+1
                foreach ($fatores as $idx => $fator) {
                    $ano = $idx + 1;
                    $batch[] = [
                        'idade_contratacao' => $idade,
                        'sexo' => $sexo,
                        'ano_vigencia' => $ano,
                        'fator' => (float) $fator,
                        'source' => 'sheet',
                    ];
                    if (count($batch) >= 800) {
                        $this->db->table('reserve_factors')->insertBatch($batch);
                        $batch = [];
                    }
                }
            }
        }
        if (!empty($batch)) {
            $this->db->table('reserve_factors')->insertBatch($batch);
        }
    }
}
