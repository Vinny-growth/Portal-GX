<?php

namespace App\Controllers;

use App\Libraries\LeadPhoneFormatter;
use App\Models\SimLeadModel;
use CodeIgniter\API\ResponseTrait;

class ApiController extends BaseController
{
    use ResponseTrait;

    /**
     * Endpoint aberto para receber leads do simulador
     * Pode ser acessado via POST sem token CSRF
     */
    public function saveSimulatorLead()
    {
        // Log para depuração
        log_message('info', 'API: Requisição recebida em saveSimulatorLead');
        
        $origin = $this->request->getHeaderLine('Origin');
        $allowedOrigins = $this->getAllowedOrigins();
        if ($origin !== '' && !in_array($origin, $allowedOrigins, true)) {
            return $this->respond([
                'status' => 'error',
                'result' => 0,
                'message' => 'Origem não permitida'
            ], 403);
        }
        if ($origin !== '') {
            $this->response->setHeader('Access-Control-Allow-Origin', $origin);
            $this->response->setHeader('Vary', 'Origin');
        }
        $this->response->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
        
        // Se for uma requisição OPTIONS (preflight), retornar 200 OK
        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200);
        }
        
        // Obter dados do corpo da requisição
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $phone = trim((string) $this->request->getPost('phone'));
        $phoneCountryInput = trim((string) $this->request->getPost('phone_country'));
        $simData = $this->request->getPost('sim_data');
        $observations = $this->request->getPost('observations');
        $message = $this->request->getPost('message');
        $company = $this->request->getPost('company');
        $origin = $this->request->getPost('origem') ?: $this->request->getPost('origin') ?: $this->request->getPost('lead_origin');
        $landingPage = $this->request->getPost('landing_page');
        $utmSource = $this->request->getPost('utm_source') ?: $this->request->getGet('utm_source');
        $utmMedium = $this->request->getPost('utm_medium') ?: $this->request->getGet('utm_medium');
        $utmCampaign = $this->request->getPost('utm_campaign') ?: $this->request->getGet('utm_campaign');
        $utmTerm = $this->request->getPost('utm_term') ?: $this->request->getGet('utm_term');
        $utmContent = $this->request->getPost('utm_content') ?: $this->request->getGet('utm_content');
        $metaContentName = $this->request->getPost('meta_content_name') ?: $this->request->getPost('content_name');
        $metaContentCategory = $this->request->getPost('meta_content_category') ?: $this->request->getPost('content_category');
        $metaValue = $this->request->getPost('meta_value') ?: $this->request->getPost('value');
        $metaCurrency = $this->request->getPost('meta_currency') ?: $this->request->getPost('currency');
        $eventId = $this->request->getPost('event_id');

        if ($observations === null || $observations === '') {
            $observations = $message;
        }
        
        // Log dos dados recebidos
        log_message('info', 'API: Dados recebidos: ' . json_encode([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'has_simdata' => !empty($simData),
            'observations' => $observations
        ]));
        
        if ($phoneCountryInput !== '') {
            $countryCodes = LeadPhoneFormatter::getCountryCodes();
            $normalizedCountry = strtoupper($phoneCountryInput);
            if (!in_array($normalizedCountry, $countryCodes, true)) {
                return $this->respond([
                    'status' => 'error',
                    'result' => 0,
                    'message' => 'País do telefone inválido'
                ], 400);
            }

            $normalizedPhone = LeadPhoneFormatter::toInternational($normalizedCountry, $phone);
            if ($normalizedPhone === null) {
                return $this->respond([
                    'status' => 'error',
                    'result' => 0,
                    'message' => 'Telefone inválido para o país selecionado'
                ], 400);
            }

            $phone = $normalizedPhone;
        }

        // Validação básica
        if (empty($name) || empty($email) || empty($phone)) {
            log_message('error', 'API: Dados inválidos - Campos obrigatórios não preenchidos');
            return $this->respond([
                'status' => 'error',
                'result' => 0,
                'message' => 'Os campos nome, email e telefone são obrigatórios'
            ], 400);
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            log_message('error', 'API: Email inválido - ' . $email);
            return $this->respond([
                'status' => 'error',
                'result' => 0,
                'message' => 'O email informado é inválido'
            ], 400);
        }
        
        // Preparar dados para salvar
        $data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'sim_data' => $simData,
            'observations' => $observations,
            'message' => $message,
            'company' => $company,
            'origem' => $origin,
            'landing_page' => $landingPage,
            'utm_source' => $utmSource,
            'utm_medium' => $utmMedium,
            'utm_campaign' => $utmCampaign,
            'utm_term' => $utmTerm,
            'utm_content' => $utmContent,
            'meta_content_name' => $metaContentName,
            'meta_content_category' => $metaContentCategory,
            'meta_value' => $metaValue,
            'meta_currency' => $metaCurrency,
            'event_id' => $eventId,
        ];
        
        try {
            // Salvar no banco de dados
            $model = new SimLeadModel();
            $result = $model->addSimLead($data);
            
            if ($result) {
                log_message('info', 'API: Lead salvo com sucesso - ' . $email);
                
                // Retornar resposta JSON formatada corretamente
                $this->response->setHeader('Content-Type', 'application/json');
                $this->response->setStatusCode(200);
                return $this->response->setJSON([
                    'status' => 'success',
                    'result' => 1,
                    'message' => 'Lead salvo com sucesso'
                ]);
            } else {
                log_message('error', 'API: Erro ao salvar lead - ' . $email);
                
                // Retornar resposta JSON formatada corretamente
                $this->response->setHeader('Content-Type', 'application/json');
                $this->response->setStatusCode(500);
                return $this->response->setJSON([
                    'status' => 'error',
                    'result' => 0,
                    'message' => 'Erro ao salvar o lead no banco de dados'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'API: Exceção ao salvar lead - ' . $e->getMessage());
            
            // Retornar resposta JSON formatada corretamente
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->setStatusCode(500);
            return $this->response->setJSON([
                'status' => 'error',
                'result' => 0,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ]);
        }
    }

    private function getAllowedOrigins(): array
    {
        $origins = [];

        $baseOrigin = $this->normalizeOrigin(base_url());
        if ($baseOrigin !== '') {
            $origins[] = $baseOrigin;
        }

        $envOrigins = getenv('SIMULATOR_ALLOWED_ORIGINS') ?: '';
        if ($envOrigins !== '') {
            foreach (explode(',', $envOrigins) as $origin) {
                $origin = trim($origin);
                if ($origin !== '') {
                    $origins[] = $origin;
                }
            }
        }

        return array_values(array_unique($origins));
    }

    private function normalizeOrigin(string $url): string
    {
        $url = rtrim($url, '/');
        if ($url === '') {
            return '';
        }
        $parts = parse_url($url);
        if (empty($parts['scheme']) || empty($parts['host'])) {
            return '';
        }
        $origin = $parts['scheme'] . '://' . $parts['host'];
        if (!empty($parts['port'])) {
            $origin .= ':' . $parts['port'];
        }
        return $origin;
    }
}
