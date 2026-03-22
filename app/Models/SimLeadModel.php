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
        
        // Enviar evento de Lead para Meta Conversions API
        if ($result) {
            $customData = [
                'content_name' => 'Simulador de Risco Cambial',
                'content_category' => 'Lead Generation',
                'value' => 1,
                'currency' => 'BRL'
            ];
            
            // Separar nome completo em primeiro e último nome se necessário
            $firstName = null;
            $lastName = null;
            if (!empty($data['name'])) {
                $nameParts = explode(' ', trim($data['name']), 2);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : null;
            }
            
            // Enviar evento para Meta API
            trackMetaLead(
                $data['email'] ?? null,
                $data['phone'] ?? null,
                $firstName,
                $lastName,
                $customData
            );

            $this->sendLeadToCrm(array_merge($data, ['external_id' => $this->db->insertID()]));
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
        $endpoint = getenv('CRM_LEAD_ENDPOINT') ?: '';
        $apiKey = getenv('CRM_LEAD_API_KEY') ?: '';
        if ($endpoint === '' || $apiKey === '') {
            return false;
        }

        $utmTerm = $data['utm_term'] ?? null;
        $utmContent = $data['utm_content'] ?? null;
        $referrer = $data['referrer'] ?? null;
        $landingPage = $data['landing_page'] ?? null;
        $sourceSystem = $data['source_system'] ?? (getenv('CRM_LEAD_SOURCE_SYSTEM') ?: 'site-gx-php');
        $status = getenv('CRM_LEAD_STATUS') ?: '';
        $assignedTo = getenv('CRM_LEAD_ASSIGNED_TO') ?: '';
        $origin = $data['origem'] ?? $data['origin'] ?? (getenv('CRM_LEAD_ORIGIN') ?: '');
        if ($origin === '' && !empty($this->request)) {
            $origin = 'Site GX Capital - ' . ($this->request->getUri()->getPath() ?: '/');
        }
        if ($utmTerm === null && !empty($this->request)) {
            $utmTerm = $this->request->getGet('utm_term');
        }
        if ($utmContent === null && !empty($this->request)) {
            $utmContent = $this->request->getGet('utm_content');
        }
        if ($referrer === null && !empty($this->request)) {
            $referrer = $this->request->getServer('HTTP_REFERER');
        }
        if ($landingPage === null && !empty($this->request)) {
            $landingPage = (string) $this->request->getUri();
        }

        $payload = [
            'nome' => $data['nome'] ?? $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'telefone' => $data['telefone'] ?? $data['phone'] ?? null,
            'empresa' => $data['empresa'] ?? $data['company'] ?? null,
            'cargo' => $data['cargo'] ?? $data['position'] ?? null,
            'mensagem' => $data['mensagem'] ?? $data['message'] ?? null,
            'observacoes' => $data['observacoes'] ?? $data['observations'] ?? $data['notes'] ?? null,
            'origem' => $origin,
            'utm_source' => $data['utm_source'] ?? null,
            'utm_medium' => $data['utm_medium'] ?? null,
            'utm_campaign' => $data['utm_campaign'] ?? null,
            'utm_term' => $utmTerm,
            'utm_content' => $utmContent,
            'referrer' => $referrer,
            'landing_page' => $landingPage,
            'source_system' => $sourceSystem,
            'external_id' => $data['external_id'] ?? null,
        ];
        if ($status !== '') {
            $payload['status'] = $status;
        }
        if ($assignedTo !== '') {
            $payload['assigned_to'] = $assignedTo;
        }

        $timeout = (int) (getenv('CRM_LEAD_TIMEOUT') ?: 10);
        if ($timeout < 3) {
            $timeout = 3;
        }

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-api-key: ' . $apiKey
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 5
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            log_message('error', 'CRM lead capture error: ' . $curlErr);
            return false;
        }

        $decoded = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300 && is_array($decoded) && !empty($decoded['success'])) {
            return true;
        }

        log_message('error', 'CRM lead capture failed: HTTP ' . $httpCode . ' Response: ' . $response);
        return false;
    }
}
