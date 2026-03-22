<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBioLinksTable extends Migration
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
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'url' => [
                'type' => 'TEXT',
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'button_color' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'default' => '#007bff',
            ],
            'text_color' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'default' => '#ffffff',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'display_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'click_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('is_active');
        $this->forge->addKey('display_order');
        $this->forge->createTable('bio_links');
    }

    public function down()
    {
        $this->forge->dropTable('bio_links');
    }
}