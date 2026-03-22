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
        $model = getenv('OPENAI_DEFAULT_MODEL') ?: 'dall-e-3';
        $size = getenv('OPENAI_DEFAULT_SIZE') ?: ($model === 'dall-e-3' ? '1024x1792' : '1024x1536');
        $quality = getenv('OPENAI_DEFAULT_QUALITY') ?: ($model === 'dall-e-3' ? 'hd' : 'high');

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
                $story = $this->webStoriesModel->getWebStory($webStoryId);
                if ($story && empty($story->image_path) && empty($story->image_url)) {
                    $this->webStoriesModel->builderWebStories
                        ->where('id', $webStoryId)
                        ->update([
                            'image_path' => $saved['image_path'],
                            'image_url' => $saved['image_url']
                        ]);
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
        return "Você é um especialista em criar Web Stories envolventes. Com base no artigo abaixo, gere EXATAMENTE 4 páginas seguindo o formato JSON fornecido.

ARTIGO:
Título: {$post->title}
Conteúdo: {$content}

INSTRUÇÕES:
1. Página 1 (Capa): Título e resumo curto
2. Página 2: Primeiro ponto principal
3. Página 3: Segundo ponto principal
4. Página 4 (CTA): Chamada com link para o artigo ({$postUrl})

RESPONDA APENAS COM JSON VÁLIDO SEM TEXTO EXTRA:
{
  \"title\": \"...\",
  \"description\": \"...\",
  \"pages\": [
    {\"page_type\": \"cover\", \"title\": \"...\", \"content\": \"...\", \"background_type\": \"gradient\", \"background_value\": \"linear-gradient(135deg, #667eea 0%, #764ba2 100%)\", \"text_position\": \"center\", \"font_size\": \"large\"},
    {\"page_type\": \"content\", \"title\": \"...\", \"content\": \"...\", \"background_type\": \"gradient\", \"background_value\": \"linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)\", \"text_position\": \"center\", \"font_size\": \"medium\"},
    {\"page_type\": \"content\", \"title\": \"...\", \"content\": \"...\", \"background_type\": \"gradient\", \"background_value\": \"linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)\", \"text_position\": \"center\", \"font_size\": \"medium\"},
    {\"page_type\": \"cta\", \"title\": \"Leia a matéria completa\", \"content\": \"...\", \"background_type\": \"gradient\", \"background_value\": \"linear-gradient(135deg, #fa709a 0%, #fee140 100%)\", \"text_position\": \"center\", \"font_size\": \"medium\", \"cta_text\": \"Leia matéria completa\", \"cta_url\": \"{$postUrl}\"}
  ]
}";
    }

    protected function callTextModel(string $prompt)
    {
        $model = getenv('OPENAI_TEXT_MODEL') ?: 'gpt-4o-mini';
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
        $fallback = getenv('OPENAI_TEXT_FALLBACK_MODEL') ?: 'gpt-4o-mini';
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
            // fallback minimal structure
            $data = [
                'title' => $post->title ?? 'Web Story',
                'description' => 'Generated from article',
                'pages' => [
                    ['page_type' => 'cover', 'title' => $post->title ?? 'Capa', 'content' => '', 'background_type' => 'gradient', 'background_value' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'text_position' => 'center', 'font_size' => 'large'],
                    ['page_type' => 'content', 'title' => 'Resumo 1', 'content' => '', 'background_type' => 'gradient', 'background_value' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)', 'text_position' => 'center', 'font_size' => 'medium'],
                    ['page_type' => 'content', 'title' => 'Resumo 2', 'content' => '', 'background_type' => 'gradient', 'background_value' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)', 'text_position' => 'center', 'font_size' => 'medium'],
                    ['page_type' => 'cta', 'title' => 'Leia a matéria completa', 'content' => '', 'background_type' => 'gradient', 'background_value' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)', 'text_position' => 'center', 'font_size' => 'medium']
                ]
            ];
        }
        // normalize pages
        $norm = [];
        foreach ($data['pages'] as $p) {
            $norm[] = [
                'page_type' => $p['page_type'] ?? 'content',
                'title' => $p['title'] ?? '',
                'content' => $p['content'] ?? '',
                'background_type' => $p['background_type'] ?? 'gradient',
                'background_value' => $p['background_value'] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'text_position' => $p['text_position'] ?? 'center',
                'font_size' => $p['font_size'] ?? 'medium',
                'cta_text' => $p['cta_text'] ?? '',
                'cta_url' => $p['cta_url'] ?? ''
            ];
        }
        $data['pages'] = $norm;
        return $data;
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
        $raw = @file_get_contents($imageUrl);
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
