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
 * Envia 1 newsletter REAL via OpenAI + SMTP para um email de teste.
 * Usage: php spark newsletter:test-real-email vinicius.teixeira@gx.capital [catId1,catId2,...]
 */
class NewsletterTestRealEmail extends BaseCommand
{
    protected $group = 'Newsletter';
    protected $name = 'newsletter:test-real-email';
    protected $description = 'Generates a real newsletter via OpenAI and sends it via SMTP. Usage: php spark newsletter:test-real-email <email> [catIds]';

    public function run(array $params)
    {
        $email = $params[0] ?? '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            CLI::write('Usage: php spark newsletter:test-real-email <email> [catId,catId,...]', 'red');
            return;
        }
        $catList = isset($params[1]) ? array_filter(array_map('intval', explode(',', $params[1]))) : [];
        if (empty($catList)) {
            // Verticais canônicos (Radar, Cambio, Credito, GX explica) resolvidos por slug — sem IDs fixos.
            $catList = (new \App\Models\ContentAISettingsModel())->categoryIdsForVerticals(['economia', 'cambio', 'credito', 'gx-explica']);
        }
        if (empty($catList)) {
            $catList = [7, 6, 8, 11]; // fallback: Radar, Cambio, Credito, GX explica
        }
        CLI::write("Target email: $email");
        CLI::write("Categories: " . implode(',', $catList));

        $db = \Config\Database::connect();
        $lineModel = new NewsletterEditorialLineModel();
        $nlModel = new NewsletterModel();
        $ai = new NewsletterAIService();
        $sender = new NewsletterSenderService();
        $sendModel = new NewsletterSendModel();

        // 1) Reusable test line (idempotent by slug)
        $slug = 'teste-real-' . date('Ymd');
        $line = $lineModel->getBySlug($slug);
        if (!$line) {
            $lineId = $lineModel->createLine([
                'name' => 'Teste Real ' . date('Y-m-d'),
                'slug' => $slug,
                'description' => 'Linha de teste para smoke-test com email real.',
                'category_ids' => json_encode(array_values($catList)),
                'send_times' => json_encode(['09:00']),
                'frequency' => 'on_demand',
                'posts_per_edition' => 4,
                'lookback_hours' => 24 * 30,
                'ai_auto_publish' => 0,
                'cta_text' => 'Falar com a GX',
                'cta_url' => 'https://gx.capital/wealth',
                'subject_prompt' => 'Foque no destaque do dia em câmbio, juros ou crédito. Use número se houver.',
                'body_prompt' => 'Tom executivo, direto. Cada post deve ter um insight acionável para o leitor financeiro.',
                'enabled' => 1,
            ]);
            $line = $lineModel->getById($lineId);
            CLI::write("Created test line id=$lineId slug=$slug", 'green');
        } else {
            $lineId = (int) $line->id;
            CLI::write("Reusing existing test line id=$lineId");
        }

        // 2) Subscriber (idempotent)
        $sub = $nlModel->getSubscriber($email);
        if (!$sub) {
            $nlModel->addSubscriber($email, [
                'source_category_id' => $catList[0],
                'source_post_id' => 0,
                'source_url' => 'https://gx.capital/newsletter-test',
            ]);
            $sub = $nlModel->getSubscriber($email);
            CLI::write("Created subscriber id=$sub->id", 'green');
        } else {
            CLI::write("Reusing subscriber id=$sub->id");
        }

        // ensure subscriber is mapped to this test line
        $current = $sub->editorial_line_ids ? json_decode($sub->editorial_line_ids, true) : [];
        if (!is_array($current)) $current = [];
        if (!in_array($lineId, $current, true)) {
            $current[] = $lineId;
            $db->table('subscribers')->where('id', $sub->id)
                ->update(['editorial_line_ids' => json_encode(array_values(array_unique($current))), 'status' => 'active']);
            CLI::write("Mapped subscriber to line $lineId (lines now: " . json_encode($current) . ')');
        }

        // 3) Generate edition (REAL OpenAI call)
        CLI::write('Generating edition via OpenAI...');
        $edition = $ai->generateEdition($line);
        if (!empty($edition['error'])) {
            CLI::write('OpenAI error: ' . $edition['error'], 'red');
            return;
        }
        CLI::write('AI subject: ' . ($edition['subject'] ?? '(none)'));
        CLI::write('AI posts: ' . count($edition['posts'] ?? []));

        // 4) Build send + force approve
        $sendId = $sender->buildSendFromEdition($lineId, $edition, ['force_status' => 'approved']);
        CLI::write("Created send id=$sendId (status=approved)", 'green');

        // 5) Dispatch — isolated to target email only.
        // Snapshot every other subscriber's status, mark them as "test_excluded" so the
        // sender's WHERE (status IS NULL OR status='active') excludes them. Restored after.
        $allOthers = $db->table('subscribers')->where('id !=', $sub->id)->get()->getResult();
        $snapshot = [];
        foreach ($allOthers as $o) {
            $snapshot[$o->id] = $o->status;
            $db->table('subscribers')->where('id', $o->id)->update(['status' => 'test_excluded']);
        }
        // Also ensure target is 'active'
        $db->table('subscribers')->where('id', $sub->id)->update(['status' => 'active']);

        CLI::write('Dispatching via SMTP (isolated to ' . $email . ' only)...');
        $result = $sender->dispatch($sendId);

        // Restore other subscribers' statuses
        foreach ($snapshot as $oid => $val) {
            $db->table('subscribers')->where('id', $oid)->update(['status' => $val ?: 'active']);
        }
        if (!empty($result['error'])) {
            CLI::write('Dispatch error: ' . $result['error'], 'red');
        } else {
            CLI::write("Dispatch: sent={$result['sent']} failed={$result['failed']} total={$result['total']}", 'green');
        }

        $send = $sendModel->getById($sendId);
        if (!empty($send->error)) {
            CLI::write('Send error: ' . $send->error, 'red');
        }
        CLI::write("Check inbox of $email. Open in browser to fire the open pixel.");
        CLI::write("Admin: " . base_url() . '/painel/newsletter/queue/view/' . $sendId);
    }
}
