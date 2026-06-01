<?php namespace App\Models;

class NewsletterCrmSyncModel extends BaseModel
{
    protected $table = 'newsletter_crm_syncs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'source',
        'trigger_type',
        'updated_since',
        'started_at',
        'finished_at',
        'status',
        'pages_fetched',
        'total_received',
        'created_count',
        'updated_count',
        'skipped_unsubscribed',
        'skipped_invalid',
        'filtered_opt_out_total',
        'error_log',
        'performed_by',
    ];

    public function start(string $source, string $trigger, ?string $updatedSince, ?int $performedBy = null): int
    {
        $this->builder()->insert([
            'source'        => $source,
            'trigger_type'  => $trigger,
            'updated_since' => $updatedSince,
            'started_at'    => date('Y-m-d H:i:s'),
            'status'        => 'running',
            'performed_by'  => $performedBy,
        ]);
        return (int) $this->db->insertID();
    }

    public function finish(int $id, string $status, array $counters = [], ?string $errorLog = null): bool
    {
        $data = array_merge($counters, [
            'status'      => $status,
            'finished_at' => date('Y-m-d H:i:s'),
            'error_log'   => $errorLog,
        ]);
        return (bool) $this->builder()->where('id', $id)->update($data);
    }

    public function getLastSuccessful(string $source): ?object
    {
        $row = $this->builder()
            ->where('source', $source)
            ->where('status', 'success')
            ->orderBy('finished_at DESC')
            ->limit(1)
            ->get()
            ->getFirstRow();
        return $row ?: null;
    }

    public function getLastBySource(string $source): ?object
    {
        $row = $this->builder()
            ->where('source', $source)
            ->orderBy('started_at DESC')
            ->limit(1)
            ->get()
            ->getFirstRow();
        return $row ?: null;
    }

    public function getRecent(int $limit = 30): array
    {
        return $this->builder()
            ->orderBy('started_at DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function getById(int $id): ?object
    {
        $row = $this->builder()->where('id', $id)->get()->getFirstRow();
        return $row ?: null;
    }
}
