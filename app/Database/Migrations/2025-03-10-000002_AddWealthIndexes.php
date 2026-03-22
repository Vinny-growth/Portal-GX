<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWealthIndexes extends Migration
{
    public function up()
    {
        $this->addIndexIfMissing('wm_income_expense', 'idx_wm_ie_tipo', ['tipo']);
        $this->addIndexIfMissing('wm_assets_financial', 'idx_wm_af_classe', ['classe']);
        $this->addIndexIfMissing('wm_sessions', 'idx_wm_se_status', ['status']);
        $this->addIndexIfMissing('wm_appointments', 'idx_wm_ap_status', ['status']);
    }

    public function down()
    {
        $this->dropIndexIfExists('wm_income_expense', 'idx_wm_ie_tipo');
        $this->dropIndexIfExists('wm_assets_financial', 'idx_wm_af_classe');
        $this->dropIndexIfExists('wm_sessions', 'idx_wm_se_status');
        $this->dropIndexIfExists('wm_appointments', 'idx_wm_ap_status');
    }

    private function addIndexIfMissing(string $table, string $indexName, array $columns)
    {
        try {
            if (! $this->db->tableExists($table)) return;
            $exists = false;
            $indexes = $this->db->query('SHOW INDEXES FROM `' . $table . '`')->getResultArray();
            foreach ($indexes as $idx) {
                if (($idx['Key_name'] ?? '') === $indexName) { $exists = true; break; }
            }
            if (!$exists) {
                $cols = array_map(function($c){ return '`' . $c . '`'; }, $columns);
                $this->db->query('ALTER TABLE `'.$table.'` ADD INDEX `'.$indexName.'` ('.implode(',', $cols).')');
            }
        } catch (\Throwable $e) {
            // no-op: avoid breaking migration chain; log if needed
        }
    }

    private function dropIndexIfExists(string $table, string $indexName)
    {
        try {
            if (! $this->db->tableExists($table)) return;
            $exists = false;
            $indexes = $this->db->query('SHOW INDEXES FROM `' . $table . '`')->getResultArray();
            foreach ($indexes as $idx) {
                if (($idx['Key_name'] ?? '') === $indexName) { $exists = true; break; }
            }
            if ($exists) {
                $this->db->query('ALTER TABLE `'.$table.'` DROP INDEX `'.$indexName.'`');
            }
        } catch (\Throwable $e) {
            // no-op
        }
    }
}

