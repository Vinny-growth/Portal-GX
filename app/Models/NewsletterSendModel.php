<?php namespace App\Models;

class NewsletterSendModel extends BaseModel
{
    public function create(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;
        $this->db->table('newsletter_sends')->insert($data);
        return (int) $this->db->insertID();
    }

    public function updateSend(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->table('newsletter_sends')->where('id', $id)->update($data);
    }

    public function getById(int $id)
    {
        return $this->db->table('newsletter_sends')->where('id', $id)->get()->getRow();
    }

    public function getByStatus($status, int $limit = 50)
    {
        $b = $this->db->table('newsletter_sends')->where('status', $status);
        if ($status === 'approved') {
            $b->where('(scheduled_for IS NULL OR scheduled_for <= NOW())', null, false);
        }
        return $b->orderBy('id', 'ASC')->limit($limit)->get()->getResult();
    }

    public function listForAdmin(int $limit = 50, int $offset = 0, ?string $status = null)
    {
        $b = $this->db->table('newsletter_sends ns')
            ->select('ns.*, el.name AS line_name, el.slug AS line_slug')
            ->join('newsletter_editorial_lines el', 'el.id = ns.editorial_line_id', 'left');
        if ($status) {
            $b->where('ns.status', $status);
        }
        return $b->orderBy('ns.id', 'DESC')->limit($limit, $offset)->get()->getResult();
    }

    public function countByStatus(?string $status = null): int
    {
        $b = $this->db->table('newsletter_sends');
        if ($status) $b->where('status', $status);
        return $b->countAllResults();
    }

    public function approve(int $id, ?int $userId = null): bool
    {
        return $this->updateSend($id, [
            'status'      => 'approved',
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => $userId,
        ]);
    }

    public function cancel(int $id): bool
    {
        return $this->updateSend($id, ['status' => 'canceled']);
    }

    public function bumpOpenCount(int $sendId): void
    {
        $this->db->table('newsletter_sends')
            ->where('id', $sendId)
            ->set('opens_count', 'opens_count + 1', false)
            ->update();
    }

    public function bumpClickCount(int $sendId): void
    {
        $this->db->table('newsletter_sends')
            ->where('id', $sendId)
            ->set('clicks_count', 'clicks_count + 1', false)
            ->update();
    }
}
