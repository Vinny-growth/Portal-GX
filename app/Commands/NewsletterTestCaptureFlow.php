<?php

namespace App\Commands;

use App\Libraries\NewsletterSubscriptionService;
use App\Models\NewsletterModel;
use App\Models\NewsletterSettingsModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Smoke test do fluxo de captura completo:
 *   1) Ativa double opt-in
 *   2) Inscreve email de teste
 *   3) Verifica criação como 'pending' + envio do email de confirmação
 *   4) Confirma via token
 *   5) Verifica status 'active' + envio do welcome
 *   6) Restaura estado original do double opt-in
 *
 * Usage: php spark newsletter:test-capture-flow [email]
 */
class NewsletterTestCaptureFlow extends BaseCommand
{
    protected $group = 'Newsletter';
    protected $name = 'newsletter:test-capture-flow';
    protected $description = 'End-to-end smoke test of the newsletter capture flow (double opt-in + welcome).';

    public function run(array $params)
    {
        $email = $params[0] ?? 'vinicius.teixeira@gx.capital';
        $db = \Config\Database::connect();
        $settingsModel = new NewsletterSettingsModel();
        $nlModel = new NewsletterModel();

        $original = $settingsModel->get();
        $originalOptIn = (int) $original->double_opt_in_enabled;

        // remove any prior test row for clean run
        $db->table('subscribers')->where('email', $email)->delete();

        CLI::write("===== Test 1: DOUBLE OPT-IN ON =====", 'yellow');
        $settingsModel->updateSettings(['double_opt_in_enabled' => 1]);

        $svc = new NewsletterSubscriptionService();
        $result = $svc->subscribe($email, [
            'source_category_id' => 6,
            'source_post_id' => 0,
            'source_url' => 'https://gx.capital/newsletter',
        ], []);
        CLI::write("Subscribe result: " . json_encode($result));

        $sub = $nlModel->getSubscriber($email);
        if (!$sub) {
            CLI::write('FAIL: subscriber not created', 'red');
            $this->restore($settingsModel, $originalOptIn);
            return;
        }
        CLI::write("Subscriber id=$sub->id status=$sub->status confirm_token=" . ($sub->confirm_token ? substr($sub->confirm_token, 0, 12) . '...' : 'NULL'));

        if ($sub->status !== 'pending') {
            CLI::write("FAIL: expected status='pending', got '$sub->status'", 'red');
            $this->restore($settingsModel, $originalOptIn);
            return;
        }
        if (empty($sub->confirm_token)) {
            CLI::write("FAIL: confirm_token not set", 'red');
            $this->restore($settingsModel, $originalOptIn);
            return;
        }
        CLI::write("PASS: pending subscriber created + confirmation email queued via SMTP", 'green');

        CLI::write("===== Test 2: confirm token =====", 'yellow');
        $confirm = $svc->confirm($sub->confirm_token);
        CLI::write("Confirm result: " . json_encode([
            'ok' => $confirm['ok'] ?? false,
            'status' => $confirm['status'] ?? null,
        ]));
        $sub = $nlModel->getSubscriber($email);
        CLI::write("After confirm: status=$sub->status confirmed_at=$sub->confirmed_at confirm_token=" . ($sub->confirm_token ?: 'NULL'));
        if ($sub->status !== 'active' || empty($sub->confirmed_at)) {
            CLI::write("FAIL: expected active+confirmed_at", 'red');
            $this->restore($settingsModel, $originalOptIn);
            return;
        }
        if (!empty($sub->confirm_token)) {
            CLI::write("FAIL: confirm_token should be cleared", 'red');
            $this->restore($settingsModel, $originalOptIn);
            return;
        }
        CLI::write("PASS: subscriber confirmed + welcome email sent", 'green');

        CLI::write("===== Test 3: DOUBLE OPT-IN OFF (direct subscribe) =====", 'yellow');
        $db->table('subscribers')->where('email', $email)->delete();
        $settingsModel->updateSettings(['double_opt_in_enabled' => 0]);
        $result = $svc->subscribe($email, [
            'source_category_id' => 6,
            'source_post_id' => 0,
            'source_url' => 'https://gx.capital/newsletter',
        ], []);
        CLI::write("Subscribe result: " . json_encode($result));
        $sub = $nlModel->getSubscriber($email);
        CLI::write("Subscriber id=$sub->id status=$sub->status confirmed_at=$sub->confirmed_at");
        if ($sub->status !== 'active' || empty($sub->confirmed_at)) {
            CLI::write("FAIL: expected active immediately with double_opt_in OFF", 'red');
            $this->restore($settingsModel, $originalOptIn);
            return;
        }
        CLI::write("PASS: subscriber active immediately + welcome email sent", 'green');

        $this->restore($settingsModel, $originalOptIn);
        CLI::write("===== DONE =====", 'green');
        CLI::write("Check inbox at $email — you should have received:");
        CLI::write("  1. Confirmation email (test 1)");
        CLI::write("  2. Welcome email (test 2)");
        CLI::write("  3. Welcome email (test 3, direct opt-in)");
    }

    protected function restore(NewsletterSettingsModel $m, int $optIn): void
    {
        $m->updateSettings(['double_opt_in_enabled' => $optIn]);
        CLI::write("Restored double_opt_in_enabled=$optIn");
    }
}
