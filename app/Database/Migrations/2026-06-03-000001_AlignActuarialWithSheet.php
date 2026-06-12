<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Alinha o schema atuarial à planilha real "Dinamica seg resgatavel.xlsx":
 *   - actuarial_rates ganha morte_acidental_taxa (a planilha tem essa cobertura).
 *   - reserve_factors passa a depender de SEXO (a matriz tem blocos masc/fem
 *     distintos). Como a tabela só tinha dados sintéticos, recriamos com a
 *     coluna sexo e o índice único (idade_contratacao, sexo, ano_vigencia).
 *
 * Após migrar, rodar:  php spark db:seed ActuarialSheetSeeder
 */
class AlignActuarialWithSheet extends Migration
{
    public function up()
    {
        // 1) morte acidental nas taxas
        if (!$this->db->fieldExists('morte_acidental_taxa', 'actuarial_rates')) {
            $this->forge->addColumn('actuarial_rates', [
                'morte_acidental_taxa' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,5',
                    'null' => true,
                    'comment' => 'Taxa por mil - Morte Acidental',
                    'after' => 'renda_hospitalar_taxa',
                ],
            ]);
        }

        // 2) reserve_factors com sexo (recriação limpa — só havia dados sintéticos)
        $this->forge->dropTable('reserve_factors', true);

        $this->forge->addField([
            'id' => [
                'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true,
            ],
            'idade_contratacao' => [
                'type' => 'SMALLINT', 'constraint' => 3, 'unsigned' => true,
                'comment' => 'Idade na contratação (14 a 65) = coluna da matriz',
            ],
            'sexo' => [
                'type' => 'CHAR', 'constraint' => 1, 'comment' => 'M ou F (blocos distintos na matriz)',
            ],
            'ano_vigencia' => [
                'type' => 'SMALLINT', 'constraint' => 3, 'unsigned' => true,
                'comment' => 'Ano da apólice (linha da matriz). Idade atingida = idade_contratacao + ano - 1',
            ],
            'fator' => [
                'type' => 'DECIMAL', 'constraint' => '12,6',
                'comment' => 'Fator multiplicador do capital atualizado',
            ],
            'source' => [
                'type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'comment' => 'sheet | synthetic',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['idade_contratacao', 'sexo', 'ano_vigencia'], false, true);
        $this->forge->addKey('idade_contratacao');
        $this->forge->createTable('reserve_factors');
    }

    public function down()
    {
        if ($this->db->fieldExists('morte_acidental_taxa', 'actuarial_rates')) {
            $this->forge->dropColumn('actuarial_rates', 'morte_acidental_taxa');
        }
        // recria reserve_factors sem sexo (schema anterior)
        $this->forge->dropTable('reserve_factors', true);
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'idade_contratacao' => ['type' => 'SMALLINT', 'constraint' => 3, 'unsigned' => true],
            'ano_vigencia' => ['type' => 'SMALLINT', 'constraint' => 3, 'unsigned' => true],
            'fator' => ['type' => 'DECIMAL', 'constraint' => '12,6'],
            'source' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['idade_contratacao', 'ano_vigencia'], false, true);
        $this->forge->addKey('idade_contratacao');
        $this->forge->createTable('reserve_factors');
    }
}
