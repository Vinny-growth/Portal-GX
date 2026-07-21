<?php

namespace App\Controllers;

use App\Libraries\ContentAIService;
use App\Libraries\TrendsFetcher;
use App\Models\ContentAISettingsModel;
use App\Models\ContentCalendarModel;
use App\Models\PopularPostsControlModel;
use App\Models\TrendItemModel;
use App\Models\TrendSourceHealthModel;
use App\Models\XPulseSnapshotModel;

class ContentAIController extends BaseAdminController
{
    protected $settingsModel;
    protected $calendarModel;
    protected $trendModel;
    protected $popularControlModel;
    protected $service;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->settingsModel = new ContentAISettingsModel();
        $this->calendarModel = new ContentCalendarModel();
        $this->trendModel = new TrendItemModel();
        $this->popularControlModel = new PopularPostsControlModel();
        $this->service = new ContentAIService();
    }

    public function index()
    {
        checkPermission('add_post');
        $req = service('request');
        $page = max(1, (int) $req->getGet('page'));
        $perPage = 50;
        $filters = [
            'status'      => cleanStr((string) $req->getGet('status')),
            'source_type' => cleanStr((string) $req->getGet('source_type')),
            'date'        => cleanStr((string) $req->getGet('date')),
            'q'           => trim((string) $req->getGet('q')),
        ];

        $paginated = $this->calendarModel->getPaginated($page, $perPage, $filters);

        $data['title'] = 'Central de Conteudos IA';
        $data['settings'] = $this->settingsModel->getSettings();
        $data['calendarItems'] = $paginated['items'];
        $data['calendarPagination'] = $paginated;
        $data['calendarFilters'] = $filters;
        $data['calendarStatusCounts'] = $this->calendarModel->getStatusCounts();
        $data['calendarSourceCounts'] = $this->calendarModel->getSourceCounts();
        $data['trendItems'] = $this->trendModel->getLatest(200);
        try {
            $data['sourceHealth'] = (new TrendSourceHealthModel())->getHealthSummary(24);
        } catch (\Throwable $e) {
            log_message('error', 'ContentAI dashboard: falha ao carregar sourceHealth — ' . $e->getMessage());
            $data['sourceHealth'] = [];
        }
        try {
            $data['xPulseItems'] = (new XPulseSnapshotModel())->getActive(24, 15);
        } catch (\Throwable $e) {
            log_message('error', 'ContentAI dashboard: falha ao carregar xPulseItems — ' . $e->getMessage());
            $data['xPulseItems'] = [];
        }
        try {
            $data['popularControl'] = $this->popularControlModel->getForAdmin(100);
        } catch (\Throwable $e) {
            log_message('error', 'ContentAI dashboard: falha ao carregar popularControl — ' . $e->getMessage());
            $data['popularControl'] = [];
        }
        $data['categories'] = $this->categories;
        $data['panelSettings'] = panelSettings();

        echo view('admin/includes/_header', $data);
        echo view('admin/content_ai/index', $data);
        echo view('admin/includes/_footer');
    }

    public function saveSettingsPost()
    {
        checkPermission('add_post');
        $run1 = cleanStr(inputPost('run_time_1'));
        $run2 = cleanStr(inputPost('run_time_2'));
        $run3 = cleanStr(inputPost('run_time_3'));
        if (!empty($run1) && strlen($run1) === 5) { $run1 .= ':00'; }
        if (!empty($run2) && strlen($run2) === 5) { $run2 .= ':00'; }
        if (!empty($run3) && strlen($run3) === 5) { $run3 .= ':00'; }
        $allowedCategories = inputPost('allowed_category_ids');
        $categoryRulesJson = trim((string) inputPost('category_rules_json'));
        $categoryGuidelinesJson = trim((string) inputPost('category_guidelines_json'));
        $voice = trim((string) inputPost('voice_guidelines'));
        $seo = trim((string) inputPost('seo_guidelines'));
        $promptTemplate = trim((string) inputPost('prompt_template'));
        $imageGuidelines = trim((string) inputPost('image_guidelines'));
        $imagePromptTemplate = trim((string) inputPost('image_prompt_template'));
        $editorPrompt = trim((string) inputPost('editor_prompt'));
        $popularEditorPrompt = trim((string) inputPost('popular_editor_prompt'));
        // Trend keywords: 3 textareas (one entry per line) -> JSON
        $kwPhrases       = $this->splitKeywordTextarea((string) inputPost('trend_keywords_phrases'));
        $kwWords         = $this->splitKeywordTextarea((string) inputPost('trend_keywords_words'));
        $kwContextWords  = $this->splitKeywordTextarea((string) inputPost('trend_keywords_context'));
        $trendKeywordsJson = json_encode([
            'phrases'       => $kwPhrases,
            'words'         => $kwWords,
            'context_words' => $kwContextWords,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $popularMetric = cleanStr(inputPost('popular_metric'));
        if (!in_array($popularMetric, ['pageviews', 'engagement', 'mixed'], true)) {
            $popularMetric = 'mixed';
        }
        $popularWindow = (int) clrNum(inputPost('popular_window_days'));
        if (!in_array($popularWindow, [1, 3, 7, 30], true)) {
            $popularWindow = 7;
        }
        // Build topic weights JSON from structured inputs
        $topicWeightsRaw = inputPost('topic_weights');
        $topicWeightsJson = null;
        if (!empty($topicWeightsRaw) && is_array($topicWeightsRaw)) {
            $weights = [];
            foreach ($topicWeightsRaw as $topic => $pct) {
                $pct = max(0, min(100, (int) $pct));
                if ($pct > 0) {
                    $weights[cleanStr($topic)] = $pct;
                }
            }
            if (!empty($weights)) {
                $topicWeightsJson = json_encode($weights);
            }
        }
        $data = [
            'auto_publish' => inputPost('auto_publish') ? 1 : 0,
            'posts_per_day' => clrNum(inputPost('posts_per_day')),
            'run_time_1' => $run1 ?: null,
            'run_time_2' => $run2 ?: null,
            'run_time_3' => $run3 ?: null,
            'lang_id' => clrNum(inputPost('lang_id')),
            'default_tone' => cleanStr(inputPost('default_tone')),
            'default_length' => cleanStr(inputPost('default_length')),
            'allowed_category_ids' => !empty($allowedCategories) ? json_encode(array_map('intval', $allowedCategories)) : null,
            'auto_add_trends' => inputPost('auto_add_trends') ? 1 : 0,
            'trends_per_day' => clrNum(inputPost('trends_per_day')),
            'popular_enabled' => inputPost('popular_enabled') ? 1 : 0,
            'popular_posts_per_day' => max(0, (int) clrNum(inputPost('popular_posts_per_day'))),
            'popular_window_days' => $popularWindow,
            'popular_metric' => $popularMetric,
            'popular_min_pageviews' => max(0, (int) clrNum(inputPost('popular_min_pageviews'))),
            'popular_max_derivations' => max(0, (int) clrNum(inputPost('popular_max_derivations'))),
            'popular_cooldown_days' => max(0, (int) clrNum(inputPost('popular_cooldown_days'))),
            'popular_diversity_enabled' => inputPost('popular_diversity_enabled') ? 1 : 0,
            'popular_per_category_cap' => max(0, (int) clrNum(inputPost('popular_per_category_cap'))),
            'popular_editor_prompt' => $popularEditorPrompt,
            'trend_keywords_json' => $trendKeywordsJson,
            'x_pulse_enabled' => inputPost('x_pulse_enabled') ? 1 : 0,
            'x_window_hours' => max(1, min(72, (int) clrNum(inputPost('x_window_hours')))),
            'x_themes_per_day' => max(1, min(30, (int) clrNum(inputPost('x_themes_per_day')))),
            'x_min_mentions' => max(0, (int) clrNum(inputPost('x_min_mentions'))),
            'x_grok_model' => cleanStr(inputPost('x_grok_model')) ?: 'grok-4-fast',
            'x_pulse_prompt' => trim((string) inputPost('x_pulse_prompt')),
            'x_seed_enabled' => inputPost('x_seed_enabled') ? 1 : 0,
            'x_seed_per_day' => max(0, (int) clrNum(inputPost('x_seed_per_day'))),
            'default_user_id' => clrNum(inputPost('default_user_id')) ?: 1,
            'voice_guidelines' => $voice,
            'seo_guidelines' => $seo,
            'prompt_template' => $promptTemplate,
            'length_short_words' => clrNum(inputPost('length_short_words')) ?: 900,
            'length_medium_words' => clrNum(inputPost('length_medium_words')) ?: 1400,
            'length_long_words' => clrNum(inputPost('length_long_words')) ?: 2000,
            'category_rules_json' => !empty($categoryRulesJson) ? $categoryRulesJson : null,
            'category_guidelines_json' => !empty($categoryGuidelinesJson) ? $categoryGuidelinesJson : null,
            'topic_weights_json' => $topicWeightsJson,
            'editor_prompt' => $editorPrompt,
            'image_guidelines' => $imageGuidelines,
            'image_prompt_template' => $imagePromptTemplate,
        ];
        if ($this->settingsModel->saveSettings($data)) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('content-ai'));
    }

    public function addCalendarItemPost()
    {
        checkPermission('add_post');
        $now = date('Y-m-d H:i:s');
        $publishAt = cleanStr(inputPost('publish_at'));
        $generateAt = cleanStr(inputPost('generate_at'));
        if (!empty($publishAt)) {
            $publishAt = str_replace('T', ' ', $publishAt);
            if (strlen($publishAt) === 16) { $publishAt .= ':00'; }
        }
        if (!empty($generateAt)) {
            $generateAt = str_replace('T', ' ', $generateAt);
            if (strlen($generateAt) === 16) { $generateAt .= ':00'; }
        }
        $data = [
            'title' => cleanStr(inputPost('title')),
            'instructions' => inputPost('instructions'),
            'category_id' => clrNum(inputPost('category_id')) ?: null,
            'lang_id' => clrNum(inputPost('lang_id')) ?: $this->activeLang->id,
            'user_id' => user()->id,
            'tone' => cleanStr(inputPost('tone')),
            'length' => cleanStr(inputPost('length')),
            'tags' => inputPost('tags'),
            'publish_at' => $publishAt ?: null,
            'generate_at' => $generateAt ?: null,
            'status' => 'planned',
            'source_type' => 'manual',
            'created_at' => $now,
            'updated_at' => $now,
        ];
        if (!empty($data['title'])) {
            $this->calendarModel->builder()->insert($data);
            setSuccessMessage("msg_added");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('content-ai'));
    }

    public function deleteCalendarItemPost()
    {
        checkPermission('add_post');
        $id = clrNum(inputPost('id'));
        if ($id > 0) {
            $this->calendarModel->builder()->where('id', $id)->delete();
            setSuccessMessage("msg_deleted");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('content-ai'));
    }

    public function approveCalendarItemPost()
    {
        checkPermission('add_post');
        $id = clrNum(inputPost('id'));
        if ($id > 0) {
            $item = $this->calendarModel->builder()->where('id', $id)->get()->getFirstRow();
            if (!empty($item) && !empty($item->post_id)) {
                $now = date('Y-m-d H:i:s');
                $publishAt = !empty($item->publish_at) ? $item->publish_at : $now;
                $isFuture = strtotime($publishAt) > strtotime($now);
                $this->calendarModel->db->table('posts')->where('id', $item->post_id)->update([
                    'status' => 1,
                    'is_scheduled' => $isFuture ? 1 : 0,
                    'created_at' => $publishAt,
                ]);
                $this->calendarModel->markStatus($id, 'generated');
                setSuccessMessage("msg_updated");
            } else {
                setErrorMessage("msg_error");
            }
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('content-ai'));
    }

    public function runNowPost()
    {
        checkPermission('add_post');
        $res = $this->service->processQueue(5);
        if (!empty($res['count'])) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('content-ai'));
    }

    public function retryCalendarItemPost()
    {
        checkPermission('add_post');
        $id = clrNum(inputPost('id'));
        if ($id <= 0) {
            setErrorMessage("msg_error");
            return redirect()->to(adminUrl('content-ai'));
        }
        $item = $this->calendarModel->builder()->where('id', $id)->get()->getFirstRow();
        if (empty($item)) {
            setErrorMessage("msg_error");
            return redirect()->to(adminUrl('content-ai'));
        }
        // Retry só faz sentido em itens que ainda não foram concluídos com sucesso.
        if (in_array($item->status, ['generated', 'needs_review'], true) && !empty($item->post_id)) {
            setErrorMessage("Este item já gerou o post #" . (int) $item->post_id . ". Exclua o post primeiro se quiser regerar.");
            return redirect()->to(adminUrl('content-ai'));
        }
        $this->calendarModel->markStatus($id, 'queued', [
            'post_id'     => null,
            'generate_at' => date('Y-m-d H:i:s'),
        ]);
        $res = $this->service->processQueue(1);
        if (!empty($res['count'])) {
            setSuccessMessage("Artigo regenerado com sucesso.");
        } elseif (!empty($res['errors'])) {
            setErrorMessage("Falhou novamente: " . mb_substr((string) $res['errors'][0], 0, 200));
        } else {
            setSuccessMessage("Item recolocado na fila — a próxima execução do cron irá processar.");
        }
        return redirect()->to(adminUrl('content-ai'));
    }

    public function fetchTrendsPost()
    {
        checkPermission('add_post');
        $fetcher = new TrendsFetcher();
        $items = $fetcher->fetchAll('BR');
        $now = date('Y-m-d H:i:s');
        $count = 0;
        $settings = $this->settingsModel->getSettings();

        if (!empty($items)) {
            foreach ($items as $item) {
                $title = $item['title'] ?? '';
                if (empty($title)) {
                    continue;
                }
                $source = $item['source'] ?? 'google_trends';
                if (!$this->service->isTrendRelevant($title, $settings)) {
                    continue;
                }
                $data = [
                    'title' => $title,
                    'title_hash' => md5(mb_strtolower($title)),
                    'semantic_hash' => \App\Libraries\TrendNormalizer::semanticHash($title),
                    'source_url' => $item['url'] ?? '',
                    'source' => $source,
                    'source_authority' => \App\Libraries\TrendNormalizer::sourceAuthority($source),
                    'score' => 0,
                    'lang_id' => $this->activeLang->id,
                    'fetched_at' => $now,
                ];
                if ($this->trendModel->upsertItem($data)) {
                    $count++;
                }
            }
        }
        if ($count > 0) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('content-ai'));
    }

    public function updateTrendFlagsPost()
    {
        checkPermission('add_post');
        $ids = inputPost('trend_ids');
        $mode = cleanStr(inputPost('mode'));
        if (!empty($ids) && in_array($mode, ['select', 'auto_add'], true)) {
            $field = $mode === 'select' ? 'selected' : 'auto_add';
            foreach ($ids as $id) {
                $this->trendModel->builder()->where('id', clrNum($id))->update([$field => 1]);
            }
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('content-ai'));
    }

    public function runXPulseNowPost()
    {
        checkPermission('add_post');
        $analyzer = new \App\Libraries\XPulseAnalyzer();
        $res = $analyzer->run();
        if (!empty($res['skipped'])) {
            setErrorMessage($res['reason'] ?? 'X Pulse pulado.');
        } elseif (!empty($res['error'])) {
            setErrorMessage('X Pulse erro: ' . $res['error']);
        } else {
            setSuccessMessage('X Pulse: ' . (int) $res['snapshot_count'] . ' temas persistidos.');
        }
        return redirect()->to(adminUrl('content-ai'));
    }

    /** Exclui um post da esteira de populares (blocklist manual). */
    public function blockPopularPostPost()
    {
        checkPermission('add_post');
        $postId = (int) clrNum(inputPost('post_id'));
        $title = trim((string) inputPost('title'));
        if ($postId > 0 && $this->popularControlModel->block($postId, 'manual', $title !== '' ? $title : null)) {
            setSuccessMessage('Post removido dos populares (blocklist).');
        } else {
            setErrorMessage('msg_error');
        }
        return redirect()->to(adminUrl('content-ai') . '#popular-control');
    }

    /** Reabilita um post excluído (remove da blocklist). */
    public function unblockPopularPostPost()
    {
        checkPermission('add_post');
        $postId = (int) clrNum(inputPost('post_id'));
        if ($postId > 0 && $this->popularControlModel->unblock($postId)) {
            setSuccessMessage('Post reabilitado nos populares.');
        } else {
            setErrorMessage('msg_error');
        }
        return redirect()->to(adminUrl('content-ai') . '#popular-control');
    }

    protected function splitKeywordTextarea(string $raw): array
    {
        if (trim($raw) === '') {
            return [];
        }
        $lines = preg_split('/[\r\n]+/', $raw);
        $out = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }
            $out[] = mb_strtolower($line);
        }
        return array_values(array_unique($out));
    }

    public function addSelectedTrendsToCalendarPost()
    {
        checkPermission('add_post');
        $ids = inputPost('trend_ids');
        if (empty($ids)) {
            setErrorMessage("msg_error");
            return redirect()->to(adminUrl('content-ai'));
        }
        $settings = $this->settingsModel->getSettings();
        $tz = new \DateTimeZone(app_timezone());
        $now = new \DateTimeImmutable('now', $tz);
        $slots = $this->service->buildPublishSlots($settings, count($ids), $now);
        $created = 0;
        foreach ($ids as $idx => $id) {
            $trend = $this->trendModel->builder()->where('id', clrNum($id))->get()->getFirstRow();
            if (empty($trend) || !empty($trend->used)) {
                continue;
            }
            $instructions = 'Escreva sobre esta tendencia do momento: "' . $trend->title . '". '
                . 'Aborde o tema dentro do contexto de cambio, credito, consorcio, investimentos ou economia brasileira. '
                . 'Foque no impacto financeiro e nas implicacoes para empresas e investidores brasileiros.';
            $data = [
                'title' => $trend->title,
                'instructions' => $instructions,
                'category_id' => $this->service->selectCategoryId($trend, $settings),
                'lang_id' => $settings->lang_id,
                'user_id' => user()->id,
                'tone' => $settings->default_tone,
                'length' => $settings->default_length,
                'publish_at' => $slots[$idx] ?? $now->format('Y-m-d H:i:s'),
                'generate_at' => $now->format('Y-m-d H:i:s'),
                'status' => 'queued',
                'source_type' => 'trend',
                'source_url' => $trend->source_url,
                'created_at' => $now->format('Y-m-d H:i:s'),
                'updated_at' => $now->format('Y-m-d H:i:s'),
            ];
            if ($this->calendarModel->builder()->insert($data)) {
                $created++;
                $this->trendModel->builder()->where('id', $trend->id)->update(['used' => 1]);
            }
        }
        if ($created > 0) {
            setSuccessMessage("msg_updated");
        } else {
            setErrorMessage("msg_error");
        }
        return redirect()->to(adminUrl('content-ai'));
    }
}
