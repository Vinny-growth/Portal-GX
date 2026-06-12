<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOriginToSeoKeywords extends Migration
{
    public function up()
    {
        $this->forge->addColumn('seo_keywords', [
            'origin' => [
                'type'    => 'VARCHAR',
                'constraint' => 20,
                'default' => 'manual',
                'comment' => 'manual | content (sincronizada dos artigos)',
                'after'   => 'source',
            ],
            'post_count' => [
                'type'    => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'em quantos artigos/tags a palavra-chave aparece',
                'after'   => 'origin',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('seo_keywords', ['origin', 'post_count']);
    }
}
