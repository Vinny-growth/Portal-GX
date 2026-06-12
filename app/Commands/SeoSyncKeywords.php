<?php

namespace App\Commands;

use App\Libraries\SeoRankingService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SeoSyncKeywords extends BaseCommand
{
    protected $group       = 'SEO';
    protected $name        = 'seo:sync-keywords';
    protected $description = 'Sync the SEO tracker with keywords used across published posts and calendar tags.';

    public function run(array $params)
    {
        $service = new SeoRankingService();
        $res = $service->syncFromContent();
        CLI::write('Distintas no conteúdo: ' . $res['scanned']);
        CLI::write('Novas adicionadas: ' . $res['added'], 'green');
        CLI::write('Atualizadas (frequência): ' . $res['updated'], 'green');
    }
}
