<?php

namespace App\Commands;

use App\Libraries\WebStoriesGenerator;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Cron worker that finishes image generation for stories whose admin
 * closed the tab before all pages had images. Limits how much it does
 * per run so a long backlog doesn't burn budget in a single tick.
 *
 * Usage:
 *   php spark webstories:process-pending          # default 5 images per run
 *   php spark webstories:process-pending 10       # process up to 10 images
 *   php spark webstories:process-pending 5 27     # only story id=27, max 5 images
 *
 * Recommended cron entry (every 5 min):
 *   #/5 * * * * php /path/spark webstories:process-pending 3
 */
class WebStoriesProcessPending extends BaseCommand
{
    protected $group = 'WebStories';
    protected $name = 'webstories:process-pending';
    protected $description = 'Gera imagens pendentes de Web Stories — pensado para rodar em cron de poucos minutos.';
    protected $usage = 'webstories:process-pending [max_images] [story_id]';

    public function run(array $params)
    {
        $maxImages = isset($params[0]) ? max(1, (int) $params[0]) : 5;
        $onlyStory = isset($params[1]) ? (int) $params[1] : 0;

        $db = \Config\Database::connect();
        if (!$db->tableExists('web_stories') || !$db->tableExists('web_story_pages')) {
            CLI::write('Tabelas web_stories/web_story_pages não existem.', 'red');
            return;
        }

        $generator = new WebStoriesGenerator();

        $processed = 0;
        $exhaustedStories = 0;
        $quotaHits = 0;

        while ($processed < $maxImages) {
            $story = $this->pickPendingStory($db, $onlyStory);
            if (empty($story)) {
                break;
            }

            // The post object only enriches the prompt — null is fine for
            // backfill of stories whose original post has been forgotten.
            $result = $generator->generateNextImageForStory((int) $story->id, null);

            if (!empty($result['quota_reached'])) {
                CLI::write('Quota diária atingida — interrompendo.', 'yellow');
                $quotaHits++;
                break;
            }

            if (!empty($result['done'])) {
                $exhaustedStories++;
                continue;
            }

            if (!empty($result['error'])) {
                CLI::write('Erro em story #' . $story->id . ': ' . $result['error'], 'red');
                break;
            }

            $processed++;
            CLI::write(sprintf(
                'Story #%d — página %s gerada (%d/%d)',
                $story->id,
                $result['page'] ?? '?',
                $processed,
                $maxImages
            ), 'green');
        }

        CLI::write(sprintf(
            'Concluído — imagens geradas: %d, stories finalizadas neste tick: %d, quota_hits: %d',
            $processed,
            $exhaustedStories,
            $quotaHits
        ));
    }

    /**
     * Find an active story that still has at least one page without
     * an image. Older stories first so we don't starve old backlog.
     */
    private function pickPendingStory($db, $onlyStory)
    {
        $sub = $db->table('web_story_pages')
            ->select('web_story_id')
            ->where('(image_path IS NULL OR image_path = "")', null, false)
            ->groupBy('web_story_id');

        $builder = $db->table('web_stories')
            ->select('web_stories.id')
            ->whereIn('web_stories.id', $sub)
            ->orderBy('web_stories.id', 'ASC')
            ->limit(1);

        if ($onlyStory > 0) {
            $builder->where('web_stories.id', $onlyStory);
        }

        return $builder->get()->getRow();
    }
}
