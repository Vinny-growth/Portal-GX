<?php

namespace App\Models;

class BioLinksModel extends BaseModel
{
    protected $table = 'bio_links';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 'url', 'icon', 'button_color', 'text_color', 
        'is_active', 'display_order', 'click_count'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getBioLinks()
    {
        return $this->where('is_active', 1)
                   ->orderBy('display_order', 'ASC')
                   ->findAll();
    }

    public function getAllBioLinks()
    {
        return $this->orderBy('display_order', 'ASC')->findAll();
    }

    public function getBioLink($id)
    {
        return $this->find($id);
    }

    public function addBioLink($data)
    {
        $data['display_order'] = $this->getNextDisplayOrder();
        return $this->insert($data);
    }

    public function updateBioLink($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteBioLink($id)
    {
        return $this->delete($id);
    }

    public function incrementClickCount($id)
    {
        $this->db->table($this->table)
                 ->where('id', $id)
                 ->set('click_count', 'click_count + 1', false)
                 ->update();
    }

    public function logClick($id, $ipAddress = null, $userAgent = null, $referrer = null)
    {
        $this->db->table('bio_link_clicks')->insert([
            'link_id'    => $id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent ? mb_substr($userAgent, 0, 512) : null,
            'referrer'   => $referrer ? mb_substr($referrer, 0, 512) : null,
            'clicked_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getClicksByDay($days = 30)
    {
        $since = date('Y-m-d', strtotime("-{$days} days"));

        return $this->db->table('bio_link_clicks')
            ->select("DATE(clicked_at) AS day, COUNT(*) AS clicks")
            ->where('clicked_at >=', $since . ' 00:00:00')
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getClicksByLink($days = 30)
    {
        $since = date('Y-m-d', strtotime("-{$days} days"));

        return $this->db->table('bio_link_clicks AS c')
            ->select('c.link_id, b.title, b.icon, b.button_color, COUNT(*) AS clicks')
            ->join('bio_links AS b', 'b.id = c.link_id', 'left')
            ->where('c.clicked_at >=', $since . ' 00:00:00')
            ->groupBy('c.link_id')
            ->orderBy('clicks', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getClicksByLinkPerDay($days = 30)
    {
        $since = date('Y-m-d', strtotime("-{$days} days"));

        return $this->db->table('bio_link_clicks AS c')
            ->select('c.link_id, b.title, DATE(c.clicked_at) AS day, COUNT(*) AS clicks')
            ->join('bio_links AS b', 'b.id = c.link_id', 'left')
            ->where('c.clicked_at >=', $since . ' 00:00:00')
            ->groupBy('c.link_id, day')
            ->orderBy('day', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getRecentClicks($limit = 20)
    {
        return $this->db->table('bio_link_clicks AS c')
            ->select('c.*, b.title, b.url')
            ->join('bio_links AS b', 'b.id = c.link_id', 'left')
            ->orderBy('c.clicked_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function getClicksAnalytics($days = 30)
    {
        $since = date('Y-m-d', strtotime("-{$days} days"));

        $totalClicks = $this->db->table('bio_link_clicks')
            ->where('clicked_at >=', $since . ' 00:00:00')
            ->countAllResults();

        $uniqueIPs = $this->db->table('bio_link_clicks')
            ->select('COUNT(DISTINCT ip_address) AS cnt')
            ->where('clicked_at >=', $since . ' 00:00:00')
            ->get()
            ->getRow()
            ->cnt ?? 0;

        $topReferrers = $this->db->table('bio_link_clicks')
            ->select("COALESCE(NULLIF(referrer, ''), 'Direto') AS source, COUNT(*) AS clicks")
            ->where('clicked_at >=', $since . ' 00:00:00')
            ->groupBy('source')
            ->orderBy('clicks', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return [
            'total_clicks'  => (int) $totalClicks,
            'unique_ips'    => (int) $uniqueIPs,
            'top_referrers' => $topReferrers,
        ];
    }

    public function updateDisplayOrders($orders)
    {
        foreach ($orders as $id => $order) {
            $this->update($id, ['display_order' => $order]);
        }
    }

    public function toggleActive($id)
    {
        $link = $this->find($id);
        if ($link) {
            $newStatus = $link['is_active'] == 1 ? 0 : 1;
            return $this->update($id, ['is_active' => $newStatus]);
        }
        return false;
    }

    private function getNextDisplayOrder()
    {
        $maxOrder = $this->selectMax('display_order')->first();
        return ($maxOrder['display_order'] ?? 0) + 1;
    }

    public function getBioLinksStats()
    {
        $totalLinks = $this->countAll();
        $activeLinks = $this->where('is_active', 1)->countAllResults();
        $totalClicks = $this->selectSum('click_count')->first()['click_count'] ?? 0;
        
        return [
            'total_links' => $totalLinks,
            'active_links' => $activeLinks,
            'total_clicks' => $totalClicks
        ];
    }
}