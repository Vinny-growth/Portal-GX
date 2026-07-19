<?php namespace Modules\Courses\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Schema da comunidade do módulo Courses — Fase 4c (§7.1 do plano).
 *
 * Tabelas PREFIXADAS com community_ DE PROPÓSITO: posts/comments/reactions já existem
 * (blog). Aditiva/idempotente. Rodar: php spark migrate -n "Modules\Courses".
 * Gamificação reusa points_ledger/achievements do 4a (novos reasons de XP).
 */
class CreateCommunityTables extends Migration
{
    public function up()
    {
        // ── community_spaces (espaços/categorias temáticos) ──────────────────
        if (!$this->db->tableExists('community_spaces')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'name'        => ['type' => 'VARCHAR', 'constraint' => 120],
                'slug'        => ['type' => 'VARCHAR', 'constraint' => 120],
                'description' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'icon'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'color'       => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'sort'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('slug');
            $this->forge->createTable('community_spaces', true);
        }

        // ── community_posts (feed) ───────────────────────────────────────────
        if (!$this->db->tableExists('community_posts')) {
            $this->forge->addField([
                'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'space_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'user_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'title'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'body'          => ['type' => 'TEXT', 'null' => true],
                'is_pinned'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'is_removed'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'reaction_count' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'comment_count' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'created_at'    => ['type' => 'DATETIME', 'null' => true],
                'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['space_id', 'is_removed']);
            $this->forge->addKey('user_id');
            $this->forge->addKey(['is_pinned', 'created_at']);
            $this->forge->createTable('community_posts', true);
        }

        // ── community_comments ───────────────────────────────────────────────
        if (!$this->db->tableExists('community_comments')) {
            $this->forge->addField([
                'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'post_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'body'       => ['type' => 'TEXT', 'null' => true],
                'is_removed' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['post_id', 'is_removed']);
            $this->forge->createTable('community_comments', true);
        }

        // ── community_reactions (1 por usuário/alvo) ─────────────────────────
        if (!$this->db->tableExists('community_reactions')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'target_type' => ['type' => 'VARCHAR', 'constraint' => 12], // post|comment
                'target_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'type'        => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'like'],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['user_id', 'target_type', 'target_id']);
            $this->forge->addKey(['target_type', 'target_id']);
            $this->forge->createTable('community_reactions', true);
        }

        // ── community_profiles (perfil do membro) ────────────────────────────
        if (!$this->db->tableExists('community_profiles')) {
            $this->forge->addField([
                'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'display_name' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
                'bio'          => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'avatar_url'   => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'created_at'   => ['type' => 'DATETIME', 'null' => true],
                'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('user_id');
            $this->forge->createTable('community_profiles', true);
        }

        // ── community_notifications ──────────────────────────────────────────
        if (!$this->db->tableExists('community_notifications')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true], // destinatário
                'actor_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'type'        => ['type' => 'VARCHAR', 'constraint' => 20], // comment|reaction
                'target_type' => ['type' => 'VARCHAR', 'constraint' => 12, 'null' => true],
                'target_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'message'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'link'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'is_read'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['user_id', 'is_read']);
            $this->forge->createTable('community_notifications', true);
        }
    }

    public function down()
    {
        foreach ([
            'community_notifications', 'community_profiles', 'community_reactions',
            'community_comments', 'community_posts', 'community_spaces',
        ] as $t) {
            if ($this->db->tableExists($t)) {
                $this->forge->dropTable($t, true);
            }
        }
    }
}
