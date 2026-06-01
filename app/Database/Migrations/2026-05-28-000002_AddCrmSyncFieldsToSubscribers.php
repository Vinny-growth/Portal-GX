<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCrmSyncFieldsToSubscribers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('subscribers', [
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'email',
            ],
            'source_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'source_url',
            ],
            'crm_external_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'source_type',
            ],
            'unsubscribed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'status',
            ],
            'last_synced_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'unsubscribed_at',
            ],
        ]);

        // Backfill source_type for existing rows so audit makes sense.
        $this->db->table('subscribers')
            ->where('source_type IS NULL', null, false)
            ->update(['source_type' => 'organic']);

        // Deduplicate emails before adding the UNIQUE constraint.
        // Strategy: keep the lowest id per email; mark the rest as unsubscribed.
        $dupes = $this->db->query(
            'SELECT email, MIN(id) AS keep_id, COUNT(*) AS c
             FROM subscribers
             WHERE email IS NOT NULL AND email <> ""
             GROUP BY email HAVING c > 1'
        )->getResultArray();
        foreach ($dupes as $row) {
            $this->db->table('subscribers')
                ->where('email', $row['email'])
                ->where('id !=', (int) $row['keep_id'])
                ->update([
                    'status'          => 'unsubscribed',
                    'unsubscribed_at' => date('Y-m-d H:i:s'),
                    'email'           => null, // free the unique slot but keep history
                ]);
        }

        $this->forge->addKey('email', false, true, 'subscribers_email_unique');
        $this->forge->processIndexes('subscribers');

        // Composite index for crm lookup
        $this->db->query('CREATE INDEX subscribers_crm_lookup ON subscribers (source_type, crm_external_id)');

        // Audit table — one row per sync execution (one endpoint = one row)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'source' => [
                'type'       => 'VARCHAR',
                'constraint' => 20, // 'leads' | 'clients'
            ],
            'trigger_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 10, // 'cron' | 'manual'
                'default'    => 'cron',
            ],
            'updated_since' => [
                'type' => 'DATETIME',
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
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20, // 'running' | 'success' | 'failed'
                'default'    => 'running',
            ],
            'pages_fetched' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'total_received' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'updated_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'skipped_unsubscribed' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'skipped_invalid' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'filtered_opt_out_total' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'error_log' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'performed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('source');
        $this->forge->addKey('started_at');
        $this->forge->addKey('status');
        $this->forge->createTable('newsletter_crm_syncs');
    }

    public function down()
    {
        $this->forge->dropTable('newsletter_crm_syncs');
        $this->db->query('DROP INDEX subscribers_crm_lookup ON subscribers');
        $this->db->query('ALTER TABLE subscribers DROP INDEX subscribers_email_unique');
        $this->forge->dropColumn('subscribers', [
            'name',
            'source_type',
            'crm_external_id',
            'unsubscribed_at',
            'last_synced_at',
        ]);
    }
}
