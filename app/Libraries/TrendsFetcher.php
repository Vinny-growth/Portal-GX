<?php

namespace App\Libraries;

class TrendsFetcher
{
    public function fetchDailyRss(string $geo = 'BR'): ?array
    {
        $url = 'https://trends.google.com/trends/trendingsearches/daily/rss?geo=' . urlencode($geo);
        $xml = $this->httpGet($url);
        if (empty($xml)) {
            return null;
        }
        $data = @simplexml_load_string($xml);
        if ($data === false || empty($data->channel->item)) {
            return null;
        }
        $items = [];
        foreach ($data->channel->item as $item) {
            $title = trim((string) $item->title);
            if (empty($title)) {
                continue;
            }
            $items[] = [
                'title' => $title,
                'url' => trim((string) $item->link),
                'traffic' => trim((string) ($item->children('ht', true)->approx_traffic ?? '')),
            ];
        }
        return $items;
    }

    protected function httpGet(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $resp = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http < 200 || $http >= 300) {
            return null;
        }
        return $resp ?: null;
    }
}
