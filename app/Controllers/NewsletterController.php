<?php

namespace App\Controllers;

use App\Libraries\NewsletterSubscriptionService;
use App\Models\NewsletterEditorialLineModel;
use App\Models\NewsletterLeadMagnetModel;
use App\Models\NewsletterSettingsModel;

class NewsletterController extends BaseController
{
    /**
     * GET /newsletter
     */
    public function landing()
    {
        $settingsModel = new NewsletterSettingsModel();
        $lineModel = new NewsletterEditorialLineModel();
        $magnetModel = new NewsletterLeadMagnetModel();

        $settings = $settingsModel->get();
        $lines = $lineModel->getAll(true);

        // map magnet per line for the "what you get" section
        $magnetsByLine = [];
        foreach ($lines as $line) {
            if (!empty($line->lead_magnet_id)) {
                $m = $magnetModel->getById((int) $line->lead_magnet_id);
                if ($m && (int) $m->active === 1) {
                    $magnetsByLine[$line->id] = $m;
                }
            }
        }

        $preselectedLine = null;
        $preSlug = $this->request->getGet('linha');
        if ($preSlug) {
            $preselectedLine = $lineModel->getBySlug((string) $preSlug);
        }

        // Últimas 6 edições enviadas (para social proof + SEO content depth)
        $db = \Config\Database::connect();
        $recentSends = $db->table('newsletter_sends ns')
            ->select('ns.subject, ns.sent_at, ns.preheader, el.name AS line_name, el.slug AS line_slug')
            ->join('newsletter_editorial_lines el', 'el.id = ns.editorial_line_id', 'left')
            ->where('ns.status', 'sent')
            ->where('ns.sent_at IS NOT NULL', null, false)
            ->orderBy('ns.sent_at', 'DESC')
            ->limit(6)
            ->get()->getResult();

        // Total de subscribers ativos (prova social dinâmica)
        $subscribersActive = (int) $db->table('subscribers')
            ->where('(status IS NULL OR status = "active")', null, false)
            ->countAllResults();

        $data = [
            'title' => $settings->landing_headline ?: 'Newsletter GX Capital',
            'settings' => $settings,
            'lines' => $lines,
            'magnetsByLine' => $magnetsByLine,
            'preselectedLineId' => $preselectedLine ? (int) $preselectedLine->id : null,
            'recentSends' => $recentSends,
            'subscribersActive' => $subscribersActive,
            'hideHeader' => false,
        ];

        return view('newsletter/landing', $data);
    }

    /**
     * POST /newsletter/subscribe
     */
    public function subscribe()
    {
        $email = trim((string) $this->request->getPost('email'));
        // honeypot
        if (!empty($this->request->getPost('url'))) {
            return redirect()->to('/newsletter/obrigado');
        }
        $lineIds = $this->request->getPost('line_ids');
        if (!is_array($lineIds)) $lineIds = [];
        $lineIds = array_values(array_filter(array_map('intval', $lineIds), fn($v) => $v > 0));

        $source = [
            'source_category_id' => (int) $this->request->getPost('source_category_id'),
            'source_post_id' => (int) $this->request->getPost('source_post_id'),
            'source_url' => (string) $this->request->getPost('source_url'),
        ];

        $svc = new NewsletterSubscriptionService();
        $result = $svc->subscribe($email, $source, $lineIds);

        if (empty($result['ok'])) {
            return redirect()->back()->withInput()->with('error', $result['error'] ?? 'unknown');
        }
        if (!empty($result['double_opt_in'])) {
            return redirect()->to('/newsletter/confirme-seu-email?email=' . urlencode($email));
        }
        return redirect()->to('/newsletter/obrigado?status=' . urlencode($result['status']));
    }

    /**
     * GET /newsletter/confirme-seu-email
     */
    public function pendingConfirmation()
    {
        $settingsModel = new NewsletterSettingsModel();
        $data = [
            'title' => 'Confirme seu email',
            'email' => (string) $this->request->getGet('email'),
            'settings' => $settingsModel->get(),
        ];
        return view('newsletter/pending_confirmation', $data);
    }

    /**
     * GET /newsletter/confirmar/{token}
     */
    public function confirm($token = null)
    {
        $svc = new NewsletterSubscriptionService();
        $result = $svc->confirm((string) $token);
        $data = [
            'title' => 'Inscrição confirmada',
            'result' => $result,
            'settings' => (new NewsletterSettingsModel())->get(),
        ];
        return view('newsletter/confirmed', $data);
    }

    /**
     * GET /newsletter/obrigado
     */
    public function thankYou()
    {
        $settingsModel = new NewsletterSettingsModel();
        $data = [
            'title' => 'Obrigado',
            'settings' => $settingsModel->get(),
        ];
        return view('newsletter/thank_you', $data);
    }

    /**
     * GET /newsletter/magnet/{token} — downloads the magnet file.
     */
    public function magnetDownload($token = null)
    {
        $token = preg_replace('/[^a-f0-9]/i', '', (string) $token);
        $magnetModel = new NewsletterLeadMagnetModel();
        $data = $magnetModel->getByToken($token);
        if (!$data) {
            return $this->response->setStatusCode(404)->setBody('Material não encontrado.');
        }
        $magnet = $data['magnet'];
        if (empty($magnet->file_path)) {
            return $this->response->setStatusCode(404)->setBody('Arquivo indisponível.');
        }
        $full = FCPATH . ltrim($magnet->file_path, '/');
        if (!is_file($full)) {
            return $this->response->setStatusCode(404)->setBody('Arquivo não encontrado em disco.');
        }
        $magnetModel->recordDownload($token, $this->request->getIPAddress());
        return $this->response->download($full, null);
    }
}
