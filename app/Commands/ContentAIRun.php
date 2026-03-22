<?php

namespace App\Commands;

use App\Libraries\ContentAIService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ContentAIRun extends BaseCommand
{
    protected $group = 'ContentAI';
    protected $name = 'content:run';
    protected $description = 'Run AI content schedule and generation.';

    public function run(array $params)
    {
        $service = new ContentAIService();
        $result = $service->runScheduled();
        CLI::write('Slots: ' . json_encode($result['slots']));
        CLI::write('Planned: ' . $result['planned']);
        CLI::write('Generated: ' . $result['generated']);
        if (!empty($result['errors'])) {
            CLI::write('Errors: ' . json_encode($result['errors']), 'red');
        }
    }
}
