<?php

namespace App\Libraries;

use App\Models\SeoKeywordModel;
use App\Models\SeoRankingModel;

/**
 * Orchestrates SEO ranking collection: pulls real positions from Google Search
 * Console (primary) and optionally from openserp (secondary, for keywords GSC
 * doesn't cover) and persists a daily snapshot per keyword.
 */
class SeoRankingService
{
    /** Hard cap on openserp (scraping) calls per run — protects against captcha/IP blocks at scale. */
    private const MAX_SERP_PER_RUN = 50;

    protected SeoKeywordModel $keywords;
    protected SeoRankingModel $rankings;
    protected GscClient $gsc;
    protected SerpClient $serp;

    public function __construct()
    {
        $this->keywords = new SeoKeywordModel();
        $this->rankings = new SeoRankingModel();
        $this->gsc      = new GscClient();
        $this->serp     = new SerpClient();
    }

    /** Integration status for the UI banners. */
    public function providersStatus(): array
    {
        return [
            'gsc'          => $this->gsc->isConfigured(),
            'gsc_site'     => $this->gsc->getSiteUrl(),
            'serp'         => $this->serp->isConfigured(),
        ];
    }

    /**
     * Collect rankings for all active keywords.
     *
     * @return array{gsc:int, serp:int, skipped:int, errors:array}
     */
    public function fetchAll(int $gscWindowDays = 28): array
    {
        $result = ['gsc' => 0, 'serp' => 0, 'skipped' => 0, 'errors' => []];
        $today = date('Y-m-d');

        $active = $this->keywords->getActive();
        if (empty($active)) {
            return $result;
        }

        // --- 1) Google Search Console: one bulk call, mapped onto our keywords ---
        $gscByKeyword = [];
        if ($this->gsc->isConfigured()) {
            $end   = date('Y-m-d', strtotime('-2 days')); // GSC data lags ~2-3 days
            $start = date('Y-m-d', strtotime('-' . ($gscWindowDays + 2) . ' days'));
            $rows  = $this->gsc->queryByKeyword($start, $end, 5000);
            if ($rows === [] && $this->gsc->getLastError()) {
                $result['errors'][] = 'GSC: ' . $this->gsc->getLastError();
            }
            foreach ($rows as $row) {
                $gscByKeyword[$this->normalize($row['query'])] = $row;
            }
        }

        // --- 2) Per keyword: prefer GSC; fall back to openserp when enabled ---
        $serpCalls = 0;
        foreach ($active as $kw) {
            $norm = $this->normalize($kw->keyword);

            if (isset($gscByKeyword[$norm])) {
                $g = $gscByKeyword[$norm];
                $this->rankings->upsertDaily([
                    'keyword_id'   => $kw->id,
                    'position'     => $g['position'],
                    'url_found'    => null,
                    'clicks'       => $g['clicks'],
                    'impressions'  => $g['impressions'],
                    'ctr'          => $g['ctr'],
                    'source'       => 'gsc',
                    'checked_date' => $today,
                ]);
                $this->keywords->update($kw->id, [
                    'last_position'   => $g['position'],
                    'last_checked_at' => date('Y-m-d H:i:s'),
                ]);
                $result['gsc']++;
                continue;
            }

            if ($this->serp->isConfigured() && $serpCalls < self::MAX_SERP_PER_RUN) {
                $serpCalls++;
                $lang = stripos($kw->locale, 'pt') === 0 ? 'pt' : 'en';
                $found = $this->serp->findPosition($kw->keyword, $lang);
                if ($found !== null) {
                    $this->rankings->upsertDaily([
                        'keyword_id'   => $kw->id,
                        'position'     => $found['position'],
                        'url_found'    => $found['url_found'],
                        'clicks'       => 0,
                        'impressions'  => 0,
                        'ctr'          => 0,
                        'source'       => 'serp',
                        'checked_date' => $today,
                    ]);
                    $this->keywords->update($kw->id, [
                        'last_position'   => $found['position'],
                        'last_checked_at' => date('Y-m-d H:i:s'),
                    ]);
                    $result['serp']++;
                    continue;
                }
                if ($this->serp->getLastError()) {
                    $result['errors'][] = 'openserp (' . $kw->keyword . '): ' . $this->serp->getLastError();
                }
            }

            $result['skipped']++;
        }

        return $result;
    }

    /**
     * Auto-populate the tracker with the exact keywords used across the project's
     * content: the `keywords` field of published posts (primary, accented) and the
     * `tags` of calendar items (secondary). Deduplicated by normalized form, so the
     * tracker always mirrors what we actually publish. Never removes manual keywords.
     *
     * @return array{added:int, updated:int, scanned:int}
     */
    public function syncFromContent(): array
    {
        $db = \Config\Database::connect();
        $result = ['added' => 0, 'updated' => 0, 'scanned' => 0];

        // normalizedKey => ['display','category_id','target_url','count']
        $candidates = [];

        // 1) Published posts — real, accented keywords + their page (slug) and category.
        $posts = $db->table('posts')
            ->select('category_id, slug, keywords')
            ->where('status', 1)
            ->where('keywords IS NOT NULL', null, false)
            ->where("keywords !=", '')
            ->orderBy('id', 'DESC')
            ->get()->getResult();
        foreach ($posts as $p) {
            $this->absorbTags($candidates, (string) $p->keywords, $p->category_id, $p->slug);
        }

        // 2) Calendar tags — covers what is scheduled but not yet published.
        $cal = $db->table('content_calendar')
            ->select('category_id, tags')
            ->where('tags IS NOT NULL', null, false)
            ->where("tags !=", '')
            ->get()->getResult();
        foreach ($cal as $c) {
            $this->absorbTags($candidates, (string) $c->tags, $c->category_id, null);
        }

        $result['scanned'] = count($candidates);

        // Existing keywords keyed by normalized form (dedup, never duplicate).
        $existing = [];
        foreach ($this->keywords->findAll() as $k) {
            $existing[$this->normalize($k->keyword)] = $k;
        }

        foreach ($candidates as $key => $cand) {
            if (isset($existing[$key])) {
                $kw = $existing[$key];
                $upd = ['post_count' => $cand['count']];
                if (empty($kw->target_url) && !empty($cand['target_url'])) {
                    $upd['target_url'] = $cand['target_url'];
                }
                if (empty($kw->category_id) && !empty($cand['category_id'])) {
                    $upd['category_id'] = $cand['category_id'];
                }
                $this->keywords->update($kw->id, $upd);
                $result['updated']++;
            } else {
                $this->keywords->insert([
                    'keyword'     => $cand['display'],
                    'target_url'  => $cand['target_url'],
                    'category_id' => $cand['category_id'],
                    'locale'      => 'pt-BR',
                    'country'     => 'bra',
                    'device'      => 'desktop',
                    'source'      => 'gsc',
                    'origin'      => 'content',
                    'post_count'  => $cand['count'],
                    'is_active'   => 1,
                ]);
                $result['added']++;
            }
        }

        return $result;
    }

    /** Split a comma-separated tag string and fold each token into the candidate map. */
    private function absorbTags(array &$candidates, string $raw, $categoryId, ?string $slug): void
    {
        foreach (explode(',', $raw) as $tag) {
            $tag = trim(preg_replace('/\s+/', ' ', $tag));
            if ($tag === '' || mb_strlen($tag) < 3 || mb_strlen($tag) > 120) {
                continue;
            }
            if (preg_match('/^\d+$/', $tag)) {
                continue; // skip pure numbers
            }
            $key = $this->normalize($tag);
            if (!isset($candidates[$key])) {
                $candidates[$key] = [
                    'display'     => $tag,
                    'category_id' => $categoryId ?: null,
                    'target_url'  => $slug ?: null,
                    'count'       => 0,
                ];
            } else {
                // Prefer an accented display (posts have proper accents; tags may not).
                if ($this->hasAccents($tag) && !$this->hasAccents($candidates[$key]['display'])) {
                    $candidates[$key]['display'] = $tag;
                }
                if (empty($candidates[$key]['target_url']) && $slug) {
                    $candidates[$key]['target_url'] = $slug;
                }
                if (empty($candidates[$key]['category_id']) && $categoryId) {
                    $candidates[$key]['category_id'] = $categoryId;
                }
            }
            $candidates[$key]['count']++;
        }
    }

    private function hasAccents(string $s): bool
    {
        return (bool) preg_match('/[áàãâäéêèíìóôõòúùûüçñ]/iu', $s);
    }

    /** Normalize a keyword/query for matching (lowercase, strip accents, collapse spaces). */
    private function normalize(string $value): string
    {
        $value = mb_strtolower(trim($value), 'UTF-8');
        $map = ['á'=>'a','à'=>'a','ã'=>'a','â'=>'a','ä'=>'a','é'=>'e','ê'=>'e','è'=>'e','í'=>'i','ì'=>'i',
                'ó'=>'o','ô'=>'o','õ'=>'o','ò'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ç'=>'c','ñ'=>'n'];
        $value = strtr($value, $map);
        $value = preg_replace('/\s+/', ' ', $value);
        return $value;
    }
}
