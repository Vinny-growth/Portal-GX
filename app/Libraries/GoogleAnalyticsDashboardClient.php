<?php

namespace App\Libraries;

require_once (defined('APPPATH') ? APPPATH : dirname(__DIR__) . DIRECTORY_SEPARATOR) . 'ThirdParty/google-apiclient/vendor/autoload.php';

use Google\Client;

class GoogleAnalyticsDashboardClient
{
    private const ANALYTICS_SCOPE = 'https://www.googleapis.com/auth/analytics.readonly';

    protected array $credentials;
    protected string $redirectUri;

    public function __construct(array $credentials, string $redirectUri)
    {
        $this->credentials = $credentials;
        $this->redirectUri = $redirectUri;
    }

    public function isConfigured()
    {
        return !empty($this->credentials['client_id']) && !empty($this->credentials['client_secret']);
    }

    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    public function getAuthorizationUrl(string $state)
    {
        $client = $this->createClient();
        $client->setState($state);
        return $client->createAuthUrl();
    }

    public function exchangeCodeForTokens(string $code)
    {
        try {
            $client = $this->createClient();
            $token = $client->fetchAccessTokenWithAuthCode($code);
            if (!empty($token['error'])) {
                return [
                    'success' => false,
                    'error' => $token['error_description'] ?? $token['error'],
                ];
            }

            $normalizedToken = $this->normalizeTokenPayload($token);
            $normalizedToken['connected_email'] = $this->extractEmailFromIdToken($client, $token['id_token'] ?? null);

            return [
                'success' => true,
                'token' => $normalizedToken,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function listAccessibleProperties(array $connection)
    {
        $auth = $this->ensureAuthorizedToken($connection);
        if (!$auth['success']) {
            return $auth;
        }

        $properties = [];
        $pageToken = null;

        do {
            $url = 'https://analyticsadmin.googleapis.com/v1alpha/accountSummaries?pageSize=200';
            if (!empty($pageToken)) {
                $url .= '&pageToken=' . rawurlencode($pageToken);
            }

            $response = $this->apiRequest($url, $auth['token']['access_token']);
            if (!$response['success']) {
                return [
                    'success' => false,
                    'error' => $response['error'],
                    'token' => $auth['token'],
                ];
            }

            foreach ($response['data']['accountSummaries'] ?? [] as $accountSummary) {
                $accountResource = (string) ($accountSummary['account'] ?? '');
                $accountId = $this->extractResourceId($accountResource);
                $accountName = (string) ($accountSummary['displayName'] ?? $accountId);

                foreach ($accountSummary['propertySummaries'] ?? [] as $propertySummary) {
                    $propertyResource = (string) ($propertySummary['property'] ?? '');
                    $propertyId = $this->extractResourceId($propertyResource);
                    if ($propertyId === '') {
                        continue;
                    }

                    $properties[] = [
                        'account_id' => $accountId,
                        'account_name' => $accountName,
                        'account_resource' => $accountResource,
                        'property_id' => $propertyId,
                        'property_name' => (string) ($propertySummary['displayName'] ?? $propertyId),
                        'property_resource' => $propertyResource,
                        'property_type' => (string) ($propertySummary['propertyType'] ?? ''),
                    ];
                }
            }

            $pageToken = $response['data']['nextPageToken'] ?? null;
        } while (!empty($pageToken));

        usort($properties, static function ($a, $b) {
            $leftValue = (string) (($a['account_name'] ?? '') . ' ' . ($a['property_name'] ?? ''));
            $rightValue = (string) (($b['account_name'] ?? '') . ' ' . ($b['property_name'] ?? ''));
            $left = function_exists('mb_strtolower') ? mb_strtolower($leftValue) : strtolower($leftValue);
            $right = function_exists('mb_strtolower') ? mb_strtolower($rightValue) : strtolower($rightValue);
            return $left <=> $right;
        });

        return [
            'success' => true,
            'properties' => $properties,
            'token' => $auth['token'],
        ];
    }

    public function getDashboardSnapshot(array $connection, int $days)
    {
        $auth = $this->ensureAuthorizedToken($connection);
        if (!$auth['success']) {
            return $auth;
        }

        $propertyId = trim((string) ($connection['property_id'] ?? ''));
        if ($propertyId === '') {
            return [
                'success' => false,
                'error' => 'Nenhuma propriedade do GA4 foi selecionada.',
                'token' => $auth['token'],
            ];
        }

        $startDate = date('Y-m-d', strtotime('-' . max(0, $days - 1) . ' days'));
        $endDate = date('Y-m-d');

        $overviewReport = $this->runReport(
            $auth['token']['access_token'],
            $propertyId,
            [],
            ['activeUsers', 'sessions', 'screenPageViews', 'newUsers', 'engagementRate', 'averageSessionDuration'],
            $startDate,
            $endDate,
            1
        );

        if (!$overviewReport['success']) {
            return [
                'success' => false,
                'error' => $overviewReport['error'],
                'token' => $auth['token'],
            ];
        }

        $channelsReport = $this->runReport(
            $auth['token']['access_token'],
            $propertyId,
            ['sessionDefaultChannelGroup'],
            ['sessions', 'activeUsers'],
            $startDate,
            $endDate,
            8,
            'sessions'
        );

        if (!$channelsReport['success']) {
            return [
                'success' => false,
                'error' => $channelsReport['error'],
                'token' => $auth['token'],
            ];
        }

        $pagesReport = $this->runReport(
            $auth['token']['access_token'],
            $propertyId,
            ['pagePath'],
            ['screenPageViews', 'activeUsers'],
            $startDate,
            $endDate,
            10,
            'screenPageViews'
        );

        if (!$pagesReport['success']) {
            return [
                'success' => false,
                'error' => $pagesReport['error'],
                'token' => $auth['token'],
            ];
        }

        $dailyReport = $this->runReport(
            $auth['token']['access_token'],
            $propertyId,
            ['date'],
            ['activeUsers', 'sessions', 'screenPageViews', 'newUsers'],
            $startDate,
            $endDate,
            $days
        );

        if (!$dailyReport['success']) {
            return [
                'success' => false,
                'error' => $dailyReport['error'],
                'token' => $auth['token'],
            ];
        }

        $devicesReport = $this->runReport(
            $auth['token']['access_token'],
            $propertyId,
            ['deviceCategory'],
            ['sessions', 'activeUsers', 'screenPageViews'],
            $startDate,
            $endDate,
            10,
            'sessions'
        );

        if (!$devicesReport['success']) {
            return [
                'success' => false,
                'error' => $devicesReport['error'],
                'token' => $auth['token'],
            ];
        }

        $browsersReport = $this->runReport(
            $auth['token']['access_token'],
            $propertyId,
            ['browser'],
            ['sessions', 'screenPageViews'],
            $startDate,
            $endDate,
            8,
            'sessions'
        );

        if (!$browsersReport['success']) {
            return [
                'success' => false,
                'error' => $browsersReport['error'],
                'token' => $auth['token'],
            ];
        }

        $newVsReturningReport = $this->runReport(
            $auth['token']['access_token'],
            $propertyId,
            ['newVsReturning'],
            ['activeUsers', 'sessions'],
            $startDate,
            $endDate,
            5
        );

        if (!$newVsReturningReport['success']) {
            return [
                'success' => false,
                'error' => $newVsReturningReport['error'],
                'token' => $auth['token'],
            ];
        }

        $realtimeReport = $this->runRealtimeReport(
            $auth['token']['access_token'],
            $propertyId,
            ['activeUsers']
        );

        if (!$realtimeReport['success']) {
            return [
                'success' => false,
                'error' => $realtimeReport['error'],
                'token' => $auth['token'],
            ];
        }

        $overviewRows = $this->extractRows($overviewReport['data']);
        $channelsRows = $this->extractRows($channelsReport['data']);
        $pagesRows = $this->extractRows($pagesReport['data']);
        $dailyRows = $this->extractRows($dailyReport['data']);
        $devicesRows = $this->extractRows($devicesReport['data']);
        $browsersRows = $this->extractRows($browsersReport['data']);
        $newVsReturningRows = $this->extractRows($newVsReturningReport['data']);
        $realtimeRows = $this->extractRows($realtimeReport['data']);

        $newReturningSplit = ['new' => 0, 'returning' => 0];
        foreach ($newVsReturningRows as $row) {
            $bucket = strtolower((string) ($row['newVsReturning'] ?? ''));
            $users = (int) round((float) ($row['activeUsers'] ?? 0));
            if ($bucket === 'new') {
                $newReturningSplit['new'] = $users;
            } elseif ($bucket === 'returning') {
                $newReturningSplit['returning'] = $users;
            }
        }

        $overviewRow = $overviewRows[0] ?? [];
        $engagementRate = (float) ($overviewRow['engagementRate'] ?? 0);
        if ($engagementRate > 0 && $engagementRate <= 1) {
            $engagementRate *= 100;
        }

        $channels = array_map(static function ($row) {
            return [
                'channel' => !empty($row['sessionDefaultChannelGroup']) ? $row['sessionDefaultChannelGroup'] : 'Não definido',
                'sessions' => (int) round((float) ($row['sessions'] ?? 0)),
                'active_users' => (int) round((float) ($row['activeUsers'] ?? 0)),
            ];
        }, $channelsRows);

        $pages = array_map(static function ($row) {
            return [
                'path' => !empty($row['pagePath']) ? $row['pagePath'] : '/',
                'views' => (int) round((float) ($row['screenPageViews'] ?? 0)),
                'active_users' => (int) round((float) ($row['activeUsers'] ?? 0)),
            ];
        }, $pagesRows);

        $daily = array_map(static function ($row) {
            $rawDate = (string) ($row['date'] ?? '');
            $isoDate = strlen($rawDate) === 8
                ? substr($rawDate, 0, 4) . '-' . substr($rawDate, 4, 2) . '-' . substr($rawDate, 6, 2)
                : $rawDate;
            return [
                'date' => $isoDate,
                'active_users' => (int) round((float) ($row['activeUsers'] ?? 0)),
                'sessions' => (int) round((float) ($row['sessions'] ?? 0)),
                'screen_page_views' => (int) round((float) ($row['screenPageViews'] ?? 0)),
                'new_users' => (int) round((float) ($row['newUsers'] ?? 0)),
            ];
        }, $dailyRows);
        usort($daily, static function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        $deviceLabels = [
            'desktop' => 'Desktop',
            'mobile' => 'Mobile',
            'tablet' => 'Tablet',
            'smart tv' => 'TV',
            'console' => 'Console',
        ];
        $devices = array_map(static function ($row) use ($deviceLabels) {
            $rawType = strtolower((string) ($row['deviceCategory'] ?? ''));
            return [
                'type' => $deviceLabels[$rawType] ?? ($rawType !== '' ? ucfirst($rawType) : 'Não identificado'),
                'sessions' => (int) round((float) ($row['sessions'] ?? 0)),
                'active_users' => (int) round((float) ($row['activeUsers'] ?? 0)),
                'views' => (int) round((float) ($row['screenPageViews'] ?? 0)),
            ];
        }, $devicesRows);

        $browsers = array_map(static function ($row) {
            return [
                'name' => (string) ($row['browser'] ?? ''),
                'sessions' => (int) round((float) ($row['sessions'] ?? 0)),
                'views' => (int) round((float) ($row['screenPageViews'] ?? 0)),
            ];
        }, $browsersRows);

        return [
            'success' => true,
            'overview' => [
                'active_users' => (int) round((float) ($overviewRow['activeUsers'] ?? 0)),
                'sessions' => (int) round((float) ($overviewRow['sessions'] ?? 0)),
                'screen_page_views' => (int) round((float) ($overviewRow['screenPageViews'] ?? 0)),
                'new_users' => (int) round((float) ($overviewRow['newUsers'] ?? 0)),
                'returning_users' => $newReturningSplit['returning'],
                'engagement_rate_pct' => round($engagementRate, 2),
                'average_session_duration_sec' => round((float) ($overviewRow['averageSessionDuration'] ?? 0), 2),
            ],
            'channels' => $channels,
            'pages' => $pages,
            'daily' => $daily,
            'devices' => $devices,
            'browsers' => $browsers,
            'realtime' => [
                'active_users' => (int) round((float) (($realtimeRows[0]['activeUsers'] ?? 0))),
            ],
            'token' => $auth['token'],
            'fetched_at' => date('Y-m-d H:i:s'),
        ];
    }

    public function getRealtimeSummary(array $connection)
    {
        $auth = $this->ensureAuthorizedToken($connection);
        if (!$auth['success']) {
            return $auth;
        }

        $propertyId = trim((string) ($connection['property_id'] ?? ''));
        if ($propertyId === '') {
            return [
                'success' => false,
                'error' => 'Nenhuma propriedade do GA4 foi selecionada.',
                'token' => $auth['token'],
            ];
        }

        $report = $this->runRealtimeReport($auth['token']['access_token'], $propertyId, ['activeUsers']);
        if (!$report['success']) {
            return [
                'success' => false,
                'error' => $report['error'],
                'token' => $auth['token'],
            ];
        }

        $rows = $this->extractRows($report['data']);
        return [
            'success' => true,
            'realtime' => [
                'active_users' => (int) round((float) ($rows[0]['activeUsers'] ?? 0)),
            ],
            'token' => $auth['token'],
        ];
    }

    private function createClient()
    {
        $client = new Client();
        $client->setClientId((string) ($this->credentials['client_id'] ?? ''));
        $client->setClientSecret((string) ($this->credentials['client_secret'] ?? ''));
        $client->setRedirectUri($this->redirectUri);
        $client->setAccessType('offline');
        // We previously called setIncludeGrantedScopes(true). Removed because
        // it can pull in extra scopes (e.g. adwords) granted to the same
        // client_id elsewhere, which triggers Google's reauth/RAPT session
        // policies and forces the admin to reconnect on a regular cadence.
        // We only need analytics.readonly + identity claims, nothing else.
        $client->setPrompt('consent select_account');
        $client->setScopes([
            'openid',
            'email',
            'profile',
            self::ANALYTICS_SCOPE,
        ]);

        return $client;
    }

    private function ensureAuthorizedToken(array $connection)
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'As credenciais OAuth do Google Analytics ainda não foram configuradas.',
            ];
        }

        $token = [
            'access_token' => (string) ($connection['access_token'] ?? ''),
            'refresh_token' => (string) ($connection['refresh_token'] ?? ''),
            'token_expires_at' => $connection['token_expires_at'] ?? null,
            'scope' => (string) ($connection['scope'] ?? ''),
            'connected_email' => (string) ($connection['connected_email'] ?? ''),
            'connected_at' => $connection['connected_at'] ?? null,
        ];

        $expiresAt = !empty($token['token_expires_at']) ? strtotime((string) $token['token_expires_at']) : 0;
        $needsRefresh = empty($token['access_token']) || empty($expiresAt) || $expiresAt <= (time() + 60);

        if ($needsRefresh) {
            if (empty($token['refresh_token'])) {
                return [
                    'success' => false,
                    'error' => 'A conta Google Analytics não está conectada ou o refresh token não está disponível.',
                ];
            }

            try {
                $client = $this->createClient();
                $newToken = $client->fetchAccessTokenWithRefreshToken($token['refresh_token']);
                if (!empty($newToken['error'])) {
                    return [
                        'success' => false,
                        'error' => $this->humanizeOAuthError(
                            (string) $newToken['error'],
                            (string) ($newToken['error_subtype'] ?? ''),
                            (string) ($newToken['error_description'] ?? '')
                        ),
                    ];
                }

                $normalizedToken = $this->normalizeTokenPayload($newToken, $token['refresh_token']);
                $normalizedToken['connected_email'] = $token['connected_email'];
                $normalizedToken['connected_at'] = $token['connected_at'] ?: date('Y-m-d H:i:s');

                return [
                    'success' => true,
                    'token' => $normalizedToken,
                ];
            } catch (\Throwable $e) {
                return [
                    'success' => false,
                    'error' => $this->humanizeOAuthError('exception', '', $e->getMessage()),
                ];
            }
        }

        return [
            'success' => true,
            'token' => $token,
        ];
    }

    /**
     * Translate Google's OAuth error codes into actionable Portuguese text.
     * Especially helps when the project is in "Testing" mode on Google Cloud
     * Console — the symptom is the user having to reconnect every ~7 days.
     */
    private function humanizeOAuthError(string $error, string $subtype, string $description): string
    {
        $error = strtolower(trim($error));
        $subtype = strtolower(trim($subtype));
        $blob = strtolower($error . ' ' . $subtype . ' ' . $description);

        if ($error === 'invalid_grant' && (str_contains($blob, 'rapt') || $subtype === 'invalid_rapt')) {
            return 'A sessão Google expirou por política de reautenticação (invalid_rapt). '
                . 'Causa típica: o app OAuth está em modo "Testing" no Google Cloud Console — '
                . 'tokens duram 7 dias nesse modo. Solução permanente: publique o consent screen '
                . '("Publishing status: In production") em https://console.cloud.google.com/apis/credentials/consent. '
                . 'Reconecte a conta para continuar.';
        }

        if ($error === 'invalid_grant') {
            return 'Token Google revogado ou expirado (invalid_grant). '
                . 'Pode ter sido removido pelo usuário em https://myaccount.google.com/permissions, '
                . 'ou expirou pelas políticas do app (modo Testing → 7 dias; conta inativa por mais de 6 meses; '
                . 'mudança de senha). Reconecte a conta.';
        }

        if ($error === 'invalid_client' || str_contains($blob, 'invalid_client')) {
            return 'Client ID/Secret OAuth inválidos. Verifique as credenciais salvas em '
                . 'Configurações → Google Analytics. Detalhe: ' . $description;
        }

        if ($error === 'unauthorized_client') {
            return 'O app OAuth não está autorizado para o tipo de fluxo solicitado. '
                . 'Confirme em Google Cloud Console → APIs & Services → Credentials que o redirect URI cadastrado '
                . 'casa exatamente com a URL do callback usada aqui. Detalhe: ' . $description;
        }

        return $description !== '' ? $description : ('Erro OAuth: ' . $error);
    }

    private function normalizeTokenPayload(array $token, ?string $fallbackRefreshToken = null)
    {
        $expiresIn = isset($token['expires_in']) ? (int) $token['expires_in'] : 3600;
        $refreshToken = !empty($token['refresh_token']) ? $token['refresh_token'] : $fallbackRefreshToken;

        return [
            'access_token' => (string) ($token['access_token'] ?? ''),
            'refresh_token' => (string) ($refreshToken ?? ''),
            'token_expires_at' => date('Y-m-d H:i:s', time() + max(60, $expiresIn)),
            'scope' => (string) ($token['scope'] ?? self::ANALYTICS_SCOPE),
            'connected_at' => date('Y-m-d H:i:s'),
        ];
    }

    private function extractEmailFromIdToken(Client $client, ?string $idToken)
    {
        if (empty($idToken)) {
            return null;
        }

        try {
            $payload = $client->verifyIdToken($idToken);
            return $payload['email'] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Relatórios de página para o painel de simuladores. Dois reports numa auth:
     *  - 'daily'  = [date, pagePath] → visitantes/views por dia (inclui "hoje")
     *  - 'period' = [pagePath]       → visitantes únicos do período (dedupados
     *               pelo GA4) + userEngagementDuration p/ tempo médio por visitante
     * Filtrado por regex de pagePath (PARTIAL_REGEXP) para não trazer o site todo.
     *
     * @return array{success:bool, daily?:array, period?:array, error?:string, token?:array}
     */
    public function getSimulatorReports(array $connection, int $days, string $pathRegex, int $limit = 5000)
    {
        $auth = $this->ensureAuthorizedToken($connection);
        if (!$auth['success']) {
            return $auth;
        }

        $propertyId = trim((string) ($connection['property_id'] ?? ''));
        if ($propertyId === '') {
            return ['success' => false, 'error' => 'Nenhuma propriedade do GA4 foi selecionada.', 'token' => $auth['token']];
        }

        $startDate = date('Y-m-d', strtotime('-' . max(0, $days - 1) . ' days'));
        $endDate   = date('Y-m-d');
        $token     = $auth['token']['access_token'];

        $filter = $pathRegex !== '' ? [
            'filter' => [
                'fieldName'    => 'pagePath',
                'stringFilter' => ['matchType' => 'PARTIAL_REGEXP', 'value' => $pathRegex, 'caseSensitive' => false],
            ],
        ] : null;

        $dailyReport = $this->runReport(
            $token, $propertyId,
            ['date', 'pagePath'],
            ['activeUsers', 'screenPageViews', 'userEngagementDuration'],
            $startDate, $endDate, $limit, null, $filter
        );
        if (!$dailyReport['success']) {
            return ['success' => false, 'error' => $dailyReport['error'], 'token' => $auth['token']];
        }

        $periodReport = $this->runReport(
            $token, $propertyId,
            ['pagePath'],
            ['activeUsers', 'screenPageViews', 'userEngagementDuration'],
            $startDate, $endDate, $limit, 'activeUsers', $filter
        );
        if (!$periodReport['success']) {
            return ['success' => false, 'error' => $periodReport['error'], 'token' => $auth['token']];
        }

        return [
            'success' => true,
            'daily'   => $this->extractRows($dailyReport['data'] ?? []),
            'period'  => $this->extractRows($periodReport['data'] ?? []),
            'token'   => $auth['token'],
        ];
    }

    private function runReport(string $accessToken, string $propertyId, array $dimensions, array $metrics, string $startDate, string $endDate, int $limit = 10, ?string $orderMetric = null, ?array $dimensionFilter = null)
    {
        $payload = [
            'dateRanges' => [
                [
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                ],
            ],
            'metrics' => array_map(static function ($metric) {
                return ['name' => $metric];
            }, $metrics),
            'limit' => (string) max(1, $limit),
        ];

        if (!empty($dimensions)) {
            $payload['dimensions'] = array_map(static function ($dimension) {
                return ['name' => $dimension];
            }, $dimensions);
        }

        if (!empty($orderMetric)) {
            $payload['orderBys'] = [
                [
                    'metric' => ['metricName' => $orderMetric],
                    'desc' => true,
                ],
            ];
        }

        if (!empty($dimensionFilter)) {
            $payload['dimensionFilter'] = $dimensionFilter;
        }

        return $this->apiRequest(
            'https://analyticsdata.googleapis.com/v1beta/properties/' . rawurlencode($propertyId) . ':runReport',
            $accessToken,
            'POST',
            $payload
        );
    }

    private function runRealtimeReport(string $accessToken, string $propertyId, array $metrics)
    {
        $payload = [
            'metrics' => array_map(static function ($metric) {
                return ['name' => $metric];
            }, $metrics),
        ];

        return $this->apiRequest(
            'https://analyticsdata.googleapis.com/v1beta/properties/' . rawurlencode($propertyId) . ':runRealtimeReport',
            $accessToken,
            'POST',
            $payload
        );
    }

    private function apiRequest(string $url, string $accessToken, string $method = 'GET', ?array $payload = null)
    {
        $ch = curl_init($url);
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json',
        ];

        if ($method === 'POST') {
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $rawResponse = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($rawResponse === false) {
            return [
                'success' => false,
                'error' => 'Falha na comunicação com o Google Analytics: ' . $curlError,
            ];
        }

        $decoded = json_decode($rawResponse, true);
        if ($httpCode < 200 || $httpCode >= 300) {
            $errorMessage = $decoded['error']['message'] ?? ('Resposta inesperada do Google Analytics (HTTP ' . $httpCode . ').');
            return [
                'success' => false,
                'error' => $errorMessage,
            ];
        }

        if (!is_array($decoded)) {
            return [
                'success' => false,
                'error' => 'O Google Analytics retornou um payload inválido.',
            ];
        }

        return [
            'success' => true,
            'data' => $decoded,
        ];
    }

    private function extractRows(array $response)
    {
        $dimensionHeaders = array_map(static function ($header) {
            return $header['name'] ?? '';
        }, $response['dimensionHeaders'] ?? []);
        $metricHeaders = array_map(static function ($header) {
            return $header['name'] ?? '';
        }, $response['metricHeaders'] ?? []);

        $rows = [];
        foreach ($response['rows'] ?? [] as $row) {
            $mapped = [];

            foreach ($dimensionHeaders as $index => $headerName) {
                $mapped[$headerName] = $row['dimensionValues'][$index]['value'] ?? null;
            }

            foreach ($metricHeaders as $index => $headerName) {
                $mapped[$headerName] = $row['metricValues'][$index]['value'] ?? null;
            }

            $rows[] = $mapped;
        }

        return $rows;
    }

    private function extractResourceId(string $resourceName)
    {
        if ($resourceName === '') {
            return '';
        }

        $parts = explode('/', $resourceName);
        return (string) end($parts);
    }
}
