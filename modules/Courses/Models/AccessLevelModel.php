<?php namespace Modules\Courses\Models;

use App\Models\BaseModel;

class AccessLevelModel extends BaseModel
{
    protected $table         = 'access_levels';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['name', 'slug', 'rank', 'description', 'created_at', 'updated_at'];

    public function all(): array
    {
        return $this->orderBy('rank', 'ASC')->findAll();
    }

    public function rankOf(?int $levelId): int
    {
        if (!$levelId) {
            return 0;
        }
        $row = $this->find($levelId);
        return $row ? (int) $row['rank'] : 0;
    }

    /** Maior rank de nível manual ativo do usuário (0 se nenhum). */
    public function maxRankForUser(int $userId): int
    {
        if ($userId <= 0) {
            return 0;
        }
        $now = date('Y-m-d H:i:s');
        $row = $this->db->table('user_access_levels ual')
            ->select('MAX(al.rank) AS max_rank')
            ->join('access_levels al', 'al.id = ual.access_level_id')
            ->where('ual.user_id', $userId)
            ->groupStart()
                ->where('ual.expires_at', null)
                ->orWhere('ual.expires_at >=', $now)
            ->groupEnd()
            ->get()->getFirstRow();
        return $row && $row->max_rank !== null ? (int) $row->max_rank : 0;
    }

    /** Concede (idempotente) um nível a um usuário. */
    public function grant(int $userId, int $accessLevelId, ?int $grantedBy = null, ?string $expiresAt = null): bool
    {
        if ($userId <= 0 || $accessLevelId <= 0) {
            return false;
        }
        $now = date('Y-m-d H:i:s');
        $b = $this->db->table('user_access_levels');
        $exists = $b->where('user_id', $userId)->where('access_level_id', $accessLevelId)->countAllResults();
        if ($exists > 0) {
            return (bool) $this->db->table('user_access_levels')
                ->where('user_id', $userId)->where('access_level_id', $accessLevelId)
                ->update(['expires_at' => $expiresAt, 'granted_by' => $grantedBy, 'granted_at' => $now]);
        }
        return (bool) $this->db->table('user_access_levels')->insert([
            'user_id'         => $userId,
            'access_level_id' => $accessLevelId,
            'granted_by'      => $grantedBy,
            'granted_at'      => $now,
            'expires_at'      => $expiresAt,
            'created_at'      => $now,
        ]);
    }
}
