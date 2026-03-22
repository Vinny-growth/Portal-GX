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