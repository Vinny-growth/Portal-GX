<?php

namespace App\Libraries;

use App\Models\NewsletterEditorialLineModel;

/**
 * Generates newsletter editions from recent posts of an editorial line.
 *
 * Pipeline:
 *  1. pickPosts(line)        -> recent posts in the line's categories
 *  2. buildPrompt(line, posts) -> JSON-mode prompt for OpenAI
 *  3. callTextModel(prompt)  -> raw OpenAI response
 *  4. generateEdition(line)  -> orchestrates 1-3 and returns structured edition
 */
class NewsletterAIService
{
    protected $db;
    protected $lineModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->lineModel = new NewsletterEditorialLineModel();
    }

    /**
     * Generate a structured edition for an editorial line.
     * Returns: ['subject','preheader','intro','posts'=>[{post_id,title,summary,cta_label,url}],
     *           'cta_text','cta_url','prompt','raw_response','post_ids'=>[]]
     * On failure returns ['error' => string]
     */
    public function generateEdition($lineOrId): array
    {
        $line = is_object($lineOrId) ? $lineOrId : $this->lineModel->getById((int) $lineOrId);
        if (empty($line)) {
            return ['error' => 'editorial line not found'];
        }

        $posts = $this->pickPosts($line);
        if (empty($posts)) {
            return ['error' => 'no posts available for line ' . $line->slug];
        }

        $prompt = $this->buildPrompt($line, $posts);
        $raw = $this->callTextModel($prompt);
        if ($raw === false) {
            return ['error' => 'openai call failed'];
        }

        $parsed = $this->parseResponse($raw);
        if (empty($parsed) || empty($parsed['subject']) || empty($parsed['posts'])) {
            return ['error' => 'invalid AI response', 'raw_response' => json_encode($raw)];
        }

        // Merge AI fields with concrete post data (id, real URL)
        $mergedPosts = [];
        $postIds = [];
        foreach ($parsed['posts'] as $aiPost) {
            $postId = (int) ($aiPost['post_id'] ?? 0);
            $match = null;
            foreach ($posts as $p) {
                if ((int) $p->id === $postId) {
                    $match = $p;
                    break;
                }
            }
            if (!$match) continue;
            $mergedPosts[] = [
                'post_id'    => (int) $match->id,
                'title'      => trim((string) ($aiPost['title'] ?? $match->title)),
                'summary'    => trim((string) ($aiPost['summary'] ?? $match->summary ?? '')),
                'cta_label'  => trim((string) ($aiPost['cta_label'] ?? 'Leia mais')),
                'image_url'  => $this->resolveImageUrl($match),
                'category_id'=> (int) $match->category_id,
                'url'        => generatePostURL($match),
            ];
            $postIds[] = (int) $match->id;
        }

        if (empty($mergedPosts)) {
            return ['error' => 'AI returned no matching posts'];
        }

        return [
            'subject'      => trim((string) $parsed['subject']),
            'preheader'    => trim((string) ($parsed['preheader'] ?? '')),
            'intro'        => trim((string) ($parsed['intro'] ?? '')),
            'posts'        => $mergedPosts,
            'cta_text'     => trim((string) ($parsed['cta_text'] ?? ($line->cta_text ?? ''))),
            'cta_url'      => trim((string) ($parsed['cta_url'] ?? ($line->cta_url ?? ''))),
            'prompt'       => $prompt,
            'raw_response' => is_array($raw) ? json_encode($raw) : (string) $raw,
            'post_ids'     => $postIds,
        ];
    }

    public function pickPosts($line): array
    {
        $catIds = $this->lineModel->decodeIds($line->category_ids ?? null);
        if (empty($catIds)) return [];

        $lookback = max(1, (int) ($line->lookback_hours ?? 24));
        $perEdition = max(1, (int) ($line->posts_per_edition ?? 5));
        $since = date('Y-m-d H:i:s', time() - $lookback * 3600);

        $rows = $this->db->table('posts')
            ->select('id, title, slug, summary, image_url, category_id, pageviews, created_at, post_url')
            ->whereIn('category_id', $catIds)
            ->where('status', 1)
            ->where('visibility', 1)
            ->where('is_scheduled', 0)
            ->where('created_at >=', $since)
            ->orderBy('pageviews', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->limit($perEdition * 3)
            ->get()->getResult();

        if (empty($rows)) {
            // fallback: latest posts in those categories without time window
            $rows = $this->db->table('posts')
                ->select('id, title, slug, summary, image_url, category_id, pageviews, created_at, post_url')
                ->whereIn('category_id', $catIds)
                ->where('status', 1)
                ->where('visibility', 1)
                ->where('is_scheduled', 0)
                ->orderBy('created_at', 'DESC')
                ->limit($perEdition * 2)
                ->get()->getResult();
        }

        return array_slice($rows, 0, $perEdition);
    }

    public function buildPrompt($line, array $posts): string
    {
        $brandRules = "Marca: GX Capital (inteligência financeira BR). Voz: brutalista financeira, "
                    . "direta, objetiva, com toque premium. Português brasileiro. Sem emojis. "
                    . "Sem markdown nos campos textuais (texto puro).";

        $editorialName = $line->name ?? 'Newsletter';
        $editorialDesc = trim((string) ($line->description ?? ''));
        $customSubjectPrompt = trim((string) ($line->subject_prompt ?? ''));
        $customBodyPrompt = trim((string) ($line->body_prompt ?? ''));

        $postsCtx = [];
        foreach ($posts as $p) {
            $postsCtx[] = [
                'post_id' => (int) $p->id,
                'title'   => $p->title,
                'summary' => mb_substr((string) ($p->summary ?? ''), 0, 400),
                'category_id' => (int) $p->category_id,
                'pageviews' => (int) ($p->pageviews ?? 0),
            ];
        }

        $schema = [
            'subject'   => 'string, max 70 chars, gancho concreto, sem clickbait',
            'preheader' => 'string, max 110 chars, complemento ao subject',
            'intro'     => 'string, 1-2 frases, contexto editorial da edição',
            'posts'     => 'array de objetos {post_id (int, do contexto), title (string, max 90), summary (string, max 220), cta_label (string, max 25)}',
            'cta_text'  => 'string opcional, CTA final',
            'cta_url'   => 'string opcional, URL do CTA final',
        ];

        $extraSubject = $customSubjectPrompt !== '' ? "\nORIENTAÇÃO ESPECÍFICA PARA SUBJECT: $customSubjectPrompt" : '';
        $extraBody    = $customBodyPrompt    !== '' ? "\nORIENTAÇÃO ESPECÍFICA PARA CORPO: $customBodyPrompt" : '';

        $prompt = "Você é o editor-chefe da newsletter \"$editorialName\" da GX Capital.\n"
                . "Linha editorial: $editorialDesc\n"
                . "$brandRules\n"
                . "$extraSubject"
                . "$extraBody"
                . "\n\nPOSTS DISPONÍVEIS (contexto):\n" . json_encode($postsCtx, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                . "\n\nTAREFA: monte uma edição usando TODOS os posts acima (mantendo seus post_id). Reescreva título e resumo de cada um em tom editorial coeso para email. NÃO invente posts."
                . "\n\nSCHEMA DE SAÍDA (responda APENAS com JSON válido, sem markdown):\n" . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return $prompt;
    }

    public function callTextModel(string $prompt)
    {
        $model = getenv('OPENAI_TEXT_MODEL') ?: 'gpt-5.4-mini';
        $timeout = max(10, (int) (getenv('OPENAI_TEXT_TIMEOUT') ?: 90));
        $apiKey = getenv('OPENAI_API_KEY') ?: '';
        if (empty($apiKey)) {
            $ai = \aiWriter();
            if (!empty($ai) && !empty($ai->apiKey)) {
                $apiKey = $ai->apiKey;
            }
        }
        if (empty($apiKey)) {
            log_message('error', 'NewsletterAI: no OpenAI API key configured');
            return false;
        }

        $systemMsg = 'Voce e o editor-chefe da newsletter GX Capital. '
                   . 'Sempre responda APENAS com JSON valido conforme o schema solicitado, sem markdown.';

        $tryModel = function (string $m) use ($prompt, $systemMsg, $apiKey, $timeout) {
            $useResponses = (strpos($m, 'gpt-5') !== false);
            $endpoint = $useResponses
                ? 'https://api.openai.com/v1/responses'
                : 'https://api.openai.com/v1/chat/completions';
            if ($useResponses) {
                $payload = [
                    'model' => $m,
                    'instructions' => $systemMsg,
                    'input' => $prompt,
                    'temperature' => 0.65,
                    'text' => ['format' => ['type' => 'json_object']],
                ];
            } else {
                $payload = [
                    'model' => $m,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemMsg],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.65,
                    'response_format' => ['type' => 'json_object'],
                ];
            }
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);
            $resp = curl_exec($ch);
            $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($http === 200) {
                return json_decode($resp, true);
            }
            log_message('error', 'NewsletterAI: model ' . $m . ' HTTP ' . $http . ' resp=' . substr((string) $resp, 0, 400));
            return null;
        };

        $result = $tryModel($model);
        if ($result === null) {
            $fallback = getenv('OPENAI_TEXT_FALLBACK_MODEL') ?: 'gpt-4.1-mini';
            if ($fallback && $fallback !== $model) {
                $result = $tryModel($fallback);
            }
        }
        return $result === null ? false : $result;
    }

    public function parseResponse($raw): ?array
    {
        if (!is_array($raw)) return null;
        $text = null;

        // chat.completions shape
        if (isset($raw['choices'][0]['message']['content'])) {
            $text = $raw['choices'][0]['message']['content'];
        }
        // responses shape
        if ($text === null && isset($raw['output'][0]['content'][0]['text'])) {
            $text = $raw['output'][0]['content'][0]['text'];
        }
        if ($text === null && isset($raw['output_text'])) {
            $text = $raw['output_text'];
        }
        if (empty($text)) return null;

        $decoded = json_decode($text, true);
        return is_array($decoded) ? $decoded : null;
    }

    protected function resolveImageUrl($post): string
    {
        if (!empty($post->image_url)) {
            return (string) $post->image_url;
        }
        return '';
    }
}
