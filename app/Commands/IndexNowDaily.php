<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Safety net for IndexNow (Bing & friends): re-submits every post published
 * in the last N hours in one batch, catching anything the realtime hooks
 * missed (network failure, rate limit, publish paths without a hook).
 *
 * Usage:
 *   php spark indexnow:daily        # default 26h window
 *   php spark indexnow:daily 48     # 48h window
 */
class IndexNowDaily extends BaseCommand
{
    protected $group = 'SEO';
    protected $name = 'indexnow:daily';
    protected $description = 'Reenvia ao IndexNow os posts publicados nas últimas N horas (default 26).';
    protected $usage = 'indexnow:daily [hours]';

    public function run(array $params)
    {
        $hours = isset($params[0]) ? max(1, (int) $params[0]) : 26;

        $client = new \App\Libraries\IndexNowClient();
        if (!$client->isEnabled()) {
            CLI::write('IndexNow desabilitado ou sem API key — nada a fazer.', 'yellow');
            return;
        }

        $cutoff = date('Y-m-d H:i:s', time() - ($hours * 3600));
        $db = \Config\Database::connect();
        $posts = $db->table('posts')->select('id, lang_id, slug, post_url')
            ->where('status', 1)->where('visibility', 1)->where('is_scheduled', 0)
            ->where('created_at >=', $cutoff)
            ->get()->getResult();

        if (empty($posts)) {
            CLI::write("Nenhum post publicado desde {$cutoff}.", 'yellow');
            return;
        }

        $urls = [];
        foreach ($posts as $post) {
            $url = generatePostURL($post, generateBaseURLByLangId($post->lang_id));
            if (!empty($url) && $url !== '#') {
                $urls[] = $url;
            }
        }
        $urls = array_values(array_unique($urls));

        $result = $client->submitUrls($urls);
        if (!empty($result['success'])) {
            CLI::write(sprintf('IndexNow OK — %d URLs enviadas (HTTP %s).', count($urls), $result['http_code'] ?? '-'), 'green');
        } else {
            CLI::error('IndexNow FALHOU: ' . ($result['message'] ?? 'erro desconhecido'));
        }
    }
}
