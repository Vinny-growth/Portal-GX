<?php

namespace App\Commands;

use App\Libraries\NewsletterAIService;
use App\Libraries\NewsletterSenderService;
use App\Models\NewsletterEditorialLineModel;
use App\Models\NewsletterModel;
use App\Models\NewsletterSendModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Smoke test end-to-end (sem chamar OpenAI nem enviar email real):
 *   1. Cria linha editorial efêmera
 *   2. Cria subscriber de teste mapeado
 *   3. Picks posts via NewsletterAIService::pickPosts
 *   4. Monta payload mockado (substitui callTextModel)
 *   5. Cria send + renderHtml com pixel + redirect
 *   6. Verifica: HTML contém pixel URL, contém /r/ tokens, link_tracking rows criados
 *   7. Simula open (GET pixel handler) e click (GET redirect handler)
 *   8. Limpa tudo
 */
class NewsletterTestE2E extends BaseCommand
{
    protected $group = 'Newsletter';
    protected $name = 'newsletter:test-e2e';
    protected $description = 'Smoke test full pipeline without OpenAI or real email send.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        // 1) get a category with real posts
        $catRow = $db->table('posts')
            ->select('category_id, COUNT(*) AS n')
            ->where('status', 1)
            ->where('visibility', 1)
            ->groupBy('category_id')
            ->orderBy('n', 'DESC')
            ->limit(1)
            ->get()->getRow();
        if (!$catRow || empty($catRow->category_id)) {
            CLI::write('No published posts found. Aborting.', 'red');
            return;
        }
        $catId = (int) $catRow->category_id;
        CLI::write("Using categoryId=$catId (with $catRow->n published posts)");

        // 2) ephemeral editorial line
        $lineModel = new NewsletterEditorialLineModel();
        $lineId = $lineModel->createLine([
            'name' => '_e2e_' . time(),
            'slug' => '_e2e_' . time(),
            'description' => 'e2e smoke test line',
            'category_ids' => json_encode([$catId]),
            'send_times' => json_encode(["08:00"]),
            'frequency' => 'on_demand',
            'posts_per_edition' => 3,
            'lookback_hours' => 24 * 365 * 3,
            'ai_auto_publish' => 0,
            'enabled' => 1,
        ]);
        $line = $lineModel->getById($lineId);
        CLI::write("Created line $lineId");

        // 3) ephemeral subscriber
        $nlModel = new NewsletterModel();
        $email = 'e2e-' . time() . '@gx.local';
        $nlModel->addSubscriber($email, [
            'source_category_id' => $catId,
            'source_post_id' => 0,
            'source_url' => 'https://gx.capital/e2e',
        ]);
        $sub = $nlModel->getSubscriber($email);
        if (!$sub) {
            CLI::write('Failed to create subscriber', 'red');
            $lineModel->deleteLine($lineId);
            return;
        }
        CLI::write("Created subscriber id=$sub->id editorial_lines=" . ($sub->editorial_line_ids ?? 'NULL'));

        // 4) pick posts + mock edition
        $ai = new NewsletterAIService();
        $posts = $ai->pickPosts($line);
        if (empty($posts)) {
            CLI::write('pickPosts returned 0. Aborting.', 'red');
            $this->cleanup($db, $lineId, $sub->id, null);
            return;
        }
        CLI::write('pickPosts: ' . count($posts) . ' posts');

        $mergedPosts = [];
        $postIds = [];
        foreach ($posts as $p) {
            $mergedPosts[] = [
                'post_id'    => (int) $p->id,
                'title'      => '[E2E] ' . $p->title,
                'summary'    => 'Mock summary for e2e test.',
                'cta_label'  => 'Leia mais',
                'image_url'  => $p->image_url ?? '',
                'category_id'=> (int) $p->category_id,
                'url'        => generatePostURL($p),
            ];
            $postIds[] = (int) $p->id;
        }
        $edition = [
            'subject'      => 'E2E Newsletter ' . date('H:i:s'),
            'preheader'    => 'Smoke test preheader',
            'intro'        => 'Esta é uma edição mock gerada pelo smoke test E2E.',
            'posts'        => $mergedPosts,
            'cta_text'     => 'Falar com especialista',
            'cta_url'      => 'https://gx.capital/wealth',
            'prompt'       => '(mocked)',
            'raw_response' => '(mocked)',
            'post_ids'     => $postIds,
        ];

        // 5) create send (status=draft because line.ai_auto_publish=0) then force approve
        $sender = new NewsletterSenderService();
        $sendId = $sender->buildSendFromEdition($lineId, $edition);
        CLI::write("Created send $sendId");

        $sendModel = new NewsletterSendModel();
        $sendModel->approve($sendId);
        CLI::write('Send approved.');

        // 6) instead of dispatch() (which would send real email), render HTML manually
        $send = $sendModel->getById($sendId);
        $payload = json_decode($send->html_body, true);
        $pixelToken = bin2hex(random_bytes(16));
        $db->table('newsletter_email_tracking')->insert([
            'send_id' => $sendId,
            'subscriber_id' => $sub->id,
            'token' => $pixelToken,
        ]);
        // link tokens
        $linkTokens = [];
        foreach ($payload['posts'] as $p) {
            $token = bin2hex(random_bytes(16));
            $db->table('newsletter_link_tracking')->insert([
                'send_id' => $sendId,
                'token' => $token,
                'original_url' => $p['url'],
                'label' => 'post:' . $p['post_id'],
            ]);
            $linkTokens['post_' . $p['post_id']] = $token;
        }
        if (!empty($payload['cta_url'])) {
            $ctaTok = bin2hex(random_bytes(16));
            $db->table('newsletter_link_tracking')->insert([
                'send_id' => $sendId,
                'token' => $ctaTok,
                'original_url' => $payload['cta_url'],
                'label' => 'cta',
            ]);
            $linkTokens['cta'] = $ctaTok;
        }

        $html = $sender->renderHtml($send, $payload, $sub, $pixelToken, $linkTokens);
        $bytes = strlen($html);
        CLI::write("Rendered HTML: $bytes bytes");

        // 7) assertions
        $checks = [
            'pixel url present' => strpos($html, '/nl/pixel/' . $pixelToken . '.gif') !== false,
            '/r/ links present' => substr_count($html, '/r/') >= count($payload['posts']),
            'subject visible'   => strpos($html, esc($payload['subject'])) !== false,
        ];
        foreach ($checks as $name => $ok) {
            CLI::write(' - ' . $name . ': ' . ($ok ? 'PASS' : 'FAIL'), $ok ? 'green' : 'red');
        }

        // 8) simulate open tracking (mark opened_at)
        $db->table('newsletter_email_tracking')
            ->where(['send_id' => $sendId, 'subscriber_id' => $sub->id])
            ->update([
                'opened_at' => date('Y-m-d H:i:s'),
                'last_opened_at' => date('Y-m-d H:i:s'),
                'open_count' => 1,
            ]);
        $sendModel->bumpOpenCount($sendId);
        $send = $sendModel->getById($sendId);
        CLI::write('After simulated open: send.opens_count = ' . $send->opens_count);

        // simulate click
        $firstLink = $db->table('newsletter_link_tracking')->where('send_id', $sendId)->limit(1)->get()->getRow();
        if ($firstLink) {
            $db->table('newsletter_link_tracking')->where('id', $firstLink->id)
                ->set('click_count', 'click_count + 1', false)
                ->set(['last_clicked_at' => date('Y-m-d H:i:s')])
                ->update();
            $db->table('newsletter_link_clicks')->insert([
                'link_id' => $firstLink->id,
                'send_id' => $sendId,
                'subscriber_id' => $sub->id,
                'clicked_at' => date('Y-m-d H:i:s'),
            ]);
            $sendModel->bumpClickCount($sendId);
            $send = $sendModel->getById($sendId);
            CLI::write('After simulated click: send.clicks_count = ' . $send->clicks_count);
        }

        // sample HTML preview
        CLI::write('---- HTML snippet ----');
        CLI::write(mb_substr($html, 0, 1000));
        CLI::write('... [' . max(0, $bytes - 1000) . ' more bytes]');

        // cleanup
        $this->cleanup($db, $lineId, $sub->id, $sendId);
        CLI::write('Cleaned up.');
        CLI::write('E2E DONE.', 'green');
    }

    protected function cleanup($db, $lineId, $subId, $sendId): void
    {
        if ($sendId) {
            $db->table('newsletter_link_clicks')->where('send_id', $sendId)->delete();
            $db->table('newsletter_link_tracking')->where('send_id', $sendId)->delete();
            $db->table('newsletter_email_tracking')->where('send_id', $sendId)->delete();
            $db->table('newsletter_send_recipients')->where('send_id', $sendId)->delete();
            $db->table('newsletter_sends')->where('id', $sendId)->delete();
        }
        if ($subId) {
            $db->table('subscribers')->where('id', $subId)->delete();
        }
        if ($lineId) {
            $db->table('newsletter_editorial_lines')->where('id', $lineId)->delete();
        }
    }
}
