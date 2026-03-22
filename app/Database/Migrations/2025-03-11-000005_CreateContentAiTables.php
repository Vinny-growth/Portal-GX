<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContentAiTables extends Migration
{
    public function up()
    {
        // Settings table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'auto_publish' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'posts_per_day' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'run_time_1' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'run_time_2' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'run_time_3' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'last_run_1' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_run_2' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_run_3' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'lang_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'default_tone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'professional',
            ],
            'default_length' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'medium',
            ],
            'allowed_category_ids' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'auto_add_trends' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'trends_per_day' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 3,
            ],
            'default_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->createTable('content_ai_settings');

        // Calendar table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'instructions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'lang_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'tone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'length' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'tags' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'publish_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'generate_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'planned',
            ],
            'source_type' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'manual',
            ],
            'source_url' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'post_id' => [
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
        $this->forge->addKey('status');
        $this->forge->addKey('publish_at');
        $this->forge->addKey('generate_at');
        $this->forge->createTable('content_calendar');

        // Runs table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'calendar_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'run_type' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'error' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'prompt' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'response' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'started_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'finished_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('calendar_id');
        $this->forge->addKey('status');
        $this->forge->createTable('content_runs');

        // Trends table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'title_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
            ],
            'source_url' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'source' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'trends',
            ],
            'score' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'lang_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'fetched_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'selected' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'auto_add' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'used' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('title_hash', false, true);
        $this->forge->addKey('fetched_at');
        $this->forge->createTable('trend_items');
    }

    public function down()
    {
        $this->forge->dropTable('trend_items');
        $this->forge->dropTable('content_runs');
        $this->forge->dropTable('content_calendar');
        $this->forge->dropTable('content_ai_settings');
    }
}
