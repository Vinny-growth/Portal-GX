<?php namespace Modules\Courses\Libraries;

use Modules\Courses\Models\CommunityPostModel;
use Modules\Courses\Models\CommunityCommentModel;
use Modules\Courses\Models\PointsLedgerModel;

/**
 * Núcleo social da comunidade (Fase 4c). Reusa o points_ledger do 4a p/ a gamificação
 * (mesma carteira de XP dos cursos → um único ranking). XP: post +5, comentário +2,
 * reação RECEBIDA +1 (vai p/ o autor do conteúdo, não p/ quem reagiu; idempotente).
 */
class CommunityService
{
    protected CommunityPostModel $posts;
    protected CommunityCommentModel $comments;
    protected PointsLedgerModel $points;
    protected $db;

    const XP_POST = 5;
    const XP_COMMENT = 2;
    const XP_REACTION = 1;

    public function __construct()
    {
        $this->posts    = new CommunityPostModel();
        $this->comments = new CommunityCommentModel();
        $this->points   = new PointsLedgerModel();
        $this->db       = \Config\Database::connect();
    }

    public function createPost(int $userId, int $spaceId, ?string $title, string $body): int
    {
        $now = date('Y-m-d H:i:s');
        $id = $this->posts->insert([
            'space_id' => $spaceId, 'user_id' => $userId,
            'title' => $title ?: null, 'body' => $body,
            'created_at' => $now, 'updated_at' => $now,
        ], true);
        $this->points->award($userId, self::XP_POST, 'community_post', 'post', (int) $id);
        return (int) $id;
    }

    public function createComment(int $userId, int $postId, string $body): int
    {
        $post = $this->posts->find($postId);
        if (empty($post)) {
            return 0;
        }
        $now = date('Y-m-d H:i:s');
        $id = $this->comments->insert([
            'post_id' => $postId, 'user_id' => $userId, 'body' => $body,
            'created_at' => $now, 'updated_at' => $now,
        ], true);
        $this->posts->bumpCounts($postId, 0, 1);
        $this->points->award($userId, self::XP_COMMENT, 'community_comment', 'comment', (int) $id);
        // notifica o autor do post (se não for ele mesmo)
        if ((int) $post['user_id'] !== $userId) {
            $this->notify((int) $post['user_id'], $userId, 'comment', 'post', $postId, 'comentou na sua publicação', 'comunidade/post/' . $postId);
        }
        return (int) $id;
    }

    /** Alterna a reação do usuário no alvo. Retorna ['reacted'=>bool, 'count'=>int]. */
    public function toggleReaction(int $userId, string $targetType, int $targetId): array
    {
        $targetType = $targetType === 'comment' ? 'comment' : 'post';
        $existing = $this->db->table('community_reactions')
            ->where('user_id', $userId)->where('target_type', $targetType)->where('target_id', $targetId)
            ->get()->getRowArray();

        if (!empty($existing)) {
            $this->db->table('community_reactions')->where('id', $existing['id'])->delete();
            if ($targetType === 'post') {
                $this->posts->bumpCounts($targetId, -1, 0);
            }
            return ['reacted' => false, 'count' => $this->reactionCount($targetType, $targetId)];
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('community_reactions')->insert([
            'user_id' => $userId, 'target_type' => $targetType, 'target_id' => $targetId,
            'type' => 'like', 'created_at' => $now,
        ]);
        $reactionId = (int) $this->db->insertID();
        if ($targetType === 'post') {
            $this->posts->bumpCounts($targetId, 1, 0);
        }
        // XP p/ o AUTOR do conteúdo (reação recebida), 1x por reação (ref = id da reação)
        $authorId = $this->authorOf($targetType, $targetId);
        if ($authorId && $authorId !== $userId) {
            $this->points->award($authorId, self::XP_REACTION, 'community_reaction', 'reaction', $reactionId);
            $this->notify($authorId, $userId, 'reaction', $targetType, $targetId, 'reagiu ao seu ' . ($targetType === 'post' ? 'post' : 'comentário'), 'comunidade');
        }
        return ['reacted' => true, 'count' => $this->reactionCount($targetType, $targetId)];
    }

    /** Ranking global por XP (carteira única cursos+comunidade). */
    public function leaderboard(int $limit = 20): array
    {
        return $this->db->table('points_ledger pl')
            ->select('pl.user_id, u.username, COALESCE(SUM(pl.points),0) AS xp')
            ->join('users u', 'u.id = pl.user_id', 'left')
            ->groupBy('pl.user_id')
            ->orderBy('xp', 'DESC')
            ->limit($limit)->get()->getResultArray();
    }

    public function userStats(int $userId): array
    {
        return [
            'xp'       => $this->points->totalFor($userId),
            'posts'    => $this->db->table('community_posts')->where('user_id', $userId)->where('is_removed', 0)->countAllResults(),
            'comments' => $this->db->table('community_comments')->where('user_id', $userId)->where('is_removed', 0)->countAllResults(),
        ];
    }

    /** IDs de alvos (de um tipo) que o usuário já reagiu — p/ estado do botão na UI. */
    public function reactedTargets(int $userId, string $targetType, array $ids): array
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if ($userId <= 0 || empty($ids)) {
            return [];
        }
        $rows = $this->db->table('community_reactions')->select('target_id')
            ->where('user_id', $userId)->where('target_type', $targetType)->whereIn('target_id', $ids)
            ->get()->getResultArray();
        return array_map(fn($r) => (int) $r['target_id'], $rows);
    }

    // ── notificações ─────────────────────────────────────────────────────────
    public function notify(int $userId, ?int $actorId, string $type, string $targetType, int $targetId, string $message, string $link): void
    {
        $this->db->table('community_notifications')->insert([
            'user_id' => $userId, 'actor_id' => $actorId, 'type' => $type,
            'target_type' => $targetType, 'target_id' => $targetId,
            'message' => $message, 'link' => $link, 'is_read' => 0, 'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function unreadCount(int $userId): int
    {
        if ($userId <= 0) {
            return 0;
        }
        return $this->db->table('community_notifications')->where('user_id', $userId)->where('is_read', 0)->countAllResults();
    }

    public function listNotifications(int $userId, int $limit = 30): array
    {
        return $this->db->table('community_notifications cn')
            ->select('cn.*, u.username AS actor')
            ->join('users u', 'u.id = cn.actor_id', 'left')
            ->where('cn.user_id', $userId)->orderBy('cn.created_at', 'DESC')->limit($limit)
            ->get()->getResultArray();
    }

    public function markAllRead(int $userId): void
    {
        $this->db->table('community_notifications')->where('user_id', $userId)->where('is_read', 0)
            ->update(['is_read' => 1]);
    }

    // ── helpers ───────────────────────────────────────────────────────────────
    private function reactionCount(string $targetType, int $targetId): int
    {
        return $this->db->table('community_reactions')
            ->where('target_type', $targetType)->where('target_id', $targetId)->countAllResults();
    }

    private function authorOf(string $targetType, int $targetId): ?int
    {
        $table = $targetType === 'comment' ? 'community_comments' : 'community_posts';
        $row = $this->db->table($table)->select('user_id')->where('id', $targetId)->get()->getRowArray();
        return $row ? (int) $row['user_id'] : null;
    }
}
