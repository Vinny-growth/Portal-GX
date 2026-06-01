<?php namespace App\Models;

use App\Libraries\TrendNormalizer;

class TrendItemModel extends BaseModel
{
    protected $table = 'trend_items';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title',
        'title_hash',
        'semantic_hash',
        'source_url',
        'source',
        'source_authority',
        'cross_source_count',
        'score',
        'lang_id',
        'category_id',
        'fetched_at',
        'last_seen_at',
        'selected',
        'auto_add',
        'used',
    ];

    public function upsertItem(array $data): bool
    {
        $title = (string) ($data['title'] ?? '');
        if ($title === '') {
            return false;
        }
        if (empty($data['title_hash'])) {
            $data['title_hash'] = md5(mb_strtolower($title));
        }
        if (empty($data['semantic_hash'])) {
            $data['semantic_hash'] = TrendNormalizer::semanticHash($title);
        }
        if (!isset($data['source_authority']) || $data['source_authority'] === '') {
            $data['source_authority'] = TrendNormalizer::sourceAuthority((string) ($data['source'] ?? ''));
        }
        if (empty($data['fetched_at'])) {
            $data['fetched_at'] = date('Y-m-d H:i:s');
        }
        $data['last_seen_at'] = $data['fetched_at'];

        $semHash = $data['semantic_hash'];
        $newSource = (string) ($data['source'] ?? '');
        $cutoff = date('Y-m-d H:i:s', time() - 172800); // 48h

        $db = $this->db;
        $db->transStart();

        // 1) Exact-title match → bump last_seen_at + recompute cross_source_count
        $exact = $this->builder()->where('title_hash', $data['title_hash'])->get()->getFirstRow();
        if (!empty($exact)) {
            $this->builder()->where('id', $exact->id)->update([
                'last_seen_at' => $data['last_seen_at'],
                'source_authority' => max((int) $exact->source_authority, (int) $data['source_authority']),
            ]);
            $this->recomputeCrossSource($semHash, $cutoff);
            $db->transComplete();
            return $db->transStatus();
        }

        // 2) Semantic sibling from a DIFFERENT source within 48h → merge into the highest-authority sibling
        $sibling = $this->builder()
            ->where('semantic_hash', $semHash)
            ->where('source !=', $newSource)
            ->where('last_seen_at >=', $cutoff)
            ->orderBy('source_authority DESC, fetched_at DESC')
            ->get()->getFirstRow();
        if (!empty($sibling)) {
            $this->builder()->where('id', $sibling->id)->update([
                'last_seen_at' => $data['last_seen_at'],
                'source_authority' => max((int) $sibling->source_authority, (int) $data['source_authority']),
            ]);
            $this->recomputeCrossSource($semHash, $cutoff);
            $db->transComplete();
            return $db->transStatus();
        }

        // 3) Brand-new theme → atomic INSERT ... ON DUPLICATE KEY UPDATE on title_hash.
        // Protects against the race where two parallel cron runs both reach this branch:
        // the second one falls through to UPDATE on the row the first just created.
        if (!isset($data['cross_source_count'])) {
            $data['cross_source_count'] = 1;
        }
        $insertData = array_intersect_key($data, array_flip($this->allowedFields));
        $fields = array_keys($insertData);
        $values = array_values($insertData);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $quoted = implode(', ', array_map(fn($f) => '`' . $f . '`', $fields));
        $sql = 'INSERT INTO `' . $this->table . '` (' . $quoted . ') VALUES (' . $placeholders . ') '
            . 'ON DUPLICATE KEY UPDATE '
            . '`last_seen_at` = VALUES(`last_seen_at`), '
            . '`source_authority` = GREATEST(`source_authority`, VALUES(`source_authority`))';
        $db->query($sql, $values);
        $this->recomputeCrossSource($semHash, $cutoff);
        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * Recompute cross_source_count for ALL rows sharing a semantic cluster within the window.
     * Self-healing: keeps the metric correct even after concurrent inserts.
     */
    protected function recomputeCrossSource(string $semHash, string $cutoff): void
    {
        if ($semHash === '') {
            return;
        }
        $row = $this->builder()
            ->select('COUNT(DISTINCT source) AS sources')
            ->where('semantic_hash', $semHash)
            ->where('last_seen_at >=', $cutoff)
            ->get()->getFirstRow();
        $count = max(1, (int) ($row->sources ?? 1));
        $this->builder()
            ->where('semantic_hash', $semHash)
            ->where('last_seen_at >=', $cutoff)
            ->update(['cross_source_count' => $count]);
    }

    public function getLatest(int $limit = 100)
    {
        return $this->builder()
            ->orderBy('last_seen_at DESC, fetched_at DESC, id DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    /**
     * Multi-factor scoring computed in SQL.
     * factors:
     *   freshness          (0-100, decays from last_seen_at)
     *   cross_source       (0-100, log-scale of distinct sources)
     *   authority          (0-100, mapped from source_authority 0-10 × 10)
     *   native_score       (0-100, normalized from google_trends raw score capped 2000)
     *
     * final_score = 0.40*freshness + 0.30*cross_source + 0.20*authority + 0.10*native_score
     */
    public function getCandidates(int $limit = 20, bool $autoAll = false): array
    {
        $builder = $this->builder();
        $builder->select([
            '*',
            // freshness: 100 if last_seen within 6h, 50 at 24h, 10 at 7d, 0 after 30d
            'GREATEST(0, 100 - (TIMESTAMPDIFF(MINUTE, COALESCE(last_seen_at, fetched_at), NOW()) / 14.4)) AS freshness_score',
            // cross-source: 1 source=20, 2=50, 3=75, 4+=100
            'LEAST(100, 20 + (LEAST(cross_source_count, 5) - 1) * 20) AS cross_source_score',
            'LEAST(100, source_authority * 10) AS authority_score',
            'LEAST(100, (score / 20)) AS native_score',
            '(GREATEST(0, 100 - (TIMESTAMPDIFF(MINUTE, COALESCE(last_seen_at, fetched_at), NOW()) / 14.4)) * 0.40 + '
            . 'LEAST(100, 20 + (LEAST(cross_source_count, 5) - 1) * 20) * 0.30 + '
            . 'LEAST(100, source_authority * 10) * 0.20 + '
            . 'LEAST(100, (score / 20)) * 0.10) AS final_score',
        ], false);
        $builder->where('used', 0);
        if (!$autoAll) {
            $builder->groupStart()
                ->where('selected', 1)
                ->orWhere('auto_add', 1)
            ->groupEnd();
        }
        return $builder
            ->orderBy('final_score', 'DESC')
            ->orderBy('last_seen_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }
}
