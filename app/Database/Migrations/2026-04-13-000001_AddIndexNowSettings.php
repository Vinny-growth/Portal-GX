<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexNowSettings extends Migration
{
    public function up()
    {
        $this->forge->addColumn('general_settings', [
            'indexnow_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'google_indexing_api',
            ],
            'indexnow_api_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
                'after'      => 'indexnow_enabled',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('general_settings', ['indexnow_enabled', 'indexnow_api_key']);
    }
}
