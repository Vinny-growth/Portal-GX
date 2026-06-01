<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddXPulseIntegration extends Migration
{
    public function up()
    {
        $this->forge->addColumn('content_ai_settings', [
            'x_pulse_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'trend_keywords_json',
            ],
            'x_window_hours' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 6,
                'null'       => false,
                'after'      => 'x_pulse_enabled',
            ],
            'x_themes_per_day' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 10,
                'null'       => false,
                'after'      => 'x_window_hours',
            ],
            'x_min_mentions' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 100,
                'null'       => false,
                'after'      => 'x_themes_per_day',
            ],
            'x_grok_model' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'grok-4-fast',
                'null'       => false,
                'after'      => 'x_min_mentions',
            ],
            'x_pulse_prompt' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'x_grok_model',
            ],
            'last_run_x_pulse' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'x_pulse_prompt',
            ],
        ]);

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'snapshot_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'window_hours' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 6,
            ],
            'theme' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => false,
            ],
            'summary' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'mentions_estimate' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'sentiment' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'neutral',
            ],
            'tickers_json' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'entities_json' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'relevance_score' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'rank' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'used_in_calendar' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'raw_response' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('snapshot_date');
        $this->forge->addKey(['snapshot_date', 'rank']);
        $this->forge->createTable('x_pulse_snapshot');
    }

    public function down()
    {
        $this->forge->dropTable('x_pulse_snapshot');
        $this->forge->dropColumn('content_ai_settings', [
            'x_pulse_enabled',
            'x_window_hours',
            'x_themes_per_day',
            'x_min_mentions',
            'x_grok_model',
            'x_pulse_prompt',
            'last_run_x_pulse',
        ]);
    }
}
