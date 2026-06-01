<?php

namespace App\Commands;

use App\Libraries\GoogleAnalyticsDashboardClient;
use App\Models\DashboardIntegrationModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Renews the Google Analytics access_token before it expires and persists
 * the result. Two reasons to run this on cron rather than wait for a
 * dashboard load:
 *
 * 1. Google may rotate the refresh_token in the response — saving it
 *    immediately preserves the new one. Letting the token sit unused for
 *    weeks risks the rotation falling out of sync.
 *
 * 2. If the consent screen is in "Testing" mode, refresh tokens expire 7
 *    days after issuance. Using the refresh_token frequently doesn't extend
 *    that window (Google's policy is fixed), but it surfaces the failure
 *    immediately in the cron log instead of when the admin opens the dashboard.
 *
 * Usage:
 *   php spark ga4:refresh-token            # only refresh if expiring within 30 min
 *   php spark ga4:refresh-token --force    # always refresh
 *
 * Recommended cron entry (every hour):
 *   0 * * * * php /path/spark ga4:refresh-token
 */
class GA4RefreshToken extends BaseCommand
{
    protected $group = 'GA4';
    protected $name = 'ga4:refresh-token';
    protected $description = 'Renova proativamente o access_token do Google Analytics.';
    protected $usage = 'ga4:refresh-token [--force]';

    public function run(array $params)
    {
        $force = in_array('--force', $params, true);

        $integration = new DashboardIntegrationModel();
        $connection = $integration->getGoogleAnalyticsConnection();

        if (empty($connection['client_id']) || empty($connection['client_secret'])) {
            CLI::write('GA4 não configurado (faltam credenciais OAuth) — nada a fazer.', 'yellow');
            return;
        }
        if (empty($connection['refresh_token'])) {
            CLI::write('Sem refresh_token salvo — usuário precisa reconectar manualmente.', 'red');
            return;
        }

        $expiresAt = !empty($connection['token_expires_at']) ? strtotime((string) $connection['token_expires_at']) : 0;
        $secondsLeft = $expiresAt > 0 ? $expiresAt - time() : 0;

        if (!$force && $secondsLeft > 1800) {
            CLI::write(sprintf('Token ainda válido por %d min — sem necessidade de renovar.', (int) ($secondsLeft / 60)), 'green');
            return;
        }

        // redirect_uri is only used during the initial auth code exchange,
        // not in refresh_token requests — any non-empty value works here.
        $client = new GoogleAnalyticsDashboardClient(
            ['client_id' => $connection['client_id'], 'client_secret' => $connection['client_secret']],
            'https://gx.capital/admin/dashboard/google-analytics/callback'
        );

        // We use the public snapshot path to force a refresh (it calls
        // ensureAuthorizedToken internally and returns the new token) — but
        // that costs API quota. Better: poke the realtime endpoint, which is
        // the cheapest call available and still triggers the refresh.
        $result = $client->getRealtimeSummary($connection);
        if (empty($result['success'])) {
            $error = $result['error'] ?? 'erro desconhecido';
            CLI::write('Falha ao renovar token: ' . $error, 'red');
            $integration->setGoogleAnalyticsLastError($error);
            return;
        }

        if (!empty($result['token']) && is_array($result['token'])) {
            $integration->saveGoogleAnalyticsToken($result['token']);
            $integration->clearGoogleAnalyticsLastError();
            CLI::write('Token renovado com sucesso. Próximo expira em: ' . ($result['token']['token_expires_at'] ?? '?'), 'green');
            return;
        }

        CLI::write('Renovação bem-sucedida mas resposta sem token — verifique logs.', 'yellow');
    }
}
