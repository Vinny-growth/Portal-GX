<?php

namespace App\Libraries;

use App\Models\WebStoriesModel;
use App\Models\WebStoryPagesModel;
use App\Helpers\OpenAIImageHelper;

class WebStoriesGenerator
{
    protected $webStoriesModel;
    protected $pagesModel;
    protected $imageHelper;

    public function __construct()
    {
        $this->webStoriesModel = new WebStoriesModel();
        $this->pagesModel = new WebStoryPagesModel();
        $this->imageHelper = new OpenAIImageHelper();
    }

    public function generateStructureFromPost($post)
    {
        $prompt = $this->buildPrompt($post);
        $resp = $this->callTextModel($prompt);
        if (!$resp) {
            return false;
        }
        $content = $this->extractTextFromResponse($resp);
        if (empty($content)) {
            return false;
        }
        $data = $this->parseStoryJson($content, $post);
        return $data;
    }

    public function createStoryAndPages($post, array $structure)
    {
        $storyId = $this->webStoriesModel->builderWebStories->insert([
            'title' => $structure['title'] ?? ($post->title ?? 'Web Story'),
            'description' => $structure['description'] ?? '',
            'link_url' => '',
            'is_generated' => 1,
            'generation_prompt' => 'Generated from article: ' . ($post->title ?? ''),
            'is_active' => 1,
            'display_order' => 1,
            'lang_id' => $post->lang_id,
            'category_id' => $post->category_id,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        if (!$storyId) {
            return false;
        }
        $id = $this->webStoriesModel->db->insertID();
        $pages = $structure['pages'] ?? [];
        $order = 1;
        foreach ($pages as $p) {
            $pageData = [
                'web_story_id' => $id,
                'page_order' => $order++,
                'page_type' => $p['page_type'] ?? 'content',
                'title' => $p['title'] ?? '',
                'content' => $p['content'] ?? '',
                'background_type' => $p['background_type'] ?? 'gradient',
                'background_value' => $p['background_value'] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'image_url' => '',
                'image_path' => '',
                'video_url' => '',
                'cta_text' => $p['cta_text'] ?? '',
                'cta_url' => $p['cta_url'] ?? '',
                'text_color' => '#FFFFFF',
                'text_position' => $p['text_position'] ?? 'center',
                'font_size' => $p['font_size'] ?? 'medium',
                'animation' => '',
                'duration' => 5,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->pagesModel->addWebStoryPage($id, $pageData);
        }
        return $id;
    }

    public function generateNextImageForStory(int $webStoryId, $post)
    {
        // Daily budget guard — each generated image counts. Stops the
        // pipeline before it can run away on a bad day.
        if (!$this->reserveDailyImageBudget()) {
            return [
                'done' => false,
                'error' => 'Quota diária de geração de imagens atingida. Tente novamente amanhã ou aumente WEBSTORIES_DAILY_IMAGE_LIMIT no .env.',
                'quota_reached' => true,
            ];
        }

        $builder = $this->pagesModel->builderWebStoryPages;
        $page = $builder->where('web_story_id', $webStoryId)
            ->where('(image_path IS NULL OR image_path = "")', null, false)
            ->orderBy('page_order', 'ASC')
            ->get()->getRow();
        if (empty($page)) {
            return [
                'done' => true,
                'message' => 'All images generated'
            ];
        }
        $prompt = $this->buildImagePrompt((array)$page, $post);
        $model = getenv('OPENAI_DEFAULT_MODEL') ?: 'gpt-image-1-mini';
        $isGptImage = is_string($model) && strpos($model, 'gpt-image-1') === 0;
        $size = getenv('OPENAI_DEFAULT_SIZE') ?: ($model === 'dall-e-3' ? '1024x1792' : '1024x1536');
        $quality = getenv('OPENAI_DEFAULT_QUALITY') ?: ($isGptImage ? 'high' : 'hd');

        $result = $this->imageHelper->generateImage($prompt, $model, $size, $quality);
        if ($result && isset($result['data'][0])) {
            $imageUrl = $result['data'][0]['url'] ?? null;
            $b64 = $result['data'][0]['b64_json'] ?? null;
            $saved = false;
            if (!empty($imageUrl)) {
                $saved = $this->downloadAndSave($imageUrl, $page->title);
            } elseif (!empty($b64)) {
                $saved = $this->saveBase64($b64, $page->title);
            }
            if ($saved) {
                $builder->where('id', $page->id)->update([
                    'background_type' => 'image',
                    'background_value' => $saved['image_path'],
                    'image_url' => $saved['image_url'],
                    'image_path' => $saved['image_path']
                ]);
                // If story has no cover image yet, set this as cover image
                // and produce the AMP poster variants beside it.
                $story = $this->webStoriesModel->getWebStory($webStoryId);
                if ($story && empty($story->image_path) && empty($story->image_url)) {
                    $this->webStoriesModel->builderWebStories
                        ->where('id', $webStoryId)
                        ->update([
                            'image_path' => $saved['image_path'],
                            'image_url' => $saved['image_url']
                        ]);

                    $coverAbs = FCPATH . ltrim($saved['image_path'], '/');
                    if (is_file($coverAbs)) {
                        $this->webStoriesModel->generatePosterVariants($coverAbs);
                    }
                }
                return [
                    'done' => false,
                    'page' => $page->page_order
                ];
            }
        }
        return [
            'done' => false,
            'error' => 'Failed to generate image'
        ];
    }

    /**
     * Cross-process daily quota counter for AI image generation. Stored in
     * the existing `dashboard_settings` key-value table to avoid a new schema.
     * Default cap is 50/day — override with WEBSTORIES_DAILY_IMAGE_LIMIT.
     * Returns true when the request fits in budget (and the counter is
     * incremented), false when the quota is already exhausted.
     */
    protected function reserveDailyImageBudget(): bool
    {
        $cap = (int) (getenv('WEBSTORIES_DAILY_IMAGE_LIMIT') ?: 50);
        if ($cap <= 0) {
            return true; // disabled
        }
        $today = date('Y-m-d');
        $key = 'webstories_image_budget_' . $today;

        $db = \Config\Database::connect();
        if (!$db->tableExists('dashboard_settings')) {
            return true; // soft-fail open if the table isn't there yet
        }

        $db->transStart();
        $row = $db->table('dashboard_settings')->where('chave', $key)->get()->getRow();
        $current = (int) ($row->valor ?? 0);
        if ($current >= $cap) {
            $db->transComplete();
            return false;
        }

        $next = $current + 1;
        if ($row) {
            $db->table('dashboard_settings')->where('chave', $key)->update(['valor' => (string) $next]);
        } else {
            $db->table('dashboard_settings')->insert(['chave' => $key, 'valor' => (string) $next]);
        }
        $db->transComplete();

        return true;
    }

    public function getImageProgress(int $webStoryId): array
    {
        $builder = $this->pagesModel->builderWebStoryPages;
        $pages = $builder->where('web_story_id', $webStoryId)->get()->getResult();
        $total = count($pages);
        $withImg = 0;
        foreach ($pages as $p) {
            if (!empty($p->image_path) || $p->background_type === 'image') {
                $withImg++;
            }
        }
        return [
            'total' => $total,
            'with' => $withImg,
            'progress' => $total > 0 ? round(($withImg / $total) * 100) : 0,
        ];
    }

    /* --------------------- internals ---------------------- */

    protected function buildPrompt($post): string
    {
        $baseUrl = generateBaseURLByLangId($post->lang_id);
        $postUrl = generatePostURL($post, $baseUrl);
        $content = strip_tags($post->content ?? '');
        if (strlen($content) > 6000) {
            $content = substr($content, 0, 6000);
        }
        // Google Web Stories indexa melhor stories com 5+ páginas (idealmente
        // 6-8). 4 páginas técnicamente válidas mas raramente surgem em Discover.
        // Paleta GX Nexus: navy #0c3163 / #000d23, champagne #87704a / #c9a96a.
        return "Você é um especialista em Web Stories editoriais. Com base no artigo abaixo, gere uma estrutura com 5 a 6 páginas seguindo EXATAMENTE o formato JSON fornecido.

ARTIGO:
Título: {$post->title}
Conteúdo: {$content}

INSTRUÇÕES:
1. Página 1 (Capa): Título do artigo (curto, ≤8 palavras) + tagline de 1 linha.
2. Página 2: Contexto / problema que motiva a leitura — 2-3 frases curtas.
3. Página 3: Primeiro insight ou dado-chave.
4. Página 4: Segundo insight, número ou benefício prático.
5. Página 5 (opcional): Terceiro insight ou exemplo aplicado. Use se o artigo tiver densidade.
6. Última página (CTA): Convite para leitura completa, link para {$postUrl}.

REGRAS:
- Cada \"content\" deve ter 1-3 frases (máx 200 caracteres). Linguagem direta, sem jargão.
- \"page_type\" deve ser 'cover' na primeira, 'cta' na última, 'content' nas do meio.
- Sempre 5 ou 6 páginas no total — nunca 4 ou menos.
- Use a paleta GX Capital nos gradientes (azul-marinho profundo + champagne).

RESPONDA APENAS COM JSON VÁLIDO SEM TEXTO EXTRA:
{
  \"title\": \"...\",
  \"description\": \"...\",
  \"pages\": [
    {\"page_type\": \"cover\", \"title\": \"...\", \"content\": \"...\", \"background_type\": \"gradient\", \"background_value\": \"linear-gradient(160deg, #000d23 0%, #0c3163 60%, #87704a 100%)\", \"text_position\": \"center\", \"font_size\": \"large\"},
    {\"page_type\": \"content\", \"title\": \"...\", \"content\": \"...\", \"background_type\": \"gradient\", \"background_value\": \"linear-gradient(160deg, #0c3163 0%, #000d23 100%)\", \"text_position\": \"center\", \"font_size\": \"medium\"},
    {\"page_type\": \"content\", \"title\": \"...\", \"content\": \"...\", \"background_type\": \"gradient\", \"background_value\": \"linear-gradient(160deg, #0c3163 0%, #000d23 100%)\", \"text_position\": \"center\", \"font_size\": \"medium\"},
    {\"page_type\": \"content\", \"title\": \"...\", \"content\": \"...\", \"background_type\": \"gradient\", \"background_value\": \"linear-gradient(160deg, #0c3163 0%, #000d23 100%)\", \"text_position\": \"center\", \"font_size\": \"medium\"},
    {\"page_type\": \"cta\", \"title\": \"Leia a matéria completa\", \"content\": \"...\", \"background_type\": \"gradient\", \"background_value\": \"linear-gradient(160deg, #87704a 0%, #0c3163 100%)\", \"text_position\": \"center\", \"font_size\": \"medium\", \"cta_text\": \"Leia matéria completa\", \"cta_url\": \"{$postUrl}\"}
  ]
}";
    }

    protected function callTextModel(string $prompt)
    {
        $model = getenv('OPENAI_TEXT_MODEL') ?: 'gpt-5.4-mini';
        $headers = [
            'Authorization: Bearer ' . (getenv('OPENAI_API_KEY') ?: ''),
            'Content-Type: application/json'
        ];
        $timeout = intval(getenv('OPENAI_TEXT_TIMEOUT') ?: 45);
        if ($timeout < 10) { $timeout = 10; }

        $endpoint = (strpos($model, 'gpt-5') !== false)
            ? 'https://api.openai.com/v1/responses'
            : 'https://api.openai.com/v1/chat/completions';

        if ($endpoint === 'https://api.openai.com/v1/responses') {
            $payload = [
                'model' => $model,
                'input' => $prompt
            ];
        } else {
            $payload = [
                'model' => $model,
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'temperature' => 0.7
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
        // fallback
        $fallback = getenv('OPENAI_TEXT_FALLBACK_MODEL') ?: 'gpt-4.1-mini';
        if ($fallback && $fallback !== $model) {
            if (strpos($fallback, 'gpt-5') !== false) {
                $endpoint = 'https://api.openai.com/v1/responses';
                $payload = ['model' => $fallback, 'input' => $prompt];
            } else {
                $endpoint = 'https://api.openai.com/v1/chat/completions';
                $payload = ['model' => $fallback, 'messages' => [['role' => 'user', 'content' => $prompt]], 'temperature' => 0.7];
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
        if (isset($resp['output_text']) && is_string($resp['output_text'])) {
            return $resp['output_text'];
        }
        if (isset($resp['output']) && is_array($resp['output'])) {
            foreach ($resp['output'] as $out) {
                if (($out['type'] ?? '') === 'message' && isset($out['content'])) {
                    foreach ($out['content'] as $part) {
                        if (isset($part['text']) && is_string($part['text']) && $part['text'] !== '') {
                            return $part['text'];
                        }
                    }
                }
            }
        }
        if (isset($resp['content'][0]['text'])) {
            return $resp['content'][0]['text'];
        }
        return null;
    }

    protected function parseStoryJson(string $raw, $post): array
    {
        $raw = trim($raw);
        // remove code fences
        $raw = preg_replace('/^```json\s*/', '', $raw);
        $raw = preg_replace('/```$/', '', $raw);
        // extract first JSON object
        $json = $this->extractFirstJson($raw);
        $data = json_decode($json, true);
        if (!is_array($data) || !isset($data['pages']) || !is_array($data['pages'])) {
            // Fallback minimal structure — 5 páginas (Google indexa melhor com ≥5).
            $data = [
                'title' => $post->title ?? 'Web Story',
                'description' => 'Generated from article',
                'pages' => $this->defaultStoryPages($post),
            ];
        }
        // normalize pages
        $defaultGradient = 'linear-gradient(160deg, #0c3163 0%, #000d23 100%)';
        $norm = [];
        foreach ($data['pages'] as $p) {
            $norm[] = [
                'page_type' => $p['page_type'] ?? 'content',
                'title' => $p['title'] ?? '',
                'content' => $p['content'] ?? '',
                'background_type' => $p['background_type'] ?? 'gradient',
                'background_value' => $p['background_value'] ?? $defaultGradient,
                'text_position' => $p['text_position'] ?? 'center',
                'font_size' => $p['font_size'] ?? 'medium',
                'cta_text' => $p['cta_text'] ?? '',
                'cta_url' => $p['cta_url'] ?? ''
            ];
        }

        // Garante mínimo de 5 páginas para indexação no Google. Se o modelo
        // devolver menos, preenche com páginas de continuidade ANTES do CTA
        // (preservando a ordem cover → content → ... → cta).
        $norm = $this->ensureMinimumPageCount($norm, $post, 5);

        $data['pages'] = $norm;
        return $data;
    }

    private function defaultStoryPages($post): array
    {
        $baseUrl = generateBaseURLByLangId($post->lang_id ?? null);
        $postUrl = generatePostURL($post, $baseUrl);
        $contentGradient = 'linear-gradient(160deg, #0c3163 0%, #000d23 100%)';
        $coverGradient   = 'linear-gradient(160deg, #000d23 0%, #0c3163 60%, #87704a 100%)';
        $ctaGradient     = 'linear-gradient(160deg, #87704a 0%, #0c3163 100%)';

        return [
            ['page_type' => 'cover',   'title' => $post->title ?? 'Capa',          'content' => '',                       'background_type' => 'gradient', 'background_value' => $coverGradient,   'text_position' => 'center', 'font_size' => 'large'],
            ['page_type' => 'content', 'title' => 'Contexto',                      'content' => '',                       'background_type' => 'gradient', 'background_value' => $contentGradient, 'text_position' => 'center', 'font_size' => 'medium'],
            ['page_type' => 'content', 'title' => 'Ponto-chave',                   'content' => '',                       'background_type' => 'gradient', 'background_value' => $contentGradient, 'text_position' => 'center', 'font_size' => 'medium'],
            ['page_type' => 'content', 'title' => 'Detalhe importante',            'content' => '',                       'background_type' => 'gradient', 'background_value' => $contentGradient, 'text_position' => 'center', 'font_size' => 'medium'],
            ['page_type' => 'cta',     'title' => 'Leia a matéria completa',       'content' => '',                       'background_type' => 'gradient', 'background_value' => $ctaGradient,     'text_position' => 'center', 'font_size' => 'medium', 'cta_text' => 'Leia matéria completa', 'cta_url' => $postUrl],
        ];
    }

    private function ensureMinimumPageCount(array $pages, $post, int $minimum): array
    {
        if (count($pages) >= $minimum) {
            return $pages;
        }

        $contentGradient = 'linear-gradient(160deg, #0c3163 0%, #000d23 100%)';

        // Localiza a página CTA (deve ficar sempre por último). Se não houver,
        // tudo bem — apenas anexamos páginas de conteúdo no final.
        $ctaIndex = null;
        foreach ($pages as $idx => $p) {
            if (($p['page_type'] ?? '') === 'cta') {
                $ctaIndex = $idx;
                break;
            }
        }

        $padding = [];
        $needed = $minimum - count($pages);
        for ($i = 0; $i < $needed; $i++) {
            $padding[] = [
                'page_type' => 'content',
                'title' => 'Mais sobre o tema',
                'content' => '',
                'background_type' => 'gradient',
                'background_value' => $contentGradient,
                'text_position' => 'center',
                'font_size' => 'medium',
                'cta_text' => '',
                'cta_url' => '',
            ];
        }

        if ($ctaIndex === null) {
            return array_merge($pages, $padding);
        }

        // Insere o padding ANTES do CTA — mantém o CTA sempre como última página.
        return array_merge(
            array_slice($pages, 0, $ctaIndex),
            $padding,
            array_slice($pages, $ctaIndex)
        );
    }

    protected function extractFirstJson(string $text): string
    {
        // naive extraction of first {...} block
        $start = strpos($text, '{');
        if ($start === false) {
            return '{}';
        }
        $level = 0;
        $len = strlen($text);
        for ($i = $start; $i < $len; $i++) {
            if ($text[$i] === '{') $level++;
            if ($text[$i] === '}') {
                $level--;
                if ($level === 0) {
                    return substr($text, $start, $i - $start + 1);
                }
            }
        }
        return substr($text, $start);
    }

    protected function buildImagePrompt(array $pageData, $post): string
    {
        $title = $pageData['title'] ?? '';
        $type = $pageData['page_type'] ?? 'content';
        $ctx = !empty($post->title) ? (" related to: " . substr($post->title, 0, 120)) : '';

        // Prompt base focado em foto realista e área segura central para texto
        $common = 
            'photorealistic, realistic textures, natural lighting, shallow depth of field, '
          . 'cinematic look, high dynamic range, detailed, noise-free, '
          . 'vertical mobile format, clean composition, no text, no watermark, '
          . 'leave clear negative space in the center as a text-safe area, '
          . 'safe margins around the central area, avoid clutter, subject framing leaves central area unobstructed';

        switch ($type) {
            case 'cover':
                $base = "Photorealistic cover image for: {$title}{$ctx}. Subject positioned to leave central negative space for text";
                break;
            case 'cta':
                $base = "Photorealistic background for call-to-action. Minimal, soft bokeh or subtle gradient, ample central negative space for text";
                break;
            default:
                $base = "Photorealistic scene for: {$title}{$ctx}. Subject off-center, keep central area clean for text overlay";
        }

        return $base . ', ' . $common . ', professional look, suitable for web stories';
    }

    protected function downloadAndSave(string $imageUrl, string $title)
    {
        $uploadPath = FCPATH . 'uploads/web_stories/ai_generated/';
        if (!is_dir($uploadPath)) { @mkdir($uploadPath, 0755, true); }
        $name = substr(preg_replace('/[^A-Za-z0-9\-]/', '_', $title), 0, 50);
        $uid = uniqid();
        $canWebp = function_exists('imagewebp');
        $ext = $canWebp ? 'webp' : 'png';
        $filePath = $uploadPath . $name . '_' . $uid . '.' . $ext;
        $raw = $this->fetchUrlBytes($imageUrl);
        if ($raw === null) { return false; }
        if ($canWebp) {
            $temp = $uploadPath . 'temp_' . $uid . '.png';
            @file_put_contents($temp, $raw);
            $ok = $this->convertToWebp($temp, $filePath);
            if (file_exists($temp)) { @unlink($temp); }
            if (!$ok) { return false; }
        } else {
            @file_put_contents($filePath, $raw);
        }
        $rel = str_replace(FCPATH, '', $filePath);
        return ['image_path' => $rel, 'image_url' => base_url($rel)];
    }

    /**
     * Bounded HTTP fetch — replaces unguarded file_get_contents() so a slow
     * or unreachable image URL can never freeze the generation pipeline.
     */
    protected function fetchUrlBytes(string $url): ?string
    {
        if ($url === '') { return null; }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $body = curl_exec($ch);
        $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);
        if ($body === false || $http < 200 || $http >= 300) {
            log_message('error', 'WebStoriesGenerator: download failed (HTTP ' . $http . ') ' . $err);
            return null;
        }
        return $body;
    }

    protected function saveBase64(string $b64, string $title)
    {
        $uploadPath = FCPATH . 'uploads/web_stories/ai_generated/';
        if (!is_dir($uploadPath)) { @mkdir($uploadPath, 0755, true); }
        $name = substr(preg_replace('/[^A-Za-z0-9\-]/', '_', $title), 0, 50);
        $uid = uniqid();
        $canWebp = function_exists('imagewebp');
        $ext = $canWebp ? 'webp' : 'png';
        $filePath = $uploadPath . $name . '_' . $uid . '.' . $ext;
        $raw = base64_decode($b64);
        if ($raw === false) { return false; }
        if ($canWebp) {
            $temp = $uploadPath . 'temp_' . $uid . '.png';
            @file_put_contents($temp, $raw);
            $ok = $this->convertToWebp($temp, $filePath);
            if (file_exists($temp)) { @unlink($temp); }
            if (!$ok) { return false; }
        } else {
            @file_put_contents($filePath, $raw);
        }
        $rel = str_replace(FCPATH, '', $filePath);
        return ['image_path' => $rel, 'image_url' => base_url($rel)];
    }

    protected function convertToWebp(string $src, string $dst): bool
    {
        if (!file_exists($src) || !function_exists('imagewebp')) { return false; }
        $im = @imagecreatefrompng($src);
        if (!$im) { return false; }
        $ok = imagewebp($im, $dst, 80);
        imagedestroy($im);
        return (bool)$ok;
    }
}
