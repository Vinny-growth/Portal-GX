<?php

namespace App\Commands;

use App\Models\NewsletterEditorialLineModel;
use App\Models\NewsletterModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class NewsletterTestSubscribe extends BaseCommand
{
    protected $group = 'Newsletter';
    protected $name = 'newsletter:test-subscribe';
    protected $description = 'Smoke test: ensure category->editorial_line mapping works on subscribe.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        // cleanup any previous test rows
        $db->table('subscribers')->like('email', 'test-nl-', 'after')->delete();
        $db->table('newsletter_editorial_lines')->whereIn('slug', ['test-cambio'])->delete();

        // categories exist (pick any real category id, default 1)
        $catRow = $db->table('categories')->limit(1)->get()->getRow();
        $catId = $catRow ? (int) $catRow->id : 1;
        CLI::write("Using categoryId = $catId");

        $lineModel = new NewsletterEditorialLineModel();
        $lineId = $lineModel->createLine([
            'name' => 'Test Cambio',
            'slug' => 'test-cambio',
            'description' => 'smoke test line',
            'category_ids' => json_encode([$catId]),
            'send_times' => json_encode(["08:00","13:00","18:00"]),
            'frequency' => 'daily',
            'posts_per_edition' => 5,
            'ai_auto_publish' => 0,
            'enabled' => 1,
        ]);
        CLI::write("Created editorial_line id = $lineId");

        $email = 'test-nl-' . time() . '@gx.local';
        $nlModel = new NewsletterModel();
        $ok = $nlModel->addSubscriber($email, [
            'source_category_id' => $catId,
            'source_post_id' => 0,
            'source_url' => 'https://gx.capital/test',
        ]);
        CLI::write("Subscribed $email => " . ($ok ? 'OK' : 'FAIL'));

        $row = $db->table('subscribers')->where('email', $email)->get()->getRow();
        if (!$row) {
            CLI::write('Subscriber not found after insert', 'red');
            return;
        }
        CLI::write("Stored editorial_line_ids: " . ($row->editorial_line_ids ?? 'NULL'));
        CLI::write("Stored source_category_id: " . ($row->source_category_id ?? 'NULL'));
        CLI::write("Stored source_url: " . ($row->source_url ?? 'NULL'));

        $decoded = $row->editorial_line_ids ? json_decode($row->editorial_line_ids, true) : [];
        if (in_array((int) $lineId, array_map('intval', $decoded), true)) {
            CLI::write('PASS: subscriber mapped to editorial_line', 'green');
        } else {
            CLI::write('FAIL: subscriber NOT mapped to editorial_line', 'red');
        }

        // cleanup
        $db->table('subscribers')->where('email', $email)->delete();
        $db->table('newsletter_editorial_lines')->where('id', $lineId)->delete();
        CLI::write('Cleaned up.');
    }
}
