<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSeoRankingsTable extends Migration
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
            'keyword_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'position' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'null' => true,
            ],
            'url_found' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'clicks' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'impressions' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'ctr' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'default' => 0,
            ],
            'source' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'gsc',
                'comment' => 'gsc | serp',
            ],
            'checked_date' => [
                'type' => 'DATE',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('keyword_id');
        $this->forge->addKey('checked_date');
        // one row per keyword/day/source — keeps the time series clean and idempotent
        $this->forge->addUniqueKey(['keyword_id', 'checked_date', 'source']);
        $this->forge->createTable('seo_rankings');
    }

    public function down()
    {
        $this->forge->dropTable('seo_rankings');
    }
}
