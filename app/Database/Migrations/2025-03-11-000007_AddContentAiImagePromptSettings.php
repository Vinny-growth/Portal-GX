<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContentAiImagePromptSettings extends Migration
{
    public function up()
    {
        $fields = [
            'image_guidelines' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'image_prompt_template' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
        ];
        $this->forge->addColumn('content_ai_settings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('content_ai_settings', 'image_guidelines');
        $this->forge->dropColumn('content_ai_settings', 'image_prompt_template');
    }
}
