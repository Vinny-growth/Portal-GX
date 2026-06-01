<?php

namespace App\Libraries;

use Config\Globals;

class IndexNowClient
{
    private const API_ENDPOINT = 'https://api.indexnow.org/IndexNow';
    private bool $isEnabled = false;
    private string $apiKey = '';
    private string $host = '';

    public function __construct()
    {
        $generalSettings = Globals::$generalSettings;
        $this->isEnabled = !empty($generalSettings->indexnow_enabled) && $generalSettings->indexnow_enabled == 1;
        $this->apiKey = $generalSettings->indexnow_api_key ?? '';
        $this->host = parse_url(base_url(), PHP_URL_HOST) ?? '';

        if (empty($this->apiKey)) {
            $this->isEnabled = false;
        }
    }

    /**
     * Submit a single URL to IndexNow.
     */
    public function submitUrl(string $url): array
    {
        if (!$this->isEnabled) {
            return $this->errorResponse('IndexNow is disabled or API key not configured.');
        }

        return $this->submitUrls([$url]);
    }

    /**
     * Submit multiple URLs to IndexNow (batch).
     */
    public function submitUrls(array $urls): array
    {
        if (!$this->isEnabled) {
            return $this->errorResponse('IndexNow is disabled or API key not configured.');
        }

        if (empty($urls)) {
            return $this->errorResponse('No URLs provided.');
        }

        $keyLocation = base_url($this->apiKey . '.txt');

        $payload = json_encode([
            'host'        => $this->host,
            'key'         => $this->apiKey,
            'keyLocation' => $keyLocation,
            'urlList'     => array_values($urls),
        ]);

        return $this->sendRequest($payload);
    }

    /**
     * Sends the POST request to the IndexNow API.
     */
    private function sendRequest(string $payload): array
    {
        try {
            $ch = curl_init(self::API_ENDPOINT);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json; charset=utf-8',
                ],
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if (!empty($curlError)) {
                return $this->errorResponse('cURL Error: ' . $curlError);
            }

            if ($httpCode === 200 || $httpCode === 202) {
                return [
                    'success'   => true,
                    'message'   => 'URLs submitted successfully to IndexNow.',
                    'http_code' => $httpCode,
                ];
            }

            $messages = [
                400 => 'Bad Request: Invalid format.',
                403 => 'Forbidden: API key not valid or key file not found.',
                422 => 'Unprocessable: URLs do not match the host.',
                429 => 'Too Many Requests: Rate limited.',
            ];

            $msg = $messages[$httpCode] ?? "Unexpected response code: {$httpCode}";
            return $this->errorResponse($msg, $httpCode);
        } catch (\Exception $e) {
            return $this->errorResponse('Exception: ' . $e->getMessage());
        }
    }

    /**
     * Returns a standardized error response.
     */
    private function errorResponse(string $message, ?int $code = null): array
    {
        log_message('error', 'IndexNow: ' . $message);
        return [
            'success' => false,
            'message' => $message,
            'code'    => $code,
        ];
    }

    /**
     * Checks if IndexNow is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * Returns the current API key.
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Generates a new API key and saves the key file to the domain root.
     * Returns the generated key or null on failure.
     */
    public static function generateAndSaveKey(): ?string
    {
        $key = bin2hex(random_bytes(16));
        $keyFilePath = FCPATH . $key . '.txt';

        if (file_put_contents($keyFilePath, $key) === false) {
            log_message('error', 'IndexNow: Failed to write key file at ' . $keyFilePath);
            return null;
        }

        return $key;
    }

    /**
     * Ensures the key file exists at the domain root.
     */
    public static function ensureKeyFile(string $apiKey): bool
    {
        if (empty($apiKey)) {
            return false;
        }

        $keyFilePath = FCPATH . $apiKey . '.txt';
        if (file_exists($keyFilePath)) {
            return true;
        }

        return file_put_contents($keyFilePath, $apiKey) !== false;
    }

    /**
     * Removes old key file from the domain root.
     */
    public static function removeKeyFile(string $apiKey): void
    {
        if (!empty($apiKey)) {
            $keyFilePath = FCPATH . $apiKey . '.txt';
            if (file_exists($keyFilePath)) {
                @unlink($keyFilePath);
            }
        }
    }
}
