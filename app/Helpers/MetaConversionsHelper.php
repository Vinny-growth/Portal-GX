<?php

namespace App\Helpers;

use App\Models\SettingsModel;

class MetaConversionsHelper
{
    private $settings;
    private $metaApiConfig;
    
    public function __construct()
    {
        $settingsModel = new SettingsModel();
        $this->settings = $settingsModel->getGeneralSettings();
        
        // Decodificar configurações da Meta API
        $this->metaApiConfig = [];
        if (!empty($this->settings->meta_conversions_api)) {
            $this->metaApiConfig = json_decode($this->settings->meta_conversions_api, true);
        }
    }
    
    /**
     * Verifica se a Meta Conversions API está ativa
     */
    public function isEnabled()
    {
        return !empty($this->metaApiConfig['api_enabled']) && 
               !empty($this->metaApiConfig['pixel_id']) && 
               !empty($this->metaApiConfig['access_token']);
    }
    
    /**
     * Verifica se um evento específico está ativo para tracking
     */
    public function isEventEnabled($eventName)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        
        $trackEvents = $this->metaApiConfig['track_events'] ?? [];
        return in_array($eventName, $trackEvents);
    }
    
    /**
     * Envia um evento para a Meta Conversions API
     */
    public function sendEvent($eventName, $eventData = [], $userData = [], $clientEventId = null)
    {
        if (!$this->isEventEnabled($eventName)) {
            return false;
        }

        $url = "https://graph.facebook.com/v21.0/{$this->metaApiConfig['pixel_id']}/events";

        // Dados do usuário (importantes para matching)
        $defaultUserData = $this->getUserData();
        $userData = array_merge($defaultUserData, $userData);

        // Dados do evento
        $eventTime = !empty($eventData['_event_time']) ? (int) $eventData['_event_time'] : time();
        unset($eventData['_event_time']);

        // event_source_url: prioriza override explícito, depois HTTP_REFERER, depois base_url
        $eventSourceUrl = $eventData['_event_source_url']
            ?? ($_SERVER['HTTP_REFERER'] ?? base_url());
        unset($eventData['_event_source_url']);

        $event = [
            'event_name' => $eventName,
            'event_time' => $eventTime,
            'event_source_url' => $eventSourceUrl,
            'user_data' => $userData,
            'custom_data' => $eventData,
            'action_source' => 'website'
        ];

        // Usar event_id do client-side para deduplicação, ou gerar um único estável
        $event['event_id'] = !empty($clientEventId)
            ? (string) $clientEventId
            : 'srv_' . bin2hex(random_bytes(12));
        
        $data = [
            'data' => [$event],
            'access_token' => $this->metaApiConfig['access_token']
        ];
        
        // Adicionar test event code se em modo de teste
        if (!empty($this->metaApiConfig['test_mode']) && !empty($this->metaApiConfig['test_event_code'])) {
            $data['test_event_code'] = $this->metaApiConfig['test_event_code'];
        }
        
        return $this->sendApiRequest($url, $data);
    }
    
    /**
     * Coleta dados do usuário para matching
     */
    private function getUserData()
    {
        $userData = [];

        // IP do cliente
        $clientIp = $this->getClientIp();
        if ($clientIp) {
            $userData['client_ip_address'] = $clientIp;
        }

        // User Agent
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $userData['client_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        }

        // FBC (Facebook Click ID): cookie _fbc OU fallback construído do fbclid da URL
        if (!empty($_COOKIE['_fbc'])) {
            $userData['fbc'] = $_COOKIE['_fbc'];
        } elseif (!empty($_GET['fbclid'])) {
            // Formato oficial Meta: fb.1.<unixMillis>.<fbclid>
            $userData['fbc'] = 'fb.1.' . (int) round(microtime(true) * 1000) . '.' . preg_replace('/[^A-Za-z0-9_-]/', '', (string) $_GET['fbclid']);
        }

        // FBP (Facebook Browser ID) do cookie
        if (!empty($_COOKIE['_fbp'])) {
            $userData['fbp'] = $_COOKIE['_fbp'];
        }

        return $userData;
    }
    
    /**
     * Obtém o IP real do cliente
     */
    private function getClientIp()
    {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }
    
    /**
     * Envia requisição para a API do Facebook
     */
    private function sendApiRequest($url, $data)
    {
        $payload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $maxAttempts = 3; // 1 tentativa inicial + 2 retries
        $lastError = null;
        $lastHttpCode = 0;
        $lastResponse = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 15,
            ]);

            $response = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            $lastResponse = $response;
            $lastHttpCode = $httpCode;
            $lastError = $error;

            // Sucesso
            if ($response !== false && $httpCode >= 200 && $httpCode < 300) {
                $decoded = json_decode($response, true);
                if (function_exists('log_message')) {
                    $trace = is_array($decoded) ? ($decoded['fbtrace_id'] ?? '-') : '-';
                    $events = is_array($decoded) ? ($decoded['events_received'] ?? '?') : '?';
                    log_message('info', 'Meta CAPI ok: events_received={events} fbtrace_id={trace}', [
                        'events' => $events,
                        'trace'  => $trace,
                    ]);
                }
                return $decoded ?: true;
            }

            // 4xx (exceto 429): erro de payload/credencial → não adianta tentar de novo
            if ($response !== false && $httpCode >= 400 && $httpCode < 500 && $httpCode !== 429) {
                break;
            }

            // Backoff antes da próxima tentativa (200ms, 600ms)
            if ($attempt < $maxAttempts) {
                usleep(200000 * $attempt * $attempt);
            }
        }

        // Falha definitiva
        $logMsg = sprintf(
            'Meta CAPI fail: http=%d err=%s response=%s',
            $lastHttpCode,
            $lastError ?: '-',
            is_string($lastResponse) ? substr($lastResponse, 0, 800) : '-'
        );
        if (function_exists('log_message')) {
            log_message('error', $logMsg);
        } else {
            error_log($logMsg);
        }
        return false;
    }
    
    /**
     * Métodos de conveniência para eventos comuns
     */
    
    public function trackPageView($pageUrl = null)
    {
        $eventData = [];
        if ($pageUrl) {
            $eventData['content_name'] = $pageUrl;
        }
        
        return $this->sendEvent('PageView', $eventData);
    }
    
    public function trackLead($email = null, $phone = null, $firstName = null, $lastName = null, $customData = [], $clientEventId = null)
    {
        return $this->sendEvent(
            'Lead',
            $customData,
            $this->buildUserDataPayload($email, $phone, $firstName, $lastName),
            $clientEventId
        );
    }

    public function trackCompleteRegistration($email = null, $customData = [], $clientEventId = null, $phone = null, $firstName = null, $lastName = null)
    {
        return $this->sendEvent(
            'CompleteRegistration',
            $customData,
            $this->buildUserDataPayload($email, $phone, $firstName, $lastName),
            $clientEventId
        );
    }

    public function trackContact($email = null, $customData = [], $clientEventId = null, $phone = null, $firstName = null, $lastName = null)
    {
        return $this->sendEvent(
            'Contact',
            $customData,
            $this->buildUserDataPayload($email, $phone, $firstName, $lastName),
            $clientEventId
        );
    }

    /**
     * Constrói o user_data hasheado conforme exigências do Meta CAPI.
     * - email/firstName/lastName: lowercase + trim + sha256
     * - phone: apenas dígitos, com código de país, sha256
     */
    private function buildUserDataPayload($email = null, $phone = null, $firstName = null, $lastName = null)
    {
        $userData = [];

        if ($email !== null && $email !== '') {
            $userData['em'] = hash('sha256', strtolower(trim((string) $email)));
        }

        if ($phone !== null && $phone !== '') {
            $normalized = $this->normalizePhone((string) $phone);
            if ($normalized !== '') {
                $userData['ph'] = hash('sha256', $normalized);
            }
        }

        if ($firstName !== null && $firstName !== '') {
            $userData['fn'] = hash('sha256', strtolower(trim((string) $firstName)));
        }

        if ($lastName !== null && $lastName !== '') {
            $userData['ln'] = hash('sha256', strtolower(trim((string) $lastName)));
        }

        return $userData;
    }

    /**
     * Normaliza telefones para o formato exigido pelo Meta CAPI:
     * apenas dígitos, com código do país (E.164 sem o "+").
     *
     * Comportamento:
     *  - Detecta sinal "+" original (independente de espaços/parênteses) → assume já internacional
     *  - 10 ou 11 dígitos sem "+" → assume Brasil (prefixa 55)
     *  - 12+ dígitos sem "+" → assume já internacional (mantém)
     *  - Strings com menos de 10 dígitos → inválido (string vazia)
     */
    private function normalizePhone(string $phone): string
    {
        $trimmed = trim($phone);
        $hasPlus = strpos($trimmed, '+') === 0;
        $digits = preg_replace('/\D+/', '', $trimmed);

        if ($digits === '' || $digits === null) {
            return '';
        }

        $len = strlen($digits);

        if ($len < 10 || $len > 15) {
            return '';
        }

        // Já internacional (com "+" explícito ou 12+ dígitos)
        if ($hasPlus || $len >= 12) {
            return $digits;
        }

        // 10 ou 11 dígitos sem "+" → Brasil
        return '55' . $digits;
    }
}