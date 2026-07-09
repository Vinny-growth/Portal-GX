<?php

namespace App\Controllers;

use App\Libraries\LeadPhoneFormatter;
use App\Libraries\QuotationEngine;
use App\Libraries\QuotationGate;
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

    /**
     * Prévia da cotação — payload SEGURO (sem R$). Não grava nada.
     * Alimenta o gráfico de break-even com a curva indexada (base 100).
     */
    public function quotationPreview()
    {
        if (($resp = $this->applyCors()) !== null) {
            return $resp;
        }
        try {
            $input   = QuotationGate::parseInput($this->request->getPost());
            $preview = (new QuotationEngine())->preview($input);

            return $this->response->setJSON([
                'status'  => 'success',
                'result'  => 1,
                'preview' => $preview,
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error', 'result' => 0, 'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'API quotationPreview: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error', 'result' => 0, 'message' => 'Erro ao calcular a prévia.',
            ]);
        }
    }

    /**
     * Desbloqueio — grava o lead e SÓ ENTÃO devolve o dossiê em R$.
     * Reusa SimLeadModel::addSimLead (dedup + deferAfterResponse -> CRM + Meta).
     */
    public function quotationUnlock()
    {
        if (($resp = $this->applyCors()) !== null) {
            return $resp;
        }

        $post  = $this->request->getPost();
        $name  = trim((string) ($post['name'] ?? ''));
        $email = trim((string) ($post['email'] ?? ''));
        $phone = trim((string) ($post['phone'] ?? ''));

        if ($name === '' || $email === '' || $phone === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error', 'result' => 0,
                'message' => 'Os campos nome, email e telefone são obrigatórios',
            ]);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error', 'result' => 0, 'message' => 'O email informado é inválido',
            ]);
        }
        // Consentimento explícito de contato (LGPD + evita abordagem fria "a frio").
        $consent = !empty($post['consent']);
        if (!$consent) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error', 'result' => 0,
                'message' => 'É necessário autorizar o contato para receber o relatório.',
            ]);
        }

        // Mesma normalização de telefone do saveSimulatorLead.
        $phoneCountry = strtoupper(trim((string) ($post['phone_country'] ?? '')));
        if ($phoneCountry !== '') {
            if (!in_array($phoneCountry, LeadPhoneFormatter::getCountryCodes(), true)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error', 'result' => 0, 'message' => 'País do telefone inválido',
                ]);
            }
            $normalized = LeadPhoneFormatter::toInternational($phoneCountry, $phone);
            if ($normalized === null) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error', 'result' => 0, 'message' => 'Telefone inválido para o país selecionado',
                ]);
            }
            $phone = $normalized;
        }

        try {
            $input   = QuotationGate::parseInput($post);
            $dossier = (new QuotationEngine())->quote($input);

            $leadData = QuotationGate::buildLeadData($input, $dossier, [
                'name'          => $name,
                'email'         => $email,
                'phone'         => $phone,
                'phone_country' => $phoneCountry ?: null,
            ], $post);
            // Marca o consentimento p/ o corretor (o dossiê em R$ segue em sim_data, só interno).
            $leadData['observations'] = trim(($leadData['observations'] ?? '') . ' [Contato autorizado pelo lead ao solicitar o relatório.]');

            $ok = (new SimLeadModel())->addSimLead($leadData);
            if (!$ok) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error', 'result' => 0, 'message' => 'Não foi possível registrar sua solicitação agora.',
                ]);
            }

            // O lead foi gravado (dossiê em R$ persistido p/ o especialista). NENHUM valor em
            // R$ é devolvido ao browser — o relatório é entregue pelo especialista no WhatsApp.
            return $this->response->setJSON([
                'status' => 'success',
                'result' => 1,
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error', 'result' => 0, 'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'API quotationUnlock: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error', 'result' => 0, 'message' => 'Erro ao gerar o relatório.',
            ]);
        }
    }

    /** CORS + preflight compartilhado (espelha o bloco do saveSimulatorLead). */
    private function applyCors()
    {
        $origin = $this->request->getHeaderLine('Origin');
        $allowed = $this->getAllowedOrigins();
        if ($origin !== '' && !in_array($origin, $allowed, true)) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error', 'result' => 0, 'message' => 'Origem não permitida',
            ]);
        }
        if ($origin !== '') {
            $this->response->setHeader('Access-Control-Allow-Origin', $origin);
            $this->response->setHeader('Vary', 'Origin');
        }
        $this->response->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200);
        }
        return null;
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
