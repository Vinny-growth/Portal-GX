<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEditorChiefSettings extends Migration
{
    public function up()
    {
        $this->forge->addColumn('content_ai_settings', [
            'topic_weights_json' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'category_guidelines_json',
            ],
            'editor_prompt' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'topic_weights_json',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('content_ai_settings', ['topic_weights_json', 'editor_prompt']);
    }
}
