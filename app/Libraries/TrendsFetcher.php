<?php

namespace App\Libraries;

use App\Models\TrendSourceHealthModel;

class TrendsFetcher
{
    protected ?TrendSourceHealthModel $health = null;
    protected int $maxAttempts = 3;
    protected int $timeoutSeconds = 20;
    protected int $connectTimeoutSeconds = 10;
    protected int $breakerThreshold = 3;
    protected int $breakerWindowMinutes = 30;

    public function __construct(?TrendSourceHealthModel $health = null)
    {
        try {
            $this->health = $health ?? new TrendSourceHealthModel();
        } catch (\Throwable $e) {
            // If the health table is not migrated yet, degrade gracefully — no telemetry, no breaker.
            $this->health = null;
        }
    }

    public function fetchDailyRss(string $geo = 'BR'): ?array
    {
        $url = 'https://trends.google.com/trending/rss?geo=' . urlencode($geo);
        return $this->parseGoogleTrendsRss($url);
    }

    public function fetchFinanceFeeds(): array
    {
        $feeds = [
            ['url' => 'https://pox.globo.com/rss/valor/', 'source' => 'valor_economico'],
            ['url' => 'https://www.infomoney.com.br/feed/', 'source' => 'infomoney'],
            ['url' => 'https://br.investing.com/rss/news.rss', 'source' => 'investing_br'],
            ['url' => 'https://www.bcb.gov.br/api/servico/sitebcb/noticiasbcb/ultimas?quantidade=10&formato=rss', 'source' => 'bacen'],
            ['url' => 'https://news.google.com/rss/search?q=economia+brasil+OR+dolar+OR+selic+OR+cambio+OR+credito+OR+investimento&hl=pt-BR&gl=BR&ceid=BR:pt-419', 'source' => 'google_news_finance'],
            ['url' => 'https://news.google.com/rss/search?q=mercado+financeiro+OR+ibovespa+OR+bolsa+OR+inflacao+OR+juros&hl=pt-BR&gl=BR&ceid=BR:pt-419', 'source' => 'google_news_market'],
        ];

        $allItems = [];
        foreach ($feeds as $feed) {
            $items = $this->parseGenericRss($feed['url'], $feed['source']);
            if (!empty($items)) {
                $allItems = array_merge($allItems, $items);
            }
        }
        return $allItems;
    }

    public function fetchAll(string $geo = 'BR'): array
    {
        $items = [];
        $trends = $this->fetchDailyRss($geo);
        if (!empty($trends)) {
            foreach ($trends as &$t) {
                $t['source'] = 'google_trends';
            }
            $items = array_merge($items, $trends);
        }
        $finance = $this->fetchFinanceFeeds();
        if (!empty($finance)) {
            $items = array_merge($items, $finance);
        }
        return $items;
    }

    protected function parseGoogleTrendsRss(string $url): ?array
    {
        $fetch = $this->httpGetWithRetry($url, 'google_trends');
        if ($fetch === null || empty($fetch['body'])) {
            return null;
        }
        $data = @simplexml_load_string($fetch['body']);
        if ($data === false || empty($data->channel->item)) {
            $this->logHealth('google_trends', $fetch['http_code'], $fetch['response_ms'], 0, $fetch['attempts'], false, 'malformed_xml');
            return null;
        }
        $items = [];
        foreach ($data->channel->item as $item) {
            $title = trim((string) $item->title);
            if (empty($title)) {
                continue;
            }
            $ht = $item->children('ht', true);
            $traffic = trim((string) ($ht->approx_traffic ?? ''));

            $newsContext = [];
            if (isset($ht->news_item)) {
                foreach ($ht->news_item as $news) {
                    $newsTitle = trim((string) ($news->news_item_title ?? ''));
                    if (!empty($newsTitle)) {
                        $newsContext[] = $newsTitle;
                    }
                }
            }

            $items[] = [
                'title' => $title,
                'url' => trim((string) $item->link),
                'traffic' => $traffic,
                'news_context' => $newsContext,
                'source' => 'google_trends',
            ];
        }
        $this->logHealth('google_trends', $fetch['http_code'], $fetch['response_ms'], count($items), $fetch['attempts'], true);
        return $items;
    }

    protected function parseGenericRss(string $url, string $source): ?array
    {
        $fetch = $this->httpGetWithRetry($url, $source);
        if ($fetch === null || empty($fetch['body'])) {
            return null;
        }
        $xml = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $fetch['body']);
        $data = @simplexml_load_string($xml);
        if ($data === false) {
            $this->logHealth($source, $fetch['http_code'], $fetch['response_ms'], 0, $fetch['attempts'], false, 'malformed_xml');
            return null;
        }

        $items = [];
        $seenInBatch = [];
        $channel = $data->channel ?? $data;
        $entries = $channel->item ?? $data->entry ?? [];

        foreach ($entries as $entry) {
            $title = trim((string) ($entry->title ?? ''));
            $link = trim((string) ($entry->link ?? ''));
            if (empty($link) && isset($entry->link['href'])) {
                $link = trim((string) $entry->link['href']);
            }
            if (empty($title) || mb_strlen($title) < 10) {
                continue;
            }
            $title = strip_tags(html_entity_decode($title, ENT_QUOTES, 'UTF-8'));
            $title = trim(preg_replace('/\s+/', ' ', $title));

            $hash = md5(mb_strtolower($title));
            if (isset($seenInBatch[$hash])) {
                continue;
            }
            $seenInBatch[$hash] = true;

            $items[] = [
                'title' => $title,
                'url' => $link,
                'traffic' => '',
                'news_context' => [],
                'source' => $source,
            ];
        }

        $logged = [
            'http_code'   => $fetch['http_code'],
            'response_ms' => $fetch['response_ms'],
            'attempts'    => $fetch['attempts'],
        ];
        $sliced = array_slice($items, 0, 15);
        $this->logHealth($source, $logged['http_code'], $logged['response_ms'], count($sliced), $logged['attempts'], true);
        return $sliced;
    }

    /**
     * Fetches URL with retry + circuit breaker.
     * Returns ['body' => string, 'http_code' => int, 'response_ms' => int, 'attempts' => int]
     * on success. Returns null on definitive failure (after logging failures via logHealth).
     * Successful HTTP responses are NOT logged here — the caller logs once after parsing
     * (so items_returned can be filled in).
     */
    protected function httpGetWithRetry(string $url, string $source): ?array
    {
        if ($this->health !== null && $this->health->isBreakerOpen($source, $this->breakerThreshold, $this->breakerWindowMinutes)) {
            log_message('warning', 'TrendsFetcher: circuit breaker OPEN for source=' . $source . ' — skipping');
            $this->logHealth($source, 0, 0, 0, 0, false, 'breaker_open');
            return null;
        }

        $attempt = 0;
        $lastError = null;
        $lastHttpCode = 0;
        $lastElapsedMs = 0;

        while ($attempt < $this->maxAttempts) {
            $attempt++;
            $startMs = (int) (microtime(true) * 1000);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeoutSeconds);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeoutSeconds);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; GXCapital/1.0)');
            $resp = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            $elapsedMs = (int) (microtime(true) * 1000) - $startMs;
            $lastHttpCode = $httpCode;
            $lastElapsedMs = $elapsedMs;

            $success = ($resp !== false && $resp !== '' && $httpCode >= 200 && $httpCode < 300);

            if ($success) {
                return [
                    'body'        => (string) $resp,
                    'http_code'   => $httpCode,
                    'response_ms' => $elapsedMs,
                    'attempts'    => $attempt,
                ];
            }

            $lastError = $curlError !== '' ? $curlError : ('http_' . $httpCode);
            $this->logHealth($source, $httpCode, $elapsedMs, 0, $attempt, false, $lastError);

            // Don't retry 4xx (except 408/429) — they won't succeed
            if ($httpCode >= 400 && $httpCode < 500 && !in_array($httpCode, [408, 429], true)) {
                log_message('error', 'TrendsFetcher: ' . $source . ' returned non-retryable HTTP ' . $httpCode);
                return null;
            }

            if ($attempt < $this->maxAttempts) {
                $sleepMs = (int) (pow(2, $attempt - 1) * 1000); // 1s, 2s, 4s
                usleep($sleepMs * 1000);
            }
        }

        log_message('error', 'TrendsFetcher: ' . $source . ' failed all ' . $this->maxAttempts . ' attempts (last_http=' . $lastHttpCode . ', last_error=' . $lastError . ')');
        return null;
    }

    protected function logHealth(string $source, int $httpCode, int $responseMs, int $itemsReturned, int $attempt, bool $success, ?string $error = null): void
    {
        if ($this->health === null) {
            return;
        }
        try {
            $this->health->log($source, $httpCode, $responseMs, $itemsReturned, $attempt, $success, $error);
        } catch (\Throwable $e) {
            log_message('warning', 'TrendsFetcher: failed to log health for ' . $source . ' — ' . $e->getMessage());
        }
    }
}
