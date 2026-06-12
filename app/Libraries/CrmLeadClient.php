<?php

namespace App\Libraries;

class CrmLeadClient
{
    private $request;

    public function __construct($request = null)
    {
        $this->request = $request ?? \Config\Services::request();
    }

    public function send(array $data): bool
    {
        $endpoint = $this->getEnv('CRM_LEAD_ENDPOINT');
        $apiKey = $this->getEnv('CRM_LEAD_API_KEY');
        if ($endpoint === '' || $apiKey === '') {
            return false;
        }

        $referrer = $data['referrer'] ?? $this->getServerValue('HTTP_REFERER');
        $landingPage = $data['landing_page'] ?? $this->getRequestUri();
        $utmSource = $data['utm_source'] ?? $this->getRequestQueryValue('utm_source');
        $utmMedium = $data['utm_medium'] ?? $this->getRequestQueryValue('utm_medium');
        $utmCampaign = $data['utm_campaign'] ?? $this->getRequestQueryValue('utm_campaign');
        $utmTerm = $data['utm_term'] ?? $this->getRequestQueryValue('utm_term');
        $utmContent = $data['utm_content'] ?? $this->getRequestQueryValue('utm_content');

        if ($referrer !== null && $referrer !== '') {
            $referrerQuery = [];
            $query = (string) parse_url($referrer, PHP_URL_QUERY);
            if ($query !== '') {
                parse_str($query, $referrerQuery);
            }
            $utmSource = $utmSource ?: ($referrerQuery['utm_source'] ?? null);
            $utmMedium = $utmMedium ?: ($referrerQuery['utm_medium'] ?? null);
            $utmCampaign = $utmCampaign ?: ($referrerQuery['utm_campaign'] ?? null);
            $utmTerm = $utmTerm ?: ($referrerQuery['utm_term'] ?? null);
            $utmContent = $utmContent ?: ($referrerQuery['utm_content'] ?? null);
        }

        $origin = $data['origem'] ?? $data['origin'] ?? $this->getEnv('CRM_LEAD_ORIGIN');
        if (($origin === null || $origin === '') && !empty($landingPage)) {
            $path = (string) parse_url($landingPage, PHP_URL_PATH);
            $origin = 'Site GX Capital - ' . ($path !== '' ? $path : '/');
        }
        if (($origin === null || $origin === '') && !empty($referrer)) {
            $path = (string) parse_url($referrer, PHP_URL_PATH);
            $origin = 'Site GX Capital - ' . ($path !== '' ? $path : '/');
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
            'utm_source' => $utmSource,
            'utm_medium' => $utmMedium,
            'utm_campaign' => $utmCampaign,
            'utm_term' => $utmTerm,
            'utm_content' => $utmContent,
            'referrer' => $referrer,
            'landing_page' => $landingPage,
            'source_system' => $data['source_system'] ?? $this->getEnv('CRM_LEAD_SOURCE_SYSTEM', 'site-gx-php'),
            'external_id' => $data['external_id'] ?? null,
        ];

        // Dossiê estruturado da simulação (quando houver) — o corretor recebe
        // capital, prêmio, reserva projetada etc. sem reabrir a planilha.
        if (!empty($data['sim_data'])) {
            $simDecoded = is_array($data['sim_data'])
                ? $data['sim_data']
                : json_decode((string) $data['sim_data'], true);
            if (is_array($simDecoded)) {
                $payload['dados_simulacao'] = $simDecoded;
            }
        }

        $status = $data['status'] ?? $this->getEnv('CRM_LEAD_STATUS');
        if ($status !== '') {
            $payload['status'] = $status;
        }

        $assignedTo = $data['assigned_to'] ?? $this->getEnv('CRM_LEAD_ASSIGNED_TO');
        if ($assignedTo !== '') {
            $payload['assigned_to'] = $assignedTo;
        }

        $timeout = (int) $this->getEnv('CRM_LEAD_TIMEOUT', '10');
        if ($timeout < 3) {
            $timeout = 3;
        }

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-api-key: ' . $apiKey,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            log_message('error', 'CRM lead capture error: {error}', ['error' => $curlErr]);
            return false;
        }

        $decoded = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300) {
            if (!is_array($decoded)) {
                return true;
            }
            if (array_key_exists('success', $decoded)) {
                return (bool) $decoded['success'];
            }
            if (array_key_exists('ok', $decoded)) {
                return (bool) $decoded['ok'];
            }
            if (!empty($decoded['error'])) {
                log_message('error', 'CRM lead capture failed: {response}', ['response' => $response]);
                return false;
            }
            return true;
        }

        log_message('error', 'CRM lead capture failed: HTTP {code} Response: {response}', [
            'code' => $httpCode,
            'response' => $response,
        ]);

        return false;
    }

    private function getEnv(string $key, string $default = ''): string
    {
        $value = getenv($key);
        if ($value === false || $value === null) {
            return $default;
        }

        return is_string($value) ? trim($value) : $default;
    }

    private function getRequestUri(): ?string
    {
        if (!is_object($this->request) || !method_exists($this->request, 'getUri')) {
            return null;
        }

        return (string) $this->request->getUri();
    }

    private function getRequestQueryValue(string $key): ?string
    {
        if (!is_object($this->request) || !method_exists($this->request, 'getGet')) {
            return null;
        }

        $value = $this->request->getGet($key);
        if ($value === null || $value === '') {
            return null;
        }

        return trim((string) $value);
    }

    private function getServerValue(string $key): ?string
    {
        if (!is_object($this->request) || !method_exists($this->request, 'getServer')) {
            return null;
        }

        $value = $this->request->getServer($key);
        if ($value === null || $value === '') {
            return null;
        }

        return trim((string) $value);
    }
}
