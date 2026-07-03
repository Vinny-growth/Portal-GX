<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Fase 0 (white-label) — tabela de configuração de marca (linha única id=1).
 * ADITIVA e behavior-preserving: cria uma tabela nova; nada existente é tocado.
 * Ninguém consome esses valores ainda (isso é a Fase 1).
 */
class CreateBrandSettings extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('brand_settings')) {
            return;
        }

        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            // Identidade
            'legal_name'          => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'display_name'        => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'tagline'             => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'founder_name'        => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'founder_title'       => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'founder_schema_id'   => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            // Contato
            'email'               => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'phone'               => ['type' => 'VARCHAR', 'constraint' => 64,  'null' => true],
            'whatsapp'            => ['type' => 'VARCHAR', 'constraint' => 64,  'null' => true],
            'address'             => ['type' => 'TEXT', 'null' => true],
            // Social / SEO
            'social_json'         => ['type' => 'TEXT', 'null' => true],
            'org_description'     => ['type' => 'TEXT', 'null' => true],
            'area_served'         => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'press_mentions_json' => ['type' => 'TEXT', 'null' => true],
            'og_image'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            // Locale
            'locale'              => ['type' => 'VARCHAR', 'constraint' => 16, 'null' => true],
            'currency'            => ['type' => 'VARCHAR', 'constraint' => 8,  'null' => true],
            'timezone'            => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            // Design tokens
            'color_primary'       => ['type' => 'VARCHAR', 'constraint' => 16, 'null' => true],
            'color_gold'          => ['type' => 'VARCHAR', 'constraint' => 16, 'null' => true],
            'color_secondary'     => ['type' => 'VARCHAR', 'constraint' => 16, 'null' => true],
            'logo'                => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'logo_footer'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'favicon'             => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('brand_settings');
    }

    public function down()
    {
        $this->forge->dropTable('brand_settings', true);
    }
}
