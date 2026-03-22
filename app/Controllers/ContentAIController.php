<?php

namespace App\Controllers;

use App\Libraries\ContentAIService;
use App\Libraries\TrendsFetcher;
use App\Models\ContentAISettingsModel;
use App\Models\ContentCalendarModel;
use App\Models\TrendItemModel;

class ContentAIController extends BaseAdminController
{
    protected $settingsModel;
    protected $calendarModel;
    protected $trendModel;
    protected $service;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->settingsModel = new ContentAISettingsModel();
        $this->calendarModel = new ContentCalendarModel();
        $this->trendModel = new TrendItemModel();
        $this->service = new ContentAIService();
    }

    public function index()
    {
        checkPermission('add_post');
        $data['title'] = 'Central de Conteudos IA';
        $data['settings'] = $this->settingsModel->getSettings();
        $data['calendarItems'] = $this->calendarModel->getUpcoming(200);
        $data['trendItems'] = $this->trendModel->getLatest(200);
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
            'default_user_id' => clrNum(inputPost('default_user_id')) ?: 1,
            'voice_guidelines' => $voice,
            'seo_guidelines' => $seo,
            'prompt_template' => $promptTemplate,
            'length_short_words' => clrNum(inputPost('length_short_words')) ?: 900,
            'length_medium_words' => clrNum(inputPost('length_medium_words')) ?: 1400,
            'length_long_words' => clrNum(inputPost('length_long_words')) ?: 2000,
            'category_rules_json' => !empty($categoryRulesJson) ? $categoryRulesJson : null,
            'category_guidelines_json' => !empty($categoryGuidelinesJson) ? $categoryGuidelinesJson : null,
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

    public function fetchTrendsPost()
    {
        checkPermission('add_post');
        $fetcher = new TrendsFetcher();
        $items = $fetcher->fetchDailyRss('BR');
        $now = date('Y-m-d H:i:s');
        $count = 0;
        if (!empty($items)) {
            foreach ($items as $item) {
                $title = $item['title'] ?? '';
                if (empty($title)) {
                    continue;
                }
                $data = [
                    'title' => $title,
                    'title_hash' => md5(mb_strtolower($title)),
                    'source_url' => $item['url'] ?? '',
                    'source' => 'google_trends',
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

    public function addSelectedTrendsToCalendarPost()
    {
        checkPermission('add_post');
        $ids = inputPost('trend_ids');
        if (empty($ids)) {
            setErrorMessage("msg_error");
            return redirect()->to(adminUrl('content-ai'));
        }
        $settings = $this->settingsModel->getSettings();
        $now = new \DateTimeImmutable('now');
        $slots = $this->service->buildPublishSlots($settings, count($ids), $now);
        $created = 0;
        foreach ($ids as $idx => $id) {
            $trend = $this->trendModel->builder()->where('id', clrNum($id))->get()->getFirstRow();
            if (empty($trend) || !empty($trend->used)) {
                continue;
            }
            $data = [
                'title' => $trend->title,
                'instructions' => 'Use a tendencia selecionada como tema principal.',
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
