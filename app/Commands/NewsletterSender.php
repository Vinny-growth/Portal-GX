<?php

namespace App\Commands;

use App\Libraries\NewsletterSenderService;
use App\Models\NewsletterSendModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Processa newsletter_sends com status=approved e scheduled_for <= NOW().
 * Roda a cada 5 min via cron. Idempotente por recipient.
 */
class NewsletterSender extends BaseCommand
{
    protected $group = 'Newsletter';
    protected $name = 'newsletter:sender';
    protected $description = 'Dispatch approved newsletter sends to subscribers.';

    public function run(array $params)
    {
        $limit = 10;
        foreach ($params as $p) {
            if (strpos($p, '--limit=') === 0) {
                $limit = max(1, (int) substr($p, 8));
            }
        }
        $onlySend = null;
        foreach ($params as $p) {
            if (strpos($p, '--send=') === 0) {
                $onlySend = (int) substr($p, 7);
            }
        }

        $sendModel = new NewsletterSendModel();
        $sender = new NewsletterSenderService();

        if ($onlySend !== null) {
            $row = $sendModel->getById($onlySend);
            if (!$row) {
                CLI::write("Send $onlySend not found", 'red');
                return;
            }
            $rows = [$row];
        } else {
            $rows = $sendModel->getByStatus('approved', $limit);
        }

        if (empty($rows)) {
            CLI::write('No approved sends to dispatch.');
            return;
        }

        foreach ($rows as $row) {
            CLI::write("Dispatching send {$row->id} (line={$row->editorial_line_id}) ...");
            $result = $sender->dispatch((int) $row->id);
            if (!empty($result['error'])) {
                CLI::write("  ERROR: {$result['error']}", 'red');
            } else {
                CLI::write("  sent={$result['sent']} failed={$result['failed']} total={$result['total']}", 'green');
            }
        }
    }
}
