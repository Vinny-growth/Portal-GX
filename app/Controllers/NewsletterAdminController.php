<?php

namespace App\Controllers;

use App\Libraries\NewsletterAIService;
use App\Libraries\NewsletterSenderService;
use App\Models\NewsletterEditorialLineModel;
use App\Models\NewsletterLeadMagnetModel;
use App\Models\NewsletterSendModel;
use App\Models\NewsletterSettingsModel;

class NewsletterAdminController extends BaseAdminController
{
    public function editorialLines()
    {
        checkPermission('newsletter');
        $lineModel = new NewsletterEditorialLineModel();
        $data['title'] = 'Linhas Editoriais';
        $data['lines'] = $lineModel->getAll();
        $data['categories'] = $this->categories;
        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/editorial_lines', $data);
        echo view('admin/includes/_footer');
    }

    public function editorialLineForm($id = null)
    {
        checkPermission('newsletter');
        $lineModel = new NewsletterEditorialLineModel();
        $magnetModel = new NewsletterLeadMagnetModel();
        $data['title'] = $id ? 'Editar Linha Editorial' : 'Nova Linha Editorial';
        $data['line'] = $id ? $lineModel->getById((int) $id) : null;
        $data['categories'] = $this->categories;
        $data['magnets'] = $magnetModel->getAll(true);
        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/editorial_line_form', $data);
        echo view('admin/includes/_footer');
    }

    public function editorialLineSave()
    {
        checkPermission('newsletter');
        $id = (int) inputPost('id');
        $name = trim((string) inputPost('name'));
        $slug = trim((string) inputPost('slug'));
        if (empty($name) || empty($slug)) {
            setErrorMessage('Nome e slug são obrigatórios.');
            return redirect()->back()->withInput();
        }
        $slug = strtolower(preg_replace('/[^a-z0-9-]+/i', '-', $slug));

        $categoryIds = inputPost('category_ids');
        if (!is_array($categoryIds)) $categoryIds = [];
        $categoryIds = array_values(array_filter(array_map('intval', $categoryIds), fn($v) => $v > 0));

        $sendTimes = trim((string) inputPost('send_times'));
        $sendTimes = preg_split('/[,;\s]+/', $sendTimes);
        $sendTimes = array_values(array_filter($sendTimes, fn($t) => preg_match('/^\d{1,2}:\d{2}$/', $t)));

        $payload = [
            'name' => $name,
            'slug' => $slug,
            'description' => trim((string) inputPost('description')),
            'category_ids' => json_encode($categoryIds),
            'send_times' => json_encode($sendTimes),
            'frequency' => inputPost('frequency') ?: 'daily',
            'posts_per_edition' => max(1, (int) inputPost('posts_per_edition')),
            'lookback_hours' => max(1, (int) inputPost('lookback_hours')),
            'ai_auto_publish' => inputPost('ai_auto_publish') ? 1 : 0,
            'subject_prompt' => trim((string) inputPost('subject_prompt')),
            'body_prompt' => trim((string) inputPost('body_prompt')),
            'cta_text' => trim((string) inputPost('cta_text')),
            'cta_url' => trim((string) inputPost('cta_url')),
            'lead_magnet_id' => inputPost('lead_magnet_id') ? (int) inputPost('lead_magnet_id') : null,
            'enabled' => inputPost('enabled') ? 1 : 0,
        ];

        $lineModel = new NewsletterEditorialLineModel();
        if ($id > 0) {
            $lineModel->updateLine($id, $payload);
            setSuccessMessage('Linha editorial atualizada.');
        } else {
            $newId = $lineModel->createLine($payload);
            setSuccessMessage('Linha editorial criada (id ' . $newId . ').');
        }
        return redirect()->to(adminUrl('newsletter/editorial-lines'));
    }

    public function editorialLineDelete($id)
    {
        checkPermission('newsletter');
        $lineModel = new NewsletterEditorialLineModel();
        $lineModel->deleteLine((int) $id);
        setSuccessMessage('Linha editorial removida.');
        return redirect()->to(adminUrl('newsletter/editorial-lines'));
    }

    public function editorialLineGenerate($id)
    {
        checkPermission('newsletter');
        $lineModel = new NewsletterEditorialLineModel();
        $line = $lineModel->getById((int) $id);
        if (!$line) {
            setErrorMessage('Linha não encontrada.');
            return redirect()->to(adminUrl('newsletter/editorial-lines'));
        }
        $ai = new NewsletterAIService();
        $sender = new NewsletterSenderService();
        $edition = $ai->generateEdition($line);
        if (!empty($edition['error'])) {
            setErrorMessage('Falha ao gerar: ' . $edition['error']);
            return redirect()->to(adminUrl('newsletter/editorial-lines'));
        }
        $sendId = $sender->buildSendFromEdition((int) $line->id, $edition, ['force_status' => 'draft']);
        setSuccessMessage('Edição gerada como rascunho (send id ' . $sendId . ').');
        return redirect()->to(adminUrl('newsletter/queue/view/' . $sendId));
    }

    public function queue()
    {
        checkPermission('newsletter');
        $sendModel = new NewsletterSendModel();
        $status = inputGet('status');
        $data['title'] = 'Fila de Envios';
        $data['sends'] = $sendModel->listForAdmin(100, 0, $status ?: null);
        $data['status'] = $status;
        $data['counts'] = [
            'draft' => $sendModel->countByStatus('draft'),
            'approved' => $sendModel->countByStatus('approved'),
            'sent' => $sendModel->countByStatus('sent'),
            'failed' => $sendModel->countByStatus('failed'),
        ];
        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/queue', $data);
        echo view('admin/includes/_footer');
    }

    public function queueView($id)
    {
        checkPermission('newsletter');
        $sendModel = new NewsletterSendModel();
        $sender = new NewsletterSenderService();
        $send = $sendModel->getById((int) $id);
        if (!$send) {
            setErrorMessage('Envio não encontrado.');
            return redirect()->to(adminUrl('newsletter/queue'));
        }
        $payload = $sender->getPayload((int) $id);
        $data['title'] = 'Envio #' . $send->id;
        $data['send'] = $send;
        $data['payload'] = $payload ?: [];
        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/queue_view', $data);
        echo view('admin/includes/_footer');
    }

    public function queueApprove($id)
    {
        checkPermission('newsletter');
        $sendModel = new NewsletterSendModel();
        $userId = function_exists('user') && user() ? (int) user()->id : null;
        if ($sendModel->approve((int) $id, $userId)) {
            setSuccessMessage('Envio aprovado.');
        } else {
            setErrorMessage('Falha ao aprovar.');
        }
        return redirect()->to(adminUrl('newsletter/queue/view/' . (int) $id));
    }

    public function queueCancel($id)
    {
        checkPermission('newsletter');
        $sendModel = new NewsletterSendModel();
        $sendModel->cancel((int) $id);
        setSuccessMessage('Envio cancelado.');
        return redirect()->to(adminUrl('newsletter/queue'));
    }

    public function queueUpdate($id)
    {
        checkPermission('newsletter');
        $sender = new NewsletterSenderService();
        $payload = $sender->getPayload((int) $id) ?: [];

        $payload['subject'] = trim((string) inputPost('subject'));
        $payload['preheader'] = trim((string) inputPost('preheader'));
        $payload['intro'] = trim((string) inputPost('intro'));
        $payload['cta_text'] = trim((string) inputPost('cta_text'));
        $payload['cta_url'] = trim((string) inputPost('cta_url'));

        $posts = inputPost('posts');
        if (is_array($posts)) {
            $newPosts = [];
            foreach ($posts as $idx => $p) {
                if (!is_array($p) || empty($p['post_id'])) continue;
                $existing = null;
                foreach (($payload['posts'] ?? []) as $orig) {
                    if ((int) ($orig['post_id'] ?? 0) === (int) $p['post_id']) {
                        $existing = $orig;
                        break;
                    }
                }
                $newPosts[] = array_merge($existing ?: [], [
                    'post_id'   => (int) $p['post_id'],
                    'title'     => trim((string) ($p['title'] ?? '')),
                    'summary'   => trim((string) ($p['summary'] ?? '')),
                    'cta_label' => trim((string) ($p['cta_label'] ?? 'Leia mais')),
                ]);
            }
            $payload['posts'] = $newPosts;
        }

        $sender->updatePayload((int) $id, $payload);
        setSuccessMessage('Conteúdo atualizado.');
        return redirect()->to(adminUrl('newsletter/queue/view/' . (int) $id));
    }

    public function queueDispatch($id)
    {
        checkPermission('newsletter');
        $sender = new NewsletterSenderService();
        $result = $sender->dispatch((int) $id);
        if (!empty($result['error'])) {
            setErrorMessage('Erro: ' . $result['error']);
        } else {
            setSuccessMessage('Disparo: enviados=' . ($result['sent'] ?? 0) . ' falhas=' . ($result['failed'] ?? 0) . ' total=' . ($result['total'] ?? 0));
        }
        return redirect()->to(adminUrl('newsletter/queue/view/' . (int) $id));
    }

    // ====== Settings ======
    public function settings()
    {
        checkPermission('newsletter');
        $model = new NewsletterSettingsModel();
        $data['title'] = 'Configurações da Newsletter';
        $data['settings'] = $model->get();
        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/settings', $data);
        echo view('admin/includes/_footer');
    }

    public function settingsSave()
    {
        checkPermission('newsletter');
        $model = new NewsletterSettingsModel();

        $payload = [
            'double_opt_in_enabled'    => inputPost('double_opt_in_enabled') ? 1 : 0,
            'confirmation_subject'     => trim((string) inputPost('confirmation_subject')),
            'confirmation_intro'       => trim((string) inputPost('confirmation_intro')),
            'confirmation_button_text' => trim((string) inputPost('confirmation_button_text')),
            'welcome_subject'          => trim((string) inputPost('welcome_subject')),
            'welcome_intro'            => trim((string) inputPost('welcome_intro')),
            'landing_eyebrow'          => trim((string) inputPost('landing_eyebrow')),
            'landing_headline'         => trim((string) inputPost('landing_headline')),
            'landing_subheadline'      => trim((string) inputPost('landing_subheadline')),
            'landing_cta_text'         => trim((string) inputPost('landing_cta_text')),
            'landing_social_proof'     => trim((string) inputPost('landing_social_proof')),
        ];

        // upload hero image if provided
        $heroFile = $this->request->getFile('landing_hero_image');
        if ($heroFile && $heroFile->isValid() && !$heroFile->hasMoved()) {
            $ext = $heroFile->getExtension();
            if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $name = 'hero_' . generateToken(true) . '.' . $ext;
                if ($heroFile->move(FCPATH . 'uploads/newsletter-magnets/', $name)) {
                    $payload['landing_hero_image'] = 'uploads/newsletter-magnets/' . $name;
                }
            }
        }

        $model->updateSettings($payload);
        setSuccessMessage('Configurações atualizadas.');
        return redirect()->to(adminUrl('newsletter/settings'));
    }

    // ====== Lead Magnets ======
    public function magnets()
    {
        checkPermission('newsletter');
        $model = new NewsletterLeadMagnetModel();
        $data['title'] = 'Lead Magnets';
        $data['magnets'] = $model->getAll();
        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/magnets', $data);
        echo view('admin/includes/_footer');
    }

    public function magnetForm($id = null)
    {
        checkPermission('newsletter');
        $model = new NewsletterLeadMagnetModel();
        $data['title'] = $id ? 'Editar magnet' : 'Novo magnet';
        $data['magnet'] = $id ? $model->getById((int) $id) : null;
        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/magnet_form', $data);
        echo view('admin/includes/_footer');
    }

    public function magnetSave()
    {
        checkPermission('newsletter');
        $id = (int) inputPost('id');
        $title = trim((string) inputPost('title'));
        if (empty($title)) {
            setErrorMessage('Título obrigatório.');
            return redirect()->back()->withInput();
        }
        $slugRaw = inputPost('slug') ?: $title;
        $slug = strtolower(preg_replace('/[^a-z0-9-]+/i', '-', trim((string) $slugRaw)));

        $payload = [
            'title'       => $title,
            'slug'        => $slug,
            'description' => trim((string) inputPost('description')),
            'cta_text'    => trim((string) inputPost('cta_text')) ?: 'Baixar material',
            'active'      => inputPost('active') ? 1 : 0,
        ];

        // upload PDF
        $pdf = $this->request->getFile('file');
        if ($pdf && $pdf->isValid() && !$pdf->hasMoved()) {
            $ext = strtolower($pdf->getExtension() ?: pathinfo($pdf->getName(), PATHINFO_EXTENSION));
            if (in_array($ext, ['pdf', 'epub'], true) || strpos($pdf->getMimeType() ?: '', 'pdf') !== false) {
                $name = 'magnet_' . generateToken(true) . '.' . $ext;
                if ($pdf->move(FCPATH . 'uploads/newsletter-magnets/', $name)) {
                    $payload['file_path'] = 'uploads/newsletter-magnets/' . $name;
                    $payload['mime_type'] = $pdf->getClientMimeType();
                    $payload['file_size'] = filesize(FCPATH . $payload['file_path']);
                }
            } else {
                setErrorMessage('Formato do arquivo não permitido (apenas PDF ou EPUB).');
                return redirect()->back()->withInput();
            }
        }

        // upload cover
        $cover = $this->request->getFile('cover_image');
        if ($cover && $cover->isValid() && !$cover->hasMoved()) {
            $ext = strtolower($cover->getExtension() ?: pathinfo($cover->getName(), PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $name = 'cover_' . generateToken(true) . '.' . $ext;
                if ($cover->move(FCPATH . 'uploads/newsletter-magnets/covers/', $name)) {
                    $payload['cover_image'] = '/uploads/newsletter-magnets/covers/' . $name;
                }
            }
        }

        $model = new NewsletterLeadMagnetModel();
        if ($id > 0) {
            $model->updateMagnet($id, $payload);
            setSuccessMessage('Magnet atualizado.');
        } else {
            $newId = $model->createMagnet($payload);
            setSuccessMessage('Magnet criado (id ' . $newId . ').');
        }
        return redirect()->to(adminUrl('newsletter/magnets'));
    }

    public function magnetDelete($id)
    {
        checkPermission('newsletter');
        $model = new NewsletterLeadMagnetModel();
        $magnet = $model->getById((int) $id);
        if ($magnet) {
            if (!empty($magnet->file_path) && is_file(FCPATH . $magnet->file_path)) {
                @unlink(FCPATH . $magnet->file_path);
            }
            if (!empty($magnet->cover_image) && is_file(FCPATH . ltrim($magnet->cover_image, '/'))) {
                @unlink(FCPATH . ltrim($magnet->cover_image, '/'));
            }
            $model->deleteMagnet((int) $id);
            setSuccessMessage('Magnet removido.');
        }
        return redirect()->to(adminUrl('newsletter/magnets'));
    }

    public function analytics()
    {
        checkPermission('newsletter');
        $db = \Config\Database::connect();
        $data['title'] = 'Newsletter Analytics';

        $data['totals'] = [
            'subscribers'     => $db->table('subscribers')->where('(status IS NULL OR status="active")', null, false)->countAllResults(),
            'sends_30d'       => $db->table('newsletter_sends')->where('sent_at >=', date('Y-m-d H:i:s', time() - 30 * 86400))->countAllResults(),
            'delivered_30d'   => (int) $db->query("SELECT COALESCE(SUM(delivered_count),0) AS x FROM newsletter_sends WHERE sent_at >= ?", [date('Y-m-d H:i:s', time() - 30 * 86400)])->getRow()->x,
            'opens_30d'       => (int) $db->query("SELECT COALESCE(SUM(opens_count),0) AS x FROM newsletter_sends WHERE sent_at >= ?", [date('Y-m-d H:i:s', time() - 30 * 86400)])->getRow()->x,
            'clicks_30d'      => (int) $db->query("SELECT COALESCE(SUM(clicks_count),0) AS x FROM newsletter_sends WHERE sent_at >= ?", [date('Y-m-d H:i:s', time() - 30 * 86400)])->getRow()->x,
        ];

        $data['perLine'] = $db->query(
            "SELECT el.id, el.name, el.slug,
                    COUNT(ns.id) AS sends,
                    COALESCE(SUM(ns.delivered_count),0) AS delivered,
                    COALESCE(SUM(ns.opens_count),0) AS opens,
                    COALESCE(SUM(ns.clicks_count),0) AS clicks
             FROM newsletter_editorial_lines el
             LEFT JOIN newsletter_sends ns ON ns.editorial_line_id = el.id AND ns.sent_at IS NOT NULL
             GROUP BY el.id
             ORDER BY el.name ASC"
        )->getResult();

        $data['recentSends'] = $db->table('newsletter_sends ns')
            ->select('ns.id, ns.subject, ns.sent_at, ns.recipients_count, ns.delivered_count, ns.opens_count, ns.clicks_count, el.name AS line_name')
            ->join('newsletter_editorial_lines el', 'el.id = ns.editorial_line_id', 'left')
            ->where('ns.sent_at IS NOT NULL', null, false)
            ->orderBy('ns.sent_at', 'DESC')->limit(20)->get()->getResult();

        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/analytics', $data);
        echo view('admin/includes/_footer');
    }

    public function crmSync()
    {
        checkPermission('newsletter');
        $syncModel = new \App\Models\NewsletterCrmSyncModel();
        $data['title'] = 'Sincronização CRM';
        $data['lastLeads']   = $syncModel->getLastBySource('leads');
        $data['lastClients'] = $syncModel->getLastBySource('clients');
        $data['history']     = $syncModel->getRecent(30);
        $data['config']      = [
            'leads_endpoint'   => getenv('CRM_NEWSLETTER_LEADS_ENDPOINT') ?: '',
            'clients_endpoint' => getenv('CRM_NEWSLETTER_CLIENTS_ENDPOINT') ?: '',
            'anon_set'         => !empty(getenv('CRM_NEWSLETTER_ANON_KEY')),
            'api_key_set'      => !empty(getenv('CRM_LEAD_API_KEY')),
        ];
        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/crm_sync', $data);
        echo view('admin/includes/_footer');
    }

    public function crmSyncRun()
    {
        checkPermission('newsletter');
        $source = inputPost('source'); // 'leads' | 'clients' | 'all'
        $full = (bool) inputPost('full');
        $userId = function_exists('user') && user() ? (int) user()->id : null;
        $service = new \App\Libraries\CrmSyncService();
        try {
            if ($source === 'leads' || $source === 'clients') {
                $res = $service->syncSource($source, 'manual', $userId, $full);
                setSuccessMessage(sprintf(
                    'Sync %s: %d criados, %d atualizados, %d pulados unsubscribed, %d inválidos.',
                    $source, $res['created_count'], $res['updated_count'], $res['skipped_unsubscribed'], $res['skipped_invalid']
                ));
            } else {
                $resLeads = $service->syncSource('leads', 'manual', $userId, $full);
                $resClients = $service->syncSource('clients', 'manual', $userId, $full);
                setSuccessMessage(sprintf(
                    'Sync leads (%d novos / %d atualizados) e clientes (%d novos / %d atualizados).',
                    $resLeads['created_count'], $resLeads['updated_count'],
                    $resClients['created_count'], $resClients['updated_count']
                ));
            }
        } catch (\Throwable $e) {
            setErrorMessage('Erro fatal: ' . $e->getMessage());
        }
        return redirect()->to(adminUrl('newsletter/crm-sync'));
    }

    public function crmSyncView($id)
    {
        checkPermission('newsletter');
        $syncModel = new \App\Models\NewsletterCrmSyncModel();
        $sync = $syncModel->getById((int) $id);
        if (!$sync) {
            setErrorMessage('Execução não encontrada.');
            return redirect()->to(adminUrl('newsletter/crm-sync'));
        }
        $data['title'] = 'Sync CRM #' . $sync->id;
        $data['sync'] = $sync;
        echo view('admin/includes/_header', $data);
        echo view('admin/newsletter/crm_sync_view', $data);
        echo view('admin/includes/_footer');
    }
}
