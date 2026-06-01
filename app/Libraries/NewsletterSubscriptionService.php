<?php

namespace App\Libraries;

use App\Models\EmailModel;
use App\Models\NewsletterEditorialLineModel;
use App\Models\NewsletterLeadMagnetModel;
use App\Models\NewsletterModel;
use App\Models\NewsletterSettingsModel;

/**
 * Handles subscription flow with double opt-in + lead magnet delivery.
 *
 *  subscribe($email, $source, $explicitLineIds=[]):
 *    - if double_opt_in ON  -> creates with status='pending', sends confirmation email; magnet sent after confirm()
 *    - if double_opt_in OFF -> creates with status='active', sends welcome email immediately (with magnet links)
 *
 *  confirm($token): flips status pending->active, sends welcome+magnet email
 *  deliverMagnetsTo($subscriber): looks up subscriber's editorial lines, finds their magnets, sends one email
 */
class NewsletterSubscriptionService
{
    protected $db;
    protected $nlModel;
    protected $lineModel;
    protected $magnetModel;
    protected $settingsModel;
    protected $emailModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->nlModel = new NewsletterModel();
        $this->lineModel = new NewsletterEditorialLineModel();
        $this->magnetModel = new NewsletterLeadMagnetModel();
        $this->settingsModel = new NewsletterSettingsModel();
        $this->emailModel = new EmailModel();
    }

    /**
     * Returns: ['ok' => bool, 'status' => 'pending'|'active'|'existing', 'subscriber_id' => int]
     */
    public function subscribe(string $email, array $source = [], array $explicitLineIds = []): array
    {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'invalid_email'];
        }

        $existing = $this->nlModel->getSubscriber($email);
        if ($existing) {
            // If pending and asking again, re-send confirmation
            if (($existing->status ?? '') === 'pending') {
                $this->sendConfirmation($existing);
                return ['ok' => true, 'status' => 'pending_resent', 'subscriber_id' => (int) $existing->id];
            }
            // If active, just acknowledge (idempotent)
            return ['ok' => true, 'status' => 'existing', 'subscriber_id' => (int) $existing->id];
        }

        $doubleOptIn = $this->settingsModel->isDoubleOptInEnabled();
        $initialStatus = $doubleOptIn ? 'pending' : 'active';

        // Build editorial line ids: explicit (from form) takes precedence; fallback to source category mapping
        $lineIds = array_values(array_filter(array_map('intval', $explicitLineIds), fn($v) => $v > 0));
        if (empty($lineIds) && !empty($source['source_category_id'])) {
            $lineIds = $this->lineModel->getMatchingLineIdsForCategory((int) $source['source_category_id']);
        }

        $insert = [
            'email' => $email,
            'token' => generateToken(),
            'confirm_token' => $doubleOptIn ? bin2hex(random_bytes(24)) : null,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => $initialStatus,
        ];
        if (!empty($lineIds)) $insert['editorial_line_ids'] = json_encode(array_values(array_unique($lineIds)));
        if (!empty($source['source_category_id'])) $insert['source_category_id'] = (int) $source['source_category_id'];
        if (!empty($source['source_post_id']))     $insert['source_post_id']     = (int) $source['source_post_id'];
        if (!empty($source['source_url']) && filter_var($source['source_url'], FILTER_VALIDATE_URL)) {
            $insert['source_url'] = mb_substr($source['source_url'], 0, 500);
        }
        if (!$doubleOptIn) {
            $insert['confirmed_at'] = date('Y-m-d H:i:s');
        }

        $this->db->table('subscribers')->insert($insert);
        $subId = (int) $this->db->insertID();
        $sub = $this->db->table('subscribers')->where('id', $subId)->get()->getRow();

        if ($doubleOptIn) {
            $this->sendConfirmation($sub);
        } else {
            $this->sendWelcomeWithMagnets($sub);
        }

        return [
            'ok' => true,
            'status' => $initialStatus,
            'subscriber_id' => $subId,
            'double_opt_in' => $doubleOptIn,
        ];
    }

    public function confirm(string $token): array
    {
        $token = preg_replace('/[^a-f0-9]/i', '', $token);
        if (strlen($token) < 16) {
            return ['ok' => false, 'error' => 'invalid_token'];
        }
        $sub = $this->db->table('subscribers')->where('confirm_token', $token)->get()->getRow();
        if (!$sub) {
            return ['ok' => false, 'error' => 'not_found'];
        }
        if (!empty($sub->confirmed_at)) {
            return ['ok' => true, 'status' => 'already_confirmed', 'subscriber' => $sub];
        }
        $this->db->table('subscribers')->where('id', $sub->id)->update([
            'status' => 'active',
            'confirmed_at' => date('Y-m-d H:i:s'),
            'confirm_token' => null,
        ]);
        $sub = $this->db->table('subscribers')->where('id', $sub->id)->get()->getRow();
        $this->sendWelcomeWithMagnets($sub);
        return ['ok' => true, 'status' => 'confirmed', 'subscriber' => $sub];
    }

    /**
     * Send the double-opt-in confirmation email.
     */
    public function sendConfirmation($subscriber): bool
    {
        $settings = $this->settingsModel->get();
        $base = function_exists('base_url') ? rtrim(base_url(), '/') : '';
        $confirmUrl = $base . '/newsletter/confirmar/' . $subscriber->confirm_token;

        $data = [
            'subject'          => $settings->confirmation_subject ?: 'Confirme sua inscrição',
            'to'               => $subscriber->email,
            'template_path'    => 'email/email_newsletter_confirm',
            'subscriber'       => $subscriber,
            'preRenderedHtml'  => view('email/email_newsletter_confirm', [
                'subject'        => $settings->confirmation_subject,
                'intro'          => $settings->confirmation_intro,
                'buttonText'     => $settings->confirmation_button_text,
                'confirmUrl'     => $confirmUrl,
                'subscriber'     => $subscriber,
                'baseSettings'   => \Config\Globals::$settings,
                'generalSettings'=> \Config\Globals::$generalSettings,
                'activeLang'     => \Config\Globals::$activeLang,
                'activeLanguages'=> \Config\Globals::$languages,
            ]),
        ];
        // EmailModel re-uses 'template_path' inside sendEmailPHPMailer; we use a passthrough shell
        $data['template_path'] = 'email/email_newsletter_ai_render';
        try {
            return (bool) $this->emailModel->sendEmail($data);
        } catch (\Throwable $e) {
            log_message('error', 'sendConfirmation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Welcome email with magnet download links for each subscribed line.
     */
    public function sendWelcomeWithMagnets($subscriber): bool
    {
        $settings = $this->settingsModel->get();
        $base = function_exists('base_url') ? rtrim(base_url(), '/') : '';

        $lineIds = [];
        if (!empty($subscriber->editorial_line_ids)) {
            $decoded = json_decode($subscriber->editorial_line_ids, true);
            if (is_array($decoded)) $lineIds = array_map('intval', $decoded);
        }

        $magnets = [];
        foreach ($lineIds as $lid) {
            $line = $this->lineModel->getById($lid);
            if (!$line || empty($line->lead_magnet_id)) continue;
            $magnet = $this->magnetModel->getById((int) $line->lead_magnet_id);
            if (!$magnet || !$magnet->active) continue;
            $token = $this->magnetModel->generateDownloadToken((int) $magnet->id, (int) $subscriber->id);
            $magnets[] = [
                'title'       => $magnet->title,
                'description' => $magnet->description,
                'cover_image' => $magnet->cover_image,
                'cta_text'    => $magnet->cta_text ?: 'Baixar material',
                'url'         => $base . '/newsletter/magnet/' . $token,
                'line_name'   => $line->name,
            ];
        }

        $html = view('email/email_newsletter_welcome', [
            'subject'        => $settings->welcome_subject,
            'intro'          => $settings->welcome_intro,
            'magnets'        => $magnets,
            'subscriber'     => $subscriber,
            'baseSettings'   => \Config\Globals::$settings,
            'generalSettings'=> \Config\Globals::$generalSettings,
            'activeLang'     => \Config\Globals::$activeLang,
            'activeLanguages'=> \Config\Globals::$languages,
        ]);

        $data = [
            'subject'         => $settings->welcome_subject ?: 'Bem-vindo à GX Capital',
            'to'              => $subscriber->email,
            'template_path'   => 'email/email_newsletter_ai_render',
            'subscriber'      => $subscriber,
            'preRenderedHtml' => $html,
        ];
        try {
            return (bool) $this->emailModel->sendEmail($data);
        } catch (\Throwable $e) {
            log_message('error', 'sendWelcomeWithMagnets failed: ' . $e->getMessage());
            return false;
        }
    }
}
