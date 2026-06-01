<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTrendKeywordsAndSourceHealth extends Migration
{
    public function up()
    {
        // Editable trend relevance keywords (3 buckets: phrases / words / context_words)
        $this->forge->addColumn('content_ai_settings', [
            'trend_keywords_json' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'topic_weights_json',
            ],
        ]);

        // Per-source telemetry for each fetch attempt
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'source' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'fetched_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'http_code' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'response_time_ms' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'items_returned' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'attempt' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'default'    => 1,
            ],
            'success' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'error_message' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['source', 'fetched_at']);
        $this->forge->addKey('fetched_at');
        $this->forge->createTable('trend_source_health');
    }

    public function down()
    {
        $this->forge->dropTable('trend_source_health');
        $this->forge->dropColumn('content_ai_settings', ['trend_keywords_json']);
    }
}
