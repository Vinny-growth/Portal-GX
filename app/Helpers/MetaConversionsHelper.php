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
    public function sendEvent($eventName, $eventData = [], $userData = [])
    {
        if (!$this->isEventEnabled($eventName)) {
            return false;
        }
        
        $url = "https://graph.facebook.com/v19.0/{$this->metaApiConfig['pixel_id']}/events";
        
        // Dados do usuário (importantes para matching)
        $defaultUserData = $this->getUserData();
        $userData = array_merge($defaultUserData, $userData);
        
        // Dados do evento
        $eventTime = time();
        
        // Validar se o timestamp está dentro do limite (7 dias)
        $maxAge = 7 * 24 * 60 * 60; // 7 dias em segundos
        if ($eventTime < (time() - $maxAge)) {
            error_log("Meta Conversions API - Event time too old: " . date('Y-m-d H:i:s', $eventTime));
            return false;
        }
        
        $eventSourceUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
        
        $event = [
            'event_name' => $eventName,
            'event_time' => $eventTime,
            'event_source_url' => $eventSourceUrl,
            'user_data' => $userData,
            'custom_data' => $eventData,
            'action_source' => 'website'
        ];
        
        // Adicionar evento único ID para deduplicação
        $event['event_id'] = md5($eventName . $eventTime . serialize($userData));
        
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
        
        // FBC (Facebook Click ID) e FBP (Facebook Browser ID) do cookie
        if (!empty($_COOKIE['_fbc'])) {
            $userData['fbc'] = $_COOKIE['_fbc'];
        }
        
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
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            error_log("Meta Conversions API - cURL Error: " . $error);
            return false;
        }
        
        $decodedResponse = json_decode($response, true);
        
        if ($httpCode !== 200) {
            error_log("Meta Conversions API - HTTP Error {$httpCode}: " . $response);
            return false;
        }
        
        return $decodedResponse;
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
    
    public function trackLead($email = null, $phone = null, $firstName = null, $lastName = null, $customData = [])
    {
        $userData = [];
        
        if ($email) {
            $userData['em'] = hash('sha256', strtolower(trim($email)));
        }
        
        if ($phone) {
            // Remover caracteres não numéricos e adicionar código do país se necessário
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            // Se não tiver código do país, assumir Brasil (+55)
            if (strlen($cleanPhone) === 11 && substr($cleanPhone, 0, 1) !== '5') {
                $cleanPhone = '55' . $cleanPhone;
            }
            $userData['ph'] = hash('sha256', $cleanPhone);
        }
        
        if ($firstName) {
            $userData['fn'] = hash('sha256', strtolower(trim($firstName)));
        }
        
        if ($lastName) {
            $userData['ln'] = hash('sha256', strtolower(trim($lastName)));
        }
        
        return $this->sendEvent('Lead', $customData, $userData);
    }
    
    public function trackCompleteRegistration($email = null, $customData = [])
    {
        $userData = [];
        
        if ($email) {
            $userData['em'] = hash('sha256', strtolower(trim($email)));
        }
        
        return $this->sendEvent('CompleteRegistration', $customData, $userData);
    }
    
    public function trackContact($email = null, $customData = [])
    {
        $userData = [];
        
        if ($email) {
            $userData['em'] = hash('sha256', strtolower(trim($email)));
        }
        
        return $this->sendEvent('Contact', $customData, $userData);
    }
}