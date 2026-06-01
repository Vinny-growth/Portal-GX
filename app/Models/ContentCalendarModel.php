<?php namespace App\Models;

class ContentCalendarModel extends BaseModel
{
    protected $table = 'content_calendar';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title',
        'instructions',
        'category_id',
        'lang_id',
        'user_id',
        'tone',
        'length',
        'tags',
        'publish_at',
        'generate_at',
        'status',
        'source_type',
        'source_url',
        'post_id',
        'created_at',
        'updated_at',
    ];

    public function getUpcoming(int $limit = 50)
    {
        return $this->builder()
            ->orderBy('publish_at ASC, id ASC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function getPaginated(int $page, int $perPage, array $filters = []): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(200, $perPage));
        $offset = ($page - 1) * $perPage;

        $base = $this->applyFilters($this->builder(), $filters);
        $total = (int) $base->countAllResults(false);

        // Subquery: latest content_runs row per calendar_id (for surfacing error + last run timestamp)
        $latestRunSub = '(SELECT cr.calendar_id, cr.error AS last_error, cr.status AS last_run_status, cr.finished_at AS last_run_finished_at, cr.id AS last_run_id'
            . ' FROM content_runs cr'
            . ' INNER JOIN (SELECT calendar_id, MAX(id) AS max_id FROM content_runs WHERE calendar_id IS NOT NULL GROUP BY calendar_id) lr'
            . ' ON lr.max_id = cr.id)';

        $builder = $this->applyFilters($this->builder(), $filters)
            ->select('content_calendar.*, lr.last_error, lr.last_run_status, lr.last_run_finished_at, lr.last_run_id')
            ->join($latestRunSub . ' lr', 'lr.calendar_id = content_calendar.id', 'left');

        $items = $builder
            ->orderBy('publish_at DESC, content_calendar.id DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResult();

        return [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => (int) max(1, ceil($total / $perPage)),
        ];
    }

    protected function applyFilters($builder, array $filters)
    {
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        if (!empty($filters['source_type'])) {
            $builder->where('source_type', $filters['source_type']);
        }
        if (!empty($filters['date'])) {
            $builder->where('DATE(publish_at)', $filters['date']);
        }
        if (!empty($filters['q'])) {
            $builder->like('title', $filters['q']);
        }
        return $builder;
    }

    public function getStatusCounts(): array
    {
        $rows = $this->builder()
            ->select('status, COUNT(*) AS c')
            ->groupBy('status')
            ->get()
            ->getResultArray();
        $out = [];
        foreach ($rows as $r) {
            $out[$r['status']] = (int) $r['c'];
        }
        return $out;
    }

    public function getSourceCounts(): array
    {
        $rows = $this->builder()
            ->select('source_type, COUNT(*) AS c')
            ->groupBy('source_type')
            ->get()
            ->getResultArray();
        $out = [];
        foreach ($rows as $r) {
            $out[$r['source_type']] = (int) $r['c'];
        }
        return $out;
    }

    public function getByStatus(string $status, int $limit = 100)
    {
        return $this->builder()
            ->where('status', $status)
            ->orderBy('publish_at ASC, id ASC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function countForDate(string $date): int
    {
        return (int) $this->builder()
            ->where("DATE(publish_at)", $date)
            ->countAllResults();
    }

    public function countForDateBySource(string $date, string $sourceType): int
    {
        return (int) $this->builder()
            ->where("DATE(publish_at)", $date)
            ->where('source_type', $sourceType)
            ->countAllResults();
    }

    public function getDueToGenerate(string $now, int $limit = 10)
    {
        return $this->builder()
            ->groupStart()
                ->where('status', 'planned')
                ->orWhere('status', 'queued')
            ->groupEnd()
            ->groupStart()
                ->where('generate_at IS NULL', null, false)
                ->orWhere('generate_at <=', $now)
            ->groupEnd()
            ->orderBy('generate_at ASC, id ASC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function markStatus(int $id, string $status, array $extra = []): bool
    {
        $data = array_merge(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')], $extra);
        return (bool) $this->builder()->where('id', $id)->update($data);
    }
}
