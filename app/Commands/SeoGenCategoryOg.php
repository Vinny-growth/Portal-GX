<?php

namespace App\Commands;

use App\Helpers\OpenAIImageHelper;
use App\Helpers\WebPConverter;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * Gera og:images on-brand (1536x1024, próximo de 1200x630) por categoria, via
 * gpt-image (allowFallback=false — DALL-E foi removido da conta). Salva em
 * uploads/marketing/og/og_cat_<id>.webp. O header usa o arquivo se existir,
 * senão cai no logo (degrada sem quebrar).
 *
 * Uso:
 *   php spark seo:gen-category-og            (todas que faltam)
 *   php spark seo:gen-category-og --only 6,8
 *   php spark seo:gen-category-og --force    (regera existentes)
 */
class SeoGenCategoryOg extends BaseCommand
{
    protected $group       = 'SEO';
    protected $name        = 'seo:gen-category-og';
    protected $description = 'Gera og:images 1536x1024 on-brand por categoria (gpt-image).';
    protected $usage       = 'seo:gen-category-og [--only 6,8] [--force]';

    /** Direção visual por categoria (o resto do prompt é comum e on-brand). */
    private array $themes = [
        6  => 'Mesa de operações de câmbio de uma assessoria financeira premium: telas com cotações de dólar e euro, gráficos de moedas, ambiente sóbrio e sofisticado.',
        7  => 'Painel macroeconômico premium: gráficos de indicadores econômicos, curvas de juros e inflação, ambiente analítico e sofisticado.',
        8  => 'Ambiente corporativo de estruturação de crédito empresarial: documentos financeiros, torres corporativas ao fundo, sensação de solidez e capital.',
        11 => 'Composição editorial abstrata sobre educação financeira: elementos geométricos sólidos, luz difusa, sensação de clareza e conhecimento.',
        12 => 'Cena editorial da indústria e dos negócios brasileiros: maquinário, produção e energia econômica, tom institucional.',
        13 => 'Ambiente de gestão de investimentos premium: alocação de portfólio, gráficos de mercado de capitais, sofisticação patrimonial.',
    ];

    public function run(array $params)
    {
        $only    = CLI::getOption('only');
        $force   = (bool) CLI::getOption('force');
        $onlyIds = $only ? array_map('intval', explode(',', (string) $only)) : [];

        $helper  = new OpenAIImageHelper();
        $model   = getenv('OPENAI_DEFAULT_MODEL') ?: 'gpt-image-1-mini';
        $destDir = FCPATH . 'uploads/marketing/og/';
        $tmpDir  = FCPATH . 'uploads/tmp/';
        foreach ([$destDir, $tmpDir] as $d) {
            if (!is_dir($d)) {
                @mkdir($d, 0755, true);
            }
        }

        $db = Database::connect();
        foreach ($this->themes as $catId => $theme) {
            if (!empty($onlyIds) && !in_array($catId, $onlyIds, true)) {
                continue;
            }
            $cat = $db->query("SELECT id, name FROM categories WHERE id = ?", [$catId])->getRow();
            if (!$cat) {
                CLI::write("cat $catId: inexistente", 'red');
                continue;
            }
            $dest = $destDir . 'og_cat_' . $catId . '.webp';
            if (is_file($dest) && !$force) {
                CLI::write("cat $catId ({$cat->name}): já existe, pulando (--force para regerar)", 'light_gray');
                continue;
            }

            $prompt = $theme
                . ' Estilo editorial sofisticado, brutalismo financeiro, fotográfico, iluminação natural fria, profundidade de campo rasa.'
                . ' Harmonize com a paleta da GX Capital: azul-marinho profundo (#0c3163, #000d23) e champagne/dourado (#dbc7a2, #87704a, #c9a96a).'
                . ' Formato paisagem 3:2. Sem texto, sem logos, sem marca d\'água, alta qualidade.';

            CLI::write("cat $catId ({$cat->name}): gerando...", 'yellow');
            $res = $helper->generateImage($prompt, $model, '1536x1024', 'high', 1, false);
            if (!$res || empty($res['data'][0])) {
                CLI::write('  falha na geração (ver logs)', 'red');
                continue;
            }
            $row = $res['data'][0];
            $tmp = $tmpDir . 'og_cat_' . $catId . '_' . uniqid('', true) . '.png';
            if (!empty($row['b64_json'])) {
                file_put_contents($tmp, base64_decode($row['b64_json']));
            } elseif (!empty($row['url'])) {
                file_put_contents($tmp, file_get_contents($row['url']));
            }
            if (!is_file($tmp)) {
                CLI::write('  arquivo temporário não criado', 'red');
                continue;
            }

            (new WebPConverter(85))->convert($tmp, $dest, true);
            if (is_file($dest)) {
                CLI::write('  OK -> uploads/marketing/og/og_cat_' . $catId . '.webp', 'green');
            } else {
                CLI::write('  falha na conversão WebP', 'red');
            }
        }
        CLI::write('Concluído.', 'green');
    }
}
