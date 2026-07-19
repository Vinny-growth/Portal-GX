<?php

namespace Modules\Courses\Controllers;

use App\Controllers\BaseAdminController;
use Modules\Courses\Models\CourseModel;
use Modules\Courses\Models\SectionModel;
use Modules\Courses\Models\LessonModel;
use Modules\Courses\Models\AccessLevelModel;
use Modules\Courses\Models\MembershipModel;
use Modules\Courses\Libraries\MembershipService;

/**
 * Course builder do admin (Fase 4a). CRUD de curso → seções → aulas + níveis de acesso
 * (grant manual) + concessão de nível a alunos. Segue o chrome do painel do core
 * (admin/includes/_header|_footer) e o design system Nexus.
 */
class CourseAdminController extends BaseAdminController
{
    protected CourseModel $courses;
    protected SectionModel $sections;
    protected LessonModel $lessons;
    protected AccessLevelModel $levels;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->courses  = new CourseModel();
        $this->sections = new SectionModel();
        $this->lessons  = new LessonModel();
        $this->levels   = new AccessLevelModel();
    }

    public function index()
    {
        checkPermission('admin_panel');
        $courses = $this->courses->getForAdmin();
        foreach ($courses as &$c) {
            $c['lesson_count'] = $this->lessons->countForCourse((int) $c['id']);
        }
        unset($c);
        $data = ['title' => 'Cursos', 'courses' => $courses];
        echo view('admin/includes/_header', $data);
        echo view('admin/courses/index', $data);
        echo view('admin/includes/_footer');
    }

    public function create()
    {
        checkPermission('admin_panel');
        $data = [
            'title'   => 'Novo curso',
            'course'  => null,
            'sections' => [],
            'lessonsBySection' => [],
            'levels'  => $this->levels->all(),
        ];
        echo view('admin/includes/_header', $data);
        echo view('admin/courses/form', $data);
        echo view('admin/includes/_footer');
    }

    public function store()
    {
        checkPermission('admin_panel');
        $now = date('Y-m-d H:i:s');
        $title = trim((string) $this->request->getPost('title'));
        if ($title === '') {
            return redirect()->back()->with('error', 'Informe o título do curso.');
        }
        $id = $this->courses->insert($this->coursePayload($title, $now), true);
        return redirect()->to(adminUrl('cursos/' . $id . '/editar'))->with('success', 'Curso criado. Agora monte as seções e aulas.');
    }

    public function edit($id)
    {
        checkPermission('admin_panel');
        $course = $this->courses->find((int) $id);
        if (empty($course)) {
            return redirect()->to(adminUrl('cursos'))->with('error', 'Curso não encontrado.');
        }
        $sections = $this->sections->forCourse((int) $id);
        $lessonsBySection = [];
        foreach ($this->lessons->forCourse((int) $id) as $l) {
            $lessonsBySection[(int) $l['section_id']][] = $l;
        }
        $data = [
            'title'   => 'Editar: ' . $course['title'],
            'course'  => $course,
            'sections' => $sections,
            'lessonsBySection' => $lessonsBySection,
            'levels'  => $this->levels->all(),
        ];
        echo view('admin/includes/_header', $data);
        echo view('admin/courses/form', $data);
        echo view('admin/includes/_footer');
    }

    public function update($id)
    {
        checkPermission('admin_panel');
        $course = $this->courses->find((int) $id);
        if (empty($course)) {
            return redirect()->to(adminUrl('cursos'))->with('error', 'Curso não encontrado.');
        }
        $now = date('Y-m-d H:i:s');
        $title = trim((string) $this->request->getPost('title')) ?: $course['title'];
        $payload = $this->coursePayload($title, null, (int) $id);
        $payload['updated_at'] = $now;
        unset($payload['created_at']);
        $this->courses->update((int) $id, $payload);
        return redirect()->to(adminUrl('cursos/' . $id . '/editar'))->with('success', 'Curso atualizado.');
    }

    public function togglePublish($id)
    {
        checkPermission('admin_panel');
        $course = $this->courses->find((int) $id);
        if (!empty($course)) {
            $this->courses->update((int) $id, [
                'is_published' => $course['is_published'] ? 0 : 1,
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
        }
        return redirect()->back()->with('success', 'Status de publicação atualizado.');
    }

    public function delete($id)
    {
        checkPermission('admin_panel');
        $this->lessons->where('course_id', (int) $id)->delete();
        $this->sections->where('course_id', (int) $id)->delete();
        $this->courses->delete((int) $id);
        return redirect()->to(adminUrl('cursos'))->with('success', 'Curso excluído.');
    }

    // ── seções ───────────────────────────────────────────────────────────────
    public function saveSection()
    {
        checkPermission('admin_panel');
        $courseId = (int) $this->request->getPost('course_id');
        $sectionId = (int) $this->request->getPost('section_id');
        $now = date('Y-m-d H:i:s');
        $payload = [
            'course_id'     => $courseId,
            'title'         => trim((string) $this->request->getPost('title')) ?: 'Nova seção',
            'description'   => trim((string) $this->request->getPost('description')) ?: null,
            'section_order' => (int) $this->request->getPost('section_order'),
            'updated_at'    => $now,
        ];
        if ($sectionId > 0) {
            $this->sections->update($sectionId, $payload);
        } else {
            $payload['created_at'] = $now;
            $this->sections->insert($payload);
        }
        return redirect()->to(adminUrl('cursos/' . $courseId . '/editar'))->with('success', 'Seção salva.');
    }

    public function deleteSection($id)
    {
        checkPermission('admin_panel');
        $section = $this->sections->find((int) $id);
        $courseId = $section['course_id'] ?? 0;
        $this->lessons->where('section_id', (int) $id)->delete();
        $this->sections->delete((int) $id);
        return redirect()->to(adminUrl('cursos/' . $courseId . '/editar'))->with('success', 'Seção removida.');
    }

    // ── aulas ─────────────────────────────────────────────────────────────────
    public function saveLesson()
    {
        checkPermission('admin_panel');
        $courseId = (int) $this->request->getPost('course_id');
        $lessonId = (int) $this->request->getPost('lesson_id');
        $now = date('Y-m-d H:i:s');
        $title = trim((string) $this->request->getPost('title')) ?: 'Nova aula';
        $accessLevel = (int) $this->request->getPost('access_level_id');
        $payload = [
            'course_id'       => $courseId,
            'section_id'      => (int) $this->request->getPost('section_id'),
            'title'           => $title,
            'slug'            => $this->lessonSlug($title, $lessonId),
            'content_type'    => in_array($this->request->getPost('content_type'), ['video', 'text'], true) ? $this->request->getPost('content_type') : 'video',
            'video_url'       => trim((string) $this->request->getPost('video_url')) ?: null,
            'video_provider'  => trim((string) $this->request->getPost('video_provider')) ?: null,
            'body_html'       => (string) $this->request->getPost('body_html') ?: null,
            'duration_seconds' => (int) $this->request->getPost('duration_seconds'),
            'access_level_id' => $accessLevel > 0 ? $accessLevel : null,
            'is_free_preview' => $this->request->getPost('is_free_preview') ? 1 : 0,
            'xp_reward'       => max(0, (int) $this->request->getPost('xp_reward')),
            'lesson_order'    => (int) $this->request->getPost('lesson_order'),
            'updated_at'      => $now,
        ];
        if ($lessonId > 0) {
            $this->lessons->update($lessonId, $payload);
        } else {
            $payload['created_at'] = $now;
            $this->lessons->insert($payload);
        }
        return redirect()->to(adminUrl('cursos/' . $courseId . '/editar'))->with('success', 'Aula salva.');
    }

    public function deleteLesson($id)
    {
        checkPermission('admin_panel');
        $lesson = $this->lessons->find((int) $id);
        $courseId = $lesson['course_id'] ?? 0;
        $this->lessons->delete((int) $id);
        return redirect()->to(adminUrl('cursos/' . $courseId . '/editar'))->with('success', 'Aula removida.');
    }

    // ── níveis de acesso ────────────────────────────────────────────────────
    public function accessLevels()
    {
        checkPermission('admin_panel');
        $data = ['title' => 'Níveis de acesso', 'levels' => $this->levels->all()];
        echo view('admin/includes/_header', $data);
        echo view('admin/courses/access_levels', $data);
        echo view('admin/includes/_footer');
    }

    public function saveAccessLevel()
    {
        checkPermission('admin_panel');
        $now = date('Y-m-d H:i:s');
        $id = (int) $this->request->getPost('id');
        $name = trim((string) $this->request->getPost('name'));
        if ($name === '') {
            return redirect()->back()->with('error', 'Informe o nome do nível.');
        }
        $payload = [
            'name'        => $name,
            'slug'        => url_title($name, '-', true),
            'rank'        => (int) $this->request->getPost('rank'),
            'description' => trim((string) $this->request->getPost('description')) ?: null,
            'updated_at'  => $now,
        ];
        if ($id > 0) {
            $this->levels->update($id, $payload);
        } else {
            $payload['created_at'] = $now;
            $this->levels->insert($payload);
        }
        return redirect()->to(adminUrl('cursos/niveis'))->with('success', 'Nível salvo.');
    }

    public function grantAccess()
    {
        checkPermission('admin_panel');
        $userId = (int) $this->request->getPost('user_id');
        $levelId = (int) $this->request->getPost('access_level_id');
        if ($userId <= 0 || $levelId <= 0) {
            return redirect()->back()->with('error', 'Informe o ID do usuário e o nível.');
        }
        $adminId = (int) (session('id') ?? session('user_id') ?? 0) ?: null;
        $this->levels->grant($userId, $levelId, $adminId);
        return redirect()->to(adminUrl('cursos/niveis'))->with('success', 'Acesso concedido ao usuário #' . $userId . '.');
    }

    // ── assinaturas / memberships (Fase 4b) ──────────────────────────────────
    public function memberships()
    {
        checkPermission('admin_panel');
        $rows = (new MembershipModel())->forAdmin();
        foreach ($rows as &$m) {
            $m['_active'] = MembershipService::isActive($m);
        }
        unset($m);
        $data = ['title' => 'Assinaturas', 'memberships' => $rows];
        echo view('admin/includes/_header', $data);
        echo view('admin/courses/memberships', $data);
        echo view('admin/includes/_footer');
    }

    public function grantMembership()
    {
        checkPermission('admin_panel');
        $document = preg_replace('/\D+/', '', (string) $this->request->getPost('document'));
        $docType = (string) ($this->request->getPost('doc_type') ?: 'cpf');
        $userId = (int) $this->request->getPost('user_id');
        $months = max(1, (int) $this->request->getPost('months') ?: 12);
        if (strlen($document) < 8) {
            return redirect()->back()->with('error', 'Informe um documento (CPF/CURP) válido.');
        }
        (new MembershipService())->grantManual($document, $docType, $userId > 0 ? $userId : null, $months);
        return redirect()->to(adminUrl('cursos/assinaturas'))->with('success', 'Membership concedido ao documento ' . $document . '.');
    }

    // ── helpers ───────────────────────────────────────────────────────────────
    private function coursePayload(string $title, ?string $createdAt, ?int $ignoreId = null): array
    {
        $accessLevel = (int) $this->request->getPost('access_level_id');
        $payload = [
            'title'             => $title,
            'slug'              => $this->courses->uniqueSlug($this->request->getPost('slug') ?: $title, $ignoreId),
            'subtitle'          => trim((string) $this->request->getPost('subtitle')) ?: null,
            'description'       => (string) $this->request->getPost('description') ?: null,
            'cover_image'       => trim((string) $this->request->getPost('cover_image')) ?: null,
            'trailer_url'       => trim((string) $this->request->getPost('trailer_url')) ?: null,
            'category'          => trim((string) $this->request->getPost('category')) ?: null,
            'level'             => trim((string) $this->request->getPost('level')) ?: null,
            'instructor'        => trim((string) $this->request->getPost('instructor')) ?: null,
            'access_level_id'   => $accessLevel > 0 ? $accessLevel : null,
            'is_published'      => $this->request->getPost('is_published') ? 1 : 0,
            'is_featured'       => $this->request->getPost('is_featured') ? 1 : 0,
            'drip_enabled'      => $this->request->getPost('drip_enabled') ? 1 : 0,
            'xp_reward'         => max(0, (int) $this->request->getPost('xp_reward')),
            'estimated_minutes' => max(0, (int) $this->request->getPost('estimated_minutes')),
            'course_order'      => (int) $this->request->getPost('course_order'),
        ];
        if ($createdAt !== null) {
            $payload['created_at'] = $createdAt;
            $payload['updated_at'] = $createdAt;
        }
        return $payload;
    }

    private function lessonSlug(string $title, int $ignoreId): string
    {
        $base = url_title($title, '-', true) ?: 'aula';
        $slug = $base;
        $i = 2;
        while (true) {
            $q = $this->lessons->where('slug', $slug);
            if ($ignoreId > 0) {
                $q->where('id !=', $ignoreId);
            }
            if ($q->countAllResults() === 0) {
                return $slug;
            }
            $slug = $base . '-' . $i++;
        }
    }
}
