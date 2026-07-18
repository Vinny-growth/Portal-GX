<?php namespace Modules\Courses\Models;

use App\Models\BaseModel;

class EnrollmentModel extends BaseModel
{
    protected $table         = 'course_enrollments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'user_id', 'course_id', 'status', 'progress_percent', 'last_lesson_id',
        'started_at', 'completed_at', 'created_at', 'updated_at',
    ];

    public function getFor(int $userId, int $courseId): ?array
    {
        return $this->where('user_id', $userId)->where('course_id', $courseId)->first();
    }

    /** Garante uma matrícula (idempotente); usado ao abrir a 1ª aula. */
    public function ensure(int $userId, int $courseId): array
    {
        $row = $this->getFor($userId, $courseId);
        if (!empty($row)) {
            return $row;
        }
        $now = date('Y-m-d H:i:s');
        $id = $this->insert([
            'user_id'    => $userId,
            'course_id'  => $courseId,
            'status'     => 'active',
            'started_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ], true);
        return $this->find($id);
    }

    public function forUser(int $userId): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('updated_at', 'DESC')->findAll();
    }

    public function updateProgress(int $userId, int $courseId, int $percent, ?int $lastLessonId = null, bool $completed = false): void
    {
        $now = date('Y-m-d H:i:s');
        $patch = [
            'progress_percent' => max(0, min(100, $percent)),
            'updated_at'       => $now,
        ];
        if ($lastLessonId) {
            $patch['last_lesson_id'] = $lastLessonId;
        }
        if ($completed) {
            $patch['status'] = 'completed';
            $patch['completed_at'] = $now;
        }
        $this->db->table('course_enrollments')
            ->where('user_id', $userId)->where('course_id', $courseId)->update($patch);
    }
}
