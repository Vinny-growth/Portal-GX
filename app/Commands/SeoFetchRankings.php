<?php

namespace App\Commands;

use App\Libraries\SeoRankingService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SeoFetchRankings extends BaseCommand
{
    protected $group       = 'SEO';
    protected $name        = 'seo:fetch-rankings';
    protected $description = 'Collect keyword rankings from Google Search Console and openserp into seo_rankings.';

    public function run(array $params)
    {
        $service = new SeoRankingService();

        // Keep the tracker in sync with the keywords actually used across the content.
        $sync = $service->syncFromContent();
        CLI::write('Sync de palavras-chave: +' . $sync['added'] . ' novas, '
            . $sync['updated'] . ' atualizadas (' . $sync['scanned'] . ' distintas no conteúdo).', 'cyan');

        $status = $service->providersStatus();

        CLI::write('Integrações: GSC=' . ($status['gsc'] ? 'ON' : 'off')
            . ' | openserp=' . ($status['serp'] ? 'ON' : 'off'));

        if (!$status['gsc'] && !$status['serp']) {
            CLI::write('Nenhuma fonte configurada. Defina GSC_* e/ou OPENSERP_* no .env.', 'yellow');
            return;
        }

        $res = $service->fetchAll();
        CLI::write('Coletados via GSC: ' . $res['gsc'], 'green');
        CLI::write('Coletados via openserp: ' . $res['serp'], 'green');
        CLI::write('Sem dados: ' . $res['skipped']);
        if (!empty($res['errors'])) {
            foreach (array_slice($res['errors'], 0, 10) as $err) {
                CLI::write('  - ' . $err, 'red');
            }
        }
    }
}
