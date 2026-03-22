<?php

namespace App\Commands;

use App\Libraries\TrendsFetcher;
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
        $items = $fetcher->fetchDailyRss($geo);
        if (empty($items)) {
            CLI::write('No trends fetched.', 'yellow');
            return;
        }
        $model = new TrendItemModel();
        $now = date('Y-m-d H:i:s');
        $count = 0;
        foreach ($items as $item) {
            $title = $item['title'] ?? '';
            if (empty($title)) {
                continue;
            }
            $data = [
                'title' => $title,
                'title_hash' => md5(mb_strtolower($title)),
                'source_url' => $item['url'] ?? '',
                'source' => 'google_trends',
                'score' => 0,
                'lang_id' => $model->activeLang->id ?? null,
                'fetched_at' => $now,
            ];
            if ($model->upsertItem($data)) {
                $count++;
            }
        }
        CLI::write('Trends stored: ' . $count);
    }
}
