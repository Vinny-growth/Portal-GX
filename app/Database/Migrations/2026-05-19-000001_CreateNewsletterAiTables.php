<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNewsletterAiTables extends Migration
{
    public function up()
    {
        // Editorial lines
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'category_ids' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of category ids',
            ],
            'send_times' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of HH:MM send times',
            ],
            'frequency' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'daily',
                'comment' => 'daily, weekly, on_demand',
            ],
            'posts_per_edition' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 5,
            ],
            'lookback_hours' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 24,
            ],
            'ai_auto_publish' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 = scheduler envia direto; 0 = requer aprovação',
            ],
            'subject_prompt' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'body_prompt' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'cta_text' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'cta_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'enabled' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'last_sent_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addKey('slug', false, true);
        $this->forge->addKey('enabled');
        $this->forge->createTable('newsletter_editorial_lines');

        // Sends (queue of editions)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'editorial_line_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'preheader' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'html_body' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'text_body' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'post_ids' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of post ids included',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'draft',
                'comment' => 'draft, approved, sending, sent, failed, canceled',
            ],
            'scheduled_for' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'generated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'approved_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'recipients_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'delivered_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'opens_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'clicks_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'ai_prompt' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'ai_response' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'error' => [
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
        $this->forge->addKey('editorial_line_id');
        $this->forge->addKey('status');
        $this->forge->addKey('scheduled_for');
        $this->forge->createTable('newsletter_sends');

        // Recipients per send
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'send_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'subscriber_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'pending',
                'comment' => 'pending, sent, failed, bounced, unsubscribed',
            ],
            'delivered_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'error' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['send_id', 'subscriber_id'], false, true);
        $this->forge->addKey('status');
        $this->forge->createTable('newsletter_send_recipients');

        // Email tracking (open + click aggregate per recipient)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'send_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'subscriber_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'comment' => 'opaque token used in pixel URL',
            ],
            'opened_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_opened_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'open_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'first_click_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_click_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'click_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'user_agent' => [
                'type' => 'VARCHAR',
                'constraint' => 512,
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('token', false, true);
        $this->forge->addKey(['send_id', 'subscriber_id'], false, true);
        $this->forge->createTable('newsletter_email_tracking');

        // Link tracking (one row per link per send)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'send_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
            ],
            'original_url' => [
                'type' => 'TEXT',
            ],
            'label' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'cta, post, footer, etc',
            ],
            'click_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'last_clicked_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('token', false, true);
        $this->forge->addKey('send_id');
        $this->forge->createTable('newsletter_link_tracking');

        // Link click log (granular - one row per click event)
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
            'send_id' => [
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
            'clicked_at' => [
                'type' => 'DATETIME',
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('link_id');
        $this->forge->addKey('send_id');
        $this->forge->addKey('subscriber_id');
        $this->forge->createTable('newsletter_link_clicks');

        // Extend subscribers
        $this->forge->addColumn('subscribers', [
            'editorial_line_ids' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'token',
                'comment' => 'JSON array of editorial_line ids the subscriber receives',
            ],
            'source_category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'editorial_line_ids',
            ],
            'source_post_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'source_category_id',
            ],
            'source_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'after' => 'source_post_id',
            ],
            'engagement_score' => [
                'type' => 'DECIMAL',
                'constraint' => '6,2',
                'default' => 0,
                'after' => 'source_url',
            ],
            'preferred_send_time' => [
                'type' => 'TIME',
                'null' => true,
                'after' => 'engagement_score',
            ],
            'last_engagement_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'preferred_send_time',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'active',
                'after' => 'last_engagement_at',
                'comment' => 'active, unsubscribed, bounced',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('subscribers', [
            'editorial_line_ids',
            'source_category_id',
            'source_post_id',
            'source_url',
            'engagement_score',
            'preferred_send_time',
            'last_engagement_at',
            'status',
        ]);
        $this->forge->dropTable('newsletter_link_clicks');
        $this->forge->dropTable('newsletter_link_tracking');
        $this->forge->dropTable('newsletter_email_tracking');
        $this->forge->dropTable('newsletter_send_recipients');
        $this->forge->dropTable('newsletter_sends');
        $this->forge->dropTable('newsletter_editorial_lines');
    }
}
