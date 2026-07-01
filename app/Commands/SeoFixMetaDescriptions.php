<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * Gera meta descriptions únicas (<=155 chars) para posts publicados cuja summary
 * esteja DUPLICADA, VAZIA ou LONGA (>160). Usa OpenAI (chat/completions, modelo
 * de fallback — barato). Idempotente e resumível: só toca nos posts que ainda
 * precisam de correção.
 *
 * Uso:
 *   php spark seo:fix-meta --dry-run --limit 5
 *   php spark seo:fix-meta --limit 50
 *   php spark seo:fix-meta            (processa todos os alvos)
 */
class SeoFixMetaDescriptions extends BaseCommand
{
    protected $group       = 'SEO';
    protected $name        = 'seo:fix-meta';
    protected $description = 'Reescreve meta descriptions duplicadas/vazias/longas dos posts via OpenAI.';
    protected $usage       = 'seo:fix-meta [--limit N] [--dry-run] [--max-len 155]';

    public function run(array $params)
    {
        $limit  = (int) (CLI::getOption('limit') ?: 0);
        $dryRun = (bool) CLI::getOption('dry-run');
        $maxLen = (int) (CLI::getOption('max-len') ?: 155);

        $apiKey = getenv('OPENAI_API_KEY') ?: '';
        if ($apiKey === '') {
            $ai = function_exists('aiWriter') ? aiWriter() : null;
            if (!empty($ai) && !empty($ai->apiKey)) {
                $apiKey = $ai->apiKey;
            }
        }
        if ($apiKey === '') {
            CLI::error('OPENAI_API_KEY ausente. Abortando.');
            return;
        }
        $model = getenv('OPENAI_TEXT_FALLBACK_MODEL') ?: 'gpt-4.1-mini';
        if (strpos($model, 'gpt-5') !== false) {
            // este comando usa chat/completions; evita modelos que exigem Responses API
            $model = 'gpt-4.1-mini';
        }

        $db  = Database::connect();
        $all = $db->query("SELECT id, title, summary FROM posts WHERE status = 1")->getResult();

        // Frequência de cada summary (para detectar duplicadas)
        $counts = [];
        foreach ($all as $p) {
            $s = trim((string) $p->summary);
            if ($s !== '') {
                $counts[$s] = ($counts[$s] ?? 0) + 1;
            }
        }

        // Conjunto de summaries já em uso (para garantir unicidade das novas)
        $seen = [];
        foreach ($all as $p) {
            $s = trim((string) $p->summary);
            if ($s !== '') {
                $seen[mb_strtolower($s)] = true;
            }
        }

        // Alvos: vazia, duplicada ou > 160
        $targets = [];
        foreach ($all as $p) {
            $s       = trim((string) $p->summary);
            $isEmpty = ($s === '');
            $isDup   = ($s !== '' && ($counts[$s] ?? 0) > 1);
            $isLong  = (mb_strlen($s) > 160);
            if ($isEmpty || $isDup || $isLong) {
                $targets[] = $p;
            }
        }

        $totalTargets = count($targets);
        if ($limit > 0) {
            $targets = array_slice($targets, 0, $limit);
        }

        CLI::write('Alvos totais: ' . $totalTargets . ' | processando: ' . count($targets)
            . ($dryRun ? ' | DRY-RUN' : '') . ' | modelo: ' . $model, 'yellow');

        $ok = 0; $skip = 0; $fail = 0;
        foreach ($targets as $i => $p) {
            $oldLen = mb_strlen(trim((string) $p->summary));
            $row    = $db->query("SELECT content FROM posts WHERE id = ?", [(int) $p->id])->getRow();
            $context = $row ? trim(mb_substr(preg_replace('/\s+/', ' ', strip_tags((string) $row->content)), 0, 600)) : '';

            $new = $this->generateMeta((string) $p->title, $context, trim((string) $p->summary), $apiKey, $model, $maxLen);
            if ($new === null || $new === '') {
                $fail++;
                CLI::write(sprintf('[%d/%d] #%d FALHA na geração', $i + 1, count($targets), $p->id), 'red');
                continue;
            }
            $new = $this->clampLen($new, $maxLen);

            // Garante unicidade (não recriar duplicata)
            $key = mb_strtolower($new);
            if (isset($seen[$key])) {
                $skip++;
                CLI::write(sprintf('[%d/%d] #%d pulado (colisão de unicidade)', $i + 1, count($targets), $p->id), 'light_gray');
                continue;
            }

            CLI::write(sprintf('[%d/%d] #%d  %d->%d  %s', $i + 1, count($targets), $p->id, $oldLen, mb_strlen($new), mb_substr($new, 0, 70)));

            if (!$dryRun) {
                $db->query("UPDATE posts SET summary = ?, updated_at = NOW() WHERE id = ?", [$new, (int) $p->id]);
            }
            $seen[$key] = true;
            $ok++;
            usleep(150000); // 150ms — evita rate limit
        }

        CLI::write('---', 'yellow');
        CLI::write("OK: $ok  | pulados: $skip  | falhas: $fail" . ($dryRun ? '  (nada gravado — DRY-RUN)' : ''), 'green');
        if ($limit > 0 && $totalTargets > $limit) {
            CLI::write('Restam ' . ($totalTargets - count($targets)) . ' alvos. Rode novamente (sem --dry-run) para continuar.', 'yellow');
        }
    }

    private function generateMeta(string $title, string $context, string $current, string $apiKey, string $model, int $maxLen): ?string
    {
        $system = 'Você escreve meta descriptions de SEO em português do Brasil para a GX Capital, uma boutique financeira (câmbio, crédito, consórcio, seguros, wealth). '
            . 'Regras: entre 120 e ' . $maxLen . ' caracteres; única e específica ao artigo; informativa e atraente; sem aspas, sem emojis, sem hashtags; '
            . 'compliance financeiro (NÃO prometa retorno, aprovação, contemplação ou garantia). Responda APENAS com o texto da meta description, sem rótulos.';
        $user = "Título: {$title}\n"
            . ($context !== '' ? "Trecho do artigo: {$context}\n" : '')
            . ($current !== '' ? "Meta atual (evite repetir literalmente): {$current}\n" : '')
            . 'Escreva a meta description.';

        $payload = [
            'model'       => $model,
            'messages'    => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user],
            ],
            'temperature' => 0.5,
            'max_tokens'  => 120,
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 40,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);
        $resp = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http !== 200 || !$resp) {
            log_message('error', 'seo:fix-meta HTTP ' . $http . ' resp=' . mb_substr((string) $resp, 0, 300));
            return null;
        }
        $data = json_decode($resp, true);
        $text = $data['choices'][0]['message']['content'] ?? '';
        $text = trim((string) $text);
        $text = trim($text, "\"'“”");           // remove aspas
        $text = preg_replace('/\s+/', ' ', $text);
        return $text !== '' ? $text : null;
    }

    private function clampLen(string $s, int $max): string
    {
        $s = trim(preg_replace('/\s+/', ' ', $s));
        if (mb_strlen($s) <= $max) {
            return $s;
        }
        $cut  = mb_substr($s, 0, $max);
        $last = mb_strrpos($cut, ' ');
        if ($last !== false && $last > $max * 0.6) {
            $cut = mb_substr($cut, 0, $last);
        }
        return rtrim($cut, " ,.;:—-");
    }
}
