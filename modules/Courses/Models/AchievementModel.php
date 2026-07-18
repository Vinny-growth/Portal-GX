<?php namespace Modules\Courses\Models;

use App\Models\BaseModel;

class AchievementModel extends BaseModel
{
    protected $table         = 'achievements';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'name', 'slug', 'description', 'icon', 'criteria_type', 'criteria_value',
        'xp_bonus', 'is_active', 'created_at', 'updated_at',
    ];

    public function active(): array
    {
        return $this->where('is_active', 1)->orderBy('id', 'ASC')->findAll();
    }

    public function activeByCriteria(string $criteriaType): array
    {
        return $this->where('is_active', 1)->where('criteria_type', $criteriaType)->findAll();
    }

    /** IDs de conquistas já ganhas pelo usuário. */
    public function userEarnedIds(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }
        $rows = $this->db->table('user_achievements')->select('achievement_id')
            ->where('user_id', $userId)->get()->getResult();
        return array_map(fn($r) => (int) $r->achievement_id, $rows);
    }

    public function userAchievements(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }
        return $this->db->table('user_achievements ua')
            ->select('a.*, ua.earned_at')
            ->join('achievements a', 'a.id = ua.achievement_id')
            ->where('ua.user_id', $userId)
            ->orderBy('ua.earned_at', 'DESC')
            ->get()->getResultArray();
    }

    /** Concede (idempotente) uma conquista. Retorna true se foi concedida agora. */
    public function grant(int $userId, int $achievementId): bool
    {
        if ($userId <= 0 || $achievementId <= 0) {
            return false;
        }
        $exists = $this->db->table('user_achievements')
            ->where('user_id', $userId)->where('achievement_id', $achievementId)->countAllResults();
        if ($exists > 0) {
            return false;
        }
        $now = date('Y-m-d H:i:s');
        return (bool) $this->db->table('user_achievements')->insert([
            'user_id'        => $userId,
            'achievement_id' => $achievementId,
            'earned_at'      => $now,
            'created_at'     => $now,
        ]);
    }
}
