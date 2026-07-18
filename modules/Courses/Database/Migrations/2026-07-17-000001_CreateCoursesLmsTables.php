<?php namespace Modules\Courses\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Schema base do módulo Courses — Fase 4a (LMS + jornada gamificada).
 *
 * Aditiva e idempotente (guard tableExists por tabela). NÃO altera nada da GX (o módulo
 * nasce desligado). Rodar com: php spark migrate -n "Modules\Courses".
 *
 * Cobre: LMS (courses/course_sections/lessons/lesson_progress/course_enrollments/
 * certificates), acesso manual (access_levels/user_access_levels) e gamificação da jornada
 * (points_ledger/achievements/user_achievements). Pagamento/membership fica p/ a Fase 4b;
 * aqui só existe o "seam" (canAccess usa nível manual + stub de membership).
 */
class CreateCoursesLmsTables extends Migration
{
    public function up()
    {
        $ts = [
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ];

        // ── courses ─────────────────────────────────────────────────────────
        if (!$this->db->tableExists('courses')) {
            $this->forge->addField([
                'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'title'             => ['type' => 'VARCHAR', 'constraint' => 255],
                'slug'              => ['type' => 'VARCHAR', 'constraint' => 255],
                'subtitle'          => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'description'       => ['type' => 'TEXT', 'null' => true],
                'cover_image'       => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'trailer_url'       => ['type' => 'VARCHAR', 'constraint' => 1000, 'null' => true],
                'category'          => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true], // carrossel/trilha
                'level'             => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],  // iniciante|intermediario|avancado
                'instructor'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'access_level_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true], // null = público/free
                'is_published'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'is_featured'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0], // hero do catálogo
                'drip_enabled'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0], // desbloqueio sequencial
                'xp_reward'         => ['type' => 'INT', 'constraint' => 11, 'default' => 0],     // XP ao concluir o curso
                'estimated_minutes' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'course_order'      => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'created_at'        => ['type' => 'DATETIME', 'null' => true],
                'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('slug');
            $this->forge->addKey(['is_published', 'is_featured']);
            $this->forge->addKey('category');
            $this->forge->createTable('courses', true);
        }

        // ── course_sections (módulos/seções) ────────────────────────────────
        if (!$this->db->tableExists('course_sections')) {
            $this->forge->addField([
                'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'course_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'title'         => ['type' => 'VARCHAR', 'constraint' => 255],
                'description'   => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'section_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'created_at'    => ['type' => 'DATETIME', 'null' => true],
                'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('course_id');
            $this->forge->createTable('course_sections', true);
        }

        // ── lessons ─────────────────────────────────────────────────────────
        if (!$this->db->tableExists('lessons')) {
            $this->forge->addField([
                'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'course_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'section_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'title'            => ['type' => 'VARCHAR', 'constraint' => 255],
                'slug'             => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'content_type'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'video'], // video|text
                'video_url'        => ['type' => 'VARCHAR', 'constraint' => 1000, 'null' => true],
                'video_provider'   => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true], // youtube|vimeo|bunny|mp4
                'body_html'        => ['type' => 'LONGTEXT', 'null' => true],
                'duration_seconds' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'access_level_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true], // override do curso
                'is_free_preview'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0], // aula-amostra sem acesso
                'xp_reward'        => ['type' => 'INT', 'constraint' => 11, 'default' => 10],
                'lesson_order'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'created_at'       => ['type' => 'DATETIME', 'null' => true],
                'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['course_id', 'section_id']);
            $this->forge->createTable('lessons', true);
        }

        // ── lesson_progress ─────────────────────────────────────────────────
        if (!$this->db->tableExists('lesson_progress')) {
            $this->forge->addField([
                'id'                    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'lesson_id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'course_id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'status'                => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'in_progress'], // in_progress|completed
                'progress_percent'      => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'last_position_seconds' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'completed_at'          => ['type' => 'DATETIME', 'null' => true],
                'created_at'            => ['type' => 'DATETIME', 'null' => true],
                'updated_at'            => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['user_id', 'lesson_id']);
            $this->forge->addKey(['user_id', 'course_id']);
            $this->forge->createTable('lesson_progress', true);
        }

        // ── course_enrollments (meus cursos / continuar) ────────────────────
        if (!$this->db->tableExists('course_enrollments')) {
            $this->forge->addField([
                'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'course_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'status'           => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'], // active|completed
                'progress_percent' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'last_lesson_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'started_at'       => ['type' => 'DATETIME', 'null' => true],
                'completed_at'     => ['type' => 'DATETIME', 'null' => true],
                'created_at'       => ['type' => 'DATETIME', 'null' => true],
                'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['user_id', 'course_id']);
            $this->forge->createTable('course_enrollments', true);
        }

        // ── certificates ────────────────────────────────────────────────────
        if (!$this->db->tableExists('certificates')) {
            $this->forge->addField([
                'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'course_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'code'       => ['type' => 'VARCHAR', 'constraint' => 40],
                'user_name'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'course_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'issued_at'  => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('code');
            $this->forge->addUniqueKey(['user_id', 'course_id']);
            $this->forge->createTable('certificates', true);
        }

        // ── access_levels (níveis de acesso — grant manual) ─────────────────
        if (!$this->db->tableExists('access_levels')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'name'        => ['type' => 'VARCHAR', 'constraint' => 120],
                'slug'        => ['type' => 'VARCHAR', 'constraint' => 120],
                'rank'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0], // maior = mais acesso
                'description' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('slug');
            $this->forge->createTable('access_levels', true);
        }

        // ── user_access_levels (concessão manual do admin) ──────────────────
        if (!$this->db->tableExists('user_access_levels')) {
            $this->forge->addField([
                'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'access_level_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'granted_by'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'granted_at'      => ['type' => 'DATETIME', 'null' => true],
                'expires_at'      => ['type' => 'DATETIME', 'null' => true],
                'created_at'      => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['user_id', 'access_level_id']);
            $this->forge->createTable('user_access_levels', true);
        }

        // ── points_ledger (XP — razão por transação) ────────────────────────
        if (!$this->db->tableExists('points_ledger')) {
            $this->forge->addField([
                'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'points'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'reason'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true], // lesson_complete|course_complete|achievement
                'ref_type'   => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true], // lesson|course|achievement
                'ref_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            // evita XP em dobro pela mesma razão+ref (ex.: concluir a mesma aula 2x)
            $this->forge->addUniqueKey(['user_id', 'reason', 'ref_type', 'ref_id']);
            $this->forge->createTable('points_ledger', true);
        }

        // ── achievements (badges/conquistas) ────────────────────────────────
        if (!$this->db->tableExists('achievements')) {
            $this->forge->addField([
                'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'name'           => ['type' => 'VARCHAR', 'constraint' => 120],
                'slug'           => ['type' => 'VARCHAR', 'constraint' => 120],
                'description'    => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'icon'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'criteria_type'  => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true], // first_lesson|course_complete|xp_threshold
                'criteria_value' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'xp_bonus'       => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'is_active'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at'     => ['type' => 'DATETIME', 'null' => true],
                'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('slug');
            $this->forge->createTable('achievements', true);
        }

        // ── user_achievements ───────────────────────────────────────────────
        if (!$this->db->tableExists('user_achievements')) {
            $this->forge->addField([
                'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'user_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'achievement_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'earned_at'      => ['type' => 'DATETIME', 'null' => true],
                'created_at'     => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['user_id', 'achievement_id']);
            $this->forge->createTable('user_achievements', true);
        }
    }

    public function down()
    {
        foreach ([
            'user_achievements', 'achievements', 'points_ledger', 'user_access_levels', 'access_levels',
            'certificates', 'course_enrollments', 'lesson_progress', 'lessons', 'course_sections', 'courses',
        ] as $t) {
            if ($this->db->tableExists($t)) {
                $this->forge->dropTable($t, true);
            }
        }
    }
}
