<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhoneToContacts extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('phone', 'contacts')) {
            $this->forge->addColumn('contacts', [
                'phone' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => true,
                    'after' => 'email',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('phone', 'contacts')) {
            $this->forge->dropColumn('contacts', 'phone');
        }
    }
}
