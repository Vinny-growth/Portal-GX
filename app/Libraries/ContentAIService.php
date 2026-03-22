<?php

namespace App\Libraries;

use App\Helpers\OpenAIImageHelper;
use App\Models\ContentAISettingsModel;
use App\Models\ContentCalendarModel;
use App\Models\ContentRunModel;
use App\Models\TrendItemModel;
use App\Models\UploadModel;

class ContentAIService
{
    protected $settingsModel;
    protected $calendarModel;
    protected $runModel;
    protected $trendModel;
    protected $imageHelper;

    public function __construct()
    {
        $this->settingsModel = new ContentAISettingsModel();
        $this->calendarModel = new ContentCalendarModel();
        $this->runModel = new ContentRunModel();
        $this->trendModel = new TrendItemModel();
        $this->imageHelper = new OpenAIImageHelper();
    }

    public function runScheduled(): array
    {
        $settings = $this->settingsModel->getSettings();
        $now = new \DateTimeImmutable('now');
        $dueSlots = $this->getDueSlots($settings, $now);
        $result = [
            'slots' => $dueSlots,
            'planned' => 0,
            'generated' => 0,
            'errors' => [],
        ];
        foreach ($dueSlots as $slot) {
            $planned = $this->planDailyFromTrends($settings, $now);
            $result['planned'] += $planned;
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
        $stale = $this->runModel->markStaleRuns(10);
        if ($stale > 0) {
            log_message('warning', 'ContentAI: marked stale runs as failed: ' . $stale);
        }
        $now = date('Y-m-d H:i:s');
        $items = $this->calendarModel->getDueToGenerate($now, $limit);
        log_message('info', 'ContentAI: processQueue items=' . count($items));
        $count = 0;
        $errors = [];
        foreach ($items as $item) {
            $runId = $this->runModel->startRun((int) $item->id, 'generate');
            log_message('info', 'ContentAI: generating calendar_id=' . $item->id . ' title=' . ($item->title ?? ''));
            $res = $this->generatePostFromCalendarItem($item);
            if ($res['success']) {
                $count++;
                $this->runModel->finishRun($runId, 'success', null, $res['prompt'] ?? null, $res['response'] ?? null);
                log_message('info', 'ContentAI: generated calendar_id=' . $item->id);
            } else {
                $errors[] = $res['error'] ?? 'unknown error';
                $this->runModel->finishRun($runId, 'failed', $res['error'] ?? 'unknown error', $res['prompt'] ?? null, $res['response'] ?? null);
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
        $maxFromTrends = !empty($settings->trends_per_day) ? (int) $settings->trends_per_day : $need;
        $limit = min($need, max(1, $maxFromTrends));
        $candidates = $this->trendModel->getCandidates($limit);
        if (empty($candidates)) {
            return 0;
        }
        $allowedCategories = $this->decodeCategoryIds($settings->allowed_category_ids);
        $slots = $this->buildPublishSlots($settings, $need, $now);
        $created = 0;
        foreach ($candidates as $idx => $trend) {
            $publishAt = $slots[$idx] ?? $now->format('Y-m-d H:i:s');
            $categoryId = !empty($trend->category_id) ? $trend->category_id : $this->pickCategory($allowedCategories);
            $data = [
                'title' => $trend->title,
                'instructions' => 'Use a tendencia selecionada como tema principal.',
                'category_id' => $categoryId,
                'lang_id' => $settings->lang_id,
                'user_id' => $settings->default_user_id,
                'tone' => $settings->default_tone,
                'length' => $settings->default_length,
                'publish_at' => $publishAt,
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
        return $created;
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
            $this->calendarModel->markStatus((int) $item->id, 'failed');
            return ['success' => false, 'error' => 'failed to call text model', 'prompt' => $prompt];
        }
        $text = $this->extractTextFromResponse($response);
        $payload = $this->parseJsonPayload($text);
        if (empty($payload)) {
            $this->calendarModel->markStatus((int) $item->id, 'failed');
            return ['success' => false, 'error' => 'invalid json response', 'prompt' => $prompt, 'response' => $text];
        }
        $title = $payload['title'] ?? $item->title;
        $summary = $payload['summary'] ?? '';
        $contentHtml = $payload['content_html'] ?? '';
        $tags = $payload['tags'] ?? [];

        if (empty($title) || empty($contentHtml)) {
            $this->calendarModel->markStatus((int) $item->id, 'failed');
            return ['success' => false, 'error' => 'missing title or content', 'prompt' => $prompt, 'response' => $text];
        }

        $postId = $this->createPost($item, $settings, $title, $summary, $contentHtml, $tags, null);
        if (empty($postId)) {
            $this->calendarModel->markStatus((int) $item->id, 'failed');
            return ['success' => false, 'error' => 'failed to create post', 'prompt' => $prompt, 'response' => $text];
        }
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
        $allowWeb = strtolower((string) (getenv('CONTENT_AI_WEB_IMAGES') ?: ''));
        if ($allowWeb === '1' || $allowWeb === 'true' || $allowWeb === 'yes') {
            return true;
        }
        return is_cli();
    }

    protected function createPost($item, $settings, string $title, string $summary, string $contentHtml, array $tags, ?int $imageId): ?int
    {
        $db = \Config\Database::connect(null, false);
        $now = date('Y-m-d H:i:s');
        $langId = $item->lang_id ?: $settings->lang_id;
        $categoryId = $item->category_id ?: $this->selectCategoryId($item, $settings);
        $userId = $item->user_id ?: $settings->default_user_id;
        $autoPublish = !empty($settings->auto_publish);
        $createdAt = $autoPublish && !empty($item->publish_at) ? $item->publish_at : $now;
        $titleHash = md5(mb_strtolower($title));
        $slug = strSlug($title);
        $dupCount = $db->table('posts')
            ->groupStart()
                ->where('title_hash', $titleHash)
                ->orWhere('slug', $slug)
            ->groupEnd()
            ->countAllResults();
        if ($dupCount > 0) {
            $db->close();
            return null;
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
            'image_description' => '',
            'video_path' => '',
            'video_embed_code' => '',
            'user_id' => $userId,
            'post_url' => '',
            'show_post_url' => 0,
            'is_scheduled' => $autoPublish ? 1 : 0,
            'status' => $autoPublish ? 1 : 0,
            'created_at' => $createdAt,
            'updated_at' => $now,
        ];
        if (!$db->table('posts')->insert($data)) {
            $db->close();
            return null;
        }
        $postId = (int) $db->insertID();
        $db->close();
        updateSlug('posts', $postId);
        resetCacheDataOnChange();
        return $postId;
    }

    protected function generateCoverImage(string $prompt, string $title, int $userId): ?int
    {
        log_message('info', 'ContentAI: generating image model=' . (getenv('OPENAI_DEFAULT_MODEL') ?: 'gpt-image-1'));
        $model = getenv('OPENAI_DEFAULT_MODEL') ?: 'gpt-image-1';
        $quality = getenv('OPENAI_DEFAULT_QUALITY') ?: (($model === 'gpt-image-1') ? 'high' : 'hd');
        $size = ($model === 'gpt-image-1') ? '1536x1024' : (getenv('OPENAI_DEFAULT_SIZE') ?: '1024x1024');

        $result = $this->imageHelper->generateImage($prompt, $model, $size, $quality, 1);
        if (!$result || empty($result['data'][0])) {
            log_message('error', 'ContentAI: image generation failed');
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
                . "  \"image_prompt\": \"...\"\n"
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

    protected function callTextModel(string $prompt)
    {
        $model = getenv('OPENAI_TEXT_MODEL') ?: 'gpt-4o-mini';
        $timeout = intval(getenv('OPENAI_TEXT_TIMEOUT') ?: 45);
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
        $endpoint = (strpos($model, 'gpt-5') !== false)
            ? 'https://api.openai.com/v1/responses'
            : 'https://api.openai.com/v1/chat/completions';

        if ($endpoint === 'https://api.openai.com/v1/responses') {
            $payload = ['model' => $model, 'input' => $prompt];
        } else {
            $payload = [
                'model' => $model,
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'temperature' => 0.7,
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
        $fallback = getenv('OPENAI_TEXT_FALLBACK_MODEL') ?: '';
        if (!empty($fallback) && $fallback !== $model) {
            $endpoint = (strpos($fallback, 'gpt-5') !== false)
                ? 'https://api.openai.com/v1/responses'
                : 'https://api.openai.com/v1/chat/completions';
            if ($endpoint === 'https://api.openai.com/v1/responses') {
                $payload = ['model' => $fallback, 'input' => $prompt];
            } else {
                $payload = [
                    'model' => $fallback,
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => 0.7,
                ];
            }
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
        $today = $now->format('Y-m-d');
        for ($i = 1; $i <= 3; $i++) {
            $timeField = 'run_time_' . $i;
            $lastField = 'last_run_' . $i;
            $time = $settings->$timeField ?? null;
            if (empty($time)) {
                continue;
            }
            $slotTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $today . ' ' . $time);
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
