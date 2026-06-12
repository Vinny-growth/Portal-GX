<?php

namespace App\Controllers;

use App\Libraries\SeoRankingService;
use App\Models\SeoKeywordModel;
use App\Models\SeoRankingModel;

class SeoAnalysisController extends BaseAdminController
{
    protected $keywordModel;
    protected $rankingModel;
    protected $service;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->keywordModel = new SeoKeywordModel();
        $this->rankingModel = new SeoRankingModel();
        $this->service      = new SeoRankingService();
    }

    /** Overview: KPIs + average-position evolution chart + integration status. */
    public function index()
    {
        checkPermission('seo_analysis');

        $days = (int) (inputGet('days') ?: 30);
        if (!in_array($days, [7, 30, 90], true)) {
            $days = 30;
        }

        $data['title']      = 'Análise de SEO';
        $data['totalKeywords'] = $this->keywordModel->where('is_active', 1)->countAllResults();
        $data['overview']   = $this->rankingModel->overview();
        $data['evolution']  = $this->rankingModel->avgPositionByDay($days);
        $data['providers']  = $this->service->providersStatus();
        $data['topMovers']  = $this->keywordModel->getWithMetrics(7);
        $data['days']       = $days;

        echo view('admin/includes/_header', $data);
        echo view('admin/seo_analysis/index', $data);
        echo view('admin/includes/_footer');
    }

    /** Keyword list with metrics + add form. */
    public function keywords()
    {
        checkPermission('seo_analysis');

        $data['title']     = 'Palavras-chave · SEO';
        $data['keywords']  = $this->keywordModel->getWithMetrics(7);
        $data['providers'] = $this->service->providersStatus();
        $data['categories']= $this->loadCategories();

        echo view('admin/includes/_header', $data);
        echo view('admin/seo_analysis/keywords', $data);
        echo view('admin/includes/_footer');
    }

    /** Detail of one keyword: evolution chart + history table. */
    public function keywordDetail($id = 0)
    {
        checkPermission('seo_analysis');
        $id = (int) $id;

        $keyword = $this->keywordModel->find($id);
        if (!$keyword) {
            setErrorMessage('Palavra-chave não encontrada.', false);
            return redirect()->to(adminUrl('seo-analysis/keywords'));
        }

        $data['title']    = 'Evolução · ' . $keyword->keyword;
        $data['keyword']  = $keyword;
        $data['series']   = $this->rankingModel->seriesForKeyword($id, 90);

        echo view('admin/includes/_header', $data);
        echo view('admin/seo_analysis/keyword_detail', $data);
        echo view('admin/includes/_footer');
    }

    public function addKeywordPost()
    {
        checkPermission('seo_analysis');

        $keyword = cleanStr(inputPost('keyword'));
        if (empty($keyword)) {
            setErrorMessage('Informe a palavra-chave.', false);
            return redirect()->to(adminUrl('seo-analysis/keywords'));
        }

        $this->keywordModel->insert([
            'keyword'     => $keyword,
            'target_url'  => cleanStr(inputPost('target_url')) ?: null,
            'category_id' => clrNum(inputPost('category_id')) ?: null,
            'locale'      => cleanStr(inputPost('locale')) ?: 'pt-BR',
            'country'     => cleanStr(inputPost('country')) ?: 'bra',
            'device'      => cleanStr(inputPost('device')) ?: 'desktop',
            'source'      => cleanStr(inputPost('source')) ?: 'gsc',
            'notes'       => cleanStr(inputPost('notes')) ?: null,
            'is_active'   => 1,
        ]);

        setSuccessMessage('Palavra-chave adicionada ao monitoramento.', false);
        return redirect()->to(adminUrl('seo-analysis/keywords'));
    }

    public function updateKeywordPost()
    {
        checkPermission('seo_analysis');
        $id = clrNum(inputPost('id'));
        if ($id <= 0 || !$this->keywordModel->find($id)) {
            setErrorMessage('Palavra-chave inválida.', false);
            return redirect()->to(adminUrl('seo-analysis/keywords'));
        }

        $this->keywordModel->update($id, [
            'keyword'     => cleanStr(inputPost('keyword')),
            'target_url'  => cleanStr(inputPost('target_url')) ?: null,
            'category_id' => clrNum(inputPost('category_id')) ?: null,
            'notes'       => cleanStr(inputPost('notes')) ?: null,
        ]);

        setSuccessMessage('Palavra-chave atualizada.', false);
        return redirect()->to(adminUrl('seo-analysis/keywords'));
    }

    public function toggleKeywordPost()
    {
        checkPermission('seo_analysis');
        $id = clrNum(inputPost('id'));
        $kw = $id > 0 ? $this->keywordModel->find($id) : null;
        if ($kw) {
            $this->keywordModel->update($id, ['is_active' => $kw->is_active ? 0 : 1]);
            setSuccessMessage('Status atualizado.', false);
        }
        return redirect()->to(adminUrl('seo-analysis/keywords'));
    }

    public function deleteKeywordPost()
    {
        checkPermission('seo_analysis');
        $id = clrNum(inputPost('id'));
        if ($id > 0) {
            $this->keywordModel->delete($id);
            $this->rankingModel->where('keyword_id', $id)->delete();
            setSuccessMessage('Palavra-chave removida.', false);
        }
        return redirect()->to(adminUrl('seo-analysis/keywords'));
    }

    /** Pull keywords used across published posts + calendar tags into the tracker. */
    public function syncKeywordsPost()
    {
        checkPermission('seo_analysis');
        $res = $this->service->syncFromContent();
        setSuccessMessage(
            "Sincronização concluída: {$res['added']} novas palavras-chave adicionadas do conteúdo, "
            . "{$res['updated']} atualizadas ({$res['scanned']} distintas encontradas).",
            false
        );
        return redirect()->to(adminUrl('seo-analysis/keywords'));
    }

    /** Manual trigger of a collection run (same code the cron uses). */
    public function fetchNowPost()
    {
        checkPermission('seo_analysis');

        $providers = $this->service->providersStatus();
        if (!$providers['gsc'] && !$providers['serp']) {
            setErrorMessage('Configure o Google Search Console ou o openserp antes de coletar (veja o painel de integrações).', false);
            return redirect()->to(adminUrl('seo-analysis'));
        }

        $res = $this->service->fetchAll();
        $msg = "Coleta concluída: {$res['gsc']} via GSC, {$res['serp']} via openserp, {$res['skipped']} sem dados.";
        if (!empty($res['errors'])) {
            $msg .= ' Avisos: ' . implode(' | ', array_slice($res['errors'], 0, 3));
        }
        setSuccessMessage($msg, false);
        return redirect()->to(adminUrl('seo-analysis'));
    }

    private function loadCategories(): array
    {
        try {
            $model = new \App\Models\CategoryModel();
            $rows = $model->where('category_status', 1)->orderBy('name', 'ASC')->findAll();
            // CategoryModel inherits the framework's default array return type; the
            // view expects objects, so normalize here.
            return array_map(static function ($c) {
                return is_array($c) ? (object) $c : $c;
            }, $rows);
        } catch (\Throwable $e) {
            return [];
        }
    }
}
