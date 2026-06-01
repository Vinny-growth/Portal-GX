<?php

namespace App\Commands;

use App\Libraries\CrmSyncService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class NewsletterSyncCrm extends BaseCommand
{
    protected $group = 'Newsletter';
    protected $name = 'newsletter:sync-crm';
    protected $description = 'Pull leads and/or clients from the CRM and upsert into the newsletter subscribers list.';
    protected $usage = 'newsletter:sync-crm [--leads] [--clients] [--full]';
    protected $arguments = [];
    protected $options = [
        '--leads'    => 'Sync only leads.',
        '--clients'  => 'Sync only clients.',
        '--full'     => 'Ignore the incremental cursor and pull the entire base.',
    ];

    public function run(array $params)
    {
        $onlyLeads = CLI::getOption('leads');
        $onlyClients = CLI::getOption('clients');
        $full = (bool) CLI::getOption('full');

        $sources = [];
        if ($onlyLeads && !$onlyClients) $sources = ['leads'];
        elseif ($onlyClients && !$onlyLeads) $sources = ['clients'];
        else $sources = ['leads', 'clients'];

        $service = new CrmSyncService();
        foreach ($sources as $source) {
            CLI::write("→ Syncing {$source}" . ($full ? ' (full)' : ' (incremental)'), 'cyan');
            try {
                $res = $service->syncSource($source, 'cron', null, $full);
                $color = $res['status'] === 'success' ? 'green' : 'red';
                CLI::write(sprintf(
                    "  status=%s pages=%d received=%d created=%d updated=%d skipped_unsub=%d skipped_invalid=%d opt_out_filtered=%d audit_id=%d",
                    $res['status'],
                    $res['pages_fetched'],
                    $res['total_received'],
                    $res['created_count'],
                    $res['updated_count'],
                    $res['skipped_unsubscribed'],
                    $res['skipped_invalid'],
                    $res['filtered_opt_out_total'],
                    $res['audit_id']
                ), $color);
                if (!empty($res['error'])) {
                    CLI::write('  error: ' . $res['error'], 'red');
                }
            } catch (\Throwable $e) {
                CLI::error("  fatal exception: " . $e->getMessage());
                log_message('error', 'newsletter:sync-crm fatal for ' . $source . ': ' . $e->getMessage());
            }
        }
    }
}
