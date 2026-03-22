<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ContentAIBackfill extends BaseCommand
{
    protected $group = 'ContentAI';
    protected $name = 'content:backfill';
    protected $description = 'Backfill title_hash for existing posts.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $count = $db->table('posts')->where('title_hash IS NULL', null, false)->orWhere('title_hash', '')->countAllResults();
        $db->query("UPDATE posts SET title_hash = MD5(LOWER(title)) WHERE title_hash IS NULL OR title_hash = ''");
        CLI::write('Backfilled title_hash. Rows affected: ' . $count);
    }
}
