<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBioLinkClicksTable extends Migration
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
            'link_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'VARCHAR',
                'constraint' => 512,
                'null' => true,
            ],
            'referrer' => [
                'type' => 'VARCHAR',
                'constraint' => 512,
                'null' => true,
            ],
            'clicked_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('link_id');
        $this->forge->addKey('clicked_at');
        $this->forge->createTable('bio_link_clicks');
    }

    public function down()
    {
        $this->forge->dropTable('bio_link_clicks');
    }
}
