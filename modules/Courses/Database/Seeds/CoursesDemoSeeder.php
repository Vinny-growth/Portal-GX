<?php namespace Modules\Courses\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seed do módulo Courses (Fase 4a). Idempotente. Rodar por-install com:
 *   php spark db:seed "Modules\Courses\Database\Seeds\CoursesDemoSeeder"
 *
 * Semeia o mínimo para o módulo ser demonstrável ao ligar: um nível de acesso, o conjunto
 * base de conquistas (necessário p/ a gamificação premiar badges) e um curso-trilha demo.
 * NÃO é rodado na GX Brasil (módulo desligado) — é a base do install de Educação.
 */
class CoursesDemoSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // nível de acesso base
        if ($this->db->table('access_levels')->where('slug', 'nivel-1')->countAllResults() === 0) {
            $this->db->table('access_levels')->insert([
                'name' => 'Nível 1', 'slug' => 'nivel-1', 'rank' => 1,
                'description' => 'Acesso ao conteúdo premium liberado por grant manual.',
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // conquistas base da jornada (a gamificação só premia badges que existam aqui)
        $achievements = [
            ['Primeiro passo', 'primeiro-passo', 'Concluiu a primeira aula.', '🎯', 'first_lesson', 0, 15],
            ['Maratonista', 'maratonista', 'Concluiu 10 aulas.', '🏃', 'lessons_completed', 10, 50],
            ['Trilha concluída', 'trilha-concluida', 'Concluiu a primeira trilha completa.', '🏆', 'course_complete', 1, 75],
            ['Centurião', 'centuriao', 'Alcançou 100 XP.', '💯', 'xp_threshold', 100, 0],
            ['Mestre', 'mestre', 'Alcançou 500 XP.', '👑', 'xp_threshold', 500, 0],
        ];
        foreach ($achievements as $a) {
            if ($this->db->table('achievements')->where('slug', $a[1])->countAllResults() === 0) {
                $this->db->table('achievements')->insert([
                    'name' => $a[0], 'slug' => $a[1], 'description' => $a[2], 'icon' => $a[3],
                    'criteria_type' => $a[4], 'criteria_value' => $a[5], 'xp_bonus' => $a[6],
                    'is_active' => 1, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        // espaços da comunidade (Fase 4c)
        $spaces = [
            ['Apresente-se', 'apresente-se', '👋', 1],
            ['Dúvidas', 'duvidas', '❓', 2],
            ['Vitórias', 'vitorias', '🏆', 3],
            ['Networking', 'networking', '🤝', 4],
        ];
        foreach ($spaces as $sp) {
            if ($this->db->table('community_spaces')->where('slug', $sp[1])->countAllResults() === 0) {
                $this->db->table('community_spaces')->insert([
                    'name' => $sp[0], 'slug' => $sp[1], 'icon' => $sp[2], 'sort' => $sp[3],
                    'is_active' => 1, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        // curso-trilha demo (idempotente por slug)
        $slug = 'jornada-financeira-essencial';
        if ($this->db->table('courses')->where('slug', $slug)->countAllResults() === 0) {
            $this->db->table('courses')->insert([
                'title' => 'Jornada Financeira Essencial', 'slug' => $slug,
                'subtitle' => 'Do zero à sua primeira carteira inteligente',
                'description' => '<p>Uma trilha gamificada para dominar os fundamentos das finanças pessoais e de investimentos, passo a passo.</p>',
                'category' => 'Trilhas em destaque', 'level' => 'iniciante', 'instructor' => 'GX Capital',
                'access_level_id' => null, 'is_published' => 1, 'is_featured' => 1, 'drip_enabled' => 0,
                'xp_reward' => 100, 'estimated_minutes' => 45, 'course_order' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $courseId = (int) $this->db->insertID();
            $this->db->table('course_sections')->insert([
                'course_id' => $courseId, 'title' => 'Módulo 1 — Fundamentos', 'section_order' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $sectionId = (int) $this->db->insertID();
            $lessons = [
                ['Bem-vindo à jornada', 'bem-vindo-a-jornada', 1, 1],
                ['Orçamento que funciona', 'orcamento-que-funciona', 2, 0],
                ['Sua reserva de emergência', 'sua-reserva-de-emergencia', 3, 0],
            ];
            foreach ($lessons as $L) {
                $this->db->table('lessons')->insert([
                    'course_id' => $courseId, 'section_id' => $sectionId, 'title' => $L[0], 'slug' => $L[1],
                    'content_type' => 'video', 'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                    'video_provider' => 'youtube', 'body_html' => '<p>Conteúdo da aula: ' . $L[0] . '.</p>',
                    'duration_seconds' => 600, 'is_free_preview' => $L[3], 'xp_reward' => 10,
                    'lesson_order' => $L[2], 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }
}
