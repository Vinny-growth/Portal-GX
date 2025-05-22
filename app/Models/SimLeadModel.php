<?php namespace App\Models;

use CodeIgniter\Model;

class SimLeadModel extends BaseModel
{
    protected $builderSimLeads;

    public function __construct()
    {
        parent::__construct();
        $this->builderSimLeads = $this->db->table('sim_leads');
    }

    //add simulator lead
    public function addSimLead($data)
    {
        $insertData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'sim_data' => isset($data['sim_data']) ? $data['sim_data'] : NULL,
            'observations' => isset($data['observations']) ? $data['observations'] : NULL,
            'status' => 'new',
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->builderSimLeads->insert($insertData);
    }

    //get simulator leads
    public function getSimLeads($limit = null)
    {
        if ($limit != null) {
            $this->builderSimLeads->limit(clrNum($limit));
        }
        return $this->builderSimLeads->orderBy('id DESC')->get()->getResult();
    }

    //get simulator lead
    public function getSimLead($id)
    {
        return $this->builderSimLeads->where('id', clrNum($id))->get()->getRow();
    }

    //update simulator lead
    public function updateSimLeadStatus($id, $status)
    {
        return $this->builderSimLeads->where('id', clrNum($id))->update(['status' => $status]);
    }

    //delete simulator lead
    public function deleteSimLead($id)
    {
        $lead = $this->getSimLead($id);
        if (!empty($lead)) {
            return $this->builderSimLeads->where('id', clrNum($id))->delete();
        }
        return false;
    }

    //delete multiple simulator leads
    public function deleteMultipleSimLeads($leadIds)
    {
        if (!empty($leadIds)) {
            foreach ($leadIds as $id) {
                $this->deleteSimLead($id);
            }
        }
    }
}