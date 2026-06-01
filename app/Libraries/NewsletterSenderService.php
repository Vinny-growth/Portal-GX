<?php

namespace App\Libraries;

use App\Models\EmailModel;
use App\Models\NewsletterEditorialLineModel;
use App\Models\NewsletterModel;
use App\Models\NewsletterSendModel;

/**
 * Materializes a newsletter_send row into actual emails.
 *
 *  - buildSendFromEdition(lineId, edition)  -> creates draft|approved send row
 *  - renderHtml(send)                        -> builds HTML body w/ pixel + link tokens
 *  - dispatch(sendId)                        -> iterates subscribers, sends, records
 */
class NewsletterSenderService
{
    protected $db;
    protected $sendModel;
    protected $lineModel;
    protected $nlModel;
    protected $emailModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->sendModel = new NewsletterSendModel();
        $this->lineModel = new NewsletterEditorialLineModel();
        $this->nlModel = new NewsletterModel();
        $this->emailModel = new EmailModel();
    }

    public function buildSendFromEdition(int $lineId, array $edition, array $opts = []): int
    {
        $line = $this->lineModel->getById($lineId);
        if (!$line) {
            throw new \RuntimeException("Editorial line $lineId not found");
        }

        $autoApprove = (int) ($line->ai_auto_publish ?? 0) === 1;
        $status = $autoApprove ? 'approved' : 'draft';
        if (!empty($opts['force_status'])) {
            $status = $opts['force_status'];
        }

        $sendId = $this->sendModel->create([
            'editorial_line_id' => $lineId,
            'slot'              => $opts['slot'] ?? null, // HH:MM slot that triggered this edition (scheduler dedupe)
            'subject'           => mb_substr($edition['subject'] ?? '', 0, 500),
            'preheader'         => mb_substr($edition['preheader'] ?? '', 0, 500),
            'html_body'         => null, // rendered at dispatch
            'text_body'         => null,
            'post_ids'          => json_encode($edition['post_ids'] ?? []),
            'status'            => $status,
            'scheduled_for'     => $opts['scheduled_for'] ?? date('Y-m-d H:i:s'),
            'generated_at'      => date('Y-m-d H:i:s'),
            'approved_at'       => $autoApprove ? date('Y-m-d H:i:s') : null,
            'ai_prompt'         => $edition['prompt'] ?? null,
            'ai_response'       => $edition['raw_response'] ?? null,
        ]);

        // store the structured edition payload in a side table? simpler: keep in ai_response as JSON for re-render
        $payload = [
            'subject'   => $edition['subject'] ?? '',
            'preheader' => $edition['preheader'] ?? '',
            'intro'     => $edition['intro'] ?? '',
            'posts'     => $edition['posts'] ?? [],
            'cta_text'  => $edition['cta_text'] ?? '',
            'cta_url'   => $edition['cta_url'] ?? '',
        ];
        $this->sendModel->updateSend($sendId, [
            'html_body' => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);

        return $sendId;
    }

    /**
     * Decodes the stored payload (JSON in html_body) back to an edition array.
     */
    public function getPayload(int $sendId): ?array
    {
        $row = $this->sendModel->getById($sendId);
        if (!$row || empty($row->html_body)) return null;
        $payload = json_decode($row->html_body, true);
        return is_array($payload) ? $payload : null;
    }

    /**
     * Update the editable payload (subject + posts) before dispatch.
     */
    public function updatePayload(int $sendId, array $payload): bool
    {
        return $this->sendModel->updateSend($sendId, [
            'subject'   => mb_substr($payload['subject'] ?? '', 0, 500),
            'preheader' => mb_substr($payload['preheader'] ?? '', 0, 500),
            'html_body' => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);
    }

    /**
     * Sends to all eligible subscribers of the line. Idempotent: skips already-sent recipients.
     */
    public function dispatch(int $sendId): array
    {
        $send = $this->sendModel->getById($sendId);
        if (!$send) {
            return ['error' => "send $sendId not found"];
        }
        if (!in_array($send->status, ['approved', 'sending'], true)) {
            return ['error' => "send $sendId status is {$send->status}, cannot dispatch"];
        }

        $payload = json_decode($send->html_body ?: '', true);
        if (!is_array($payload)) {
            $this->sendModel->updateSend($sendId, ['status' => 'failed', 'error' => 'invalid payload']);
            return ['error' => 'invalid payload'];
        }

        $this->sendModel->updateSend($sendId, ['status' => 'sending']);

        $lineId = (int) $send->editorial_line_id;
        $subscribers = $this->nlModel->getActiveSubscribersForLine($lineId);
        if (empty($subscribers)) {
            $this->sendModel->updateSend($sendId, ['status' => 'sent', 'sent_at' => date('Y-m-d H:i:s'), 'recipients_count' => 0]);
            return ['sent' => 0, 'error' => 'no subscribers'];
        }

        // Pre-create link tokens for the posts (shared across recipients)
        $linkTokens = $this->ensureLinkTokens($sendId, $payload);

        $sent = 0;
        $failed = 0;
        $errors = [];
        foreach ($subscribers as $sub) {
            $existing = $this->db->table('newsletter_send_recipients')
                ->where(['send_id' => $sendId, 'subscriber_id' => $sub->id])
                ->get()->getRow();
            if ($existing && $existing->status === 'sent') {
                continue;
            }
            if (!$existing) {
                $this->db->table('newsletter_send_recipients')->insert([
                    'send_id'       => $sendId,
                    'subscriber_id' => $sub->id,
                    'email'         => $sub->email,
                    'status'        => 'pending',
                ]);
            }

            // ensure subscriber has a token (used for unsubscribe link)
            if (empty($sub->token)) {
                $this->nlModel->updateSubscriberToken($sub->email);
                $sub = $this->nlModel->getSubscriber($sub->email);
            }

            $trackingToken = $this->ensureRecipientToken($sendId, (int) $sub->id);
            $html = $this->renderHtml($send, $payload, $sub, $trackingToken, $linkTokens);

            $data = [
                'subject'       => $payload['subject'] ?? $send->subject,
                'message'       => $html, // unused by template but kept for compatibility
                'to'            => $sub->email,
                'template_path' => 'email/email_newsletter_ai_render', // simple shell that echoes pre-rendered html
                'subscriber'    => $sub,
                'preRenderedHtml' => $html,
            ];

            try {
                $ok = $this->emailModel->sendEmail($data);
            } catch (\Throwable $e) {
                $ok = false;
                $errors[] = $e->getMessage();
            }

            if ($ok) {
                $sent++;
                $this->db->table('newsletter_send_recipients')
                    ->where(['send_id' => $sendId, 'subscriber_id' => $sub->id])
                    ->update(['status' => 'sent', 'delivered_at' => date('Y-m-d H:i:s')]);
            } else {
                $failed++;
                $this->db->table('newsletter_send_recipients')
                    ->where(['send_id' => $sendId, 'subscriber_id' => $sub->id])
                    ->update(['status' => 'failed', 'error' => mb_substr(end($errors) ?: 'send failed', 0, 500)]);
            }
        }

        // Authoritative cumulative counts from the recipient ledger, so resuming a
        // crashed/partial dispatch does not overwrite delivered_count with only this
        // run's freshly-sent total (previously-sent recipients are skipped above).
        $recipientsTotal = $this->db->table('newsletter_send_recipients')
            ->where('send_id', $sendId)->countAllResults();
        $deliveredTotal = $this->db->table('newsletter_send_recipients')
            ->where(['send_id' => $sendId, 'status' => 'sent'])->countAllResults();

        $finalStatus = ($deliveredTotal === 0 && $recipientsTotal > 0) ? 'failed' : 'sent';
        $this->sendModel->updateSend($sendId, [
            'status'           => $finalStatus,
            'sent_at'          => date('Y-m-d H:i:s'),
            'recipients_count' => $recipientsTotal,
            'delivered_count'  => $deliveredTotal,
            'error'            => $failed > 0 ? ('failed=' . $failed . '; ' . substr(implode(' | ', $errors), 0, 400)) : null,
        ]);
        $this->lineModel->touchLastSent($lineId);

        return ['sent' => $sent, 'failed' => $failed, 'total' => count($subscribers)];
    }

    /**
     * Renders the email HTML for a specific recipient (pixel + redirect tokens injected).
     */
    public function renderHtml($send, array $payload, $subscriber, string $pixelToken, array $linkTokens): string
    {
        $base = function_exists('base_url') ? rtrim(base_url(), '/') : '';

        // Per-recipient tracking token, appended to every wrapped link as ?t= so the
        // click redirect can attribute the click (and engagement) back to this subscriber.
        $t = '?t=' . urlencode($pixelToken);

        // wrap each post URL via /r/{token}
        $postsList = [];
        foreach (($payload['posts'] ?? []) as $idx => $item) {
            $postId = (int) ($item['post_id'] ?? 0);
            $token = $linkTokens['post_' . $postId] ?? null;
            $url = $item['url'] ?? '#';
            $wrapped = $token ? $base . '/r/' . $token . $t : $url;
            $postsList[] = [
                'title'      => $item['title'] ?? '',
                'summary'    => $item['summary'] ?? '',
                'image_url'  => $item['image_url'] ?? '',
                'cta_label'  => $item['cta_label'] ?? 'Leia mais',
                'url'        => $wrapped,
            ];
        }

        $ctaText = $payload['cta_text'] ?? '';
        $ctaUrl = $payload['cta_url'] ?? '';
        if (!empty($ctaUrl) && !empty($linkTokens['cta'])) {
            $ctaUrl = $base . '/r/' . $linkTokens['cta'] . $t;
        }

        $pixelUrl = $base . '/nl/pixel/' . $pixelToken . '.gif';

        $viewData = [
            'subject'         => $payload['subject'] ?? $send->subject,
            'preheader'       => $payload['preheader'] ?? '',
            'intro'           => $payload['intro'] ?? '',
            'postsList'       => $postsList,
            'ctaText'         => $ctaText,
            'ctaUrl'          => $ctaUrl,
            'pixelUrl'        => $pixelUrl,
            'subscriber'      => $subscriber,
            'baseSettings'    => \Config\Globals::$settings,
            'generalSettings' => \Config\Globals::$generalSettings,
            'activeLang'      => \Config\Globals::$activeLang,
            'activeLanguages' => \Config\Globals::$languages,
        ];

        return view('email/email_newsletter_ai', $viewData);
    }

    protected function ensureRecipientToken(int $sendId, int $subscriberId): string
    {
        $row = $this->db->table('newsletter_email_tracking')
            ->where(['send_id' => $sendId, 'subscriber_id' => $subscriberId])
            ->get()->getRow();
        if ($row && !empty($row->token)) {
            return $row->token;
        }
        $token = bin2hex(random_bytes(16));
        $this->db->table('newsletter_email_tracking')->insert([
            'send_id'       => $sendId,
            'subscriber_id' => $subscriberId,
            'token'         => $token,
            'open_count'    => 0,
            'click_count'   => 0,
        ]);
        return $token;
    }

    protected function ensureLinkTokens(int $sendId, array $payload): array
    {
        $tokens = [];
        foreach (($payload['posts'] ?? []) as $item) {
            $postId = (int) ($item['post_id'] ?? 0);
            if (!$postId) continue;
            $url = (string) ($item['url'] ?? '');
            if ($url === '') continue;
            $tokens['post_' . $postId] = $this->ensureLinkToken($sendId, $url, 'post:' . $postId);
        }
        $ctaUrl = (string) ($payload['cta_url'] ?? '');
        if ($ctaUrl !== '') {
            $tokens['cta'] = $this->ensureLinkToken($sendId, $ctaUrl, 'cta');
        }
        return $tokens;
    }

    protected function ensureLinkToken(int $sendId, string $url, string $label): string
    {
        $row = $this->db->table('newsletter_link_tracking')
            ->where(['send_id' => $sendId, 'label' => $label])
            ->get()->getRow();
        if ($row && !empty($row->token)) {
            // url could have changed if admin edited; update
            if ($row->original_url !== $url) {
                $this->db->table('newsletter_link_tracking')
                    ->where('id', $row->id)
                    ->update(['original_url' => $url]);
            }
            return $row->token;
        }
        $token = bin2hex(random_bytes(16));
        $this->db->table('newsletter_link_tracking')->insert([
            'send_id'      => $sendId,
            'token'        => $token,
            'original_url' => $url,
            'label'        => $label,
            'click_count'  => 0,
        ]);
        return $token;
    }
}
