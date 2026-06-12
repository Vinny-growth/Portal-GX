<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Helpers\OpenAIImageHelper;
use App\Helpers\WebPConverter;

/**
 * Gera a imagem hero do Simulador de Seguro de Vida Resgatável.
 * Usa o pipeline padrão (OpenAIImageHelper + gpt-image-1), converte para WebP
 * e grava em uploads/marketing/srs_hero.webp (sobrescreve ao regerar).
 *
 * Uso:  php spark seguro:gen-hero
 */
class SeguroGenHero extends BaseCommand
{
    protected $group       = 'Quotation';
    protected $name        = 'seguro:gen-hero';
    protected $description = 'Gera a imagem hero do simulador de seguro (gpt-image-1 -> WebP em uploads/marketing).';

    public function run(array $params)
    {
        $prompt = 'Imagem editorial para o topo (hero) de uma pagina premium de seguro de vida '
            . 'resgatavel e construcao de patrimonio de uma assessoria financeira. Composicao abstrata '
            . 'e sofisticada que sugere protecao + crescimento de longo prazo: um escudo/cofre geometrico '
            . 'solido em marmore e metal escovado que se transforma numa curva ascendente de patrimonio, '
            . 'linhas finas de grafico subindo ao fundo, arquitetura monolitica, sensacao de solidez, '
            . 'legado e seguranca. Estilo editorial sofisticado, brutalismo financeiro, fotografico, '
            . 'iluminacao natural fria e dramatica, profundidade de campo rasa, alto contraste. '
            . 'Harmonize com a paleta da GX Capital: azul-marinho profundo (#0c3163, #000d23) e '
            . 'champagne/dourado (#dbc7a2, #87704a, #c9a96a). Formato paisagem 3:2, elemento focal a '
            . 'DIREITA, muito espaco negativo escuro (azul-marinho) a ESQUERDA preservado para overlay de '
            . 'titulo. Sem texto, sem letras, sem numeros, sem logos, sem marca d agua, sem pessoas, '
            . 'sem rostos. Altissima qualidade.';

        $helper = new OpenAIImageHelper();
        $model  = getenv('OPENAI_DEFAULT_MODEL') ?: 'gpt-image-1-mini';

        CLI::write("Gerando hero ({$model}, 1536x1024, high)... pode levar ~30-60s.", 'yellow');
        $res = $helper->generateImage($prompt, $model, '1536x1024', 'high', 1);

        if (!$res || empty($res['data'][0])) {
            CLI::error('Falha na geração da imagem (ver writable/logs). Verifique OPENAI_API_KEY e conexão.');
            return;
        }

        $row = $res['data'][0];
        $tmpDir = FCPATH . 'uploads/tmp/';
        if (!is_dir($tmpDir)) { @mkdir($tmpDir, 0755, true); }
        $tmpPng = $tmpDir . 'srs_hero_' . uniqid('', true) . '.png';

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
        $dest = $destDir . 'srs_hero.webp';

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
