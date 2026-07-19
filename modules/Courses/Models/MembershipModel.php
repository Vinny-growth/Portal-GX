<?php namespace Modules\Courses\Models;

use App\Models\BaseModel;

class MembershipModel extends BaseModel
{
    protected $table         = 'memberships';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'user_id', 'document', 'doc_type', 'source', 'client_active', 'status',
        'started_at', 'paid_until', 'access_until', 'canceled_at', 'created_at', 'updated_at',
    ];

    public function getByDocument(string $document): ?array
    {
        return $this->where('document', $document)->first();
    }

    public function getForUser(int $userId): ?array
    {
        if ($userId <= 0) {
            return null;
        }
        return $this->where('user_id', $userId)->orderBy('id', 'DESC')->first();
    }

    public function forAdmin(int $limit = 200): array
    {
        return $this->orderBy('updated_at', 'DESC')->findAll($limit);
    }

    /** Vincula o user_id a um membership existente pelo documento (ex.: ao registrar). */
    public function linkUser(string $document, int $userId): void
    {
        if ($userId <= 0 || $document === '') {
            return;
        }
        $this->db->table('memberships')->where('document', $document)->where('user_id', null)
            ->update(['user_id' => $userId, 'updated_at' => date('Y-m-d H:i:s')]);
    }
}
