<?php

namespace App\Libraries;

/**
 * Minimal Google Search Console (Search Analytics) client.
 *
 * Uses a Google Cloud SERVICE ACCOUNT (JSON key) — no OAuth dance, no composer
 * google/apiclient dependency. The service account e-mail must be added as a
 * (restricted/full) user of the Search Console property.
 *
 * Config (.env):
 *   GSC_SITE_URL            = "sc-domain:gx.capital"   (or "https://gx.capital/")
 *   GSC_SERVICE_ACCOUNT_JSON= "/path/to/service-account.json"
 *
 * Everything degrades gracefully: when not configured, isConfigured() returns
 * false and the panel shows an empty/instructional state instead of breaking.
 */
class GscClient
{
    private string $siteUrl;
    private string $jsonPath;
    private ?string $lastError = null;

    public function __construct()
    {
        $this->siteUrl  = trim((string) ($this->env('GSC_SITE_URL', '')));
        $this->jsonPath = trim((string) ($this->env('GSC_SERVICE_ACCOUNT_JSON', '')));
    }

    public function isConfigured(): bool
    {
        return $this->siteUrl !== '' && $this->jsonFileReadable();
    }

    /**
     * Checa o arquivo da service account sem nunca lançar erro.
     *
     * is_file()/is_readable() emitem E_WARNING quando o caminho está fora do
     * open_basedir — e em CI_ENVIRONMENT=development esse warning vira exceção
     * e derruba a página. O '@' suprime o aviso e tratamos como "não usável",
     * mantendo o degrade gracioso prometido (banner em vez de tela de erro).
     */
    private function jsonFileReadable(): bool
    {
        if ($this->jsonPath === '') {
            return false;
        }
        if (!@is_file($this->jsonPath) || !@is_readable($this->jsonPath)) {
            $this->lastError = 'JSON da service account não encontrado/legível (verifique o caminho e o open_basedir): ' . $this->jsonPath;
            return false;
        }
        return true;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function getSiteUrl(): string
    {
        return $this->siteUrl;
    }

    /**
     * Query Search Analytics by query string.
     *
     * @return array<int, array{query:string, clicks:int, impressions:int, ctr:float, position:float}>
     */
    public function queryByKeyword(string $startDate, string $endDate, int $rowLimit = 1000): array
    {
        if (!$this->isConfigured()) {
            $this->lastError = 'Google Search Console não configurado.';
            return [];
        }

        $token = $this->getAccessToken();
        if ($token === null) {
            return [];
        }

        $endpoint = 'https://searchconsole.googleapis.com/webmasters/v3/sites/'
            . rawurlencode($this->siteUrl) . '/searchAnalytics/query';

        $payload = [
            'startDate'  => $startDate,
            'endDate'    => $endDate,
            'dimensions' => ['query'],
            'rowLimit'   => $rowLimit,
            'dataState'  => 'all',
        ];

        $response = $this->httpJson($endpoint, $payload, $token);
        if ($response === null) {
            return [];
        }

        $out = [];
        foreach (($response['rows'] ?? []) as $row) {
            $out[] = [
                'query'       => (string) ($row['keys'][0] ?? ''),
                'clicks'      => (int) round($row['clicks'] ?? 0),
                'impressions' => (int) round($row['impressions'] ?? 0),
                'ctr'         => round(((float) ($row['ctr'] ?? 0)) * 100, 2),
                'position'    => round((float) ($row['position'] ?? 0), 2),
            ];
        }
        return $out;
    }

    /** Build a JWT, exchange it for an access token (cached for the request). */
    private function getAccessToken(): ?string
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }

        $raw = @file_get_contents($this->jsonPath);
        $creds = $raw ? json_decode($raw, true) : null;
        if (!is_array($creds) || empty($creds['client_email']) || empty($creds['private_key'])) {
            $this->lastError = 'JSON da service account inválido ou ilegível.';
            return null;
        }

        $now = time();
        $tokenUri = $creds['token_uri'] ?? 'https://oauth2.googleapis.com/token';
        $header = $this->b64(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claim  = $this->b64(json_encode([
            'iss'   => $creds['client_email'],
            'scope' => 'https://www.googleapis.com/auth/webmasters.readonly',
            'aud'   => $tokenUri,
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        $signature = '';
        $ok = openssl_sign($header . '.' . $claim, $signature, $creds['private_key'], 'sha256WithRSAEncryption');
        if (!$ok) {
            $this->lastError = 'Falha ao assinar o JWT (chave privada inválida).';
            return null;
        }
        $jwt = $header . '.' . $claim . '.' . $this->b64($signature);

        $tokenResponse = $this->httpForm($tokenUri, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        if (!is_array($tokenResponse) || empty($tokenResponse['access_token'])) {
            $this->lastError = 'Não foi possível obter o access token do Google.';
            return null;
        }

        return $cached = (string) $tokenResponse['access_token'];
    }

    private function httpJson(string $url, array $payload, string $token): ?array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_CONNECTTIMEOUT => 8,
        ]);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            $this->lastError = 'Erro de conexão com o Search Console: ' . $err;
            return null;
        }
        $decoded = json_decode($body, true);
        if ($code < 200 || $code >= 300) {
            $this->lastError = 'Search Console retornou HTTP ' . $code . ': '
                . ($decoded['error']['message'] ?? substr((string) $body, 0, 200));
            return null;
        }
        return is_array($decoded) ? $decoded : null;
    }

    private function httpForm(string $url, array $fields): ?array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($fields),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_CONNECTTIMEOUT => 8,
        ]);
        $body = curl_exec($ch);
        curl_close($ch);
        if ($body === false) {
            $this->lastError = 'Erro ao trocar o JWT por access token.';
            return null;
        }
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : null;
    }

    private function b64(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function env(string $key, string $default = ''): string
    {
        $value = getenv($key);
        if ($value === false || $value === null) {
            $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
        }
        return is_string($value) ? trim($value) : $default;
    }
}
