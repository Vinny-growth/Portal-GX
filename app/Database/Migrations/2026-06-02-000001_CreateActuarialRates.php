<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Matriz de Precificação de Risco (PRD Tabela 1).
 *
 * Cruza Idade (14-65) x Sexo (M/F) e guarda a taxa por mil de cada cobertura,
 * mais o fator de fracionamento (anual -> mensal).
 *
 * Os valores reais vêm do CSV do cotador. Enquanto ele não chega, o
 * ActuarialDemoSeeder popula uma curva sintética ancorada nos números do PRD
 * (Homem/28 -> wl10 34.67, dg_plus 1.32) e marca a linha como source='synthetic'.
 */
class CreateActuarialRates extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'idade' => [
                'type' => 'SMALLINT',
                'constraint' => 3,
                'unsigned' => true,
                'comment' => 'Idade de contratação (14 a 65)',
            ],
            'sexo' => [
                'type' => 'CHAR',
                'constraint' => 1,
                'comment' => 'M ou F',
            ],
            'wl10_taxa' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
                'null' => true,
                'comment' => 'Taxa por mil - Vida Inteira quitação 10 anos',
            ],
            'wl20_taxa' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
                'null' => true,
                'comment' => 'Taxa por mil - Vida Inteira quitação 20 anos',
            ],
            'dg_plus_taxa' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
                'null' => true,
                'comment' => 'Taxa por mil - Doenças Graves Plus',
            ],
            'dg_basico_taxa' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
                'null' => true,
                'comment' => 'Taxa por mil - Doenças Graves Básico',
            ],
            'invalidez_taxa' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
                'null' => true,
                'comment' => 'Taxa por mil - Invalidez',
            ],
            'renda_hospitalar_taxa' => [
                'type' => 'DECIMAL',
                'constraint' => '10,5',
                'null' => true,
                'comment' => 'Taxa por mil - Renda Hospitalar (DIT)',
            ],
            'frac_fator' => [
                'type' => 'DECIMAL',
                'constraint' => '8,5',
                'default' => 1.00000,
                'comment' => 'Fator de fracionamento anual -> mensal',
            ],
            'source' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'comment' => 'synthetic | csv - guarda contra subir dado fake em produção',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['idade', 'sexo'], false, true);
        $this->forge->createTable('actuarial_rates');
    }

    public function down()
    {
        $this->forge->dropTable('actuarial_rates');
    }
}
