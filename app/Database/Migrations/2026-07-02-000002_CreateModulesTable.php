<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Fase 0 (white-label) — tabela de feature flags de módulos por install.
 * ADITIVA: cria tabela nova; nada existente é tocado.
 * `module_key` (não `key`, que é palavra reservada no MySQL) é único.
 */
class CreateModulesTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('modules')) {
            return;
        }

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'module_key' => ['type' => 'VARCHAR', 'constraint' => 64],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => true],
            'version'    => ['type' => 'VARCHAR', 'constraint' => 32,  'null' => true],
            'enabled'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'sort'       => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'meta_json'  => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('module_key');
        $this->forge->createTable('modules');
    }

    public function down()
    {
        $this->forge->dropTable('modules', true);
    }
}
