<?php

namespace Modules\Courses\Controllers;

use App\Controllers\BaseController;
use Modules\Courses\Models\CourseModel;
use Modules\Courses\Models\SectionModel;
use Modules\Courses\Models\LessonModel;
use Modules\Courses\Models\LessonProgressModel;
use Modules\Courses\Models\EnrollmentModel;
use Modules\Courses\Models\CertificateModel;
use Modules\Courses\Models\PointsLedgerModel;
use Modules\Courses\Models\AchievementModel;
use Modules\Courses\Libraries\AccessService;
use Modules\Courses\Libraries\LearningService;

/**
 * Área do aluno (Fase 4a): catálogo estilo Netflix, jornada/trilha gamificada, player de
 * aula com progresso/XP, conclusão → certificado. Rotas de aula/progresso/conclusão são
 * gated por filtro `auth`; catálogo e página do curso são públicos (aula pede login/acesso).
 */
class StudentController extends BaseController
{
    protected CourseModel $courses;
    protected SectionModel $sections;
    protected LessonModel $lessons;
    protected LessonProgressModel $progress;
    protected EnrollmentModel $enrollments;
    protected CertificateModel $certificates;
    protected PointsLedgerModel $points;
    protected AchievementModel $achievements;
    protected AccessService $access;
    protected LearningService $learning;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->courses      = new CourseModel();
        $this->sections     = new SectionModel();
        $this->lessons      = new LessonModel();
        $this->progress     = new LessonProgressModel();
        $this->enrollments  = new EnrollmentModel();
        $this->certificates = new CertificateModel();
        $this->points       = new PointsLedgerModel();
        $this->achievements = new AchievementModel();
        $this->access       = new AccessService();
        $this->learning     = new LearningService();
    }

    private function userId(): int
    {
        return (int) (user()->id ?? 0);
    }

    private function userName(): string
    {
        $u = user();
        return (string) ($u->fullname ?? $u->name ?? $u->username ?? ('Aluno #' . $this->userId()));
    }

    public function catalog()
    {
        $userId = $this->userId();
        $data = [
            'featured'   => $this->courses->getFeatured(),
            'grouped'    => $this->courses->getPublishedGroupedByCategory(),
            'enrollMap'  => $this->enrollmentMap($userId),
            'totalXp'    => $this->points->totalFor($userId),
            'userId'     => $userId,
        ];
        return view('courses/catalog', $data);
    }

    public function myCourses()
    {
        $userId = $this->userId();
        $rows = $this->enrollments->forUser($userId);
        $courses = [];
        foreach ($rows as $e) {
            $c = $this->courses->find((int) $e['course_id']);
            if (!empty($c)) {
                $c['_enroll'] = $e;
                $courses[] = $c;
            }
        }
        return view('courses/my_courses', [
            'courses'  => $courses,
            'totalXp'  => $this->points->totalFor($userId),
            'achievements' => $this->achievements->userAchievements($userId),
        ]);
    }

    public function course($slug)
    {
        $course = $this->courses->getBySlug((string) $slug);
        if (empty($course) || (empty($course['is_published']) && !$this->isAdmin())) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $userId = $this->userId();
        $sections = $this->sections->forCourse((int) $course['id']);
        $allLessons = $this->lessons->forCourse((int) $course['id']);
        $progressMap = $userId ? $this->progress->mapForCourse($userId, (int) $course['id']) : [];

        // monta a trilha: aulas por seção + estado (concluída / bloqueada por drip / acesso)
        $lessonsBySection = [];
        $prevCompleted = true; // p/ drip: 1ª aula sempre liberada
        $completedCount = 0;
        foreach ($allLessons as $l) {
            $lid = (int) $l['id'];
            $isCompleted = ($progressMap[$lid]['status'] ?? '') === 'completed';
            if ($isCompleted) {
                $completedCount++;
            }
            $hasAccess = $this->access->canAccessLesson($userId, $l, $course);
            $dripLocked = !empty($course['drip_enabled']) && !$prevCompleted && !$isCompleted;
            $l['_completed'] = $isCompleted;
            $l['_locked']    = $dripLocked || !$hasAccess;
            $l['_lockReason'] = !$hasAccess ? 'acesso' : ($dripLocked ? 'drip' : '');
            $l['_percent']   = (int) ($progressMap[$lid]['progress_percent'] ?? 0);
            $lessonsBySection[(int) $l['section_id']][] = $l;
            $prevCompleted = $isCompleted;
        }
        $total = max(1, count($allLessons));
        $coursePercent = (int) floor(($completedCount / $total) * 100);

        return view('courses/course', [
            'course'           => $course,
            'sections'         => $sections,
            'lessonsBySection' => $lessonsBySection,
            'coursePercent'    => $coursePercent,
            'completedCount'   => $completedCount,
            'totalLessons'     => count($allLessons),
            'certificate'      => $userId ? $this->certificates->getFor($userId, (int) $course['id']) : null,
            'canAccessCourse'  => $this->access->canAccessCourse($userId, $course),
            'userId'           => $userId,
        ]);
    }

    public function lesson($courseSlug, $lessonSlug)
    {
        $course = $this->courses->getBySlug((string) $courseSlug);
        if (empty($course)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $lesson = $this->lessons->getBySlugInCourse((int) $course['id'], (string) $lessonSlug);
        if (empty($lesson)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $userId = $this->userId();

        // bloqueio de acesso → volta p/ a página do curso
        if (!$this->access->canAccessLesson($userId, $lesson, $course)) {
            return redirect()->to(site_url('curso/' . $course['slug']))
                ->with('error', 'Você ainda não tem acesso a esta aula.');
        }

        $this->enrollments->ensure($userId, (int) $course['id']);

        // ordena todas as aulas p/ prev/next + outline
        $all = $this->lessons->forCourse((int) $course['id']);
        $progressMap = $this->progress->mapForCourse($userId, (int) $course['id']);
        $idx = null;
        foreach ($all as $i => $l) {
            if ((int) $l['id'] === (int) $lesson['id']) {
                $idx = $i;
                break;
            }
        }
        $prev = ($idx !== null && $idx > 0) ? $all[$idx - 1] : null;
        $next = ($idx !== null && $idx < count($all) - 1) ? $all[$idx + 1] : null;

        return view('courses/lesson', [
            'course'      => $course,
            'lesson'      => $lesson,
            'allLessons'  => $all,
            'progressMap' => $progressMap,
            'prev'        => $prev,
            'next'        => $next,
            'isCompleted' => ($progressMap[(int) $lesson['id']]['status'] ?? '') === 'completed',
            'totalXp'     => $this->points->totalFor($userId),
        ]);
    }

    public function saveProgress()
    {
        $userId = $this->userId();
        $lessonId = (int) $this->request->getPost('lesson_id');
        $percent = (int) $this->request->getPost('percent');
        $position = (int) $this->request->getPost('position');
        $lesson = $this->lessons->find($lessonId);
        if (empty($lesson) || $userId <= 0) {
            return $this->response->setJSON(['ok' => false]);
        }
        $this->learning->saveProgress($userId, $lesson, (int) $lesson['course_id'], $percent, $position);
        return $this->response->setJSON(['ok' => true]);
    }

    public function completeLesson()
    {
        $userId = $this->userId();
        $lessonId = (int) $this->request->getPost('lesson_id');
        $lesson = $this->lessons->find($lessonId);
        if (empty($lesson) || $userId <= 0) {
            return $this->response->setJSON(['ok' => false]);
        }
        $course = $this->courses->find((int) $lesson['course_id']);
        if (empty($course)) {
            return $this->response->setJSON(['ok' => false]);
        }
        if (!$this->access->canAccessLesson($userId, $lesson, $course)) {
            return $this->response->setJSON(['ok' => false, 'error' => 'no_access']);
        }
        $result = $this->learning->markLessonComplete($userId, $lesson, $course, $this->userName());

        // próxima aula (para "avançar")
        $all = $this->lessons->forCourse((int) $course['id']);
        $nextUrl = null;
        foreach ($all as $i => $l) {
            if ((int) $l['id'] === $lessonId && isset($all[$i + 1])) {
                $nextUrl = site_url('curso/' . $course['slug'] . '/aula/' . $all[$i + 1]['slug']);
                break;
            }
        }

        return $this->response->setJSON([
            'ok'               => true,
            'xp_awarded'       => $result['xp_awarded'],
            'total_xp'         => $result['total_xp'],
            'course_percent'   => $result['course_percent'],
            'course_completed' => $result['course_completed'],
            'certificate_url'  => $result['certificate'] ? site_url('certificado/' . $result['certificate']['code']) : null,
            'new_achievements' => array_map(fn($a) => ['name' => $a['name'], 'icon' => $a['icon']], $result['new_achievements']),
            'next_url'         => $nextUrl,
        ]);
    }

    public function certificate($code)
    {
        $cert = $this->certificates->getByCode((string) $code);
        if (empty($cert)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $course = $this->courses->find((int) $cert['course_id']);
        return view('courses/certificate', ['cert' => $cert, 'course' => $course]);
    }

    // ── helpers ───────────────────────────────────────────────────────────────
    private function enrollmentMap(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }
        $map = [];
        foreach ($this->enrollments->forUser($userId) as $e) {
            $map[(int) $e['course_id']] = $e;
        }
        return $map;
    }

    private function isAdmin(): bool
    {
        $u = user();
        return !empty($u) && (int) ($u->user_type ?? 0) === 1;
    }
}
