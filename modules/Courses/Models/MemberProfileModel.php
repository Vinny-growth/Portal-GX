<?php namespace Modules\Courses\Models;

use App\Models\BaseModel;

class MemberProfileModel extends BaseModel
{
    protected $table         = 'community_profiles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['user_id', 'display_name', 'bio', 'avatar_url', 'created_at', 'updated_at'];

    public function getForUser(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    public function upsert(int $userId, array $fields): void
    {
        $now = date('Y-m-d H:i:s');
        $existing = $this->getForUser($userId);
        if (empty($existing)) {
            $this->insert(array_merge(['user_id' => $userId, 'created_at' => $now, 'updated_at' => $now], $fields));
        } else {
            $this->db->table('community_profiles')->where('user_id', $userId)
                ->update(array_merge($fields, ['updated_at' => $now]));
        }
    }
}
