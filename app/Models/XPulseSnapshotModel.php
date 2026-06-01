<?php namespace App\Models;

class XPulseSnapshotModel extends BaseModel
{
    protected $table = 'x_pulse_snapshot';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'snapshot_date',
        'window_hours',
        'theme',
        'summary',
        'mentions_estimate',
        'sentiment',
        'tickers_json',
        'entities_json',
        'relevance_score',
        'rank',
        'used_in_calendar',
        'raw_response',
        'created_at',
    ];

    public function insertSnapshot(string $date, int $windowHours, array $themes, ?string $rawResponse = null): int
    {
        if (empty($themes)) {
            return 0;
        }
        // Replace today's snapshot for this window — keep history per day but one shot per window
        $this->builder()
            ->where('snapshot_date', $date)
            ->where('window_hours', $windowHours)
            ->delete();

        $now = date('Y-m-d H:i:s');
        $count = 0;
        $rawAttached = false;
        foreach ($themes as $idx => $t) {
            if (!is_array($t)) {
                continue;
            }
            $theme = trim((string) ($t['theme'] ?? ''));
            if ($theme === '') {
                continue;
            }
            $data = [
                'snapshot_date'     => $date,
                'window_hours'      => $windowHours,
                'theme'             => mb_substr($theme, 0, 500),
                'summary'           => isset($t['summary']) ? mb_substr((string) $t['summary'], 0, 2000) : null,
                'mentions_estimate' => max(0, (int) ($t['mentions_estimate'] ?? 0)),
                'sentiment'         => $this->normalizeSentiment((string) ($t['sentiment'] ?? 'neutral')),
                'tickers_json'      => isset($t['tickers']) ? json_encode($t['tickers'], JSON_UNESCAPED_UNICODE) : null,
                'entities_json'     => isset($t['entities']) ? json_encode($t['entities'], JSON_UNESCAPED_UNICODE) : null,
                'relevance_score'   => max(0, min(100, (int) ($t['relevance_score'] ?? 0))),
                'rank'              => $idx + 1,
                'used_in_calendar'  => 0,
                'raw_response'      => !$rawAttached ? $rawResponse : null,
                'created_at'        => $now,
            ];
            if ($this->builder()->insert($data)) {
                $count++;
                $rawAttached = true; // attach raw to first row only, to save space
            }
        }
        return $count;
    }

    public function getActive(int $hoursWindow = 24, int $limit = 20): array
    {
        $cutoff = date('Y-m-d H:i:s', time() - ($hoursWindow * 3600));
        return $this->builder()
            ->where('created_at >=', $cutoff)
            ->orderBy('rank', 'ASC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function getLatest(int $limit = 20): array
    {
        return $this->builder()
            ->orderBy('snapshot_date DESC, rank ASC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function markUsed(int $id): bool
    {
        return (bool) $this->builder()->where('id', $id)->update(['used_in_calendar' => 1]);
    }

    protected function normalizeSentiment(string $s): string
    {
        $s = strtolower(trim($s));
        return in_array($s, ['positive', 'negative', 'neutral', 'mixed'], true) ? $s : 'neutral';
    }
}
