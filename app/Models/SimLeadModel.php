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

        // Resolve os dados de origem do lead (origem + utm + landing_page + referrer)
        // para que fiquem GRAVADOS na tabela e visíveis no painel/dashboard, e não
        // apenas repassados ao CRM/Meta. Espelha a lógica do CrmLeadClient.
        $tracking = $this->resolveTracking($data);

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

            // Atribuição first-touch: preserva a origem da PRIMEIRA navegação.
            // Só preenche os campos de tracking se ainda estiverem vazios no lead.
            foreach ($tracking as $field => $value) {
                if (!empty($value) && empty($existing->{$field})) {
                    $updateData[$field] = $value;
                }
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
            'origem' => $tracking['origem'],
            'utm_source' => $tracking['utm_source'],
            'utm_medium' => $tracking['utm_medium'],
            'utm_campaign' => $tracking['utm_campaign'],
            'utm_term' => $tracking['utm_term'],
            'utm_content' => $tracking['utm_content'],
            'landing_page' => $tracking['landing_page'],
            'referrer' => $tracking['referrer'],
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

    /**
     * Normaliza os dados de origem do lead a partir do payload do simulador,
     * com fallback para os cabeçalhos da requisição (referer / URI / query utm).
     * Quando nenhuma origem explícita é informada, deriva uma a partir do
     * caminho da landing page / referrer — para que TODO lead tenha origem.
     *
     * @return array{origem:?string,utm_source:?string,utm_medium:?string,utm_campaign:?string,utm_term:?string,utm_content:?string,landing_page:?string,referrer:?string}
     */
    private function resolveTracking(array $data): array
    {
        $clean = static function ($value): ?string {
            if ($value === null) {
                return null;
            }
            $value = trim((string) $value);
            return $value === '' ? null : $value;
        };

        $referrer    = $clean($data['referrer'] ?? null) ?? $this->getServerValue('HTTP_REFERER');
        $landingPage = $clean($data['landing_page'] ?? null) ?? $this->getRequestUri();
        $utmSource   = $clean($data['utm_source'] ?? null) ?? $this->getQueryValue('utm_source');
        $utmMedium   = $clean($data['utm_medium'] ?? null) ?? $this->getQueryValue('utm_medium');
        $utmCampaign = $clean($data['utm_campaign'] ?? null) ?? $this->getQueryValue('utm_campaign');
        $utmTerm     = $clean($data['utm_term'] ?? null) ?? $this->getQueryValue('utm_term');
        $utmContent  = $clean($data['utm_content'] ?? null) ?? $this->getQueryValue('utm_content');

        // UTMs presentes na querystring do referrer (ex.: clique de anúncio).
        if (!empty($referrer)) {
            $query = (string) parse_url($referrer, PHP_URL_QUERY);
            if ($query !== '') {
                parse_str($query, $referrerQuery);
                $utmSource   = $utmSource   ?: ($clean($referrerQuery['utm_source'] ?? null));
                $utmMedium   = $utmMedium   ?: ($clean($referrerQuery['utm_medium'] ?? null));
                $utmCampaign = $utmCampaign ?: ($clean($referrerQuery['utm_campaign'] ?? null));
                $utmTerm     = $utmTerm     ?: ($clean($referrerQuery['utm_term'] ?? null));
                $utmContent  = $utmContent  ?: ($clean($referrerQuery['utm_content'] ?? null));
            }
        }

        $origem = $clean($data['origem'] ?? null)
            ?? $clean($data['origin'] ?? null)
            ?? $clean($data['lead_origin'] ?? null);

        // Rede de segurança: simuladores que mandam só "observations" no padrão
        // "Dados da Simulação <X> GX" passam a ter uma origem legível.
        if ($origem === null) {
            $obs = $clean($data['observations'] ?? null);
            if ($obs !== null && preg_match('/^Dados da Simula[çc][ãa]o (?:de )?(.+?) GX/u', $obs, $mm)) {
                $origem = 'Simulador de ' . trim($mm[1]);
            }
        }

        // Último fallback: deriva do caminho da landing page / referrer.
        if ($origem === null && !empty($landingPage)) {
            $path = (string) parse_url($landingPage, PHP_URL_PATH);
            $origem = 'Site GX Capital - ' . ($path !== '' ? $path : '/');
        }
        if ($origem === null && !empty($referrer)) {
            $path = (string) parse_url($referrer, PHP_URL_PATH);
            $origem = 'Site GX Capital - ' . ($path !== '' ? $path : '/');
        }

        return [
            'origem'       => $origem !== null ? mb_substr($origem, 0, 255) : null,
            'utm_source'   => $utmSource,
            'utm_medium'   => $utmMedium,
            'utm_campaign' => $utmCampaign,
            'utm_term'     => $utmTerm,
            'utm_content'  => $utmContent,
            'landing_page' => $landingPage,
            'referrer'     => $referrer,
        ];
    }

    private function getServerValue(string $key): ?string
    {
        if (!is_object($this->request) || !method_exists($this->request, 'getServer')) {
            return null;
        }
        $value = $this->request->getServer($key);
        return ($value === null || $value === '') ? null : trim((string) $value);
    }

    private function getRequestUri(): ?string
    {
        if (!is_object($this->request) || !method_exists($this->request, 'getUri')) {
            return null;
        }
        $uri = (string) $this->request->getUri();
        return $uri === '' ? null : $uri;
    }

    private function getQueryValue(string $key): ?string
    {
        if (!is_object($this->request) || !method_exists($this->request, 'getGet')) {
            return null;
        }
        $value = $this->request->getGet($key);
        return ($value === null || $value === '') ? null : trim((string) $value);
    }

    private function sendLeadToCrm(array $data): bool
    {
        return (new CrmLeadClient($this->request))->send($data);
    }
}
