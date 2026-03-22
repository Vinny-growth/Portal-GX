<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWealthManagerTables extends Migration
{
    public function up()
    {
        // wm_user_profile
        $this->forge->addField([
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'estado_civil' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'ano_nascimento' => ['type' => 'INT', 'constraint' => 4, 'null' => true],
            'perfil_risco' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'horizonte' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'consent_accepted_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('user_id', true);
        $this->forge->createTable('wm_user_profile', true);

        // wm_income_expense
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tipo' => ['type' => 'ENUM', 'constraint' => ['renda', 'despesa']],
            'categoria' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'valor_mensal' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('tipo');
        $this->forge->createTable('wm_income_expense', true);

        // wm_assets_financial
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'classe' => ['type' => 'VARCHAR', 'constraint' => 50],
            'subtipo' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'valor_atual' => ['type' => 'DECIMAL', 'constraint' => '18,2', 'default' => '0.00'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('classe');
        $this->forge->createTable('wm_assets_financial', true);

        // wm_assets_realestate
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tipo' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'valor_estimado' => ['type' => 'DECIMAL', 'constraint' => '18,2', 'default' => '0.00'],
            'renda_aluguel' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'saldo_divida' => ['type' => 'DECIMAL', 'constraint' => '18,2', 'default' => '0.00'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('wm_assets_realestate', true);

        // wm_business_holdings
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'participacao_pct' => ['type' => 'DECIMAL', 'constraint' => '7,2', 'default' => '0.00'],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('wm_business_holdings', true);

        // wm_liabilities
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tipo' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'saldo_atual' => ['type' => 'DECIMAL', 'constraint' => '18,2', 'default' => '0.00'],
            'taxa_aprox' => ['type' => 'DECIMAL', 'constraint' => '7,3', 'default' => '0.000'],
            'prazo_meses' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('wm_liabilities', true);

        // wm_goals
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nome_meta' => ['type' => 'VARCHAR', 'constraint' => 255],
            'valor_objetivo' => ['type' => 'DECIMAL', 'constraint' => '18,2', 'default' => '0.00'],
            'prazo_meses' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'prioridade' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('wm_goals', true);

        // wm_sessions
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'started_at' => ['type' => 'DATETIME', 'null' => true],
            'ended_at' => ['type' => 'DATETIME', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'ativa'],
            'messages_count' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('status');
        $this->forge->createTable('wm_sessions', true);

        // wm_messages
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'session_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'role' => ['type' => 'ENUM', 'constraint' => ['user', 'agent']],
            'content' => ['type' => 'TEXT'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('session_id');
        $this->forge->createTable('wm_messages', true);

        // wm_tokens
        $this->forge->addField([
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tokens_disponiveis' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('user_id', true);
        $this->forge->createTable('wm_tokens', true);

        // wm_settings (key-value)
        $this->forge->addField([
            'chave' => ['type' => 'VARCHAR', 'constraint' => 191],
            'valor' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('chave', true);
        $this->forge->createTable('wm_settings', true);

        // wm_appointments
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'telefone' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'preferencia_horario' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'novo'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('status');
        $this->forge->createTable('wm_appointments', true);

        // wm_audit_logs
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'admin_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'acao' => ['type' => 'VARCHAR', 'constraint' => 100],
            'detalhes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('admin_id');
        $this->forge->createTable('wm_audit_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('wm_audit_logs', true);
        $this->forge->dropTable('wm_appointments', true);
        $this->forge->dropTable('wm_settings', true);
        $this->forge->dropTable('wm_tokens', true);
        $this->forge->dropTable('wm_messages', true);
        $this->forge->dropTable('wm_sessions', true);
        $this->forge->dropTable('wm_goals', true);
        $this->forge->dropTable('wm_liabilities', true);
        $this->forge->dropTable('wm_business_holdings', true);
        $this->forge->dropTable('wm_assets_realestate', true);
        $this->forge->dropTable('wm_assets_financial', true);
        $this->forge->dropTable('wm_income_expense', true);
        $this->forge->dropTable('wm_user_profile', true);
    }
}
