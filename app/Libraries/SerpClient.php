<?php

namespace App\Libraries;

/**
 * Thin client for an openserp instance (https://github.com/karust/openserp).
 *
 * openserp is a self-hosted Go service that returns normalized SERP results.
 * We use it to find OUR domain's organic position for a target keyword —
 * useful for keywords we don't yet rank for in GSC, or competitor tracking.
 *
 * Config (.env):
 *   OPENSERP_ENABLED   = "true"
 *   OPENSERP_URL       = "http://127.0.0.1:7000"
 *   SEO_TARGET_DOMAIN  = "gx.capital"
 *
 * NOTE: openserp scrapes search engines and can be rate-limited / CAPTCHA-blocked
 * (HTTP 503). Use sparingly. Everything degrades gracefully when not configured.
 */
class SerpClient
{
    private bool $enabled;
    private string $baseUrl;
    private string $targetDomain;
    private string $engine;
    private string $proxyUrl;
    private ?string $lastError = null;

    public function __construct()
    {
        $this->enabled      = filter_var($this->env('OPENSERP_ENABLED', 'false'), FILTER_VALIDATE_BOOLEAN);
        $this->baseUrl      = rtrim((string) $this->env('OPENSERP_URL', ''), '/');
        $this->targetDomain = $this->normalizeDomain((string) $this->env('SEO_TARGET_DOMAIN', ''));
        $this->engine       = strtolower(trim((string) $this->env('OPENSERP_ENGINE', 'google'))) ?: 'google';
        // Optional per-request proxy (residential/mobile) — REQUIRED for reliable
        // scraping from a datacenter IP, which search engines captcha/block.
        $this->proxyUrl     = trim((string) $this->env('OPENSERP_PROXY_URL', ''));
    }

    public function isConfigured(): bool
    {
        return $this->enabled && $this->baseUrl !== '' && $this->targetDomain !== '';
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Find our domain's organic position for a keyword.
     *
     * @return array{position: ?int, url_found: ?string}|null
     */
    public function findPosition(string $keyword, string $lang = 'pt', int $limit = 100): ?array
    {
        if (!$this->isConfigured()) {
            $this->lastError = 'openserp não configurado.';
            return null;
        }

        $url = $this->baseUrl . '/' . $this->engine . '/search?' . http_build_query([
            'text'  => $keyword,
            'lang'  => strtoupper($lang),
            'limit' => $limit,
        ]);

        $headers = ['Accept: application/json'];
        if ($this->proxyUrl !== '') {
            // openserp routes this request through the given proxy (CORS-allowed header).
            $headers[] = 'X-Proxy-URL: ' . $this->proxyUrl;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 45,
            CURLOPT_CONNECTTIMEOUT => 8,
        ]);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            $this->lastError = 'Erro de conexão com o openserp: ' . $err;
            return null;
        }
        if ($code === 503 || $code === 429) {
            $this->lastError = 'openserp bloqueado (captcha/limite, HTTP ' . $code . '). Configure um proxy em OPENSERP_PROXY_URL.';
            return null;
        }
        if ($code < 200 || $code >= 300) {
            $this->lastError = 'openserp retornou HTTP ' . $code . '.';
            return null;
        }

        $decoded = json_decode($body, true);
        if (!is_array($decoded)) {
            $this->lastError = 'Resposta inválida do openserp.';
            return null;
        }

        // openserp v2 wraps results: {query, meta, results:[...]}; older builds returned a flat array.
        $results = $decoded['results'] ?? (array_is_list($decoded) ? $decoded : []);
        $failed  = $decoded['meta']['engines_failed'] ?? [];
        if (empty($results)) {
            if (!empty($failed)) {
                $this->lastError = 'openserp: motor falhou (' . implode(',', (array) $failed) . ') — captcha/bloqueio. Configure um proxy.';
                return null;
            }
            return ['position' => null, 'url_found' => null]; // not in results (or empty SERP)
        }

        foreach ($results as $item) {
            $itemUrl = (string) ($item['url'] ?? $item['link'] ?? '');
            if ($itemUrl === '') {
                continue;
            }
            if (strpos($this->normalizeDomain($itemUrl), $this->targetDomain) !== false) {
                $rank = $item['rank'] ?? $item['position'] ?? null;
                return [
                    'position'  => $rank !== null ? (int) $rank : null,
                    'url_found' => $itemUrl,
                ];
            }
        }

        // Not found in the top results — record as "beyond limit" (null position).
        return ['position' => null, 'url_found' => null];
    }

    private function normalizeDomain(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('#^https?://#', '', $value);
        $value = preg_replace('#^www\.#', '', $value);
        return explode('/', $value)[0] ?? $value;
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
