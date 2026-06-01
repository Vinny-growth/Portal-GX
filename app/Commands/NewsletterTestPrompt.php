<?php

namespace App\Commands;

use App\Libraries\NewsletterAIService;
use App\Models\NewsletterEditorialLineModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class NewsletterTestPrompt extends BaseCommand
{
    protected $group = 'Newsletter';
    protected $name = 'newsletter:test-prompt';
    protected $description = 'Dry-run: builds the prompt without calling OpenAI. Usage: php spark newsletter:test-prompt [lineId]';

    public function run(array $params)
    {
        $service = new NewsletterAIService();
        $lineModel = new NewsletterEditorialLineModel();
        $db = \Config\Database::connect();

        $lineId = isset($params[0]) ? (int) $params[0] : 0;
        $cleanupLine = false;

        if (!$lineId) {
            $catRow = $db->table('posts')->select('category_id')->where('status', 1)->groupBy('category_id')->orderBy('COUNT(*)', 'DESC', false)->limit(1)->get()->getRow();
            $catId = $catRow ? (int) $catRow->category_id : 1;
            CLI::write("No lineId given; using ephemeral line with category $catId");
            $lineId = $lineModel->createLine([
                'name' => '_dryrun_' . time(),
                'slug' => '_dryrun_' . time(),
                'description' => 'ephemeral test line',
                'category_ids' => json_encode([$catId]),
                'send_times' => json_encode(["08:00"]),
                'frequency' => 'daily',
                'posts_per_edition' => 5,
                'lookback_hours' => 24 * 30 * 12,
                'ai_auto_publish' => 0,
                'enabled' => 1,
            ]);
            $cleanupLine = true;
        }

        $line = $lineModel->getById($lineId);
        if (!$line) {
            CLI::write("Line $lineId not found", 'red');
            return;
        }

        $posts = $service->pickPosts($line);
        CLI::write('Posts picked: ' . count($posts));
        foreach ($posts as $p) {
            CLI::write('  - [' . $p->id . '] ' . mb_substr($p->title, 0, 60) . ' (cat=' . $p->category_id . ', views=' . ($p->pageviews ?? 0) . ')');
        }

        if (empty($posts)) {
            CLI::write('No posts available — cannot build prompt.', 'red');
            if ($cleanupLine) $lineModel->deleteLine($lineId);
            return;
        }

        $prompt = $service->buildPrompt($line, $posts);
        CLI::write('--- PROMPT START ---');
        CLI::write($prompt);
        CLI::write('--- PROMPT END ---');
        CLI::write('Prompt length: ' . strlen($prompt) . ' bytes');

        if ($cleanupLine) {
            $lineModel->deleteLine($lineId);
            CLI::write('Ephemeral line cleaned up.');
        }
    }
}
