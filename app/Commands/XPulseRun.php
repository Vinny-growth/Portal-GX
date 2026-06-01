<?php

namespace App\Commands;

use App\Libraries\XPulseAnalyzer;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class XPulseRun extends BaseCommand
{
    protected $group = 'ContentAI';
    protected $name = 'x:pulse';
    protected $description = 'Fetch the X (Twitter) pulse for financial themes via Grok live search.';

    public function run(array $params)
    {
        $analyzer = new XPulseAnalyzer();
        $result = $analyzer->run();

        if (!empty($result['skipped'])) {
            CLI::write('Skipped: ' . ($result['reason'] ?? 'unknown'), 'yellow');
            return;
        }
        if (!empty($result['error'])) {
            CLI::write('Error: ' . $result['error'], 'red');
            if (!empty($result['raw'])) {
                CLI::write('Raw (first 400 chars): ' . substr((string) $result['raw'], 0, 400));
            }
            return;
        }
        CLI::write('X Pulse: persisted ' . $result['snapshot_count'] . ' themes.', 'green');
    }
}
