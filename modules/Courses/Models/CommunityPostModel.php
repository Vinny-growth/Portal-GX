<?php namespace Modules\Courses\Models;

use App\Models\BaseModel;

class CommunityPostModel extends BaseModel
{
    protected $table         = 'community_posts';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'space_id', 'user_id', 'title', 'body', 'is_pinned', 'is_removed',
        'reaction_count', 'comment_count', 'created_at', 'updated_at',
    ];

    /** Feed com dados do autor + espaço (join). $spaceId null = todos os espaços. */
    public function feed(?int $spaceId = null, int $limit = 30, int $offset = 0): array
    {
        $b = $this->db->table('community_posts p')
            ->select('p.*, u.username AS author, cs.name AS space_name, cs.slug AS space_slug, cs.color AS space_color')
            ->join('users u', 'u.id = p.user_id', 'left')
            ->join('community_spaces cs', 'cs.id = p.space_id', 'left')
            ->where('p.is_removed', 0);
        if ($spaceId) {
            $b->where('p.space_id', $spaceId);
        }
        return $b->orderBy('p.is_pinned', 'DESC')->orderBy('p.created_at', 'DESC')
            ->limit($limit, $offset)->get()->getResultArray();
    }

    public function withAuthor(int $id): ?array
    {
        return $this->db->table('community_posts p')
            ->select('p.*, u.username AS author, cs.name AS space_name, cs.slug AS space_slug, cs.color AS space_color')
            ->join('users u', 'u.id = p.user_id', 'left')
            ->join('community_spaces cs', 'cs.id = p.space_id', 'left')
            ->where('p.id', $id)->get()->getRowArray();
    }

    public function forUser(int $userId, int $limit = 30): array
    {
        return $this->where('user_id', $userId)->where('is_removed', 0)
            ->orderBy('created_at', 'DESC')->findAll($limit);
    }

    public function bumpCounts(int $postId, int $reactions = 0, int $comments = 0): void
    {
        $b = $this->db->table('community_posts')->where('id', $postId);
        if ($reactions) {
            $b->set('reaction_count', 'GREATEST(0, reaction_count + (' . (int) $reactions . '))', false);
        }
        if ($comments) {
            $b->set('comment_count', 'GREATEST(0, comment_count + (' . (int) $comments . '))', false);
        }
        $b->set('updated_at', date('Y-m-d H:i:s'))->update();
    }
}
