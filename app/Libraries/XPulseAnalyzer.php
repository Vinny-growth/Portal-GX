<?php

namespace App\Libraries;

use App\Models\ContentAISettingsModel;
use App\Models\XPulseSnapshotModel;

/**
 * Fetches and persists the "X Pulse" — top financial themes being discussed
 * on X (Twitter) in the last N hours, via Grok API with Live Search enabled.
 *
 * Designed as the 3rd signal layer for the AI editor, alongside trends (RSS/Google Trends)
 * and popular posts (our own portal analytics).
 *
 * Grok API is OpenAI-compatible. We use `search_parameters.mode=on` with
 * `sources=[{type:"x"}]` to make the model retrieve real-time data from X
 * rather than relying on training data. Each search costs roughly $0.025.
 *
 * Degrades gracefully when GROK_API_KEY is unset — returns null without erroring.
 */
class XPulseAnalyzer
{
    protected ContentAISettingsModel $settingsModel;
    protected XPulseSnapshotModel $snapshotModel;
    protected int $httpTimeout = 90;
    protected int $connectTimeout = 10;

    public function __construct()
    {
        $this->settingsModel = new ContentAISettingsModel();
        $this->snapshotModel = new XPulseSnapshotModel();
    }

    /**
     * Run a full pulse cycle: fetch, parse, persist.
     * Returns ['snapshot_count' => N, 'error' => string|null, 'skipped' => bool].
     */
    public function run(?object $settingsOverride = null): array
    {
        $settings = $settingsOverride ?: $this->settingsModel->getSettings();

        if (empty($settings->x_pulse_enabled)) {
            return ['snapshot_count' => 0, 'error' => null, 'skipped' => true, 'reason' => 'x_pulse_enabled=0'];
        }

        $apiKey = $this->resolveApiKey();
        if (empty($apiKey)) {
            return ['snapshot_count' => 0, 'error' => null, 'skipped' => true, 'reason' => 'GROK_API_KEY not configured'];
        }

        $windowHours = max(1, (int) ($settings->x_window_hours ?? 6));
        $themesPerDay = max(1, (int) ($settings->x_themes_per_day ?? 10));
        $minMentions = max(0, (int) ($settings->x_min_mentions ?? 100));
        $model = (string) ($settings->x_grok_model ?? 'grok-4.3');
        $promptTemplate = (string) ($settings->x_pulse_prompt ?? '');
        if (trim($promptTemplate) === '') {
            $promptTemplate = $this->settingsModel->getDefaultXPulsePrompt();
        }

        $systemPrompt = strtr($promptTemplate, [
            '{themes_per_day}' => $themesPerDay,
            '{window_hours}'   => $windowHours,
            '{min_mentions}'   => $minMentions,
        ]);

        $userPrompt = "Execute a busca no X agora. Periodo: ultimas " . $windowHours . " horas. "
            . "Retorne ate " . $themesPerDay . " temas em JSON conforme o formato instruido. "
            . "Foco no publico-alvo brasileiro de financas corporativas e investimentos.";

        log_message('info', 'XPulse: calling Grok model=' . $model . ' window=' . $windowHours . 'h target=' . $themesPerDay);

        $response = $this->callGrok($apiKey, $model, $systemPrompt, $userPrompt, $windowHours);
        if ($response === null) {
            return ['snapshot_count' => 0, 'error' => 'grok_api_call_failed', 'skipped' => false];
        }

        $rawText = $this->extractText($response);
        $parsed = $this->parseJson($rawText);
        if (empty($parsed)) {
            log_message('error', 'XPulse: failed to parse JSON. raw=' . substr((string) $rawText, 0, 800));
            return ['snapshot_count' => 0, 'error' => 'invalid_json_response', 'skipped' => false, 'raw' => $rawText];
        }

        $themes = $parsed['pulse'] ?? (isset($parsed[0]) ? $parsed : []);
        if (empty($themes)) {
            return ['snapshot_count' => 0, 'error' => 'no_themes_returned', 'skipped' => false, 'raw' => $rawText];
        }

        $today = date('Y-m-d');
        $inserted = $this->snapshotModel->insertSnapshot($today, $windowHours, $themes, $rawText);
        $this->settingsModel->updateLastRunXPulse(date('Y-m-d H:i:s'));

        log_message('info', 'XPulse: persisted ' . $inserted . ' themes for ' . $today);
        return ['snapshot_count' => $inserted, 'error' => null, 'skipped' => false];
    }

    protected function resolveApiKey(): string
    {
        return (string) (getenv('GROK_API_KEY') ?: getenv('XAI_API_KEY') ?: '');
    }

    protected function callGrok(string $apiKey, string $model, string $systemPrompt, string $userPrompt, int $windowHours): ?array
    {
        // Migrated to the Agent Tools API (the old `search_parameters` Live Search is deprecated).
        // - Endpoint: /v1/responses (not /v1/chat/completions)
        // - messages -> input
        // - search_parameters -> tools: [{type: "x_search"}]
        // - No declarative `from_date` filter — recency must be expressed in the prompt.
        $endpoint = 'https://api.x.ai/v1/responses';

        // Inject a hard recency hint at the top of the user prompt so the agent
        // restricts its X search to the requested window.
        $userPromptWithWindow = "JANELA OBRIGATORIA: considere SOMENTE posts publicados no X nas ultimas "
            . $windowHours . " horas (a partir de agora, " . gmdate('Y-m-d H:i') . " UTC). "
            . "Ignore qualquer conteudo mais antigo.\n\n" . $userPrompt;

        $payload = [
            'model' => $model,
            'input' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $userPromptWithWindow],
            ],
            'tools' => [
                ['type' => 'x_search'],
            ],
            'temperature' => 0.3,
            // /v1/responses uses `text.format` (not the legacy `response_format` from /v1/chat/completions)
            'text' => [
                'format' => ['type' => 'json_object'],
            ],
        ];

        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->httpTimeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        $body = curl_exec($ch);
        $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($http !== 200) {
            log_message('error', 'XPulse: Grok HTTP ' . $http . ' err=' . $err . ' body=' . substr((string) $body, 0, 800));
            return null;
        }

        $decoded = json_decode((string) $body, true);
        return is_array($decoded) ? $decoded : null;
    }

    protected function extractText(array $response): ?string
    {
        // /v1/responses shorthand (some clients hydrate this for convenience)
        if (!empty($response['output_text'])) {
            return (string) $response['output_text'];
        }
        // /v1/responses canonical: output is an array of items (messages, tool calls...)
        // The final assistant message contains content items with type=output_text|text.
        if (!empty($response['output']) && is_array($response['output'])) {
            $collected = [];
            foreach ($response['output'] as $item) {
                if (!is_array($item)) {
                    continue;
                }
                // Skip non-message items (tool_call, web_search_call, etc.)
                if (isset($item['type']) && $item['type'] !== 'message') {
                    continue;
                }
                if (!empty($item['content']) && is_array($item['content'])) {
                    foreach ($item['content'] as $c) {
                        if (!is_array($c)) {
                            continue;
                        }
                        if (!empty($c['text'])) {
                            $collected[] = (string) $c['text'];
                        } elseif (!empty($c['content'])) {
                            $collected[] = (string) $c['content'];
                        }
                    }
                }
            }
            if (!empty($collected)) {
                return implode("\n", $collected);
            }
        }
        // OpenAI chat/completions fallback (still useful if user pins a legacy model)
        if (isset($response['choices'][0]['message']['content'])) {
            return (string) $response['choices'][0]['message']['content'];
        }
        return null;
    }

    protected function parseJson(?string $text): ?array
    {
        if ($text === null || $text === '') {
            return null;
        }
        $payload = json_decode($text, true);
        if (is_array($payload)) {
            return $payload;
        }
        // strip code fences / trailing prose
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start === false || $end === false || $end <= $start) {
            return null;
        }
        $json = substr($text, $start, ($end - $start) + 1);
        $payload = json_decode($json, true);
        return is_array($payload) ? $payload : null;
    }
}
