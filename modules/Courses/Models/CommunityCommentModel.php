<?php namespace Modules\Courses\Models;

use App\Models\BaseModel;

class CommunityCommentModel extends BaseModel
{
    protected $table         = 'community_comments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['post_id', 'user_id', 'body', 'is_removed', 'created_at', 'updated_at'];

    public function forPost(int $postId): array
    {
        return $this->db->table('community_comments c')
            ->select('c.*, u.username AS author')
            ->join('users u', 'u.id = c.user_id', 'left')
            ->where('c.post_id', $postId)->where('c.is_removed', 0)
            ->orderBy('c.created_at', 'ASC')->get()->getResultArray();
    }
}
