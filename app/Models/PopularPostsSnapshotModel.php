<?php namespace App\Models;

class PopularPostsSnapshotModel extends BaseModel
{
    protected $table = 'popular_posts_snapshot';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'snapshot_date',
        'window_days',
        'metric',
        'post_id',
        'rank',
        'pageviews',
        'unique_visitors',
        'interactions',
        'score',
        'category_id',
        'title',
        'used',
        'created_at',
    ];

    public function insertSnapshot(string $date, int $windowDays, string $metric, array $rows): int
    {
        if (empty($rows)) {
            return 0;
        }
        $this->builder()
            ->where('snapshot_date', $date)
            ->where('window_days', $windowDays)
            ->where('metric', $metric)
            ->delete();

        $now = date('Y-m-d H:i:s');
        $count = 0;
        foreach ($rows as $rank => $r) {
            $data = [
                'snapshot_date'   => $date,
                'window_days'     => $windowDays,
                'metric'          => $metric,
                'post_id'         => (int) ($r['post_id'] ?? 0),
                'rank'            => $rank + 1,
                'pageviews'       => (int) ($r['pageviews'] ?? 0),
                'unique_visitors' => (int) ($r['unique_visitors'] ?? 0),
                'interactions'    => (int) ($r['interactions'] ?? 0),
                'score'           => (float) ($r['score'] ?? 0),
                'category_id'     => !empty($r['category_id']) ? (int) $r['category_id'] : null,
                'title'           => isset($r['title']) ? mb_substr((string) $r['title'], 0, 500) : null,
                'used'            => 0,
                'created_at'      => $now,
            ];
            if ($data['post_id'] > 0 && $this->builder()->insert($data)) {
                $count++;
            }
        }
        return $count;
    }

    public function getSnapshot(string $date, int $windowDays, string $metric, int $limit = 20): array
    {
        return $this->builder()
            ->where('snapshot_date', $date)
            ->where('window_days', $windowDays)
            ->where('metric', $metric)
            ->orderBy('rank', 'ASC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function markUsedByPostId(int $postId, string $date, int $windowDays): bool
    {
        return (bool) $this->builder()
            ->where('post_id', $postId)
            ->where('snapshot_date', $date)
            ->where('window_days', $windowDays)
            ->update(['used' => 1]);
    }
}
