<?php namespace App\Models;

class TrendItemModel extends BaseModel
{
    protected $table = 'trend_items';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title',
        'title_hash',
        'source_url',
        'source',
        'score',
        'lang_id',
        'category_id',
        'fetched_at',
        'selected',
        'auto_add',
        'used',
    ];

    public function upsertItem(array $data): bool
    {
        if (empty($data['title_hash'])) {
            return false;
        }
        $row = $this->builder()->where('title_hash', $data['title_hash'])->get()->getFirstRow();
        if (!empty($row)) {
            return (bool) $this->builder()->where('id', $row->id)->update($data);
        }
        return (bool) $this->builder()->insert($data);
    }

    public function getLatest(int $limit = 100)
    {
        return $this->builder()
            ->orderBy('fetched_at DESC, id DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function getCandidates(int $limit = 20): array
    {
        return $this->builder()
            ->groupStart()
                ->where('selected', 1)
                ->orWhere('auto_add', 1)
            ->groupEnd()
            ->where('used', 0)
            ->orderBy('fetched_at DESC, id DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }
}
