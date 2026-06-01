<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAnalyticsDimensionsToPostPageviews extends Migration
{
    public function up()
    {
        $fields = [];

        if (!$this->db->fieldExists('referrer_host', 'post_pageviews_month')) {
            $fields['referrer_host'] = [
                'type' => 'VARCHAR',
                'constraint' => 190,
                'null' => true,
                'after' => 'ip_address',
            ];
        }

        if (!$this->db->fieldExists('source_group', 'post_pageviews_month')) {
            $fields['source_group'] = [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'referrer_host',
            ];
        }

        if (!$this->db->fieldExists('browser_name', 'post_pageviews_month')) {
            $fields['browser_name'] = [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
                'after' => 'source_group',
            ];
        }

        if (!$this->db->fieldExists('platform_name', 'post_pageviews_month')) {
            $fields['platform_name'] = [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
                'after' => 'browser_name',
            ];
        }

        if (!$this->db->fieldExists('device_type', 'post_pageviews_month')) {
            $fields['device_type'] = [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
                'after' => 'platform_name',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('post_pageviews_month', $fields);
        }
    }

    public function down()
    {
        $columns = ['device_type', 'platform_name', 'browser_name', 'source_group', 'referrer_host'];
        foreach ($columns as $column) {
            if ($this->db->fieldExists($column, 'post_pageviews_month')) {
                $this->forge->dropColumn('post_pageviews_month', $column);
            }
        }
    }
}
