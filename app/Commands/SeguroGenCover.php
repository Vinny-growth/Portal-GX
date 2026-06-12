<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Helpers\OpenAIImageHelper;
use App\Helpers\WebPConverter;

/**
 * Gera a CAPA do relatório do Simulador de Seguro de Vida Resgatável.
 * Pipeline padrão (OpenAIImageHelper + gpt-image-1) -> WebP em
 * uploads/marketing/srs_report_cover.webp (sobrescreve ao regerar).
 *
 * Uso:  php spark seguro:gen-cover
 */
class SeguroGenCover extends BaseCommand
{
    protected $group       = 'Quotation';
    protected $name        = 'seguro:gen-cover';
    protected $description = 'Gera a capa do relatório (gpt-image-1 -> WebP em uploads/marketing).';

    public function run(array $params)
    {
        $prompt = 'Capa editorial premium para um relatório de planejamento patrimonial e sucessório '
            . 'de uma assessoria financeira de alto padrão. Composição abstrata e sofisticada evocando '
            . 'legado, continuidade e solidez do patrimônio para as próximas gerações: arquitetura '
            . 'monolítica de mármore claro e metal dourado escovado, linhas verticais elegantes que '
            . 'sobem como colunas/torres, geometria nobre, sensação de perenidade e blindagem. '
            . 'Estilo editorial sofisticado, brutalismo financeiro, fotográfico, iluminação natural '
            . 'difusa e nobre, profundidade de campo rasa, alto contraste, muito requinte. '
            . 'Harmonize com a paleta da GX Capital: azul-marinho profundo (#0c3163, #000d23) e '
            . 'champagne/dourado (#dbc7a2, #87704a, #c9a96a). Formato paisagem 3:2, elemento principal '
            . 'à direita/centro, base e canto inferior esquerdo em azul-marinho escuro preservados para '
            . 'overlay de título. Sem texto, sem letras, sem números, sem logos, sem marca d agua, '
            . 'sem pessoas, sem rostos. Altíssima qualidade.';

        $helper = new OpenAIImageHelper();
        $model  = getenv('OPENAI_DEFAULT_MODEL') ?: 'gpt-image-1-mini';

        CLI::write("Gerando capa ({$model}, 1536x1024, high)... ~30-60s.", 'yellow');
        $res = $helper->generateImage($prompt, $model, '1536x1024', 'high', 1);

        if (!$res || empty($res['data'][0])) {
            CLI::error('Falha na geração da capa (ver writable/logs).');
            return;
        }

        $row = $res['data'][0];
        $tmpDir = FCPATH . 'uploads/tmp/';
        if (!is_dir($tmpDir)) { @mkdir($tmpDir, 0755, true); }
        $tmpPng = $tmpDir . 'srs_cover_' . uniqid('', true) . '.png';

        if (!empty($row['b64_json'])) {
            file_put_contents($tmpPng, base64_decode($row['b64_json']));
        } elseif (!empty($row['url'])) {
            file_put_contents($tmpPng, file_get_contents($row['url']));
        } else {
            CLI::error('Resposta da API sem b64_json/url.');
            return;
        }

        if (!is_file($tmpPng) || filesize($tmpPng) < 1000) {
            CLI::error('PNG temporário inválido.');
            return;
        }

        $destDir = FCPATH . 'uploads/marketing/';
        if (!is_dir($destDir)) { @mkdir($destDir, 0755, true); }
        $dest = $destDir . 'srs_report_cover.webp';

        $webp = (new WebPConverter(85))->convert($tmpPng, $dest, true);
        if (!$webp || !is_file($dest)) {
            CLI::error('Falha ao converter para WebP.');
            return;
        }

        $rel = str_replace(FCPATH, '', $dest);
        CLI::write('OK: ' . $rel . ' (' . round(filesize($dest) / 1024) . ' KB)', 'green');
        CLI::write('URL: ' . base_url($rel), 'white');
    }
}
