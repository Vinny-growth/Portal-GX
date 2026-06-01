<?php namespace App\Models;

class TrendSourceHealthModel extends BaseModel
{
    protected $table = 'trend_source_health';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'source',
        'fetched_at',
        'http_code',
        'response_time_ms',
        'items_returned',
        'attempt',
        'success',
        'error_message',
    ];

    public function log(string $source, int $httpCode, int $responseMs, int $itemsReturned, int $attempt, bool $success, ?string $error = null): void
    {
        $this->builder()->insert([
            'source'           => $source,
            'fetched_at'       => date('Y-m-d H:i:s'),
            'http_code'        => $httpCode,
            'response_time_ms' => $responseMs,
            'items_returned'   => $itemsReturned,
            'attempt'          => $attempt,
            'success'          => $success ? 1 : 0,
            'error_message'    => $error !== null ? mb_substr($error, 0, 500) : null,
        ]);
    }

    /**
     * Circuit breaker check.
     * Returns true if the source is currently OPEN (skip the fetch).
     * Opens when last $threshold attempts within $windowMinutes all failed.
     */
    public function isBreakerOpen(string $source, int $threshold = 3, int $windowMinutes = 30): bool
    {
        $cutoff = date('Y-m-d H:i:s', time() - ($windowMinutes * 60));
        $rows = $this->builder()
            ->select('success')
            ->where('source', $source)
            ->where('fetched_at >=', $cutoff)
            ->orderBy('id', 'DESC')
            ->limit($threshold)
            ->get()
            ->getResult();
        if (count($rows) < $threshold) {
            return false;
        }
        foreach ($rows as $r) {
            if ((int) $r->success === 1) {
                return false;
            }
        }
        return true;
    }

    public function getHealthSummary(int $hours = 24): array
    {
        $cutoff = date('Y-m-d H:i:s', time() - ($hours * 3600));
        $rows = $this->builder()
            ->select([
                'source',
                'COUNT(*) AS attempts',
                'SUM(success) AS successes',
                'SUM(items_returned) AS items',
                'AVG(response_time_ms) AS avg_latency',
                'MAX(fetched_at) AS last_attempt',
            ], false)
            ->where('fetched_at >=', $cutoff)
            ->groupBy('source')
            ->orderBy('source', 'ASC')
            ->get()
            ->getResult();
        $out = [];
        foreach ($rows as $r) {
            $attempts = (int) $r->attempts;
            $successes = (int) $r->successes;
            $out[] = [
                'source'        => $r->source,
                'attempts'      => $attempts,
                'successes'     => $successes,
                'failures'      => $attempts - $successes,
                'success_rate'  => $attempts > 0 ? round($successes / $attempts * 100, 1) : 0.0,
                'items'         => (int) $r->items,
                'avg_latency_ms' => (int) round((float) $r->avg_latency),
                'last_attempt'  => $r->last_attempt,
                'status'        => $successes === $attempts ? 'healthy' : ($successes === 0 ? 'down' : 'degraded'),
            ];
        }
        return $out;
    }
}
