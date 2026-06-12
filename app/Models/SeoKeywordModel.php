<?php

namespace App\Models;

class SeoKeywordModel extends BaseModel
{
    protected $table = 'seo_keywords';
    protected $primaryKey = 'id';
    // Framework default ./system/BaseModel.php is 'array'; this module is written
    // against objects ($row->prop), so force object return here.
    protected $returnType = 'object';
    protected $allowedFields = [
        'keyword', 'target_url', 'category_id', 'locale', 'country', 'device',
        'source', 'origin', 'post_count', 'is_active', 'notes', 'last_position', 'last_checked_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /** Active keywords being tracked. */
    public function getActive()
    {
        return $this->where('is_active', 1)->orderBy('keyword', 'ASC')->findAll();
    }

    /**
     * Keywords joined with their latest ranking snapshot + the position N days ago,
     * so the UI can show current position, deltas, clicks, impressions and CTR.
     */
    public function getWithMetrics(int $deltaDays = 7)
    {
        $rows = $this->orderBy('is_active', 'DESC')->orderBy('post_count', 'DESC')->orderBy('keyword', 'ASC')->findAll();
        if (empty($rows)) {
            return [];
        }

        $rankingModel = new SeoRankingModel();
        $latest = $rankingModel->latestByKeyword();
        $past = $rankingModel->positionAsOf($deltaDays);

        foreach ($rows as $row) {
            $cur = $latest[$row->id] ?? null;
            $row->position     = $cur->position ?? null;
            $row->url_found    = $cur->url_found ?? null;
            $row->clicks       = (int) ($cur->clicks ?? 0);
            $row->impressions  = (int) ($cur->impressions ?? 0);
            $row->ctr          = (float) ($cur->ctr ?? 0);
            $row->checked_date = $cur->checked_date ?? null;
            $row->ranking_source = $cur->source ?? null;

            $prev = $past[$row->id]->position ?? null;
            // Positive delta = improved (moved up the page, i.e. lower number).
            $row->delta = ($row->position !== null && $prev !== null)
                ? round(((float) $prev) - ((float) $row->position), 1)
                : null;
        }

        return $rows;
    }
}
