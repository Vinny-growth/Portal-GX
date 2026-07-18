<?php namespace Modules\Courses\Models;

use App\Models\BaseModel;

class SectionModel extends BaseModel
{
    protected $table         = 'course_sections';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'course_id', 'title', 'description', 'section_order', 'created_at', 'updated_at',
    ];

    public function forCourse(int $courseId): array
    {
        return $this->where('course_id', $courseId)
            ->orderBy('section_order', 'ASC')->orderBy('id', 'ASC')->findAll();
    }
}
