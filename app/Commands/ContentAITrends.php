<?php

namespace App\Commands;

use App\Libraries\ContentAIService;
use App\Libraries\TrendNormalizer;
use App\Libraries\TrendsFetcher;
use App\Models\ContentAISettingsModel;
use App\Models\TrendItemModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ContentAITrends extends BaseCommand
{
    protected $group = 'ContentAI';
    protected $name = 'content:trends';
    protected $description = 'Fetch daily trends and store in trend_items.';

    public function run(array $params)
    {
        $geo = !empty($params[0]) ? strtoupper($params[0]) : 'BR';
        $fetcher = new TrendsFetcher();
        $items = $fetcher->fetchAll($geo);
        if (empty($items)) {
            CLI::write('No trends fetched.', 'yellow');
            return;
        }
        $model = new TrendItemModel();
        $service = new ContentAIService();
        $settingsModel = new ContentAISettingsModel();
        $settings = $settingsModel->getSettings();
        $now = date('Y-m-d H:i:s');
        $count = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $title = $item['title'] ?? '';
            if (empty($title)) {
                continue;
            }
            $source = $item['source'] ?? 'google_trends';

            // ALL sources pass through the relevance filter — 100% on-scope
            if (!$service->isTrendRelevant($title, $settings)) {
                $skipped++;
                continue;
            }

            $traffic = $item['traffic'] ?? '';
            $score = 0;
            if (!empty($traffic)) {
                $score = (int) preg_replace('/[^0-9]/', '', $traffic);
            }
            $data = [
                'title' => $title,
                'title_hash' => md5(mb_strtolower($title)),
                'semantic_hash' => TrendNormalizer::semanticHash($title),
                'source_url' => $item['url'] ?? '',
                'source' => $source,
                'source_authority' => TrendNormalizer::sourceAuthority($source),
                'score' => $score,
                'lang_id' => $model->activeLang->id ?? null,
                'fetched_at' => $now,
            ];
            if ($model->upsertItem($data)) {
                $count++;
            }
        }
        CLI::write('Trends stored: ' . $count . ' | Skipped (irrelevant): ' . $skipped);
    }
}
