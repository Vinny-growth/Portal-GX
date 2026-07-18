<?php namespace Modules\Courses\Models;

use App\Models\BaseModel;

class CertificateModel extends BaseModel
{
    protected $table         = 'certificates';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'user_id', 'course_id', 'code', 'user_name', 'course_title', 'issued_at', 'created_at',
    ];

    public function getFor(int $userId, int $courseId): ?array
    {
        return $this->where('user_id', $userId)->where('course_id', $courseId)->first();
    }

    public function getByCode(string $code): ?array
    {
        return $this->where('code', $code)->first();
    }

    /** Emite (idempotente) o certificado de conclusão do curso. */
    public function issue(int $userId, int $courseId, string $userName, string $courseTitle): array
    {
        $existing = $this->getFor($userId, $courseId);
        if (!empty($existing)) {
            return $existing;
        }
        $now = date('Y-m-d H:i:s');
        $code = $this->makeCode($userId, $courseId);
        $id = $this->insert([
            'user_id'      => $userId,
            'course_id'    => $courseId,
            'code'         => $code,
            'user_name'    => $userName,
            'course_title' => $courseTitle,
            'issued_at'    => $now,
            'created_at'   => $now,
        ], true);
        return $this->find($id);
    }

    private function makeCode(int $userId, int $courseId): string
    {
        // Código verificável, sem depender de rand() global: determinístico + hash curto.
        $seed = 'GXC-' . $courseId . '-' . $userId . '-' . date('YmdHis');
        return 'GXC-' . strtoupper(substr(hash('sha256', $seed), 0, 10));
    }
}
