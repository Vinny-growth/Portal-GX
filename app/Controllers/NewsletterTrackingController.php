<?php

namespace App\Controllers;

use App\Models\NewsletterModel;
use App\Models\NewsletterSendModel;

class NewsletterTrackingController extends BaseController
{
    protected $db;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
    }

    /**
     * GET /nl/pixel/{token}.gif
     */
    public function pixel($token = null)
    {
        $token = $this->stripExt((string) $token);
        if ($token !== '') {
            $this->recordOpen($token);
        }
        return $this->emitTransparentGif();
    }

    /**
     * GET /r/{token}
     */
    public function redirect($token = null)
    {
        $token = (string) $token;
        $target = '/';
        if ($token !== '') {
            $row = $this->db->table('newsletter_link_tracking')->where('token', $token)->get()->getRow();
            if ($row) {
                $target = $row->original_url ?: '/';
                $this->recordClick($row);
            }
        }
        return $this->response->redirect($target, 'auto', 302);
    }

    protected function recordOpen(string $token): void
    {
        $row = $this->db->table('newsletter_email_tracking')->where('token', $token)->get()->getRow();
        if (!$row) return;

        $now = date('Y-m-d H:i:s');
        $update = [
            'last_opened_at' => $now,
            'user_agent'     => mb_substr((string) ($this->request->getUserAgent() ?? ''), 0, 510),
            'ip_address'     => $this->request->getIPAddress(),
        ];
        if (empty($row->opened_at)) {
            $update['opened_at'] = $now;
            $sendModel = new NewsletterSendModel();
            $sendModel->bumpOpenCount((int) $row->send_id);
        }
        $this->db->table('newsletter_email_tracking')
            ->where('id', $row->id)
            ->set('open_count', 'open_count + 1', false)
            ->set($update)
            ->update();

        // bump engagement on first open
        if (empty($row->opened_at)) {
            (new NewsletterModel())->updateEngagement((int) $row->subscriber_id, 1.0);
        }
    }

    protected function recordClick($linkRow): void
    {
        $now = date('Y-m-d H:i:s');
        $sendId = (int) $linkRow->send_id;
        $ua = mb_substr((string) ($this->request->getUserAgent() ?? ''), 0, 510);
        $ip = $this->request->getIPAddress();

        // resolve subscriber by tracking token if passed (?t=)
        $subscriberId = 0;
        $emailToken = $this->request->getGet('t');
        if (!empty($emailToken)) {
            $tr = $this->db->table('newsletter_email_tracking')->where('token', $emailToken)->get()->getRow();
            if ($tr) {
                $subscriberId = (int) $tr->subscriber_id;
            }
        }

        $this->db->table('newsletter_link_tracking')
            ->where('id', $linkRow->id)
            ->set('click_count', 'click_count + 1', false)
            ->set(['last_clicked_at' => $now])
            ->update();

        $this->db->table('newsletter_link_clicks')->insert([
            'link_id'       => (int) $linkRow->id,
            'send_id'       => $sendId,
            'subscriber_id' => $subscriberId ?: null,
            'clicked_at'    => $now,
            'ip_address'    => $ip,
            'user_agent'    => $ua,
        ]);

        (new NewsletterSendModel())->bumpClickCount($sendId);

        if ($subscriberId > 0) {
            $tr = $this->db->table('newsletter_email_tracking')
                ->where(['send_id' => $sendId, 'subscriber_id' => $subscriberId])
                ->get()->getRow();
            if ($tr) {
                $patch = ['last_click_at' => $now];
                if (empty($tr->first_click_at)) {
                    $patch['first_click_at'] = $now;
                }
                $this->db->table('newsletter_email_tracking')
                    ->where('id', $tr->id)
                    ->set('click_count', 'click_count + 1', false)
                    ->set($patch)
                    ->update();
            }
            (new NewsletterModel())->updateEngagement($subscriberId, 2.0);
        }
    }

    protected function emitTransparentGif()
    {
        // 1x1 transparent GIF
        $gif = base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==');
        return $this->response
            ->setHeader('Content-Type', 'image/gif')
            ->setHeader('Content-Length', (string) strlen($gif))
            ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody($gif);
    }

    protected function stripExt(string $token): string
    {
        $token = preg_replace('/\.[a-z0-9]+$/i', '', $token);
        return preg_replace('/[^a-f0-9]/i', '', (string) $token);
    }
}
