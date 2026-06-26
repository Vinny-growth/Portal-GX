<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOriginTrackingToSimLeads extends Migration
{
    public function up()
    {
        $columns = [
            'origem' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'observations',
            ],
            'utm_source' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
                'null' => true,
                'after' => 'origem',
            ],
            'utm_medium' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
                'null' => true,
                'after' => 'utm_source',
            ],
            'utm_campaign' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
                'null' => true,
                'after' => 'utm_medium',
            ],
            'utm_term' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
                'null' => true,
                'after' => 'utm_campaign',
            ],
            'utm_content' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
                'null' => true,
                'after' => 'utm_term',
            ],
            'landing_page' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'utm_content',
            ],
            'referrer' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'landing_page',
            ],
        ];

        $toAdd = [];
        foreach ($columns as $name => $definition) {
            if (!$this->db->fieldExists($name, 'sim_leads')) {
                $toAdd[$name] = $definition;
            }
        }

        if (!empty($toAdd)) {
            $this->forge->addColumn('sim_leads', $toAdd);
        }

        // Índice para agregação "leads por origem" no dashboard.
        if (!$this->indexExists('sim_leads', 'idx_sim_leads_origem')) {
            $this->db->query('CREATE INDEX idx_sim_leads_origem ON sim_leads (origem)');
        }
    }

    public function down()
    {
        if ($this->indexExists('sim_leads', 'idx_sim_leads_origem')) {
            $this->db->query('DROP INDEX idx_sim_leads_origem ON sim_leads');
        }

        foreach (['origem', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'landing_page', 'referrer'] as $name) {
            if ($this->db->fieldExists($name, 'sim_leads')) {
                $this->forge->dropColumn('sim_leads', $name);
            }
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $db = $this->db->getDatabase();
        $row = $this->db->query(
            'SELECT COUNT(*) AS total FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$db, $table, $index]
        )->getRow();

        return $row && (int) $row->total > 0;
    }
}
