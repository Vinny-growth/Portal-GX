<?php namespace Modules\Courses\Libraries;

use Modules\Courses\Models\LessonModel;
use Modules\Courses\Models\LessonProgressModel;
use Modules\Courses\Models\EnrollmentModel;
use Modules\Courses\Models\CertificateModel;
use Modules\Courses\Models\PointsLedgerModel;
use Modules\Courses\Models\AchievementModel;

/**
 * Motor da jornada gamificada (Fase 4a). Orquestra, de forma IDEMPOTENTE:
 * concluir aula → creditar XP → recalcular progresso do curso → (se 100%) certificado +
 * XP de conclusão → avaliar conquistas. Chamar 2x a mesma conclusão NÃO duplica XP nem
 * reemite certificado (unique keys em points_ledger/certificates/user_achievements).
 */
class LearningService
{
    protected LessonModel $lessons;
    protected LessonProgressModel $progress;
    protected EnrollmentModel $enrollments;
    protected CertificateModel $certificates;
    protected PointsLedgerModel $points;
    protected AchievementModel $achievements;
    protected $db;

    public function __construct()
    {
        $this->lessons      = new LessonModel();
        $this->progress     = new LessonProgressModel();
        $this->enrollments  = new EnrollmentModel();
        $this->certificates = new CertificateModel();
        $this->points       = new PointsLedgerModel();
        $this->achievements = new AchievementModel();
        $this->db           = \Config\Database::connect();
    }

    /**
     * Marca uma aula como concluída e propaga tudo. Retorna um resumo para a UI.
     * @return array{xp_awarded:int,total_xp:int,course_percent:int,course_completed:bool,certificate:?array,new_achievements:array}
     */
    public function markLessonComplete(int $userId, array $lesson, array $course, string $userName): array
    {
        $lessonId = (int) $lesson['id'];
        $courseId = (int) $course['id'];
        $now = date('Y-m-d H:i:s');

        $existing = $this->progress->getFor($userId, $lessonId);
        $wasCompleted = !empty($existing) && ($existing['status'] ?? '') === 'completed';

        // 1) marca a aula concluída (100%)
        if (empty($existing)) {
            $this->progress->insert([
                'user_id' => $userId, 'lesson_id' => $lessonId, 'course_id' => $courseId,
                'status' => 'completed', 'progress_percent' => 100, 'completed_at' => $now,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        } elseif (!$wasCompleted) {
            $this->db->table('lesson_progress')->where('user_id', $userId)->where('lesson_id', $lessonId)
                ->update(['status' => 'completed', 'progress_percent' => 100, 'completed_at' => $now, 'updated_at' => $now]);
        }

        $xpAwarded = 0;

        // 2) XP da aula (idempotente)
        if ($this->points->award($userId, (int) ($lesson['xp_reward'] ?? 0), 'lesson_complete', 'lesson', $lessonId)) {
            $xpAwarded += (int) ($lesson['xp_reward'] ?? 0);
        }

        // 3) recalcula progresso do curso
        $total = max(1, $this->lessons->countForCourse($courseId));
        $done  = $this->progress->completedCountForCourse($userId, $courseId);
        $percent = (int) floor(($done / $total) * 100);
        $courseCompleted = $done >= $total;

        $this->enrollments->ensure($userId, $courseId);
        $enrollBefore = $this->enrollments->getFor($userId, $courseId);
        $courseWasCompleted = !empty($enrollBefore) && ($enrollBefore['status'] ?? '') === 'completed';
        $this->enrollments->updateProgress($userId, $courseId, $percent, $lessonId, $courseCompleted);

        // 4) conclusão do curso (só na 1ª vez): XP + certificado
        $certificate = null;
        if ($courseCompleted) {
            if (!$courseWasCompleted && $this->points->award($userId, (int) ($course['xp_reward'] ?? 0), 'course_complete', 'course', $courseId)) {
                $xpAwarded += (int) ($course['xp_reward'] ?? 0);
            }
            $certificate = $this->certificates->issue($userId, $courseId, $userName, (string) ($course['title'] ?? 'Curso'));
        }

        // 5) conquistas
        $newAchievements = $this->evaluateAchievements($userId);
        foreach ($newAchievements as $a) {
            if ((int) ($a['xp_bonus'] ?? 0) > 0
                && $this->points->award($userId, (int) $a['xp_bonus'], 'achievement', 'achievement', (int) $a['id'])) {
                $xpAwarded += (int) $a['xp_bonus'];
            }
        }

        return [
            'xp_awarded'       => $xpAwarded,
            'total_xp'         => $this->points->totalFor($userId),
            'course_percent'   => $percent,
            'course_completed' => $courseCompleted,
            'certificate'      => $certificate,
            'new_achievements' => $newAchievements,
        ];
    }

    /** Avalia todas as conquistas ativas e concede as recém-alcançadas. Retorna as novas. */
    public function evaluateAchievements(int $userId): array
    {
        $active = $this->achievements->active();
        if (empty($active)) {
            return [];
        }
        $earned = array_flip($this->achievements->userEarnedIds($userId));

        $completedLessons = $this->completedLessonCount($userId);
        $completedCourses = $this->completedCourseCount($userId);
        $totalXp = $this->points->totalFor($userId);

        $new = [];
        foreach ($active as $a) {
            if (isset($earned[(int) $a['id']])) {
                continue;
            }
            $val = (int) ($a['criteria_value'] ?? 0);
            $met = false;
            switch ($a['criteria_type'] ?? '') {
                case 'first_lesson':      $met = $completedLessons >= 1; break;
                case 'lessons_completed': $met = $completedLessons >= max(1, $val); break;
                case 'course_complete':   $met = $completedCourses >= max(1, $val); break;
                case 'xp_threshold':      $met = $totalXp >= $val; break;
            }
            if ($met && $this->achievements->grant($userId, (int) $a['id'])) {
                $new[] = $a;
            }
        }
        return $new;
    }

    public function completedLessonCount(int $userId): int
    {
        $row = $this->db->table('lesson_progress')->select('COUNT(*) AS c')
            ->where('user_id', $userId)->where('status', 'completed')->get()->getFirstRow();
        return $row ? (int) $row->c : 0;
    }

    public function completedCourseCount(int $userId): int
    {
        $row = $this->db->table('course_enrollments')->select('COUNT(*) AS c')
            ->where('user_id', $userId)->where('status', 'completed')->get()->getFirstRow();
        return $row ? (int) $row->c : 0;
    }

    /** Registra progresso parcial (posição do vídeo/percentual) sem concluir. */
    public function saveProgress(int $userId, array $lesson, int $courseId, int $percent, int $positionSeconds): void
    {
        $this->enrollments->ensure($userId, $courseId);
        $this->progress->touchProgress($userId, (int) $lesson['id'], $courseId, $percent, $positionSeconds);
        // atualiza "continuar de onde parou"
        $total = max(1, $this->lessons->countForCourse($courseId));
        $done  = $this->progress->completedCountForCourse($userId, $courseId);
        $this->enrollments->updateProgress($userId, $courseId, (int) floor(($done / $total) * 100), (int) $lesson['id'], false);
    }
}
