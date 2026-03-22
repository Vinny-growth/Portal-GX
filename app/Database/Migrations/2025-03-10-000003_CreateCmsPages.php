<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCmsPages extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'title' => ['type'=>'VARCHAR','constraint'=>255],
            'slug' => ['type'=>'VARCHAR','constraint'=>255],
            'status' => ['type'=>'VARCHAR','constraint'=>20,'default'=>'draft'],
            'data_json' => ['type'=>'LONGTEXT','null'=>true], // working copy
            'published_json' => ['type'=>'LONGTEXT','null'=>true],
            'created_at' => ['type'=>'DATETIME','null'=>true],
            'updated_at' => ['type'=>'DATETIME','null'=>true],
            'published_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('slug');
        $this->forge->createTable('cms_pages', true);
    }

    public function down()
    {
        $this->forge->dropTable('cms_pages', true);
    }
}

