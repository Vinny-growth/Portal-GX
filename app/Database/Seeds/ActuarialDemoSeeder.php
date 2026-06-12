<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Popula actuarial_rates e reserve_factors com dados SINTÉTICOS provisórios.
 *
 * Objetivo: permitir construir e testar o QuotationEngine e o front-end antes
 * de o CSV real do cotador chegar. As curvas são plausíveis e monotônicas, mas
 * NÃO são atuarialmente corretas entre os pontos âncora — só os pontos do PRD
 * são exatos:
 *     - Homem 28: wl10_taxa = 34.67 ; dg_plus_taxa = 1.32
 *     - Reserva idade 28: ano 4 = 0.01498 ; ano 5 = 0.03786
 *
 * Tudo é gravado com source='synthetic'. O importador do CSV (Fase 0/1, quando
 * o arquivo chegar) deve apagar estas linhas e regravar com source='csv'.
 *
 * Uso:  php spark db:seed ActuarialDemoSeeder
 */
class ActuarialDemoSeeder extends Seeder
{
    private const IDADE_MIN = 14;
    private const IDADE_MAX = 65;
    private const ANO_MAX   = 100;

    public function run()
    {
        // Idempotência: limpa apenas o que for sintético.
        $this->db->table('actuarial_rates')->where('source', 'synthetic')->delete();
        $this->db->table('reserve_factors')->where('source', 'synthetic')->delete();

        $this->seedRates();
        $this->seedReserveFactors();
    }

    /** Tabela 1 — taxas de risco por idade/sexo. */
    private function seedRates(): void
    {
        $rows = [];

        for ($idade = self::IDADE_MIN; $idade <= self::IDADE_MAX; $idade++) {
            foreach (['M', 'F'] as $sexo) {
                // Mortalidade sobe ~6% ao ano de idade; mulher ~15% menor.
                $ageStep   = pow(1.06, $idade - 28);
                $sexFactor = ($sexo === 'F') ? 0.85 : 1.00;

                $wl10 = 34.67 * $ageStep * $sexFactor;        // âncora M/28 = 34.67
                $dgP  = 1.32  * pow(1.07, $idade - 28) * $sexFactor; // âncora M/28 = 1.32

                $rows[] = [
                    'idade'                 => $idade,
                    'sexo'                  => $sexo,
                    'wl10_taxa'             => round($wl10, 5),
                    'wl20_taxa'             => round($wl10 * 0.62, 5),   // 20 anos dilui o anual
                    'dg_plus_taxa'          => round($dgP, 5),
                    'dg_basico_taxa'        => round($dgP * 0.60, 5),
                    'invalidez_taxa'        => round(0.45 * pow(1.05, $idade - 28) * $sexFactor, 5),
                    'renda_hospitalar_taxa' => round(0.28 * pow(1.04, $idade - 28) * $sexFactor, 5),
                    'frac_fator'            => 1.00000, // provisório — vem do CSV
                    'source'                => 'synthetic',
                ];
            }
        }

        $this->db->table('actuarial_rates')->insertBatch($rows);
    }

    /** Tabela 2 — fatores de resgate (idade de contratação x ano de vigência). */
    private function seedReserveFactors(): void
    {
        $batch = [];

        for ($idade = self::IDADE_MIN; $idade <= self::IDADE_MAX; $idade++) {
            // Atinge idade 100 neste ano de vigência -> cash value ~ capital (fator ~1).
            $maturityYear = max(1, 101 - $idade);

            for ($ano = 1; $ano <= self::ANO_MAX; $ano++) {
                $t     = min(1.0, $ano / $maturityYear);
                $fator = round(pow($t, 1.8), 6); // ramp lento que acelera até ~1.0

                // Pontos âncora exatos do PRD (idade 28).
                if ($idade === 28 && $ano === 4) {
                    $fator = 0.014980;
                } elseif ($idade === 28 && $ano === 5) {
                    $fator = 0.037860;
                }

                $batch[] = [
                    'idade_contratacao' => $idade,
                    'ano_vigencia'      => $ano,
                    'fator'             => $fator,
                    'source'            => 'synthetic',
                ];

                // Insere em blocos de 500 para não estourar o packet do MySQL.
                if (count($batch) >= 500) {
                    $this->db->table('reserve_factors')->insertBatch($batch);
                    $batch = [];
                }
            }
        }

        if (!empty($batch)) {
            $this->db->table('reserve_factors')->insertBatch($batch);
        }
    }
}
