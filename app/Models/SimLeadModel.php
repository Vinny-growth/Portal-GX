<?php namespace App\Models;

use App\Libraries\CrmLeadClient;

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
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';
        $dedupMinutes = (int) (getenv('LEAD_DEDUP_MINUTES') ?: 60);
        if ($dedupMinutes < 1) {
            $dedupMinutes = 60;
        }

        $existing = null;
        if (!empty($email) || !empty($phone)) {
            $cutoff = date('Y-m-d H:i:s', time() - ($dedupMinutes * 60));
            $builder = $this->db->table('sim_leads');
            $builder->groupStart();
            if (!empty($email)) {
                $builder->where('email', $email);
            }
            if (!empty($phone)) {
                if (!empty($email)) {
                    $builder->orWhere('phone', $phone);
                } else {
                    $builder->where('phone', $phone);
                }
            }
            $builder->groupEnd();
            $builder->where('created_at >=', $cutoff);
            $existing = $builder->orderBy('id', 'DESC')->get(1)->getRow();
        }

        if (!empty($existing)) {
            $updateData = [];
            if (!empty($data['name'])) {
                $updateData['name'] = $data['name'];
            }
            if (!empty($email)) {
                $updateData['email'] = $email;
            }
            if (!empty($phone)) {
                $updateData['phone'] = $phone;
            }
            if (!empty($data['sim_data'])) {
                $updateData['sim_data'] = $data['sim_data'];
            }
            if (!empty($data['observations'])) {
                $updateData['observations'] = $data['observations'];
            }

            if (!empty($updateData)) {
                $updated = (bool) $this->builderSimLeads->where('id', $existing->id)->update($updateData);
                $this->sendLeadToCrm(array_merge($data, ['external_id' => $existing->id]));
                return $updated;
            }

            $this->sendLeadToCrm(array_merge($data, ['external_id' => $existing->id]));
            return true;
        }

        $insertData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'sim_data' => isset($data['sim_data']) ? $data['sim_data'] : NULL,
            'observations' => isset($data['observations']) ? $data['observations'] : NULL,
            'status' => 'new',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $this->builderSimLeads->insert($insertData);

        // Agendar envio para Meta API e CRM APÓS a resposta HTTP (non-blocking)
        if ($result) {
            $insertId = $this->db->insertID();
            $deferData = $data; // snapshot dos dados para o closure

            deferAfterResponse(function () use ($deferData, $insertId) {
                $customData = [
                    'content_name' => $deferData['meta_content_name'] ?? $deferData['content_name'] ?? 'Simulador de Risco Cambial',
                    'content_category' => $deferData['meta_content_category'] ?? $deferData['content_category'] ?? 'Lead Generation',
                    'value' => isset($deferData['meta_value']) ? (float)$deferData['meta_value'] : (isset($deferData['value']) ? (float)$deferData['value'] : 1),
                    'currency' => $deferData['meta_currency'] ?? $deferData['currency'] ?? 'BRL'
                ];

                $firstName = null;
                $lastName = null;
                if (!empty($deferData['name'])) {
                    $nameParts = explode(' ', trim($deferData['name']), 2);
                    $firstName = $nameParts[0];
                    $lastName = $nameParts[1] ?? null;
                }

                $clientEventId = $deferData['event_id'] ?? null;
                trackMetaLead(
                    $deferData['email'] ?? null,
                    $deferData['phone'] ?? null,
                    $firstName,
                    $lastName,
                    $customData,
                    $clientEventId
                );

                (new CrmLeadClient())->send(array_merge($deferData, ['external_id' => $insertId]));
            });
        }

        return $result;
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

    private function sendLeadToCrm(array $data): bool
    {
        return (new CrmLeadClient($this->request))->send($data);
    }
}
