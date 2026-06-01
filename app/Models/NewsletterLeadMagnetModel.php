<?php namespace App\Models;

class NewsletterLeadMagnetModel extends BaseModel
{
    public function getAll($onlyActive = false)
    {
        $b = $this->db->table('newsletter_lead_magnets');
        if ($onlyActive) $b->where('active', 1);
        return $b->orderBy('title')->get()->getResult();
    }

    public function getById($id)
    {
        return $this->db->table('newsletter_lead_magnets')->where('id', (int) $id)->get()->getRow();
    }

    public function getBySlug($slug)
    {
        return $this->db->table('newsletter_lead_magnets')->where('slug', cleanStr($slug))->get()->getRow();
    }

    public function getByToken($token)
    {
        $row = $this->db->table('newsletter_magnet_downloads')
            ->where('token', $token)
            ->get()->getRow();
        if (!$row) return null;
        $magnet = $this->getById((int) $row->magnet_id);
        return $magnet ? ['magnet' => $magnet, 'audit' => $row] : null;
    }

    public function createMagnet(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;
        $this->db->table('newsletter_lead_magnets')->insert($data);
        return (int) $this->db->insertID();
    }

    public function updateMagnet(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->table('newsletter_lead_magnets')->where('id', $id)->update($data);
    }

    public function deleteMagnet(int $id): bool
    {
        return $this->db->table('newsletter_lead_magnets')->where('id', $id)->delete();
    }

    public function generateDownloadToken(int $magnetId, ?int $subscriberId = null): string
    {
        $token = bin2hex(random_bytes(16));
        $this->db->table('newsletter_magnet_downloads')->insert([
            'magnet_id' => $magnetId,
            'subscriber_id' => $subscriberId,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $token;
    }

    public function recordDownload(string $token, ?string $ip = null): void
    {
        $this->db->table('newsletter_magnet_downloads')
            ->where('token', $token)
            ->update(['downloaded_at' => date('Y-m-d H:i:s'), 'ip_address' => $ip]);
        // bump magnet counter
        $row = $this->db->table('newsletter_magnet_downloads')->where('token', $token)->get()->getRow();
        if ($row) {
            $this->db->table('newsletter_lead_magnets')
                ->where('id', $row->magnet_id)
                ->set('downloads_count', 'downloads_count + 1', false)
                ->update();
        }
    }
}
