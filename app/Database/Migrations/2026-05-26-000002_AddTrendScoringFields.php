<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTrendScoringFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('trend_items', [
            'semantic_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => true,
                'after'      => 'title_hash',
            ],
            'source_authority' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 5,
                'null'       => false,
                'after'      => 'source',
            ],
            'cross_source_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1,
                'null'       => false,
                'after'      => 'source_authority',
            ],
            'last_seen_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'fetched_at',
            ],
        ]);
        $this->forge->addKey('semantic_hash', false, false, 'trend_items');
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_trend_semantic ON trend_items(semantic_hash)');
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_trend_lastseen ON trend_items(last_seen_at)');
    }

    public function down()
    {
        $this->db->query('DROP INDEX IF EXISTS idx_trend_semantic ON trend_items');
        $this->db->query('DROP INDEX IF EXISTS idx_trend_lastseen ON trend_items');
        $this->forge->dropColumn('trend_items', [
            'semantic_hash',
            'source_authority',
            'cross_source_count',
            'last_seen_at',
        ]);
    }
}
