<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPopularContentSettings extends Migration
{
    public function up()
    {
        $this->forge->addColumn('content_ai_settings', [
            'popular_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'trends_per_day',
            ],
            'popular_posts_per_day' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
                'after'      => 'popular_enabled',
            ],
            'popular_window_days' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 7,
                'null'       => false,
                'after'      => 'popular_posts_per_day',
            ],
            'popular_metric' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'mixed',
                'null'       => false,
                'after'      => 'popular_window_days',
            ],
            'popular_min_pageviews' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 5,
                'null'       => false,
                'after'      => 'popular_metric',
            ],
            'popular_editor_prompt' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'popular_min_pageviews',
            ],
            'last_run_popular' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'popular_editor_prompt',
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
            'window_days' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 7,
            ],
            'metric' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'mixed',
            ],
            'post_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'rank' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'pageviews' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'unique_visitors' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'interactions' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'score' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'used' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('snapshot_date');
        $this->forge->addKey(['snapshot_date', 'window_days', 'rank']);
        $this->forge->addKey('post_id');
        $this->forge->createTable('popular_posts_snapshot');
    }

    public function down()
    {
        $this->forge->dropTable('popular_posts_snapshot');
        $this->forge->dropColumn('content_ai_settings', [
            'popular_enabled',
            'popular_posts_per_day',
            'popular_window_days',
            'popular_metric',
            'popular_min_pageviews',
            'popular_editor_prompt',
            'last_run_popular',
        ]);
    }
}
