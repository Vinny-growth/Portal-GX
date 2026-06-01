<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NewsletterCaptureV2 extends Migration
{
    public function up()
    {
        // Singleton settings
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'double_opt_in_enabled' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'confirmation_subject' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'default' => 'Confirme sua inscrição na newsletter GX Capital',
            ],
            'confirmation_intro' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'confirmation_button_text' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'default' => 'Confirmar inscrição',
            ],
            'welcome_subject' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'default' => 'Bem-vindo à inteligência GX Capital',
            ],
            'welcome_intro' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'landing_hero_image' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'landing_eyebrow' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'Newsletter GX Capital',
            ],
            'landing_headline' => [
                'type' => 'VARCHAR',
                'constraint' => 300,
                'default' => 'Inteligência financeira que chega antes do mercado reagir',
            ],
            'landing_subheadline' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'landing_cta_text' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'default' => 'Inscrever-me',
            ],
            'landing_social_proof' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('newsletter_settings');

        // Seed singleton row
        $this->db->table('newsletter_settings')->insert([
            'double_opt_in_enabled' => 0,
            'confirmation_intro' => 'Você está a um clique de receber nossa inteligência financeira. Clique no botão abaixo para confirmar sua inscrição.',
            'welcome_intro' => 'Sua inscrição está confirmada. Como agradecimento, segue o material exclusivo que preparamos para você.',
            'landing_subheadline' => 'Briefings curtos e acionáveis sobre câmbio, crédito, economia e consórcio — direto para executivos que tomam decisões financeiras.',
            'landing_social_proof' => 'Mais de 1.500 executivos recebem a newsletter da GX Capital',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Lead magnets
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'comment' => 'relative path inside uploads/newsletter-magnets/',
            ],
            'cover_image' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'cta_text' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'Baixar material',
            ],
            'mime_type' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
            ],
            'file_size' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'downloads_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('slug', false, true);
        $this->forge->addKey('active');
        $this->forge->createTable('newsletter_lead_magnets');

        // Magnet downloads (audit log)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'magnet_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'subscriber_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
            ],
            'downloaded_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('token', false, true);
        $this->forge->addKey('magnet_id');
        $this->forge->createTable('newsletter_magnet_downloads');

        // Extend editorial lines with magnet relation
        $this->forge->addColumn('newsletter_editorial_lines', [
            'lead_magnet_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'cta_url',
            ],
        ]);

        // Extend subscribers for double opt-in
        $this->forge->addColumn('subscribers', [
            'confirm_token' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'after' => 'token',
            ],
            'confirmed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'last_engagement_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('subscribers', ['confirm_token', 'confirmed_at']);
        $this->forge->dropColumn('newsletter_editorial_lines', ['lead_magnet_id']);
        $this->forge->dropTable('newsletter_magnet_downloads');
        $this->forge->dropTable('newsletter_lead_magnets');
        $this->forge->dropTable('newsletter_settings');
    }
}
