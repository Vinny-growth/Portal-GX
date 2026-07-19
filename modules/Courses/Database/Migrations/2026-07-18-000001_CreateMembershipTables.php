<?php namespace Modules\Courses\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Schema de acesso/pagamento do módulo Courses — Fase 4b (§7.3 do plano).
 *
 * Modelo ENXUTO graças ao "acesso único" (AUVP): UM membership por pessoa desbloqueia tudo.
 * Identidade casada por DOCUMENTO NACIONAL (CPF/CURP/RFC), não e-mail — o CRM da GX dispara
 * webhooks por documento. Aditiva/idempotente. Rodar: php spark migrate -n "Modules\Courses".
 */
class CreateMembershipTables extends Migration
{
    public function up()
    {
        // ── memberships (1 por documento; acesso = client_active OU dentro do pago OU carência) ──
        if (!$this->db->tableExists('memberships')) {
            $this->forge->addField([
                'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'document'     => ['type' => 'VARCHAR', 'constraint' => 32], // CPF/CURP/RFC — chave de identidade
                'doc_type'     => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'cpf'],
                'source'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'paid'], // paid|client_comp|manual
                'client_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0], // é cliente GX ativo → comp
                'status'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'], // active|grace|expired|canceled (cache)
                'started_at'   => ['type' => 'DATETIME', 'null' => true],
                'paid_until'   => ['type' => 'DATETIME', 'null' => true], // fim do período pago (12m); null = nunca pagou
                'access_until' => ['type' => 'DATETIME', 'null' => true], // corte explícito/carência; null = aberto (enquanto client_active)
                'canceled_at'  => ['type' => 'DATETIME', 'null' => true],
                'created_at'   => ['type' => 'DATETIME', 'null' => true],
                'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('document');
            $this->forge->addKey('user_id');
            $this->forge->addKey('status');
            $this->forge->createTable('memberships', true);
        }

        // ── payments (histórico de cobranças) ────────────────────────────────
        if (!$this->db->tableExists('payments')) {
            $this->forge->addField([
                'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'membership_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'user_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'document'           => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
                'gateway'            => ['type' => 'VARCHAR', 'constraint' => 20], // mercadopago|stripe|manual
                'gateway_payment_id' => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => true],
                'amount'             => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
                'currency'           => ['type' => 'VARCHAR', 'constraint' => 8, 'default' => 'BRL'],
                'status'             => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'], // pending|paid|failed|refunded
                'period_start'       => ['type' => 'DATETIME', 'null' => true],
                'period_end'         => ['type' => 'DATETIME', 'null' => true],
                'raw_json'           => ['type' => 'LONGTEXT', 'null' => true],
                'created_at'         => ['type' => 'DATETIME', 'null' => true],
                'updated_at'         => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('membership_id');
            $this->forge->addKey('gateway_payment_id');
            $this->forge->createTable('payments', true);
        }

        // ── payment_events (log bruto de webhooks de pagamento — dedup/idempotência) ──
        if (!$this->db->tableExists('payment_events')) {
            $this->forge->addField([
                'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'gateway'      => ['type' => 'VARCHAR', 'constraint' => 20],
                'event_type'   => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
                'gateway_ref'  => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true], // id do evento/pagamento p/ dedup
                'payload_json' => ['type' => 'LONGTEXT', 'null' => true],
                'processed'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'created_at'   => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['gateway', 'gateway_ref']);
            $this->forge->createTable('payment_events', true);
        }

        // ── crm_client_events (webhooks de cliente do CRM da GX — concede/corta comp) ──
        if (!$this->db->tableExists('crm_client_events')) {
            $this->forge->addField([
                'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'document'     => ['type' => 'VARCHAR', 'constraint' => 32],
                'doc_type'     => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
                'event_type'   => ['type' => 'VARCHAR', 'constraint' => 40], // client_activated|client_canceled
                'event_ref'    => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
                'payload_json' => ['type' => 'LONGTEXT', 'null' => true],
                'processed'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'created_at'   => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('event_ref');
            $this->forge->addKey('document');
            $this->forge->createTable('crm_client_events', true);
        }
    }

    public function down()
    {
        foreach (['crm_client_events', 'payment_events', 'payments', 'memberships'] as $t) {
            if ($this->db->tableExists($t)) {
                $this->forge->dropTable($t, true);
            }
        }
    }
}
