<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adds the HH:MM "slot" that triggered each newsletter edition so the scheduler
 * can dedupe per (line, day, slot) instead of per (line, day) — enabling lines
 * with multiple send_times per day to produce one edition per slot.
 */
class AddSlotToNewsletterSends extends Migration
{
    public function up()
    {
        $this->forge->addColumn('newsletter_sends', [
            'slot' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => true,
                'after'      => 'editorial_line_id',
                'comment'    => 'HH:MM send_times slot that triggered this edition',
            ],
        ]);
        // Speeds up the scheduler dedupe lookup (line + slot + generated day).
        $this->db->query('CREATE INDEX newsletter_sends_line_slot ON newsletter_sends (editorial_line_id, slot)');
    }

    public function down()
    {
        $this->db->query('DROP INDEX newsletter_sends_line_slot ON newsletter_sends');
        $this->forge->dropColumn('newsletter_sends', 'slot');
    }
}
