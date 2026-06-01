<?php

namespace App\Commands;

use App\Helpers\OpenAIImageHelper;
use App\Helpers\WebPConverter;
use App\Models\NewsletterSettingsModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class NewsletterGenerateHero extends BaseCommand
{
    protected $group = 'Newsletter';
    protected $name = 'newsletter:generate-hero';
    protected $description = 'Generate AI hero image for /newsletter landing page.';

    public function run(array $params)
    {
        helper(['filesystem']);
        if (!function_exists('helper')) {
            require_once APPPATH . 'Helpers/OpenAIImageHelper.php';
            require_once APPPATH . 'Helpers/WebPConverter.php';
        }

        $prompt = "Imagem editorial para o topo de uma landing page premium de newsletter financeira da GX Capital. "
                . "Composição abstrata sugerindo mercados, liquidez e inteligência financeira: linhas de gráficos discretas, "
                . "paineis de luz, geometria sólida tipo brutalismo arquitetônico — sem candlesticks óbvios, sem números visíveis. "
                . "Cenário urbano corporativo ao entardecer com torres de vidro reflexivas em segundo plano. "
                . "Estilo editorial sofisticado, brutalismo financeiro, fotográfico, iluminação natural fria com toques quentes "
                . "de luz dourada, profundidade de campo rasa, atmosfera tipo capa da The Economist ou Bloomberg Markets. "
                . "Harmonize com a paleta da GX Capital: azul-marinho profundo (#0c3163, #000d23) e champagne/dourado "
                . "(#dbc7a2, #87704a, #c9a96a). Formato paisagem 3:2, foco central deslocado para a direita para deixar "
                . "espaço negativo à esquerda para overlay de título. Sem texto, sem logos, sem marca d'água, sem pessoas "
                . "em primeiro plano. Alta qualidade, ultra detalhado.";

        $helper = new OpenAIImageHelper();
        CLI::write('Generating hero (1536x1024, high)...');
        $result = $helper->generateImage(
            $prompt,
            getenv('OPENAI_DEFAULT_MODEL') ?: 'gpt-image-1-mini',
            '1536x1024',
            'high',
            1
        );

        if (!$result || empty($result['data'][0])) {
            CLI::write('Generation failed.', 'red');
            CLI::write(json_encode($result), 'yellow');
            return;
        }

        $row = $result['data'][0];
        $tmpDir = FCPATH . 'uploads/tmp/';
        if (!is_dir($tmpDir)) @mkdir($tmpDir, 0755, true);
        $tmpPath = $tmpDir . 'nl_hero_' . uniqid('', true) . '.png';

        if (!empty($row['b64_json'])) {
            file_put_contents($tmpPath, base64_decode($row['b64_json']));
        } elseif (!empty($row['url'])) {
            file_put_contents($tmpPath, file_get_contents($row['url']));
        } else {
            CLI::write('No image data in response.', 'red');
            return;
        }
        if (!is_file($tmpPath) || filesize($tmpPath) < 1024) {
            CLI::write('Saved image looks empty.', 'red');
            return;
        }
        CLI::write('Raw png saved: ' . filesize($tmpPath) . ' bytes');

        // convert to webp
        $webp = (new WebPConverter(85))->convert($tmpPath, null, true);
        if (!$webp || !is_file($webp)) {
            CLI::write('WebP conversion failed.', 'red');
            return;
        }
        $finalDir = FCPATH . 'uploads/newsletter-magnets/';
        if (!is_dir($finalDir)) @mkdir($finalDir, 0755, true);
        $finalName = 'newsletter_hero_' . date('Ymd_His') . '.webp';
        $finalPath = $finalDir . $finalName;
        rename($webp, $finalPath);
        $relative = 'uploads/newsletter-magnets/' . $finalName;
        CLI::write('Hero saved: ' . $relative . ' (' . number_format(filesize($finalPath) / 1024) . ' KB)', 'green');

        // persist on settings
        $sm = new NewsletterSettingsModel();
        $sm->updateSettings(['landing_hero_image' => $relative]);
        CLI::write('newsletter_settings.landing_hero_image updated.', 'green');
        CLI::write('Open https://gx.capital/newsletter to preview.');
    }
}
