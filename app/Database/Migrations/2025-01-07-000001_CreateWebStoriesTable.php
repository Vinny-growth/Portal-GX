<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWebStoriesTable extends Migration
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
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'image_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'image_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'link_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'is_generated' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '0: uploaded, 1: AI generated'
            ],
            'generation_prompt' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'OpenAI prompt used for image generation'
            ],
            'openai_image_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'OpenAI image generation ID'
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
            'view_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'click_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'lang_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 1,
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addKey('display_order');
        $this->forge->addKey('lang_id');
        $this->forge->addKey('category_id');
        $this->forge->createTable('web_stories');
    }

    public function down()
    {
        $this->forge->dropTable('web_stories');
    }
}