<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCmsTemplates extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'title' => ['type'=>'VARCHAR','constraint'=>255],
            'type' => ['type'=>'VARCHAR','constraint'=>50,'default'=>'section'],
            'json' => ['type'=>'LONGTEXT','null'=>true],
            'created_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('cms_templates', true);
    }

    public function down()
    {
        $this->forge->dropTable('cms_templates', true);
    }
}

