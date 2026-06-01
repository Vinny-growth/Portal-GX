<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDashboardSettingsTable extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('dashboard_settings')) {
            $this->forge->addField([
                'chave' => [
                    'type' => 'VARCHAR',
                    'constraint' => 191,
                ],
                'valor' => [
                    'type' => 'MEDIUMTEXT',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('chave', true);
            $this->forge->createTable('dashboard_settings', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('dashboard_settings', true);
    }
}
