<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Matriz de Fatores de Resgate / Cash Value (PRD Tabela 2).
 *
 * Cruza Idade de Contratação (14-65) x Ano de Vigência da apólice (1-100)
 * e devolve o fator multiplicador do resgate:
 *     Reserva do Ano = Capital Segurado Atualizado do Ano * fator
 *
 * Validação do PRD (Homem 28): ano 4 -> 0.01498 ; ano 5 -> 0.03786.
 *
 * Sexo fica de fora por ora (o PRD não cruza sexo no resgate). Se o CSV
 * exigir, adicionamos a coluna e o índice composto numa migration nova.
 */
class CreateReserveFactors extends Migration
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
            'idade_contratacao' => [
                'type' => 'SMALLINT',
                'constraint' => 3,
                'unsigned' => true,
                'comment' => 'Idade na contratação (14 a 65)',
            ],
            'ano_vigencia' => [
                'type' => 'SMALLINT',
                'constraint' => 3,
                'unsigned' => true,
                'comment' => 'Ano da apólice (1 a 100)',
            ],
            'fator' => [
                'type' => 'DECIMAL',
                'constraint' => '12,6',
                'comment' => 'Fator multiplicador do capital atualizado',
            ],
            'source' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'comment' => 'synthetic | csv',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['idade_contratacao', 'ano_vigencia'], false, true);
        $this->forge->addKey('idade_contratacao');
        $this->forge->createTable('reserve_factors');
    }

    public function down()
    {
        $this->forge->dropTable('reserve_factors');
    }
}
