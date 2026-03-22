<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Commands extends BaseConfig
{
    public $commands = [
        'content:run' => \App\Commands\ContentAIRun::class,
        'content:trends' => \App\Commands\ContentAITrends::class,
        'content:backfill' => \App\Commands\ContentAIBackfill::class,
    ];
}
