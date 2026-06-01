<?php namespace App\Models;

class NewsletterEditorialLineModel extends BaseModel
{
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('newsletter_editorial_lines');
    }

    public function getAll($onlyEnabled = false)
    {
        $b = $this->db->table('newsletter_editorial_lines');
        if ($onlyEnabled) {
            $b->where('enabled', 1);
        }
        return $b->orderBy('name')->get()->getResult();
    }

    public function getById($id)
    {
        return $this->db->table('newsletter_editorial_lines')->where('id', (int) $id)->get()->getRow();
    }

    public function getBySlug($slug)
    {
        return $this->db->table('newsletter_editorial_lines')->where('slug', cleanStr($slug))->get()->getRow();
    }

    public function getLinesForCategory($categoryId)
    {
        $categoryId = (int) $categoryId;
        if ($categoryId <= 0) {
            return [];
        }
        $rows = $this->db->table('newsletter_editorial_lines')->where('enabled', 1)->get()->getResult();
        $matched = [];
        foreach ($rows as $row) {
            $ids = $this->decodeIds($row->category_ids ?? null);
            if (in_array($categoryId, $ids, true)) {
                $matched[] = $row;
            }
        }
        return $matched;
    }

    public function getMatchingLineIdsForCategory($categoryId)
    {
        $lines = $this->getLinesForCategory($categoryId);
        return array_map(fn($l) => (int) $l->id, $lines);
    }

    public function decodeIds($json)
    {
        if (empty($json)) return [];
        $arr = json_decode($json, true);
        if (!is_array($arr)) return [];
        return array_values(array_map('intval', $arr));
    }

    public function createLine(array $data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = $data['created_at'];
        $this->db->table('newsletter_editorial_lines')->insert($data);
        return $this->db->insertID();
    }

    public function updateLine($id, array $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->table('newsletter_editorial_lines')->where('id', (int) $id)->update($data);
    }

    public function deleteLine($id)
    {
        return $this->db->table('newsletter_editorial_lines')->where('id', (int) $id)->delete();
    }

    public function touchLastSent($id, $when = null)
    {
        $when = $when ?: date('Y-m-d H:i:s');
        return $this->db->table('newsletter_editorial_lines')->where('id', (int) $id)->update(['last_sent_at' => $when]);
    }
}
