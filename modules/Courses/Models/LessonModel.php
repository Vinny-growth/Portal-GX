<?php namespace Modules\Courses\Models;

use App\Models\BaseModel;

class LessonModel extends BaseModel
{
    protected $table         = 'lessons';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'course_id', 'section_id', 'title', 'slug', 'cover_image', 'content_type', 'video_url',
        'video_provider', 'body_html', 'duration_seconds', 'access_level_id',
        'is_free_preview', 'xp_reward', 'lesson_order', 'created_at', 'updated_at',
    ];

    public function forCourse(int $courseId): array
    {
        return $this->where('course_id', $courseId)
            ->orderBy('section_id', 'ASC')->orderBy('lesson_order', 'ASC')->orderBy('id', 'ASC')
            ->findAll();
    }

    public function forSection(int $sectionId): array
    {
        return $this->where('section_id', $sectionId)
            ->orderBy('lesson_order', 'ASC')->orderBy('id', 'ASC')->findAll();
    }

    public function getBySlugInCourse(int $courseId, string $slug): ?array
    {
        return $this->where('course_id', $courseId)->where('slug', $slug)->first();
    }

    public function countForCourse(int $courseId): int
    {
        return $this->where('course_id', $courseId)->countAllResults();
    }
}
