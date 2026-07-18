<?php namespace Modules\Courses\Models;

use App\Models\BaseModel;

class LessonProgressModel extends BaseModel
{
    protected $table         = 'lesson_progress';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'user_id', 'lesson_id', 'course_id', 'status', 'progress_percent',
        'last_position_seconds', 'completed_at', 'created_at', 'updated_at',
    ];

    public function getFor(int $userId, int $lessonId): ?array
    {
        return $this->where('user_id', $userId)->where('lesson_id', $lessonId)->first();
    }

    /** Mapa lesson_id => row de progresso do usuário num curso. */
    public function mapForCourse(int $userId, int $courseId): array
    {
        $rows = $this->where('user_id', $userId)->where('course_id', $courseId)->findAll();
        $map = [];
        foreach ($rows as $r) {
            $map[(int) $r['lesson_id']] = $r;
        }
        return $map;
    }

    public function completedCountForCourse(int $userId, int $courseId): int
    {
        return $this->where('user_id', $userId)->where('course_id', $courseId)
            ->where('status', 'completed')->countAllResults();
    }

    /** Registra progresso parcial (posição/percentual) sem marcar concluído. */
    public function touchProgress(int $userId, int $lessonId, int $courseId, int $percent, int $positionSeconds): void
    {
        $now = date('Y-m-d H:i:s');
        $existing = $this->getFor($userId, $lessonId);
        if (empty($existing)) {
            $this->insert([
                'user_id'               => $userId,
                'lesson_id'             => $lessonId,
                'course_id'             => $courseId,
                'status'                => $percent >= 100 ? 'completed' : 'in_progress',
                'progress_percent'      => max(0, min(100, $percent)),
                'last_position_seconds' => max(0, $positionSeconds),
                'completed_at'          => $percent >= 100 ? $now : null,
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
            return;
        }
        // nunca regride o percentual nem o status já concluído
        $patch = [
            'progress_percent'      => max((int) $existing['progress_percent'], max(0, min(100, $percent))),
            'last_position_seconds' => max(0, $positionSeconds),
            'updated_at'            => $now,
        ];
        $this->db->table('lesson_progress')
            ->where('user_id', $userId)->where('lesson_id', $lessonId)->update($patch);
    }
}
