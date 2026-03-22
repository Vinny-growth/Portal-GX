<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWebStoryPagesTable extends Migration
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
            'web_story_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'page_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'page_type' => [
                'type' => 'ENUM',
                'constraint' => ['cover', 'content', 'image', 'video', 'cta', 'custom'],
                'default' => 'content',
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'background_type' => [
                'type' => 'ENUM',
                'constraint' => ['color', 'gradient', 'image'],
                'default' => 'gradient',
            ],
            'background_value' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'comment' => 'Color hex, gradient CSS, or image URL'
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
            'video_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'cta_text' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'cta_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'text_color' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'default' => '#FFFFFF',
                'comment' => 'Hex color for text'
            ],
            'text_position' => [
                'type' => 'ENUM',
                'constraint' => ['top', 'center', 'bottom'],
                'default' => 'center',
            ],
            'font_size' => [
                'type' => 'ENUM',
                'constraint' => ['small', 'medium', 'large', 'xlarge'],
                'default' => 'medium',
            ],
            'animation' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'CSS animation class'
            ],
            'duration' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 5,
                'comment' => 'Duration in seconds for auto-advance'
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
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
        $this->forge->addKey('web_story_id');
        $this->forge->addKey('page_order');
        $this->forge->addKey('is_active');
        
        // Add foreign key constraint
        $this->forge->addForeignKey('web_story_id', 'web_stories', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('web_story_pages');
    }

    public function down()
    {
        $this->forge->dropTable('web_story_pages');
    }
}