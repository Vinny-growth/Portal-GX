<?php

namespace App\Libraries;

use App\Helpers\OpenAIImageHelper;
use App\Models\ContentAISettingsModel;
use App\Models\ContentCalendarModel;
use App\Models\ContentRunModel;
use App\Models\DashboardModel;
use App\Models\PopularPostsSnapshotModel;
use App\Models\TrendItemModel;
use App\Models\UploadModel;
use App\Models\XPulseSnapshotModel;

class ContentAIService
{
    protected $settingsModel;
    protected $calendarModel;
    protected $runModel;
    protected $trendModel;
    protected $popularModel;
    protected $dashboardModel;
    protected $xPulseModel;
    protected $imageHelper;

    public function __construct()
    {
        $this->settingsModel = new ContentAISettingsModel();
        $this->calendarModel = new ContentCalendarModel();
        $this->runModel = new ContentRunModel();
        $this->trendModel = new TrendItemModel();
        $this->popularModel = new PopularPostsSnapshotModel();
        $this->dashboardModel = new DashboardModel();
        try {
            $this->xPulseModel = new XPulseSnapshotModel();
        } catch (\Throwable $e) {
            // X Pulse table may not be migrated yet on older deploys
            $this->xPulseModel = null;
        }
        $this->imageHelper = new OpenAIImageHelper();
    }

    public function runScheduled(): array
    {
        $settings = $this->settingsModel->getSettings();
        $tz = new \DateTimeZone(app_timezone());
        $now = new \DateTimeImmutable('now', $tz);
        $dueSlots = $this->getDueSlots($settings, $now);
        $result = [
            'slots' => $dueSlots,
            'planned' => 0,
            'planned_popular' => 0,
            'generated' => 0,
            'errors' => [],
        ];
        foreach ($dueSlots as $slot) {
            $planned = $this->planDailyFromTrends($settings, $now);
            $result['planned'] += $planned;
            $plannedPopular = $this->planDailyFromPopular($settings, $now);
            $result['planned_popular'] += $plannedPopular;
            $this->settingsModel->updateLastRun($slot, $now->format('Y-m-d H:i:s'));
        }
        $generated = $this->processQueue();
        $result['generated'] = $generated['count'];
        if (!empty($generated['errors'])) {
            $result['errors'] = array_merge($result['errors'], $generated['errors']);
        }
        return $result;
    }

    public function processQueue(int $limit = 10): array
    {
        $tz = new \DateTimeZone(app_timezone());
        $stale = $this->runModel->markStaleRuns(10);
        if ($stale > 0) {
            log_message('warning', 'ContentAI: marked stale runs as failed: ' . $stale);
            $cutoff = (new \DateTimeImmutable('now', $tz))->modify('-10 minutes')->format('Y-m-d H:i:s');
            $this->calendarModel->builder()
                ->where('status', 'generating')
                ->where('updated_at <', $cutoff)
                ->update(['status' => 'queued', 'updated_at' => (new \DateTimeImmutable('now', $tz))->format('Y-m-d H:i:s')]);
        }
        $now = (new \DateTimeImmutable('now', $tz))->format('Y-m-d H:i:s');
        $items = $this->calendarModel->getDueToGenerate($now, $limit);
        log_message('info', 'ContentAI: processQueue items=' . count($items));
        $count = 0;
        $errors = [];
        foreach ($items as $item) {
            $runId = $this->runModel->startRun((int) $item->id, 'generate');
            log_message('info', 'ContentAI: generating calendar_id=' . $item->id . ' title=' . ($item->title ?? ''));
            $this->calendarModel->markStatus((int) $item->id, 'generating');
            try {
                $res = $this->generatePostFromCalendarItem($item);
            } catch (\Throwable $e) {
                $res = ['success' => false, 'error' => $e->getMessage()];
                log_message('critical', 'ContentAI: exception calendar_id=' . $item->id . ' ' . $e->getMessage());
            }
            if ($res['success']) {
                $count++;
                $this->runModel->finishRun($runId, 'success', null, $res['prompt'] ?? null, $res['response'] ?? null);
                log_message('info', 'ContentAI: generated calendar_id=' . $item->id);
            } else {
                $errors[] = $res['error'] ?? 'unknown error';
                $this->runModel->finishRun($runId, 'failed', $res['error'] ?? 'unknown error', $res['prompt'] ?? null, $res['response'] ?? null);
                $this->calendarModel->markStatus((int) $item->id, 'failed');
                log_message('error', 'ContentAI: failed calendar_id=' . $item->id . ' error=' . ($res['error'] ?? 'unknown'));
            }
        }
        return ['count' => $count, 'errors' => $errors];
    }

    protected function planDailyFromTrends($settings, \DateTimeImmutable $now): int
    {
        if (empty($settings->auto_add_trends)) {
            return 0;
        }
        $today = $now->format('Y-m-d');
        $target = max(1, (int) $settings->posts_per_day);
        $existing = $this->calendarModel->countForDate($today);
        $need = $target - $existing;
        if ($need <= 0) {
            return 0;
        }

        // Fetch a broad set of candidates for the AI editor to choose from
        $candidates = $this->trendModel->getCandidates($need * 10, !empty($settings->auto_add_trends));
        if (empty($candidates)) {
            return 0;
        }

        // Pre-filter for relevance (keyword-based).
        // We DO NOT mark irrelevants as used=1 — they remain available so the editor
        // (or a future broader relevance pass) can still pick them up.
        $relevant = [];
        foreach ($candidates as $trend) {
            if ($this->isTrendRelevant($trend->title, $settings)) {
                $relevant[] = $trend;
            }
        }
        if (empty($relevant)) {
            log_message('info', 'ContentAI: no relevant trends found for planning');
            return 0;
        }

        // AI Editor-in-Chief: let the AI select and prioritize which articles to produce
        $selected = $this->aiEditorSelect($relevant, $settings, $need);

        // Fallback: if AI editor fails, use first N relevant trends
        if (empty($selected)) {
            log_message('warning', 'ContentAI: AI editor returned empty, using fallback selection');
            $selected = [];
            foreach (array_slice($relevant, 0, $need) as $trend) {
                $selected[] = [
                    'title' => $trend->title,
                    'category_id' => $this->selectCategoryId($trend, $settings),
                    'instructions' => '',
                    'trend_id' => $trend->id,
                    'source_url' => $trend->source_url,
                ];
            }
        }

        $slots = $this->buildPublishSlots($settings, count($selected), $now);
        $created = 0;
        foreach ($selected as $idx => $item) {
            $publishAt = $slots[$idx] ?? $now->format('Y-m-d H:i:s');
            $instructions = !empty($item['instructions'])
                ? $item['instructions']
                : 'Escreva sobre: "' . $item['title'] . '". Aborde dentro do contexto financeiro brasileiro.';
            $data = [
                'title' => $item['title'],
                'instructions' => $instructions,
                'category_id' => $item['category_id'],
                'lang_id' => $settings->lang_id,
                'user_id' => $settings->default_user_id,
                'tone' => $settings->default_tone,
                'length' => $settings->default_length,
                'publish_at' => $publishAt,
                'generate_at' => $now->format('Y-m-d H:i:s'),
                'status' => 'queued',
                'source_type' => 'trend',
                'source_url' => $item['source_url'] ?? '',
                'created_at' => $now->format('Y-m-d H:i:s'),
                'updated_at' => $now->format('Y-m-d H:i:s'),
            ];
            if ($this->calendarModel->builder()->insert($data)) {
                $created++;
                if (!empty($item['trend_id'])) {
                    $this->trendModel->builder()->where('id', $item['trend_id'])->update(['used' => 1]);
                } else {
                    // Mark by title hash
                    $this->trendModel->builder()
                        ->where('title_hash', md5(mb_strtolower($item['title'])))
                        ->update(['used' => 1]);
                }
            }
        }
        return $created;
    }

    /**
     * Plan derivative articles based on the most popular posts of the portal.
     * Runs in parallel with planDailyFromTrends and ADDS to the daily output —
     * does not replace the editorial line.
     */
    public function planDailyFromPopular($settings, \DateTimeImmutable $now): int
    {
        if (empty($settings->popular_enabled)) {
            return 0;
        }
        $target = max(0, (int) ($settings->popular_posts_per_day ?? 0));
        if ($target <= 0) {
            return 0;
        }
        $today = $now->format('Y-m-d');
        $existing = $this->calendarModel->countForDateBySource($today, 'popular');
        $need = $target - $existing;
        if ($need <= 0) {
            return 0;
        }

        $windowDays = max(1, (int) ($settings->popular_window_days ?? 7));
        $metric = (string) ($settings->popular_metric ?? 'mixed');
        $minViews = max(0, (int) ($settings->popular_min_pageviews ?? 0));

        $candidates = $this->fetchPopularCandidates($windowDays, $metric, max(20, $need * 6), $minViews);
        if (empty($candidates)) {
            log_message('info', 'ContentAI: no popular candidates found (window=' . $windowDays . 'd metric=' . $metric . ') - skipping popular plan');
            return 0;
        }

        // Persist daily snapshot for auditability
        $this->popularModel->insertSnapshot($today, $windowDays, $metric, $candidates);
        $snapshot = $this->popularModel->getSnapshot($today, $windowDays, $metric, count($candidates));

        $selected = $this->aiEditorSelectFromPopular($snapshot, $settings, $need);
        if (empty($selected)) {
            log_message('warning', 'ContentAI: popular editor returned empty - skipping popular plan for ' . $today);
            return 0;
        }

        $slots = $this->buildPublishSlots($settings, count($selected), $now);
        $created = 0;
        foreach ($selected as $idx => $item) {
            $publishAt = $slots[$idx] ?? $now->modify('+' . ($idx * 20) . ' minutes')->format('Y-m-d H:i:s');
            $instructions = !empty($item['instructions'])
                ? $item['instructions']
                : 'Escreva um artigo derivado do post popular "' . ($item['source_title'] ?? '') . '". Aborde sob um angulo novo dentro do contexto financeiro brasileiro.';
            $sourceUrl = '';
            if (!empty($item['derived_from_post_id'])) {
                $sourceUrl = 'popular_post_id:' . (int) $item['derived_from_post_id'];
            }
            $data = [
                'title' => $item['title'],
                'instructions' => $instructions,
                'category_id' => $item['category_id'],
                'lang_id' => $settings->lang_id,
                'user_id' => $settings->default_user_id,
                'tone' => $settings->default_tone,
                'length' => $settings->default_length,
                'publish_at' => $publishAt,
                'generate_at' => $now->format('Y-m-d H:i:s'),
                'status' => 'queued',
                'source_type' => 'popular',
                'source_url' => $sourceUrl,
                'created_at' => $now->format('Y-m-d H:i:s'),
                'updated_at' => $now->format('Y-m-d H:i:s'),
            ];
            if ($this->calendarModel->builder()->insert($data)) {
                $created++;
                if (!empty($item['derived_from_post_id'])) {
                    $this->popularModel->markUsedByPostId(
                        (int) $item['derived_from_post_id'],
                        $today,
                        $windowDays
                    );
                }
            }
        }
        $this->settingsModel->updateLastRunPopular($now->format('Y-m-d H:i:s'));
        log_message('info', 'ContentAI: popular planner created=' . $created . ' (target=' . $target . ' need=' . $need . ' window=' . $windowDays . 'd)');
        return $created;
    }

    /**
     * Fetch the most popular posts in the requested window combining views and engagement.
     * Returns an array of normalized rows ready for snapshot insertion.
     */
    protected function fetchPopularCandidates(int $windowDays, string $metric, int $limit, int $minViews = 0): array
    {
        $limit = max(1, $limit);
        $rows = [];

        try {
            $topByViews = $this->dashboardModel->getTopPosts($limit, $windowDays);
        } catch (\Throwable $e) {
            log_message('error', 'ContentAI: getTopPosts failed - ' . $e->getMessage());
            $topByViews = [];
        }

        try {
            $topByEngagement = $this->dashboardModel->getMostEngagedPosts($limit, $windowDays);
        } catch (\Throwable $e) {
            log_message('error', 'ContentAI: getMostEngagedPosts failed - ' . $e->getMessage());
            $topByEngagement = [];
        }

        $merged = [];
        foreach ($topByViews as $rank => $p) {
            $pid = (int) ($p->id ?? 0);
            if ($pid <= 0) continue;
            $merged[$pid] = [
                'post_id'         => $pid,
                'title'           => $p->title ?? '',
                'pageviews'       => (int) ($p->pageviews ?? 0),
                'unique_visitors' => (int) ($p->unique_visitors ?? 0),
                'interactions'    => 0,
                'category_id'     => null,
                'views_rank'      => $rank + 1,
                'engagement_rank' => null,
            ];
        }
        foreach ($topByEngagement as $rank => $p) {
            $pid = (int) ($p->id ?? 0);
            if ($pid <= 0) continue;
            if (!isset($merged[$pid])) {
                $merged[$pid] = [
                    'post_id'         => $pid,
                    'title'           => $p->title ?? '',
                    'pageviews'       => (int) ($p->period_views ?? 0),
                    'unique_visitors' => 0,
                    'interactions'    => (int) ($p->total_interactions ?? 0),
                    'category_id'     => null,
                    'views_rank'      => null,
                    'engagement_rank' => $rank + 1,
                ];
            } else {
                $merged[$pid]['interactions'] = (int) ($p->total_interactions ?? 0);
                $merged[$pid]['engagement_rank'] = $rank + 1;
            }
        }

        if (empty($merged)) {
            return [];
        }

        // Enrich with category_id from posts table in one query
        $ids = array_keys($merged);
        $catRows = $this->dashboardModel->db->table('posts')
            ->select('id, category_id')
            ->whereIn('id', $ids)
            ->get()->getResult();
        foreach ($catRows as $cr) {
            if (isset($merged[(int) $cr->id])) {
                $merged[(int) $cr->id]['category_id'] = (int) $cr->category_id;
            }
        }

        // Compute score per metric mode
        $maxViews = max(1, max(array_column($merged, 'pageviews')));
        $maxInter = max(1, max(array_column($merged, 'interactions')));
        foreach ($merged as &$row) {
            $vNorm = $row['pageviews'] / $maxViews;
            $iNorm = $row['interactions'] / $maxInter;
            switch ($metric) {
                case 'pageviews':
                    $row['score'] = round($vNorm * 100, 2);
                    break;
                case 'engagement':
                    $row['score'] = round($iNorm * 100, 2);
                    break;
                case 'mixed':
                default:
                    $row['score'] = round((0.6 * $vNorm + 0.4 * $iNorm) * 100, 2);
                    break;
            }
        }
        unset($row);

        if ($minViews > 0) {
            $merged = array_filter($merged, fn($r) => $r['pageviews'] >= $minViews);
        }
        if (empty($merged)) {
            return [];
        }

        usort($merged, fn($a, $b) => $b['score'] <=> $a['score']);
        $rows = array_slice($merged, 0, $limit);
        return $rows;
    }

    /**
     * AI Editor for popular-derived content: receives the snapshot rows and returns
     * a list of NEW article ideas derived from those popular posts.
     */
    protected function aiEditorSelectFromPopular(array $snapshot, $settings, int $need): array
    {
        if (empty($snapshot)) {
            return [];
        }

        $candidateList = [];
        $sourceLookup = [];
        foreach ($snapshot as $i => $row) {
            $line = sprintf(
                "#%d [post_id=%d] %s — pageviews=%d, unique=%d, interacoes=%d, score=%s",
                $i + 1,
                (int) $row->post_id,
                (string) ($row->title ?? '(sem titulo)'),
                (int) $row->pageviews,
                (int) $row->unique_visitors,
                (int) $row->interactions,
                (string) $row->score
            );
            $candidateList[] = $line;
            $sourceLookup[(int) $row->post_id] = $row;
        }

        $recentTitles = [];
        $recentPosts = $this->calendarModel->builder()
            ->select('title')
            ->whereIn('status', ['queued', 'generating', 'generated', 'needs_review'])
            ->where('created_at >', date('Y-m-d H:i:s', time() - 172800))
            ->get()->getResult();
        foreach ($recentPosts as $rp) {
            $recentTitles[] = $rp->title;
        }

        $categoriesText = '';
        $guidelines = json_decode($settings->category_guidelines_json ?? '{}', true) ?: [];
        if (!empty($guidelines)) {
            foreach ($guidelines as $catId => $desc) {
                $categoriesText .= 'ID ' . $catId . ': ' . $desc . "\n";
            }
        }

        $editorTemplate = trim($settings->popular_editor_prompt ?? '');
        if (empty($editorTemplate)) {
            $editorTemplate = (new ContentAISettingsModel())->getDefaultPopularEditorPrompt();
        }

        $systemPrompt = strtr($editorTemplate, [
            '{popular_per_day}' => $need,
            '{window_days}'     => (int) ($settings->popular_window_days ?? 7),
            '{metric}'          => (string) ($settings->popular_metric ?? 'mixed'),
            '{categories}'      => $categoriesText,
            '{recent_titles}'   => !empty($recentTitles) ? implode('; ', array_slice($recentTitles, 0, 20)) : 'Nenhum recente.',
        ]);

        $userPrompt = "POSTS MAIS POPULARES (use post_id como referencia em derived_from_post_id):\n"
            . implode("\n", $candidateList)
            . "\n\nProponha exatamente " . $need . " NOVOS artigos derivados. Responda APENAS com JSON valido.";

        log_message('info', 'ContentAI: popular editor evaluating ' . count($snapshot) . ' posts for ' . $need . ' derivatives');

        $response = $this->callEditorModel($systemPrompt, $userPrompt);
        if (empty($response)) {
            log_message('warning', 'ContentAI: popular editor call failed');
            return [];
        }

        $text = $this->extractTextFromResponse($response);
        $payload = $this->parseJsonPayload($text);

        if (is_array($payload) && isset($payload['articles']) && is_array($payload['articles'])) {
            $payload = $payload['articles'];
        } elseif (is_array($payload) && isset($payload[0])) {
            // already a flat array
        } elseif (is_array($payload)) {
            foreach ($payload as $v) {
                if (is_array($v) && isset($v[0])) {
                    $payload = $v;
                    break;
                }
            }
        }

        if (empty($payload) || !isset($payload[0])) {
            log_message('warning', 'ContentAI: popular editor returned invalid format - raw=' . substr($text ?? '', 0, 500));
            return [];
        }

        $allowedCatIds = $this->decodeCategoryIds($settings->allowed_category_ids);
        $selected = [];
        foreach ($payload as $item) {
            if (!is_array($item) || empty($item['title'])) {
                continue;
            }
            $catId = (int) ($item['category_id'] ?? 0);
            $derivedFrom = (int) ($item['derived_from_post_id'] ?? 0);
            $sourceTitle = '';
            if ($derivedFrom > 0 && isset($sourceLookup[$derivedFrom])) {
                $sourceTitle = (string) ($sourceLookup[$derivedFrom]->title ?? '');
                // If editor didn't assign a category, inherit from source post
                if ($catId <= 0 && !empty($sourceLookup[$derivedFrom]->category_id)) {
                    $catId = (int) $sourceLookup[$derivedFrom]->category_id;
                }
            }
            if ($catId <= 0 || (!empty($allowedCatIds) && !in_array($catId, $allowedCatIds))) {
                $catId = $this->pickCategory($allowedCatIds);
            }
            $selected[] = [
                'title' => $item['title'],
                'category_id' => $catId,
                'instructions' => $item['instructions'] ?? '',
                'derived_from_post_id' => $derivedFrom > 0 ? $derivedFrom : null,
                'source_title' => $sourceTitle,
            ];
            if (count($selected) >= $need) {
                break;
            }
        }

        log_message('info', 'ContentAI: popular editor selected ' . count($selected) . ' derivative articles');
        return $selected;
    }

    /**
     * AI Editor-in-Chief: analyze trend candidates and select the best articles for the day.
     */
    protected function aiEditorSelect(array $candidates, $settings, int $need): array
    {
        // Build the candidate list for the prompt
        $candidateList = [];
        foreach ($candidates as $i => $trend) {
            $candidateList[] = ($i + 1) . '. ' . $trend->title . ' [fonte: ' . ($trend->source ?? 'trends') . ']';
        }

        // Get recently published titles to avoid duplicates
        $recentTitles = [];
        $recentPosts = $this->calendarModel->builder()
            ->select('title')
            ->whereIn('status', ['queued', 'generating', 'generated', 'needs_review'])
            ->where('created_at >', date('Y-m-d H:i:s', time() - 172800)) // last 48h
            ->get()->getResult();
        foreach ($recentPosts as $rp) {
            $recentTitles[] = $rp->title;
        }

        // Parse topic weights
        $weights = json_decode($settings->topic_weights_json ?? '{}', true) ?: [];
        $weightsText = '';
        if (!empty($weights)) {
            $parts = [];
            foreach ($weights as $topic => $pct) {
                $parts[] = ucfirst($topic) . ': ' . $pct . '%';
            }
            $weightsText = implode(', ', $parts);
        } else {
            $weightsText = 'Distribuicao igual entre todos os topicos.';
        }

        // Parse categories
        $categoriesText = '';
        $guidelines = json_decode($settings->category_guidelines_json ?? '{}', true) ?: [];
        if (!empty($guidelines)) {
            foreach ($guidelines as $catId => $desc) {
                $categoriesText .= 'ID ' . $catId . ': ' . $desc . "\n";
            }
        }

        // Build the editor prompt (use DB value, fall back to hardcoded default)
        $editorTemplate = trim($settings->editor_prompt ?? '');
        if (empty($editorTemplate)) {
            $editorTemplate = (new \App\Models\ContentAISettingsModel())->getDefaultEditorPrompt();
        }

        // X Pulse context — fetched from last 24h snapshot. Empty string if disabled / no data.
        $xPulseContext = $this->buildXPulseContext($settings);

        $systemPrompt = strtr($editorTemplate, [
            '{posts_per_day}' => $need,
            '{topic_weights}' => $weightsText,
            '{categories}' => $categoriesText,
            '{recent_titles}' => !empty($recentTitles) ? implode('; ', array_slice($recentTitles, 0, 20)) : 'Nenhum recente.',
            '{x_pulse_context}' => $xPulseContext,
        ]);

        // If the prompt template does not include {x_pulse_context} but we DO have X Pulse data,
        // append it to the user prompt so the editor still benefits.
        $userPrompt = "CANDIDATOS DISPONIVEIS:\n" . implode("\n", $candidateList);
        if ($xPulseContext !== '' && strpos($editorTemplate, '{x_pulse_context}') === false) {
            $userPrompt .= "\n\n=== X PULSE (sinal complementar do que esta sendo discutido no X agora) ===\n" . $xPulseContext
                . "\nPRIORIZE candidatos cujo tema converge com um item do X Pulse — esses sao os mais propensos a render no Google.";
        }
        $userPrompt .= "\n\nSelecione exatamente " . $need . " artigos. Responda APENAS com JSON valido.";

        log_message('info', 'ContentAI: AI editor evaluating ' . count($candidates) . ' candidates for ' . $need . ' slots');

        // Call the AI editor
        $response = $this->callEditorModel($systemPrompt, $userPrompt);
        if (empty($response)) {
            log_message('warning', 'ContentAI: AI editor call failed');
            return [];
        }

        $text = $this->extractTextFromResponse($response);
        $payload = $this->parseJsonPayload($text);

        // Handle multiple response formats:
        // 1. {"articles": [...]} (preferred format)
        // 2. Direct array [{...}, {...}]
        // 3. Object with any key containing an array
        if (is_array($payload) && isset($payload['articles']) && is_array($payload['articles'])) {
            $payload = $payload['articles'];
        } elseif (is_array($payload) && isset($payload[0])) {
            // Already an array of items — keep as-is
        } elseif (is_array($payload)) {
            // Look for the first array value in any key
            foreach ($payload as $v) {
                if (is_array($v) && isset($v[0])) {
                    $payload = $v;
                    break;
                }
            }
        }

        if (empty($payload) || !isset($payload[0])) {
            log_message('warning', 'ContentAI: AI editor returned invalid format - raw=' . substr($text ?? '', 0, 500));
            return [];
        }

        // Map AI selections back to trend objects
        $allowedCatIds = $this->decodeCategoryIds($settings->allowed_category_ids);
        $selected = [];
        foreach ($payload as $item) {
            if (!is_array($item) || empty($item['title'])) {
                continue;
            }
            $catId = (int) ($item['category_id'] ?? 0);
            // Validate category
            if ($catId <= 0 || (!empty($allowedCatIds) && !in_array($catId, $allowedCatIds))) {
                $catId = $this->pickCategory($allowedCatIds);
            }
            // Find the matching trend for source_url
            $matchedTrend = null;
            $titleNorm = $this->normalizeText($item['title']);
            foreach ($candidates as $trend) {
                if ($this->normalizeText($trend->title) === $titleNorm) {
                    $matchedTrend = $trend;
                    break;
                }
            }
            $selected[] = [
                'title' => $item['title'],
                'category_id' => $catId,
                'instructions' => $item['instructions'] ?? '',
                'trend_id' => $matchedTrend ? $matchedTrend->id : null,
                'source_url' => $matchedTrend ? $matchedTrend->source_url : '',
            ];
            if (count($selected) >= $need) {
                break;
            }
        }

        log_message('info', 'ContentAI: AI editor selected ' . count($selected) . ' articles');
        return $selected;
    }

    /**
     * Builds a compact textual digest of the latest X Pulse snapshot.
     * Returns "" when X Pulse is disabled / no fresh snapshot / model not available.
     */
    protected function buildXPulseContext($settings): string
    {
        if (empty($settings->x_pulse_enabled) || $this->xPulseModel === null) {
            return '';
        }
        try {
            $items = $this->xPulseModel->getActive(24, 15);
        } catch (\Throwable $e) {
            return '';
        }
        if (empty($items)) {
            return '';
        }
        $lines = [];
        foreach ($items as $i => $row) {
            $tickers = json_decode((string) ($row->tickers_json ?? '[]'), true) ?: [];
            $entities = json_decode((string) ($row->entities_json ?? '[]'), true) ?: [];
            $extras = [];
            if (!empty($tickers)) $extras[] = '$' . implode(' $', array_slice($tickers, 0, 4));
            if (!empty($entities)) $extras[] = implode(', ', array_slice($entities, 0, 4));
            $tail = !empty($extras) ? ' (' . implode(' | ', $extras) . ')' : '';
            $lines[] = sprintf(
                "X%d. %s — sent=%s, mentions~%d, rel=%d%s",
                $i + 1,
                $row->theme,
                $row->sentiment,
                (int) $row->mentions_estimate,
                (int) $row->relevance_score,
                $tail
            );
        }
        return implode("\n", $lines);
    }

    /**
     * Call OpenAI for the editor role (system + user prompt, structured JSON output).
     */
    protected function callEditorModel(string $systemPrompt, string $userPrompt)
    {
        $model = getenv('OPENAI_TEXT_MODEL') ?: 'gpt-5.4-mini';
        $apiKey = getenv('OPENAI_API_KEY') ?: '';
        if (empty($apiKey)) {
            $ai = \aiWriter();
            if (!empty($ai) && !empty($ai->apiKey)) {
                $apiKey = $ai->apiKey;
            }
        }
        if (empty($apiKey)) {
            return false;
        }

        $timeout = intval(getenv('OPENAI_TEXT_TIMEOUT') ?: 60);
        if ($timeout < 10) {
            $timeout = 10;
        }
        $useResponses = (strpos($model, 'gpt-5') !== false);
        $endpoint = $useResponses
            ? 'https://api.openai.com/v1/responses'
            : 'https://api.openai.com/v1/chat/completions';

        if ($useResponses) {
            $payload = [
                'model' => $model,
                'instructions' => $systemPrompt,
                'input' => $userPrompt,
                'temperature' => 0.4,
                'text' => ['format' => ['type' => 'json_object']],
            ];
        } else {
            $payload = [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.4,
                'response_format' => ['type' => 'json_object'],
            ];
        }

        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ];
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $response = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http === 200) {
            return json_decode($response, true);
        }
        log_message('error', 'ContentAI: editor model HTTP ' . $http . ' model=' . $model . ' response=' . substr($response, 0, 500));
        // Fallback to secondary model if available
        $fallback = getenv('OPENAI_TEXT_FALLBACK_MODEL') ?: 'gpt-4.1-mini';
        if (!empty($fallback) && $fallback !== $model) {
            $useFbResponses = (strpos($fallback, 'gpt-5') !== false);
            $fbEndpoint = $useFbResponses
                ? 'https://api.openai.com/v1/responses'
                : 'https://api.openai.com/v1/chat/completions';
            if ($useFbResponses) {
                $payload = [
                    'model' => $fallback,
                    'instructions' => $systemPrompt,
                    'input' => $userPrompt,
                    'temperature' => 0.4,
                    'text' => ['format' => ['type' => 'json_object']],
                ];
            } else {
                $payload = [
                    'model' => $fallback,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'temperature' => 0.4,
                    'response_format' => ['type' => 'json_object'],
                ];
            }
            $ch = curl_init($fbEndpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $response = curl_exec($ch);
            $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($http === 200) {
                return json_decode($response, true);
            }
            log_message('error', 'ContentAI: editor fallback model HTTP ' . $http . ' model=' . $fallback);
        }
        return false;
    }

    protected function generatePostFromCalendarItem($item): array
    {
        $settings = $this->settingsModel->getSettings();
        if (empty($item->category_id)) {
            $item->category_id = $this->selectCategoryId($item, $settings);
        }
        $categoryName = $this->getCategoryName($item->category_id);
        log_message('info', 'ContentAI: building prompt calendar_id=' . $item->id . ' category=' . $categoryName);
        $prompt = $this->buildPrompt($item, $settings, $categoryName);
        $response = $this->callTextModel($prompt);
        if (!$response) {
            return ['success' => false, 'error' => 'OpenAI text model returned empty/HTTP error response (check OPENAI_API_KEY and model availability)', 'prompt' => $prompt];
        }
        $text = $this->extractTextFromResponse($response);
        $payload = $this->parseJsonPayload($text);
        if (empty($payload)) {
            $excerpt = $text !== '' ? ' excerpt=' . mb_substr($text, 0, 200) : '';
            return ['success' => false, 'error' => 'OpenAI response was not valid JSON' . $excerpt, 'prompt' => $prompt, 'response' => $text];
        }
        $title = $payload['title'] ?? $item->title;
        $summary = $payload['summary'] ?? '';
        $contentHtml = $payload['content_html'] ?? '';
        $tags = $payload['tags'] ?? [];
        $imageAlt = trim((string) ($payload['image_alt'] ?? ''));
        $imageCaption = trim((string) ($payload['image_caption'] ?? ''));

        if (empty($title) || empty($contentHtml)) {
            $missing = empty($title) ? 'title' : 'content_html';
            return ['success' => false, 'error' => 'LLM payload missing required field: ' . $missing, 'prompt' => $prompt, 'response' => $text];
        }

        // Inject simulator CTAs based on article category and content
        $contentHtml = $this->injectSimulatorCtas($contentHtml, (int) $item->category_id, $title);

        try {
            $postId = $this->createPost($item, $settings, $title, $summary, $contentHtml, $tags, null, $imageAlt, $imageCaption);
        } catch (\RuntimeException $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'prompt' => $prompt, 'response' => $text];
        }

        // Notify search engines about the new post
        $this->requestSearchEngineIndexing($postId);

        $status = !empty($settings->auto_publish) ? 'generated' : 'needs_review';
        $this->calendarModel->markStatus((int) $item->id, $status, ['post_id' => $postId]);
        if ($this->shouldGenerateImages()) {
            $imagePromptBase = $payload['image_prompt'] ?? $title;
            $imagePrompt = $this->buildImagePrompt(
                $title,
                $summary,
                $imagePromptBase,
                $categoryName,
                (int) $item->category_id,
                $settings
            );
            log_message('info', 'ContentAI: image prompt prepared calendar_id=' . $item->id);
            $imageId = $this->generateCoverImage($imagePrompt, $title, (int) ($item->user_id ?: $settings->default_user_id));
            if (!empty($imageId)) {
                $this->calendarModel->db->table('posts')->where('id', $postId)->update(['image_id' => $imageId]);
            } else {
                log_message('warning', 'ContentAI: image generation skipped or failed calendar_id=' . $item->id);
            }
        } else {
            log_message('info', 'ContentAI: image generation skipped for web request calendar_id=' . $item->id);
        }
        return ['success' => true, 'prompt' => $prompt, 'response' => $text];
    }

    protected function shouldGenerateImages(): bool
    {
        return true;
    }

    protected function createPost($item, $settings, string $title, string $summary, string $contentHtml, array $tags, ?int $imageId, string $imageAlt = '', string $imageCaption = ''): int
    {
        // Ensure text/url helpers are loaded (needed in CLI context)
        helper(['text', 'url']);

        $db = \Config\Database::connect(null, false);
        $now = date('Y-m-d H:i:s');
        $langId = $item->lang_id ?: $settings->lang_id;
        $categoryId = $item->category_id ?: $this->selectCategoryId($item, $settings);
        $userId = $item->user_id ?: $settings->default_user_id;
        $autoPublish = !empty($settings->auto_publish);
        $createdAt = $autoPublish && !empty($item->publish_at) ? $item->publish_at : $now;
        $titleHash = md5(mb_strtolower($title));
        $slug = strSlug($title);

        $titleDup = $db->table('posts')->where('title_hash', $titleHash)->get()->getFirstRow();
        if (!empty($titleDup)) {
            $db->close();
            throw new \RuntimeException('Duplicate title: post #' . (int) $titleDup->id . ' already uses the same title hash ("' . mb_substr((string) $titleDup->title, 0, 80) . '"). Edit the calendar item title before retrying.');
        }
        $slugDup = $db->table('posts')->where('slug', $slug)->get()->getFirstRow();
        if (!empty($slugDup)) {
            $db->close();
            throw new \RuntimeException('Duplicate slug "' . $slug . '" already used by post #' . (int) $slugDup->id . ' ("' . mb_substr((string) $slugDup->title, 0, 80) . '"). Edit the calendar item title before retrying.');
        }
        $data = [
            'lang_id' => $langId,
            'title' => $title,
            'slug' => $slug,
            'title_hash' => $titleHash,
            'summary' => $summary,
            'content' => $contentHtml,
            'keywords' => implode(',', array_filter($tags)),
            'category_id' => $categoryId,
            'optional_url' => '',
            'need_auth' => 0,
            'visibility' => 1,
            'show_right_column' => 1,
            'slider_order' => 1,
            'featured_order' => 1,
            'post_type' => 'article',
            'image_id' => $imageId,
            'image_url' => '',
            'image_alt' => $imageAlt,
            'image_description' => $imageCaption,
            'video_path' => '',
            'video_embed_code' => '',
            'user_id' => $userId,
            'post_url' => '',
            'show_post_url' => 0,
            'is_scheduled' => ($autoPublish && !empty($item->publish_at) && strtotime($item->publish_at) > time()) ? 1 : 0,
            'status' => $autoPublish ? 1 : 0,
            'created_at' => $createdAt,
            'updated_at' => $now,
        ];
        if (!$db->table('posts')->insert($data)) {
            $err = $db->error();
            $db->close();
            throw new \RuntimeException('Database insert into posts failed: ' . ($err['message'] ?? 'unknown DB error'));
        }
        $postId = (int) $db->insertID();
        $db->close();
        updateSlug('posts', $postId);
        resetCacheDataOnChange();
        return $postId;
    }

    /**
     * Submits a published post URL to Google Indexing API and IndexNow (Bing).
     */
    protected function requestSearchEngineIndexing(int $postId): void
    {
        $post = getPostById($postId);
        if (empty($post) || !isPostPublished($post)) {
            return;
        }

        $postAdminModel = new \App\Models\PostAdminModel();
        $postAdminModel->requestGoogleIndexing($post);
        $postAdminModel->requestIndexNowSubmission($post);
    }

    protected function generateCoverImage(string $prompt, string $title, int $userId): ?int
    {
        helper(['text', 'url']);
        log_message('info', 'ContentAI: generating image model=' . (getenv('OPENAI_DEFAULT_MODEL') ?: 'gpt-image-1-mini'));
        $model = getenv('OPENAI_DEFAULT_MODEL') ?: 'gpt-image-1-mini';
        $isGptImage = is_string($model) && strpos($model, 'gpt-image-1') === 0;
        $quality = getenv('OPENAI_DEFAULT_QUALITY') ?: ($isGptImage ? 'high' : 'hd');
        $size = $isGptImage ? '1536x1024' : (getenv('OPENAI_DEFAULT_SIZE') ?: '1024x1024');

        $result = $this->imageHelper->generateImage($prompt, $model, $size, $quality, 1);
        if (!$result || empty($result['data'][0])) {
            log_message('error', 'ContentAI: image generation failed - result=' . json_encode($result));
            return null;
        }
        $imageData = $result['data'][0];
        $imageUrl = $imageData['url'] ?? null;
        $b64 = $imageData['b64_json'] ?? null;

        $tmpDir = FCPATH . 'uploads/tmp/';
        if (!is_dir($tmpDir)) {
            @mkdir($tmpDir, 0755, true);
        }
        $tmpPath = $tmpDir . 'ai_cover_' . uniqid('', true) . '.png';
        $saved = false;
        if (!empty($imageUrl)) {
            $saved = $this->downloadImage($imageUrl, $tmpPath);
        } elseif (!empty($b64)) {
            $saved = (bool) file_put_contents($tmpPath, base64_decode($b64));
        }
        if (!$saved || !file_exists($tmpPath)) {
            return null;
        }

        $uploadModel = new UploadModel();
        $paths = [];
        $paths['image_discover'] = $uploadModel->uploadPostImage($tmpPath, 'discover');
        $paths['image_big'] = $uploadModel->uploadPostImage($tmpPath, 'big');
        $paths['image_default'] = $uploadModel->uploadPostImage($tmpPath, 'default');
        $paths['image_slider'] = $uploadModel->uploadPostImage($tmpPath, 'slider');
        $paths['image_mid'] = $uploadModel->uploadPostImage($tmpPath, 'mid');
        $paths['image_small'] = $uploadModel->uploadPostImage($tmpPath, 'small');

        $imageExt = '';
        if (!empty($paths['image_default'])) {
            $imageExt = pathinfo($paths['image_default'], PATHINFO_EXTENSION);
        }
        $dataImage = [
            'image_discover' => $paths['image_discover'] ?? '',
            'image_big' => $paths['image_big'] ?? '',
            'image_default' => $paths['image_default'] ?? '',
            'image_slider' => $paths['image_slider'] ?? '',
            'image_mid' => $paths['image_mid'] ?? '',
            'image_small' => $paths['image_small'] ?? '',
            'image_mime' => $imageExt ?: 'jpg',
            'file_name' => strSlug($title),
            'user_id' => $userId > 0 ? $userId : 1,
            'storage' => $this->settingsModel->generalSettings->storage ?? 'local',
        ];
        $db = \Config\Database::connect(null, false);
        $ok = $db->table('images')->insert($dataImage);
        $imageId = $ok ? (int) $db->insertID() : null;
        $db->close();
        @unlink($tmpPath);
        return $imageId;
    }

    protected function buildPrompt($item, $settings, string $categoryName): string
    {
        $title = $item->title ?? '';
        $instructions = trim((string) ($item->instructions ?? ''));
        $tone = $item->tone ?: $settings->default_tone;
        $length = $item->length ?: $settings->default_length;
        $lengthGuide = $this->lengthToWords($length, $settings);
        $categoryGuidelines = $this->getCategoryGuidelines((int) $item->category_id, $settings);
        $voice = trim((string) ($settings->voice_guidelines ?? ''));
        $seo = trim((string) ($settings->seo_guidelines ?? ''));
        $rules = [
            'Idioma: portugues do Brasil.',
            'Nao copie trechos de fontes externas. Reescreva com originalidade.',
            'SEO: use titulo com ate 60 caracteres, meta descricao com 150-160 caracteres.',
            'Estrutura: introducao curta, 3 a 5 H2, paragrafo curtos, lista com bullets, conclusao com CTA.',
            'Inclua palavras-chave naturais e variacoes sem keyword stuffing.',
            'Formato HTML valido usando <p>, <h2>, <ul><li>, <strong> quando necessario.',
            $lengthGuide,
            // [NOVO — SEO 2026] E-E-A-T + Experience
            'E-E-A-T: feche o artigo com bloco de autoria "Equipe GX Capital — boutique financeira em Porto Alegre/RS, 15+ anos em cambio, credito estruturado, trade finance e wealth management". Em artigos densamente tecnicos, traga ao menos uma observacao em primeira pessoa do plural ("na nossa mesa de cambio…", "nossos clientes exportadores…") com caso anonimizado.',
            // [NOVO — SEO 2026] Information Gain
            'Information Gain: inclua ao menos UM elemento proprio que diferencie de concorrentes — um numero de mercado observado, uma tabela comparativa autoral ou uma regra pratica (rule of thumb) nao trivial. Marque o bloco com <strong>Observacao GX:</strong>.',
            // [NOVO — SEO 2026] AI Overviews / SGE
            'AI Overviews: abra CADA H2 com uma frase declarativa auto-contida de 1-2 linhas que responda diretamente a pergunta implicita do subtitulo, em formato extraivel pelo Google. O restante do bloco aprofunda.',
            // [NOVO — SEO 2026] Cobertura de entidades
            'Grafo semantico: mapeie e mencione as entidades relacionadas ao tema (orgaos, normas, instrumentos, atores). Ex.: ACC -> Bacen, Resolucao CMN, cedula de credito a exportacao, PTAX, exportador, prazo contratual, Circular Bacen.',
            // [NOVO — SEO 2026] Intro sem APP + hierarquia
            'Intro: va direto a intencao de busca nas 2 primeiras linhas. PROIBIDO abrir com "No mundo de hoje", "Em um cenario cada vez mais", "Voce ja se perguntou" ou formula APP (concordar-prometer-prever).',
            'Use H3 para subtopicos dentro de H2s longos. Paragrafos de no maximo 3-4 linhas.',
            // [NOVO — SEO 2026] YMYL compliance + data
            'YMYL: feche com disclaimer "Este conteudo e informativo e nao constitui recomendacao de investimento ou solicitacao de servico." PROIBIDO prometer retorno, rentabilidade, garantia ou dar recomendacao personalizada.',
            'Inclua no primeiro paragrafo "Atualizado em " seguido do mes e ano correntes (ex: "Atualizado em abril/2026").',
            // [NOVO — SEO 2026] Links e autoridade
            'Cite 2-3 fontes externas de alta autoridade pertinentes ao tema, priorizando Bacen (bcb.gov.br), CVM (gov.br/cvm), Anbima, B3, BIS, IMF ou Valor Economico. Use <a href="URL"> com anchor descritivo — nunca "clique aqui".',
            'Em tags[], sugira 3-5 topicos do mesmo cluster semantico (para links internos por cluster, nao temas aleatorios).',
            // [NOVO — SEO 2026] ALT semantico
            'Se o HTML contiver <img>, use atributo alt descritivo semantico (8-16 palavras), nunca keyword stuffing.',
            // [NOVO — SEO 2026] Capa do artigo: alt + caption
            'Capa do artigo: gere image_alt (8-16 palavras) descrevendo a cena visual ANCORADA no tema (ex.: "Mesa de operacao cambial analisando contrato NDF para hedge de margem em USD"). Nao faca keyword stuffing nem repita o titulo literal. Combine sujeito visual + assunto do artigo.',
            'Capa do artigo: gere image_caption (1-2 frases, ate 220 caracteres) que sera renderizada como <figcaption>. A caption deve ancorar a imagem no argumento central do artigo — pode ser editorial e mencionar dado/insight do texto, sem precisar descrever literalmente o que ha na imagem.',
            // [NOVO — SEO 2026] Proibicoes criticas
            'PROIBIDO emitir o token ":contentReference[oaicite:N]{index=N}" ou qualquer marcador interno de citacao do modelo. Gere HTML limpo.',
        ];
        $ruleText = implode("\n", $rules);
        $template = trim((string) ($settings->prompt_template ?? ''));
        if (empty($template)) {
            $template = "Voce e um editor de conteudo com foco em SEO.\n"
                . "Categoria: {category_name}\n"
                . "Guidelines da categoria: {category_guidelines}\n"
                . "Tema/Titulo base: {title}\n"
                . "Instrucoes adicionais: {instructions}\n"
                . "Tom: {tone}\n"
                . "Tamanho: {length_words}\n"
                . "Guidelines de voz: {voice}\n"
                . "Guidelines de SEO: {seo}\n"
                . "Regras gerais:\n{rules}\n"
                . "Responda APENAS com JSON valido no formato:\n"
                . "{\n"
                . "  \"title\": \"...\",\n"
                . "  \"summary\": \"...\",\n"
                . "  \"content_html\": \"<p>...</p>\",\n"
                . "  \"tags\": [\"...\"],\n"
                . "  \"image_prompt\": \"...\",\n"
                . "  \"image_alt\": \"...\",\n"
                . "  \"image_caption\": \"...\"\n"
                . "}";
        }
        $vars = [
            '{category_name}' => $categoryName,
            '{category_guidelines}' => $categoryGuidelines,
            '{title}' => $title,
            '{instructions}' => $instructions,
            '{tone}' => $tone,
            '{length_words}' => $lengthGuide,
            '{voice}' => $voice,
            '{seo}' => $seo,
            '{rules}' => $ruleText,
        ];
        return $this->renderTemplate($template, $vars);
    }

    protected function buildImagePrompt(string $title, string $summary, string $imagePrompt, string $categoryName, int $categoryId, $settings): string
    {
        $template = trim((string) ($settings->image_prompt_template ?? ''));
        if (empty($template)) {
            $template = "Crie uma imagem editorial realista e sem texto.\n"
                . "Tema: {title}\n"
                . "Categoria: {category_name}\n"
                . "Direcao: {image_prompt}\n"
                . "Contexto curto: {summary}\n"
                . "Guidelines da categoria: {category_guidelines}\n"
                . "Guidelines de imagem: {image_guidelines}\n"
                . "Requisitos: composicao limpa, foco central, iluminacao natural, estilo profissional.";
        }
        $categoryGuidelines = $this->getCategoryGuidelines($categoryId, $settings);
        $guidelines = trim((string) ($settings->image_guidelines ?? ''));
        $cleanSummary = trim((string) $summary);
        if (mb_strlen($cleanSummary) > 220) {
            $cleanSummary = mb_substr($cleanSummary, 0, 220);
        }
        $vars = [
            '{title}' => $title,
            '{summary}' => $cleanSummary,
            '{category_name}' => $categoryName,
            '{category_guidelines}' => $categoryGuidelines,
            '{image_prompt}' => $imagePrompt,
            '{image_guidelines}' => $guidelines,
        ];
        $rendered = $this->renderTemplate($template, $vars);
        return trim($rendered) !== '' ? $rendered : $imagePrompt;
    }

    protected function lengthToWords(string $length, $settings): string
    {
        $short = !empty($settings->length_short_words) ? (int) $settings->length_short_words : 900;
        $medium = !empty($settings->length_medium_words) ? (int) $settings->length_medium_words : 1400;
        $long = !empty($settings->length_long_words) ? (int) $settings->length_long_words : 2000;
        switch ($length) {
            case 'short':
                return 'Tamanho: ' . max(200, $short) . ' palavras.';
            case 'long':
                return 'Tamanho: ' . max(600, $long) . ' palavras.';
            case 'medium':
            default:
                return 'Tamanho: ' . max(400, $medium) . ' palavras.';
        }
    }

    protected function renderTemplate(string $template, array $vars): string
    {
        return strtr($template, $vars);
    }

    protected function getCategoryGuidelines(int $categoryId, $settings): string
    {
        $json = $settings->category_guidelines_json ?? '';
        $data = json_decode((string) $json, true);
        if (is_array($data) && isset($data[$categoryId])) {
            return (string) $data[$categoryId];
        }
        return '';
    }

    protected function getCategoryRules($settings): array
    {
        $json = $settings->category_rules_json ?? '';
        $data = json_decode((string) $json, true);
        if (is_array($data)) {
            return $data;
        }
        return [];
    }

    public function selectCategoryId($item, $settings): ?int
    {
        $text = trim((string) ($item->title ?? '')) . ' ' . trim((string) ($item->instructions ?? ''));
        $rules = $this->getCategoryRules($settings);
        if (!empty($rules)) {
            $normalized = $this->normalizeText($text);
            // Priority order: GX explica, cambio, credito, investimentos, radar
            $priority = [11, 6, 8, 13, 7];
            foreach ($priority as $catId) {
                if (empty($rules[$catId]) || !is_array($rules[$catId])) {
                    continue;
                }
                foreach ($rules[$catId] as $kw) {
                    $kwNorm = $this->normalizeText((string) $kw);
                    if ($kwNorm !== '' && strpos($normalized, $kwNorm) !== false) {
                        return (int) $catId;
                    }
                }
            }
        }
        return $this->pickCategory($this->decodeCategoryIds($settings->allowed_category_ids));
    }

    protected function normalizeText(string $text): string
    {
        $text = mb_strtolower($text);
        if (function_exists('iconv')) {
            $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        }
        $text = preg_replace('/[^a-z0-9\\s]/', ' ', $text);
        $text = preg_replace('/\\s+/', ' ', $text);
        return trim($text);
    }

    /**
     * Check if a trend title is relevant to the GX Capital domain.
     * Only trends about cambio, credito, consorcio, economia, investimentos
     * and related financial topics are accepted.
     */
    public function isTrendRelevant(string $title, $settings = null): bool
    {
        $normalized = $this->normalizeText($title);
        if (empty($normalized)) {
            return false;
        }

        $kw = $this->getTrendKeywords($settings);

        foreach ($kw['phrases'] as $phrase) {
            if ($phrase !== '' && strpos($normalized, $phrase) !== false) {
                return true;
            }
        }
        foreach ($kw['words'] as $word) {
            if ($word !== '' && preg_match('/\b' . preg_quote($word, '/') . '\b/', $normalized)) {
                return true;
            }
        }
        foreach ($kw['context_words'] as $cw) {
            if ($cw !== '' && preg_match('/\b' . preg_quote($cw, '/') . '\b/', $normalized)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Pulls the 3 keyword buckets from content_ai_settings.trend_keywords_json,
     * falling back to the canonical default if missing/empty.
     */
    protected function getTrendKeywords($settings = null): array
    {
        if ($settings === null) {
            $settings = $this->settingsModel->getSettings();
        }
        $json = is_object($settings) ? ($settings->trend_keywords_json ?? '') : '';
        $data = !empty($json) ? json_decode((string) $json, true) : null;
        if (!is_array($data) || empty($data['phrases']) && empty($data['words']) && empty($data['context_words'])) {
            $data = ContentAISettingsModel::getDefaultTrendKeywords();
        }
        return [
            'phrases'       => array_map(fn($s) => $this->normalizeText((string) $s), $data['phrases'] ?? []),
            'words'         => array_map(fn($s) => $this->normalizeText((string) $s), $data['words'] ?? []),
            'context_words' => array_map(fn($s) => $this->normalizeText((string) $s), $data['context_words'] ?? []),
        ];
    }

    protected function callTextModel(string $prompt)
    {
        $model = getenv('OPENAI_TEXT_MODEL') ?: 'gpt-5.4-mini';
        $timeout = intval(getenv('OPENAI_TEXT_TIMEOUT') ?: 90);
        if ($timeout < 10) {
            $timeout = 10;
        }
        $apiKey = getenv('OPENAI_API_KEY') ?: '';
        if (empty($apiKey)) {
            $ai = \aiWriter();
            if (!empty($ai) && !empty($ai->apiKey)) {
                $apiKey = $ai->apiKey;
            }
        }
        if (empty($apiKey)) {
            return false;
        }
        $systemMsg = 'Voce e um redator senior da GX Capital, portal de inteligencia financeira do Brasil. '
            . 'Escreva conteudo original, factual e otimizado para SEO. '
            . 'Responda APENAS com JSON valido, sem markdown ou texto extra.';
        $useResponses = (strpos($model, 'gpt-5') !== false);
        $endpoint = $useResponses
            ? 'https://api.openai.com/v1/responses'
            : 'https://api.openai.com/v1/chat/completions';

        if ($useResponses) {
            $payload = [
                'model' => $model,
                'instructions' => $systemMsg,
                'input' => $prompt,
                'temperature' => 0.7,
                'text' => ['format' => ['type' => 'json_object']],
            ];
        } else {
            $payload = [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemMsg],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'response_format' => ['type' => 'json_object'],
            ];
        }
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ];
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $response = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http === 200) {
            return json_decode($response, true);
        }
        log_message('error', 'ContentAI: text model HTTP ' . $http . ' model=' . $model . ' response=' . substr($response ?? '', 0, 500));
        $fallback = getenv('OPENAI_TEXT_FALLBACK_MODEL') ?: 'gpt-4.1-mini';
        if (!empty($fallback) && $fallback !== $model) {
            $useFbResponses = (strpos($fallback, 'gpt-5') !== false);
            $fbEndpoint = $useFbResponses
                ? 'https://api.openai.com/v1/responses'
                : 'https://api.openai.com/v1/chat/completions';
            if ($useFbResponses) {
                $payload = [
                    'model' => $fallback,
                    'instructions' => $systemMsg,
                    'input' => $prompt,
                    'temperature' => 0.7,
                    'text' => ['format' => ['type' => 'json_object']],
                ];
            } else {
                $payload = [
                    'model' => $fallback,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemMsg],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                    'response_format' => ['type' => 'json_object'],
                ];
            }
            $ch = curl_init($fbEndpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $response = curl_exec($ch);
            $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($http === 200) {
                return json_decode($response, true);
            }
            log_message('error', 'ContentAI: fallback model HTTP ' . $http . ' model=' . $fallback);
        }
        return false;
    }

    protected function extractTextFromResponse(array $resp): ?string
    {
        if (isset($resp['choices'][0]['message']['content'])) {
            return $resp['choices'][0]['message']['content'];
        }
        if (isset($resp['output_text']) && !empty($resp['output_text'])) {
            return $resp['output_text'];
        }
        if (isset($resp['output']) && is_array($resp['output'])) {
            foreach ($resp['output'] as $outItem) {
                if (!empty($outItem['content'][0]['text'])) {
                    return $outItem['content'][0]['text'];
                }
            }
        }
        if (isset($resp['content'][0]['text'])) {
            return $resp['content'][0]['text'];
        }
        return null;
    }

    protected function parseJsonPayload(?string $text): ?array
    {
        if (empty($text)) {
            return null;
        }
        $payload = json_decode($text, true);
        if (is_array($payload)) {
            return $payload;
        }
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start === false || $end === false || $end <= $start) {
            return null;
        }
        $json = substr($text, $start, ($end - $start) + 1);
        $payload = json_decode($json, true);
        return is_array($payload) ? $payload : null;
    }

    protected function getCategoryName(?int $categoryId): string
    {
        if (empty($categoryId)) {
            return 'Geral';
        }
        $row = $this->settingsModel->db->table('categories')->select('name')->where('id', $categoryId)->get()->getFirstRow();
        return !empty($row) ? $row->name : 'Geral';
    }

    protected function decodeCategoryIds(?string $json): array
    {
        if (empty($json)) {
            return [];
        }
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    protected function pickCategory(array $ids): ?int
    {
        if (empty($ids)) {
            $row = $this->settingsModel->db->table('categories')->select('id')->orderBy('id ASC')->get()->getFirstRow();
            return !empty($row) ? (int) $row->id : null;
        }
        return $ids[array_rand($ids)];
    }

    /**
     * Get simulators relevant to a given category and content keywords.
     */
    protected function getSimulatorsForContent(int $categoryId, string $title, string $contentHtml): array
    {
        // Full simulator registry: slug => [url, label, eyebrow, title, description, cta, keywords]
        $simulators = [
            'risco-cambial' => [
                'url' => '/simuladores/cambio',
                'label' => 'FX',
                'title' => 'Simulador de Risco Cambial',
                'description' => 'Calcule a exposicao cambial da sua empresa e veja como proteger suas margens.',
                'cta' => 'Simular risco cambial',
                'keywords' => ['cambio', 'dolar', 'exporta', 'importa', 'hedge', 'comex', 'ptax', 'moeda', 'forex', 'remessa', 'trade finance'],
                'categories' => [6], // Cambio
            ],
            'fx-loan' => [
                'url' => '/fx-loan',
                'label' => '4131',
                'title' => 'Simulador de Estrutura 4131 e FX Loan',
                'description' => 'Compare o custo de funding internacional vs credito local com hedge embutido.',
                'cta' => 'Avaliar estrutura',
                'keywords' => ['4131', 'funding', 'offshore', 'emprestimo internacional', 'fx loan'],
                'categories' => [6],
            ],
            'aurum' => [
                'url' => '/aurum-simulador-de-custo-de-capital',
                'label' => 'CAP',
                'title' => 'Simulador de Custo de Capital',
                'description' => 'Compare custos de diferentes linhas de credito e descubra a estrutura ideal para sua operacao.',
                'cta' => 'Calcular custo de capital',
                'keywords' => ['credito', 'emprestimo', 'financiamento', 'capital de giro', 'bndes', 'juros', 'spread', 'custo de capital'],
                'categories' => [8], // Credito
            ],
            'mercado-capitais' => [
                'url' => '/simulador-mercado-de-capitais',
                'label' => 'MKT',
                'title' => 'Simulador de Mercado de Capitais',
                'description' => 'Teste cenarios para debentures, CRA, CRI e outras estruturas de captacao fora do credito bancario.',
                'cta' => 'Explorar estruturas',
                'keywords' => ['debenture', 'cra', 'cri', 'mercado de capitais', 'captacao', 'renda fixa', 'titulo'],
                'categories' => [13], // Investimentos
            ],
            'antecipacao' => [
                'url' => '/simulador-de-custo-de-antecipacao',
                'label' => 'FIDC',
                'title' => 'Simulador de Custo de Antecipacao',
                'description' => 'Compare desconto bancario vs FIDC e descubra a antecipacao de recebiveis mais eficiente.',
                'cta' => 'Comparar custos',
                'keywords' => ['fidc', 'recebive', 'antecipacao', 'duplicata', 'factoring', 'desconto'],
                'categories' => [8],
            ],
            'consorcio' => [
                'url' => '/simulador-consorcio',
                'label' => 'CONS',
                'title' => 'Simulador de Consorcio Estrategico',
                'description' => 'Simule cenarios de consorcio, compare com financiamento e descubra a melhor estrategia de lance.',
                'cta' => 'Simular consorcio',
                'keywords' => ['consorcio', 'contemplacao', 'lance', 'carta de credito', 'consorcio imobiliario'],
                'categories' => [],
            ],
        ];

        $normalized = $this->normalizeText($title . ' ' . strip_tags($contentHtml));
        $matched = [];

        foreach ($simulators as $key => $sim) {
            $score = 0;
            // Category match = strong signal
            if (!empty($sim['categories']) && in_array($categoryId, $sim['categories'])) {
                $score += 10;
            }
            // Keyword matches in content
            foreach ($sim['keywords'] as $kw) {
                if (strpos($normalized, $kw) !== false) {
                    $score += 3;
                }
            }
            if ($score > 0) {
                $matched[$key] = ['sim' => $sim, 'score' => $score];
            }
        }

        // Sort by score descending, return top 2
        uasort($matched, fn($a, $b) => $b['score'] - $a['score']);
        $results = array_slice(array_column(array_values($matched), 'sim'), 0, 2);

        // Fallback: if no match, show the simulators hub CTA
        if (empty($results)) {
            $results[] = [
                'url' => '/simuladores',
                'label' => 'GX',
                'title' => 'Simuladores Financeiros GX Capital',
                'description' => 'Explore nossas ferramentas de simulacao para cambio, credito, consorcio e mercado de capitais.',
                'cta' => 'Conhecer simuladores',
            ];
        }

        return $results;
    }

    /**
     * Build HTML CTA block for a simulator to inject into article content.
     */
    protected function buildSimulatorCtaHtml(array $sim): string
    {
        $url = base_url($sim['url']);
        return '<div style="background:linear-gradient(135deg,#0a1628 0%,#142744 100%);border-radius:12px;padding:28px 32px;margin:32px 0;color:#fff;font-family:inherit;">'
            . '<div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">'
            . '<span style="background:#2a6af0;color:#fff;font-size:11px;font-weight:700;padding:3px 8px;border-radius:4px;letter-spacing:0.5px;">' . esc($sim['label']) . '</span>'
            . '<span style="font-size:13px;color:#8fa4c8;">Ferramenta GX Capital</span>'
            . '</div>'
            . '<h3 style="color:#fff;font-size:20px;margin:0 0 8px 0;font-weight:700;">' . esc($sim['title']) . '</h3>'
            . '<p style="color:#b0c4de;font-size:15px;margin:0 0 18px 0;line-height:1.5;">' . esc($sim['description']) . '</p>'
            . '<a href="' . esc($url) . '" style="display:inline-block;background:#2a6af0;color:#fff;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:15px;font-weight:600;transition:background 0.2s;" onmouseover="this.style.background=\'#1d5ad4\'" onmouseout="this.style.background=\'#2a6af0\'">' . esc($sim['cta']) . ' &rarr;</a>'
            . '</div>';
    }

    /**
     * Inject simulator CTAs into article HTML content.
     * Places the first CTA after the 2nd H2, and the second (if any) before the last H2.
     */
    protected function injectSimulatorCtas(string $contentHtml, int $categoryId, string $title): string
    {
        $sims = $this->getSimulatorsForContent($categoryId, $title, $contentHtml);
        if (empty($sims)) {
            return $contentHtml;
        }

        // Find all H2 positions
        preg_match_all('/<h2[\s>]/i', $contentHtml, $matches, PREG_OFFSET_CAPTURE);
        $h2Positions = array_column($matches[0], 1);

        if (count($h2Positions) < 2) {
            // Not enough H2s — append CTA at the end
            $contentHtml .= $this->buildSimulatorCtaHtml($sims[0]);
            return $contentHtml;
        }

        // Insert first CTA before the 3rd H2 (after 2nd section)
        $insertPos1 = $h2Positions[2] ?? $h2Positions[count($h2Positions) - 1];
        $cta1 = $this->buildSimulatorCtaHtml($sims[0]);
        $contentHtml = substr($contentHtml, 0, $insertPos1) . $cta1 . substr($contentHtml, $insertPos1);

        // If we have a 2nd simulator and enough H2s, insert before the last H2
        if (count($sims) > 1 && count($h2Positions) >= 4) {
            // Recalculate positions after first insertion
            preg_match_all('/<h2[\s>]/i', $contentHtml, $matches2, PREG_OFFSET_CAPTURE);
            $h2Pos2 = array_column($matches2[0], 1);
            $lastH2 = end($h2Pos2);
            $cta2 = $this->buildSimulatorCtaHtml($sims[1]);
            $contentHtml = substr($contentHtml, 0, $lastH2) . $cta2 . substr($contentHtml, $lastH2);
        }

        return $contentHtml;
    }

    public function buildPublishSlots($settings, int $count, \DateTimeImmutable $now): array
    {
        $times = [];
        foreach (['run_time_1', 'run_time_2', 'run_time_3'] as $field) {
            if (!empty($settings->$field)) {
                $times[] = $settings->$field;
            }
        }
        if (empty($times)) {
            $slots = [];
            for ($i = 0; $i < $count; $i++) {
                $slots[] = $now->modify('+' . ($i * 30) . ' minutes')->format('Y-m-d H:i:s');
            }
            return $slots;
        }
        sort($times);
        $today = $now->format('Y-m-d');
        $slots = [];
        if ($count <= count($times)) {
            for ($i = 0; $i < $count; $i++) {
                $slots[] = $today . ' ' . $times[$i];
            }
            return $slots;
        }
        $start = new \DateTimeImmutable($today . ' ' . $times[0]);
        $end = new \DateTimeImmutable($today . ' ' . $times[count($times) - 1]);
        $totalMinutes = max(30, (int) round(($end->getTimestamp() - $start->getTimestamp()) / 60));
        $step = (int) floor($totalMinutes / max(1, $count - 1));
        for ($i = 0; $i < $count; $i++) {
            $slots[] = $start->modify('+' . ($i * $step) . ' minutes')->format('Y-m-d H:i:s');
        }
        return $slots;
    }

    protected function getDueSlots($settings, \DateTimeImmutable $now): array
    {
        $slots = [];
        $tz = $now->getTimezone();
        $today = $now->format('Y-m-d');
        for ($i = 1; $i <= 3; $i++) {
            $timeField = 'run_time_' . $i;
            $lastField = 'last_run_' . $i;
            $time = $settings->$timeField ?? null;
            if (empty($time)) {
                continue;
            }
            $slotTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $today . ' ' . $time, $tz);
            if (!$slotTime) {
                continue;
            }
            $windowStart = $slotTime->modify('-10 minutes');
            $windowEnd = $slotTime->modify('+10 minutes');
            $lastRun = !empty($settings->$lastField) ? new \DateTimeImmutable($settings->$lastField) : null;
            $alreadyRanToday = $lastRun && $lastRun->format('Y-m-d') === $today;
            if ($now >= $windowStart && $now <= $windowEnd && !$alreadyRanToday) {
                $slots[] = $i;
            }
        }
        return $slots;
    }

    protected function downloadImage(string $url, string $destPath): bool
    {
        $ch = curl_init($url);
        $fp = fopen($destPath, 'w');
        if ($fp === false) {
            curl_close($ch);
            return false;
        }
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $ok = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        fclose($fp);
        if ($ok === false || $http < 200 || $http >= 300) {
            if (file_exists($destPath)) {
                @unlink($destPath);
            }
            log_message('error', 'AI image download failed: HTTP ' . $http . ' - ' . $err);
            return false;
        }
        return true;
    }
}
