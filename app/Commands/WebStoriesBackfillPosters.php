<?php

namespace App\Commands;

use App\Models\WebStoriesModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Generate the AMP poster variants (-3x4, -1x1, -4x3) for every existing
 * web story that still lacks them. New stories get variants on save —
 * this command exists for one-time backfill of legacy data.
 *
 * Usage:
 *   php spark webstories:backfill-posters
 *   php spark webstories:backfill-posters 12      # only id=12
 */
class WebStoriesBackfillPosters extends BaseCommand
{
    protected $group = 'WebStories';
    protected $name = 'webstories:backfill-posters';
    protected $description = 'Gera variantes de poster AMP (3:4, 1:1, 4:3) para histórias existentes.';
    protected $usage = 'webstories:backfill-posters [story_id]';

    public function run(array $params)
    {
        $model = new WebStoriesModel();
        $only = isset($params[0]) ? (int) $params[0] : 0;

        $db = \Config\Database::connect();
        $builder = $db->table('web_stories')->select('id, image_path');
        if ($only > 0) {
            $builder->where('id', $only);
        }
        $rows = $builder->get()->getResult();

        $touched = 0;
        $skipped = 0;
        foreach ($rows as $row) {
            if (empty($row->image_path)) {
                $skipped++;
                continue;
            }
            $abs = FCPATH . ltrim($row->image_path, '/');
            if (!is_file($abs)) {
                CLI::write('Imagem não encontrada para story #' . $row->id . ' — ' . $row->image_path, 'yellow');
                $skipped++;
                continue;
            }
            $model->generatePosterVariants($abs);
            $touched++;
            CLI::write('Story #' . $row->id . ' — variantes geradas', 'green');
        }

        CLI::write(sprintf(
            'Concluído — processados: %d, pulados: %d',
            $touched,
            $skipped
        ));
    }
}
