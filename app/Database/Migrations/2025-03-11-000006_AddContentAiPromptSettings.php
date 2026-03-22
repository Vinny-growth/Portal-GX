<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContentAiPromptSettings extends Migration
{
    public function up()
    {
        $fields = [
            'voice_guidelines' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'seo_guidelines' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'prompt_template' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'length_short_words' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 900,
            ],
            'length_medium_words' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1400,
            ],
            'length_long_words' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 2000,
            ],
            'category_rules_json' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'category_guidelines_json' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
        ];
        $this->forge->addColumn('content_ai_settings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('content_ai_settings', 'voice_guidelines');
        $this->forge->dropColumn('content_ai_settings', 'seo_guidelines');
        $this->forge->dropColumn('content_ai_settings', 'prompt_template');
        $this->forge->dropColumn('content_ai_settings', 'length_short_words');
        $this->forge->dropColumn('content_ai_settings', 'length_medium_words');
        $this->forge->dropColumn('content_ai_settings', 'length_long_words');
        $this->forge->dropColumn('content_ai_settings', 'category_rules_json');
        $this->forge->dropColumn('content_ai_settings', 'category_guidelines_json');
    }
}
