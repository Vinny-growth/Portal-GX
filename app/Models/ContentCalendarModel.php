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
            ->where("DATE(publish_at) = ", $date, false)
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
