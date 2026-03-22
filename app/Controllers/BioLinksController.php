<?php

namespace App\Controllers;

use App\Models\BioLinksModel;
use App\Models\SettingsModel;

class BioLinksController extends BaseController
{
    protected $bioLinksModel;
    public $settingsModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->bioLinksModel = new BioLinksModel();
        $this->settingsModel = new SettingsModel();
    }

    public function index()
    {
        $appName = $this->generalSettings->site_title ?? 'GX Capital';
        $bioSettings = $this->settingsModel->getBioSettings();
        
        $data = [
            'title' => $appName . ' - Bio Links',
            'description' => 'Links importantes do ' . $appName,
            'keywords' => 'bio, links, ' . $appName,
            'bioLinks' => $this->bioLinksModel->getBioLinks(),
            'bioDescription' => $bioSettings->bio_description ?? 'Assessoria em investimentos, gestão de patrimônio e consultoria financeira personalizada para o seu sucesso.',
        ];

        return view('bio_links/simple', $data);
    }

    public function click($id)
    {
        $link = $this->bioLinksModel->getBioLink($id);
        
        if (!$link || !$link['is_active']) {
            return redirect()->to('/bio');
        }

        // Incrementa contador de cliques
        $this->bioLinksModel->incrementClickCount($id);

        // Redireciona para o link
        return redirect()->to($link['url']);
    }

    // Métodos administrativos
    public function admin()
    {
        if (!isAdmin()) {
            return redirect()->to(base_url());
        }

        // Buscar configurações da bio
        $bioSettings = $this->settingsModel->getBioSettings();

        $data = [
            'title' => 'Bio Links - Administração',
            'bioLinks' => $this->bioLinksModel->getAllBioLinks(),
            'stats' => $this->bioLinksModel->getBioLinksStats(),
            'bioSettings' => $bioSettings,
            'baseAIWriter' => aiWriter(),
        ];

        return view('admin/bio_links/index', $data);
    }

    public function adminAdd()
    {
        if (!isAdmin()) {
            return redirect()->to(base_url());
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'title' => $this->request->getPost('title'),
                'url' => $this->request->getPost('url'),
                'icon' => $this->request->getPost('icon'),
                'button_color' => $this->request->getPost('button_color') ?: '#007bff',
                'text_color' => $this->request->getPost('text_color') ?: '#ffffff',
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            ];

            if ($this->bioLinksModel->addBioLink($data)) {
                $this->session->setFlashdata('success', 'Link adicionado com sucesso!');
            } else {
                $this->session->setFlashdata('error', 'Erro ao adicionar link.');
            }

            return redirect()->to(adminUrl('bio-links'));
        }

        $data = [
            'title' => 'Adicionar Bio Link',
            'baseAIWriter' => aiWriter(),
        ];

        return view('admin/bio_links/add', $data);
    }

    public function adminEdit($id)
    {
        if (!isAdmin()) {
            return redirect()->to(base_url());
        }

        $link = $this->bioLinksModel->getBioLink($id);
        if (!$link) {
            $this->session->setFlashdata('error', 'Link não encontrado.');
            return redirect()->to(adminUrl('bio-links'));
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'title' => $this->request->getPost('title'),
                'url' => $this->request->getPost('url'),
                'icon' => $this->request->getPost('icon'),
                'button_color' => $this->request->getPost('button_color'),
                'text_color' => $this->request->getPost('text_color'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            ];

            if ($this->bioLinksModel->updateBioLink($id, $data)) {
                $this->session->setFlashdata('success', 'Link atualizado com sucesso!');
            } else {
                $this->session->setFlashdata('error', 'Erro ao atualizar link.');
            }

            return redirect()->to(adminUrl('bio-links'));
        }

        $data = [
            'title' => 'Editar Bio Link',
            'link' => $link,
            'baseAIWriter' => aiWriter(),
        ];

        return view('admin/bio_links/edit', $data);
    }

    public function adminDelete($id)
    {
        if (!isAdmin()) {
            if ($this->request->getMethod() === 'POST') {
                return $this->response->setJSON(['success' => false, 'message' => 'Acesso negado']);
            }
            return redirect()->to(base_url());
        }

        if ($this->bioLinksModel->deleteBioLink($id)) {
            $message = 'Link removido com sucesso!';
            if ($this->request->getMethod() === 'POST') {
                return $this->response->setJSON(['success' => true, 'message' => $message]);
            }
            $this->session->setFlashdata('success', $message);
        } else {
            $message = 'Erro ao remover link.';
            if ($this->request->getMethod() === 'POST') {
                return $this->response->setJSON(['success' => false, 'message' => $message]);
            }
            $this->session->setFlashdata('error', $message);
        }

        return redirect()->to(adminUrl('bio-links'));
    }

    public function adminToggle($id)
    {
        if (!isAdmin()) {
            return redirect()->to(base_url());
        }

        if ($this->bioLinksModel->toggleActive($id)) {
            $this->session->setFlashdata('success', 'Status do link alterado com sucesso!');
        } else {
            $this->session->setFlashdata('error', 'Erro ao alterar status do link.');
        }

        return redirect()->to(adminUrl('bio-links'));
    }

    public function adminUpdateOrder()
    {
        if (!isAdmin()) {
            return $this->response->setJSON(['success' => false]);
        }

        $orders = $this->request->getJSON(true)['orders'] ?? [];
        
        if (!empty($orders)) {
            $this->bioLinksModel->updateDisplayOrders($orders);
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false]);
    }

    public function updateBioSettings()
    {
        if (!isAdmin()) {
            return redirect()->to(base_url());
        }

        if ($this->request->getMethod() === 'POST') {
            $bioDescription = $this->request->getPost('bio_description');
            $langId = $this->activeLang->id;

            if ($this->settingsModel->updateBioSettings($langId, $bioDescription)) {
                $this->session->setFlashdata('success', 'Configurações da bio atualizadas com sucesso!');
            } else {
                $this->session->setFlashdata('error', 'Erro ao atualizar configurações da bio.');
            }
        }

        return redirect()->to(adminUrl('bio-links'));
    }
}