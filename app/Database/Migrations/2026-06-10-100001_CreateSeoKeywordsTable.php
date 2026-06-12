<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSeoKeywordsTable extends Migration
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
            'keyword' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'target_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'locale' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'default' => 'pt-BR',
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'default' => 'bra',
            ],
            'device' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'default' => 'desktop',
            ],
            'source' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'gsc',
                'comment' => 'gsc | serp',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'notes' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'last_position' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'last_checked_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey('keyword');
        $this->forge->addKey('category_id');
        $this->forge->createTable('seo_keywords');
    }

    public function down()
    {
        $this->forge->dropTable('seo_keywords');
    }
}
