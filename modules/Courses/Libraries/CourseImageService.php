<?php namespace Modules\Courses\Libraries;

use App\Helpers\OpenAIImageHelper;
use App\Helpers\WebPConverter;

/**
 * Geração de imagem por IA para capas de curso e de aula (Fase 4a+). REUSA o mesmo pipeline
 * das capas do blog (OpenAIImageHelper + gpt-image → WebP). O PROMPT segue o design system da
 * marca (Nexus) e é PARAMETRIZADO por brand() → funciona white-label (cada install usa suas
 * cores). Salva em uploads/courses/ e retorna a URL pública (ou null em falha).
 */
class CourseImageService
{
    /**
     * Gera e persiste a imagem. $type = 'course' | 'lesson'.
     * @return string|null URL pública da imagem (ou null se falhar).
     */
    public function generate(string $type, string $title, ?string $subtitle = null, ?string $category = null, ?string $level = null): ?string
    {
        $prompt = $this->buildPrompt($type, $title, $subtitle, $category, $level);

        $helper  = new OpenAIImageHelper();
        $model   = getenv('OPENAI_DEFAULT_MODEL') ?: 'gpt-image-1-mini';
        $quality = getenv('OPENAI_DEFAULT_QUALITY') ?: 'high';
        // Paisagem 3:2 — serve à capa do card (Netflix) e ao hero da trilha.
        $result = $helper->generateImage($prompt, $model, '1536x1024', $quality, 1);

        if (empty($result['data'][0])) {
            log_message('error', 'CourseImageService: geração de imagem falhou para ' . $type . ' "' . $title . '"');
            return null;
        }
        $row = $result['data'][0];

        $dir = FCPATH . 'uploads/courses/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $base   = ($type === 'lesson' ? 'lesson_' : 'course_')
                . (url_title($title, '-', true) ?: $type) . '_' . substr(md5(uniqid('', true)), 0, 8);
        $tmpPng = $dir . $base . '.png';

        // gpt-image-1* retornam b64_json; dall-e-3 (fallback) retorna url.
        if (!empty($row['b64_json'])) {
            file_put_contents($tmpPng, base64_decode($row['b64_json']));
        } elseif (!empty($row['url'])) {
            $bytes = @file_get_contents($row['url']);
            if ($bytes === false) {
                return null;
            }
            file_put_contents($tmpPng, $bytes);
        } else {
            return null;
        }

        $webp = (new WebPConverter(85))->convert($tmpPng, null, true);
        if (!$webp) {
            @unlink($tmpPng);
            return null;
        }
        $relative = ltrim(str_replace(FCPATH, '', $webp), '/');

        // espelha no S3 se o storage do install for aws_s3 (mesmo padrão das capas de post)
        try {
            $gs = function_exists('generalSettings') ? generalSettings() : null;
            if ($gs && ($gs->storage ?? '') === 'aws_s3' && class_exists('\App\Models\AwsModel')) {
                (new \App\Models\AwsModel())->uploadFile($relative);
            }
        } catch (\Throwable $e) {
            // storage local é suficiente — não falha por causa do S3
        }

        return base_url($relative);
    }

    /**
     * Prompt PT-BR seguindo o design system (brutalismo financeiro, paleta da marca, cantos retos,
     * espaço negativo para overlay, SEM texto/logo). Cores vêm de brand() → white-label.
     */
    public function buildPrompt(string $type, string $title, ?string $subtitle, ?string $category, ?string $level): string
    {
        $brandName = function_exists('brand') ? brand('display_name', 'GX Capital') : 'GX Capital';
        $primary   = function_exists('brand') ? brand('color_primary', '#0c3163') : '#0c3163';
        $gold      = function_exists('brand') ? brand('color_gold', '#c9a96a') : '#c9a96a';
        $secondary = function_exists('brand') ? brand('color_secondary', '#dbc7a2') : '#dbc7a2';

        if ($type === 'lesson') {
            $subject = 'Imagem editorial para a capa de uma AULA online intitulada "' . $title . '"';
        } else {
            $subject = 'Capa editorial cinematográfica para um CURSO online intitulado "' . $title . '"';
            if ($category) {
                $subject .= ', da trilha "' . $category . '"';
            }
        }
        if ($subtitle) {
            $subject .= '. Tema: ' . $subtitle;
        }
        if ($level) {
            $subject .= '. Nível: ' . $level;
        }

        return $subject . '. '
            . 'Composição abstrata evocando o tema com geometria sólida, gráficos sutis, painéis de luz e profundidade. '
            . 'Estilo editorial sofisticado e premium, BRUTALISMO FINANCEIRO: sério, preciso, confiante; fotográfico, '
            . 'iluminação natural fria, profundidade de campo rasa, composição limpa, formas de cantos retos. '
            . 'Harmonize ESTRITAMENTE com a paleta da marca ' . $brandName . ': azul-marinho profundo (' . $primary . ') '
            . 'como base e champagne/dourado (' . $gold . ', ' . $secondary . ') como acento. '
            . 'Formato paisagem 3:2, foco centralizado, margens de segurança nas bordas, ESPAÇO NEGATIVO central '
            . 'preservado para overlay de título. SEM texto, SEM letras, SEM números, SEM logos, SEM marca d\'água, '
            . 'SEM rostos identificáveis. Alta qualidade, aparência premium.';
    }
}
