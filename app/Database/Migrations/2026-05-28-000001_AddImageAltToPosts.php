<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImageAltToPosts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('posts', [
            'image_alt' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'image_url',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('posts', 'image_alt');
    }
}
