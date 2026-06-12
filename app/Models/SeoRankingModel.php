<?php

namespace App\Models;

class SeoRankingModel extends BaseModel
{
    protected $table = 'seo_rankings';
    protected $primaryKey = 'id';
    // Framework default ./system/BaseModel.php is 'array'; force objects for this module.
    protected $returnType = 'object';
    protected $allowedFields = [
        'keyword_id', 'position', 'url_found', 'clicks', 'impressions',
        'ctr', 'source', 'checked_date',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    /**
     * Insert or update the single row for a keyword/day/source.
     * Idempotent so re-running the fetch on the same day overwrites, never duplicates.
     */
    public function upsertDaily(array $data): void
    {
        $existing = $this->where('keyword_id', $data['keyword_id'])
            ->where('checked_date', $data['checked_date'])
            ->where('source', $data['source'] ?? 'gsc')
            ->first();

        if ($existing) {
            $this->update($existing->id, $data);
        } else {
            $this->insert($data);
        }
    }

    /** Latest ranking row per keyword, keyed by keyword_id. */
    public function latestByKeyword(): array
    {
        $rows = $this->orderBy('checked_date', 'DESC')->orderBy('id', 'DESC')->findAll();
        $out = [];
        foreach ($rows as $row) {
            if (!isset($out[$row->keyword_id])) {
                $out[$row->keyword_id] = $row;
            }
        }
        return $out;
    }

    /** Position as of ~N days ago per keyword (closest snapshot at or before that date). */
    public function positionAsOf(int $daysAgo): array
    {
        $cutoff = date('Y-m-d', strtotime('-' . $daysAgo . ' days'));
        $rows = $this->where('checked_date <=', $cutoff)
            ->orderBy('checked_date', 'DESC')->orderBy('id', 'DESC')->findAll();
        $out = [];
        foreach ($rows as $row) {
            if (!isset($out[$row->keyword_id])) {
                $out[$row->keyword_id] = $row;
            }
        }
        return $out;
    }

    /** Daily series for one keyword over the window, for the evolution chart. */
    public function seriesForKeyword(int $keywordId, int $days = 30): array
    {
        $start = date('Y-m-d', strtotime('-' . $days . ' days'));
        return $this->where('keyword_id', $keywordId)
            ->where('checked_date >=', $start)
            ->orderBy('checked_date', 'ASC')
            ->findAll();
    }

    /** Aggregate KPIs across the latest snapshot of every keyword. */
    public function overview(): array
    {
        $latest = $this->latestByKeyword();
        $positions = [];
        $clicks = 0;
        $impressions = 0;
        $top3 = 0;
        $top10 = 0;
        $top100 = 0;

        foreach ($latest as $row) {
            $clicks += (int) $row->clicks;
            $impressions += (int) $row->impressions;
            if ($row->position !== null) {
                $p = (float) $row->position;
                $positions[] = $p;
                if ($p <= 3) $top3++;
                if ($p <= 10) $top10++;
                if ($p <= 100) $top100++;
            }
        }

        return [
            'tracked'      => count($latest),
            'with_position'=> count($positions),
            'avg_position' => $positions ? round(array_sum($positions) / count($positions), 1) : null,
            'top3'         => $top3,
            'top10'        => $top10,
            'top100'       => $top100,
            'clicks'       => $clicks,
            'impressions'  => $impressions,
        ];
    }

    /** Average tracked position per day, for the overview evolution chart. */
    public function avgPositionByDay(int $days = 30): array
    {
        $start = date('Y-m-d', strtotime('-' . $days . ' days'));
        return $this->select('checked_date, AVG(position) AS avg_position, SUM(clicks) AS clicks, SUM(impressions) AS impressions')
            ->where('checked_date >=', $start)
            ->where('position IS NOT NULL')
            ->groupBy('checked_date')
            ->orderBy('checked_date', 'ASC')
            ->findAll();
    }
}
