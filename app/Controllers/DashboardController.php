<?php

namespace App\Controllers;

use App\Libraries\GoogleAnalyticsDashboardClient;
use App\Models\DashboardIntegrationModel;
use App\Models\DashboardModel;

class DashboardController extends BaseAdminController
{
    private const GA4_SNAPSHOT_CACHE_TTL = 300;
    private const GA4_PROPERTIES_CACHE_TTL = 300;
    private const GA4_REALTIME_CACHE_TTL = 20;

    protected $dashboardModel;
    protected $dashboardIntegrationModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->dashboardModel = new DashboardModel();
        $this->dashboardIntegrationModel = new DashboardIntegrationModel();
    }

    /**
     * Main dashboard page
     */
    public function index()
    {
        checkPermission('admin_panel');
        $days = $this->dashboardModel->normalizeDays(inputGet('days'));
        $data = $this->buildDashboardViewData($days);
        $data['title'] = 'Dashboard de Analytics';
        $data['panelSettings'] = (object) ['theme' => 'default'];

        echo view('admin/includes/_header', $data);
        echo view('admin/dashboard/index_analytics', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Get analytics data via AJAX
     */
    public function getAnalyticsData()
    {
        checkPermission('admin_panel');

        $type = inputPost('type');
        $days = $this->dashboardModel->normalizeDays(inputPost('days'));
        $response = ['success' => false, 'data' => []];

        try {
            switch ($type) {
                case 'visitors':
                    $response['data'] = [
                        'overview' => $this->dashboardModel->getOverview($days),
                        'daily' => $this->dashboardModel->getDailyVisitors($days),
                        'segments' => $this->dashboardModel->getVisitorSegments($days),
                    ];
                    break;

                case 'retention':
                    $response['data'] = [
                        'overview' => $this->dashboardModel->getRetentionMetrics($days),
                        'segments' => $this->dashboardModel->getVisitorSegments($days),
                    ];
                    break;

                case 'top_posts':
                    $response['data'] = $this->dashboardModel->getTopPosts(10, $days);
                    break;

                case 'engagement':
                    $response['data'] = $this->dashboardModel->getMostEngagedPosts(10, $days);
                    break;

                case 'traffic_sources':
                    $response['data'] = $this->dashboardModel->getTrafficSources($days);
                    break;

                case 'categories':
                    $response['data'] = $this->dashboardModel->getCategoryPerformance($days);
                    break;

                case 'user_engagement':
                    $response['data'] = $this->dashboardModel->getUserEngagement($days);
                    break;

                case 'content_summary':
                    $response['data'] = $this->dashboardModel->getContentSummary($days);
                    break;

                case 'devices':
                case 'device_analytics':
                    $response['data'] = $this->dashboardModel->getDeviceAnalytics($days);
                    break;

                case 'real_time':
                    $response['data'] = $this->dashboardModel->getRealTimeAnalytics();
                    break;

                case 'conversions':
                    $response['data'] = $this->dashboardModel->getConversionMetrics($days);
                    break;

                case 'ga4_overview':
                case 'ga4_channels':
                case 'ga4_pages':
                    $ga4Payload = $this->getGoogleAnalyticsDashboardPayload($days);
                    if (empty($ga4Payload['snapshot']['success'])) {
                        throw new \Exception($ga4Payload['snapshot']['error'] ?? 'Google Analytics 4 não está conectado.');
                    }

                    if ($type === 'ga4_overview') {
                        $response['data'] = $ga4Payload['snapshot']['overview'] ?? [];
                    } elseif ($type === 'ga4_channels') {
                        $response['data'] = $ga4Payload['snapshot']['channels'] ?? [];
                    } else {
                        $response['data'] = $ga4Payload['snapshot']['pages'] ?? [];
                    }
                    break;

                default:
                    throw new \Exception('Tipo de dados inválido');
            }

            $response['success'] = true;
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
        }

        return $this->response->setJSON($response);
    }

    /**
     * Save widget configuration
     */
    public function saveWidgetConfig()
    {
        checkPermission('admin_panel');

        $config = inputPost('config');
        $response = ['success' => false];

        try {
            if (empty($config)) {
                throw new \Exception('Configuração não pode estar vazia');
            }

            if (is_string($config)) {
                $decodedConfig = json_decode($config, true);
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($decodedConfig)) {
                    throw new \Exception('Configuração inválida');
                }
                $config = $decodedConfig;
            }

            if ($this->dashboardModel->saveUserWidgetConfig($this->getDashboardUserId(), $config)) {
                $response['success'] = true;
                $response['message'] = 'Configuração salva com sucesso';
            } else {
                $response['error'] = 'Erro ao salvar configuração';
            }
        } catch (\Exception $e) {
            error_log("Dashboard saveWidgetConfig error: " . $e->getMessage());
            $response['error'] = $e->getMessage();
        }
        $this->response->setHeader('Content-Type', 'application/json');
        return $this->response->setJSON($response);
    }

    /**
     * Export analytics data
     */
    public function exportData()
    {
        checkPermission('admin_panel');

        $type = inputGet('type');
        $days = $this->dashboardModel->normalizeDays(inputGet('days'));
        $format = inputGet('format') ?: 'csv';

        try {
            [$data, $filename] = $this->resolveExportPayload($type, $days);
            if ($format === 'csv') {
                return $this->exportToCsv($data, $filename);
            }
            if ($format === 'json') {
                return $this->exportToJson($data, $filename);
            }

            throw new \Exception('Formato de exportação inválido');
        } catch (\Exception $e) {
            setErrorMessage('Erro ao exportar dados: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Export data to CSV
     */
    private function exportToCsv($data, $filename)
    {
        if (empty($data)) {
            throw new \Exception('Nenhum dado para exportar');
        }
        
        $csvData = [];
        $headers = array_keys((array)$data[0]);
        $csvData[] = $headers;
        
        foreach ($data as $row) {
            $csvData[] = array_values((array)$row);
        }
        
        $csv = '';
        foreach ($csvData as $row) {
            $csv .= implode(',', array_map(function($field) {
                return '"' . str_replace('"', '""', $field) . '"';
            }, $row)) . "\n";
        }
        
        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '_' . date('Y-m-d') . '.csv"')
            ->setBody($csv);
    }

    /**
     * Export data to JSON
     */
    private function exportToJson($data, $filename)
    {
        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '_' . date('Y-m-d') . '.json"')
            ->setBody(json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Widget customization page
     */
    public function widgets()
    {
        checkPermission('admin_panel');

        $data['title'] = 'Personalizar Dashboard';
        $data['panelSettings'] = (object) ['theme' => 'default'];
        $data['widgetConfig'] = $this->dashboardModel->getUserWidgetConfig($this->getDashboardUserId());
        $data['availableWidgets'] = $this->dashboardModel->getWidgetDefinitions();

        echo view('admin/includes/_header', $data);
        echo view('admin/dashboard/widgets', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Google Analytics 4 integration page
     */
    public function googleAnalytics()
    {
        checkPermission('admin_panel');

        $connection = $this->dashboardIntegrationModel->getGoogleAnalyticsStatus();
        $properties = [];

        if (!empty($connection['credentials_configured']) && !empty($connection['is_connected'])) {
            $propertyResult = $this->getGoogleAnalyticsPropertyResult();

            if (!empty($propertyResult['success'])) {
                $properties = $propertyResult['properties'] ?? [];
                $this->dashboardIntegrationModel->clearGoogleAnalyticsLastError();
            } elseif (!empty($propertyResult['error'])) {
                $this->dashboardIntegrationModel->setGoogleAnalyticsLastError($propertyResult['error']);
            }
        }

        $data = [
            'title' => 'Google Analytics 4',
            'panelSettings' => (object) ['theme' => 'default'],
            'ga4Connection' => $this->dashboardIntegrationModel->getGoogleAnalyticsStatus(),
            'ga4Properties' => $properties,
            'ga4RedirectUri' => $this->getGoogleAnalyticsRedirectUri(),
        ];

        echo view('admin/includes/_header', $data);
        echo view('admin/dashboard/google_analytics', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Save Google Analytics credentials
     */
    public function saveGoogleAnalyticsCredentials()
    {
        checkPermission('admin_panel');

        $current = $this->dashboardIntegrationModel->getGoogleAnalyticsConnection();
        $clientIdInput = trim((string) inputPost('ga4_client_id'));
        $clientSecretInput = trim((string) inputPost('ga4_client_secret'));

        $clientId = $clientIdInput !== '' ? $clientIdInput : ($current['client_id'] ?? '');
        $clientSecret = $clientSecretInput !== '' ? $clientSecretInput : ($current['client_secret'] ?? '');

        if ($clientId === '' || $clientSecret === '') {
            setErrorMessage('Informe o Client ID e o Client Secret do OAuth do Google.', false);
            return redirect()->to(adminUrl('dashboard/google-analytics'));
        }

        $credentialsChanged = $clientId !== ($current['client_id'] ?? '') || ($clientSecretInput !== '' && $clientSecret !== ($current['client_secret'] ?? ''));

        $this->dashboardIntegrationModel->saveGoogleAnalyticsCredentials($clientId, $clientSecret);

        if ($credentialsChanged) {
            $this->purgeGoogleAnalyticsCache($current);
            $this->dashboardIntegrationModel->clearGoogleAnalyticsConnection(false);
            setSuccessMessage('Credenciais salvas. Reconecte a conta do Google para gerar um novo refresh token.', false);
        } else {
            setSuccessMessage('Credenciais do Google Analytics atualizadas.', false);
        }

        return redirect()->to(adminUrl('dashboard/google-analytics'));
    }

    /**
     * Start Google Analytics OAuth flow
     */
    public function connectGoogleAnalytics()
    {
        checkPermission('admin_panel');

        $connection = $this->dashboardIntegrationModel->getGoogleAnalyticsConnection();
        if (empty($connection['client_id']) || empty($connection['client_secret'])) {
            setErrorMessage('Salve o Client ID e o Client Secret antes de conectar a conta Google.', false);
            return redirect()->to(adminUrl('dashboard/google-analytics'));
        }

        $state = generateToken();
        $this->session->set('dashboard_ga_oauth_state', $state);

        return redirect()->to($this->getGoogleAnalyticsClient()->getAuthorizationUrl($state));
    }

    /**
     * Google Analytics OAuth callback
     */
    public function googleAnalyticsCallback()
    {
        checkPermission('admin_panel');

        $expectedState = (string) $this->session->get('dashboard_ga_oauth_state');
        $receivedState = trim((string) inputGet('state'));
        $error = trim((string) inputGet('error'));

        if ($error !== '') {
            setErrorMessage('Google retornou um erro na autorização: ' . $error, false);
            return redirect()->to(adminUrl('dashboard/google-analytics'));
        }

        if ($expectedState === '' || $receivedState === '' || $expectedState !== $receivedState) {
            setErrorMessage('Estado OAuth inválido para a conexão com o Google Analytics.', false);
            return redirect()->to(adminUrl('dashboard/google-analytics'));
        }

        $this->session->remove('dashboard_ga_oauth_state');

        $code = trim((string) inputGet('code'));
        if ($code === '') {
            setErrorMessage('O Google não retornou o código de autorização esperado.', false);
            return redirect()->to(adminUrl('dashboard/google-analytics'));
        }

        $tokenResult = $this->getGoogleAnalyticsClient()->exchangeCodeForTokens($code);
        if (empty($tokenResult['success'])) {
            $this->dashboardIntegrationModel->setGoogleAnalyticsLastError($tokenResult['error'] ?? 'Falha ao concluir a autorização com o Google.');
            setErrorMessage($tokenResult['error'] ?? 'Falha ao concluir a autorização com o Google.', false);
            return redirect()->to(adminUrl('dashboard/google-analytics'));
        }

        $this->dashboardIntegrationModel->saveGoogleAnalyticsToken($tokenResult['token']);
        $this->dashboardIntegrationModel->clearGoogleAnalyticsLastError();
        $this->purgeGoogleAnalyticsCache($this->dashboardIntegrationModel->getGoogleAnalyticsConnection());

        $propertyResult = $this->getGoogleAnalyticsPropertyResult(true);

        if (!empty($propertyResult['success']) && count($propertyResult['properties'] ?? []) === 1) {
            $this->dashboardIntegrationModel->saveGoogleAnalyticsProperty($propertyResult['properties'][0]);
            setSuccessMessage('Conta Google conectada e propriedade GA4 selecionada automaticamente.', false);
        } else {
            setSuccessMessage('Conta Google conectada com sucesso. Agora selecione a propriedade do GA4.', false);
        }

        return redirect()->to(adminUrl('dashboard/google-analytics'));
    }

    /**
     * Save selected Google Analytics property
     */
    public function saveGoogleAnalyticsProperty()
    {
        checkPermission('admin_panel');

        $selectedPropertyId = trim((string) inputPost('property_id'));
        if ($selectedPropertyId === '') {
            setErrorMessage('Selecione uma propriedade do Google Analytics 4.', false);
            return redirect()->to(adminUrl('dashboard/google-analytics'));
        }

        $currentConnection = $this->dashboardIntegrationModel->getGoogleAnalyticsConnection();
        $propertyResult = $this->getGoogleAnalyticsPropertyResult(true);

        if (empty($propertyResult['success'])) {
            setErrorMessage($propertyResult['error'] ?? 'Não foi possível listar as propriedades disponíveis.', false);
            return redirect()->to(adminUrl('dashboard/google-analytics'));
        }

        $selectedProperty = null;
        foreach ($propertyResult['properties'] as $property) {
            if ((string) ($property['property_id'] ?? '') === $selectedPropertyId) {
                $selectedProperty = $property;
                break;
            }
        }

        if (!$selectedProperty) {
            setErrorMessage('A propriedade selecionada não está disponível para a conta conectada.', false);
            return redirect()->to(adminUrl('dashboard/google-analytics'));
        }

        $this->dashboardIntegrationModel->saveGoogleAnalyticsProperty($selectedProperty);
        $this->dashboardIntegrationModel->clearGoogleAnalyticsLastError();
        $this->purgeGoogleAnalyticsCache($currentConnection);
        $this->purgeGoogleAnalyticsCache($this->dashboardIntegrationModel->getGoogleAnalyticsConnection());
        setSuccessMessage('Propriedade do GA4 vinculada ao dashboard.', false);

        return redirect()->to(adminUrl('dashboard/google-analytics'));
    }

    /**
     * Disconnect Google Analytics integration
     */
    public function disconnectGoogleAnalytics()
    {
        checkPermission('admin_panel');

        $clearCredentials = inputPost('clear_credentials') == '1';
        $this->purgeGoogleAnalyticsCache($this->dashboardIntegrationModel->getGoogleAnalyticsConnection());
        $this->dashboardIntegrationModel->clearGoogleAnalyticsConnection($clearCredentials);

        if ($clearCredentials) {
            setSuccessMessage('Integração do Google Analytics e credenciais removidas.', false);
        } else {
            setSuccessMessage('Conexão com a conta Google removida. As credenciais OAuth foram mantidas.', false);
        }

        return redirect()->to(adminUrl('dashboard/google-analytics'));
    }

    /**
     * Real-time data endpoint for live updates
     */
    public function liveData()
    {
        checkPermission('admin_panel');

        try {
            $realTimeData = $this->dashboardModel->getRealTimeAnalytics();
            if (!$realTimeData || !is_array($realTimeData)) {
                $realTimeData = [
                    'current_visitors' => 0,
                    'views_last_hour' => 0,
                    'current_views' => 0,
                    'last_event_at' => null,
                    'top_pages_now' => []
                ];
            }

            $realTimeData = array_merge([
                'current_visitors' => 0,
                'views_last_hour' => 0,
                'current_views' => 0,
                'last_event_at' => null,
                'top_pages_now' => []
            ], $realTimeData);

            $data = [
                'timestamp' => time(),
                'realTime' => $realTimeData,
                'success' => true
            ];

            $ga4Realtime = $this->getGoogleAnalyticsRealtimePayload();
            if (!empty($ga4Realtime['success'])) {
                $data['googleAnalytics'] = $ga4Realtime['realtime'];
            }

            $this->response->setHeader('Content-Type', 'application/json; charset=utf-8');
            $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
            $this->response->setHeader('Pragma', 'no-cache');
            $this->response->setHeader('Expires', '0');
            
            return $this->response->setJSON($data);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            error_log("Dashboard liveData error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Return error in JSON format with proper structure
            $errorData = [
                'success' => false,
                'error' => true,
                'message' => 'Erro ao carregar dados em tempo real',
                'realTime' => [
                    'current_visitors' => 0,
                    'views_last_hour' => 0,
                    'current_views' => 0,
                    'last_event_at' => null,
                    'top_pages_now' => []
                ],
                'latestViews' => 0,
                'timestamp' => time()
            ];
            
            $this->response->setHeader('Content-Type', 'application/json; charset=utf-8');
            return $this->response->setJSON($errorData);
        }
    }

    private function buildDashboardViewData($days)
    {
        $ga4Payload = $this->getGoogleAnalyticsDashboardPayload($days);
        $internalOverview = $this->dashboardModel->getOverview($days);
        $internalDailyVisitors = $this->dashboardModel->getDailyVisitors($days);
        $internalTrafficSources = $this->dashboardModel->getTrafficSources($days);
        $internalDeviceAnalytics = $this->dashboardModel->getDeviceAnalytics($days);

        $analyticsSource = !empty($ga4Payload['snapshot']['success']) ? 'ga4' : 'internal';
        $primaryOverview = $this->buildPrimaryOverview($analyticsSource, $internalOverview, $ga4Payload['snapshot']);
        $primaryDailyVisitors = $this->buildPrimaryDailyVisitors($analyticsSource, $internalDailyVisitors, $ga4Payload['snapshot'], $days);
        $primaryTrafficSources = $this->buildPrimaryTrafficSources($analyticsSource, $internalTrafficSources, $ga4Payload['snapshot']);
        $primaryDeviceAnalytics = $this->buildPrimaryDeviceAnalytics($analyticsSource, $internalDeviceAnalytics, $ga4Payload['snapshot']);

        return [
            'days' => $days,
            'availableWindows' => $this->dashboardModel->getAllowedWindows(),
            'overview' => $primaryOverview,
            'overviewInternal' => $internalOverview,
            'analyticsSource' => $analyticsSource,
            'topPosts' => $this->dashboardModel->getTopPosts(10, $days),
            'engagedPosts' => $this->dashboardModel->getMostEngagedPosts(10, $days),
            'trafficSources' => $primaryTrafficSources,
            'categoryPerformance' => $this->dashboardModel->getCategoryPerformance($days),
            'userEngagement' => $this->dashboardModel->getUserEngagement($days),
            'contentSummary' => $this->dashboardModel->getContentSummary($days),
            'deviceAnalytics' => $primaryDeviceAnalytics,
            'realTime' => $this->dashboardModel->getRealTimeAnalytics(),
            'conversions' => $this->dashboardModel->getConversionMetrics($days),
            'dailyVisitors' => $primaryDailyVisitors,
            'visitorSegments' => $this->dashboardModel->getVisitorSegments($days),
            'retention' => $this->dashboardModel->getRetentionMetrics($days),
            'trackingAvailability' => $this->dashboardModel->getTrackingAvailability(),
            'widgetDefinitions' => $this->dashboardModel->getWidgetDefinitions(),
            'widgetConfig' => $this->dashboardModel->getUserWidgetConfig($this->getDashboardUserId()),
            'dashboardWidgets' => $this->dashboardModel->getEnabledWidgets($this->getDashboardUserId()),
            'ga4Connection' => $ga4Payload['connection'],
            'ga4Data' => $ga4Payload['snapshot'],
        ];
    }

    private function buildPrimaryOverview($source, array $internal, array $ga4Snapshot)
    {
        if ($source !== 'ga4') {
            return array_merge($internal, ['source' => 'internal']);
        }

        $ga4Overview = $ga4Snapshot['overview'] ?? [];
        $activeUsers = (int) ($ga4Overview['active_users'] ?? 0);
        $newUsers = (int) ($ga4Overview['new_users'] ?? 0);
        $sessions = (int) ($ga4Overview['sessions'] ?? 0);
        $pageViews = (int) ($ga4Overview['screen_page_views'] ?? 0);
        // GA4's `newVsReturning` dimension is the source of truth for returning users —
        // subtracting newUsers from activeUsers is wrong for long windows because almost
        // everyone is "new" when their first session lands inside the date range.
        $returningVisitors = (int) ($ga4Overview['returning_users'] ?? max(0, $activeUsers - $newUsers));
        $returningRate = $activeUsers > 0 ? round(($returningVisitors / $activeUsers) * 100, 2) : 0;
        $avgPagesPerVisitor = $activeUsers > 0 ? round($pageViews / $activeUsers, 2) : 0;

        return [
            'source' => 'ga4',
            'total_visitors' => $activeUsers,
            'total_pageviews' => $pageViews,
            'total_sessions' => $sessions,
            'avg_pages_per_visitor' => $avgPagesPerVisitor,
            'returning_rate' => $returningRate,
            'returning_visitors' => $returningVisitors,
            'engagement_rate_pct' => (float) ($ga4Overview['engagement_rate_pct'] ?? 0),
            'average_session_duration_sec' => (float) ($ga4Overview['average_session_duration_sec'] ?? 0),
            // Editorial-only metrics keep flowing from the internal counter.
            'active_posts' => (int) ($internal['active_posts'] ?? 0),
            'approved_comments' => (int) ($internal['approved_comments'] ?? 0),
        ];
    }

    private function buildPrimaryDailyVisitors($source, array $internal, array $ga4Snapshot, $days)
    {
        if ($source !== 'ga4') {
            return $internal;
        }

        $daily = $ga4Snapshot['daily'] ?? [];
        if (empty($daily)) {
            return $internal;
        }

        $byDate = [];
        foreach ($daily as $row) {
            $date = (string) ($row['date'] ?? '');
            if ($date === '') {
                continue;
            }
            $byDate[$date] = $row;
        }

        $series = [];
        for ($index = (int) $days - 1; $index >= 0; $index--) {
            $date = date('Y-m-d', strtotime('-' . $index . ' days'));
            $row = $byDate[$date] ?? [];
            $series[] = [
                'date' => $date,
                'label' => date('d/m', strtotime($date)),
                'visitors' => (int) ($row['active_users'] ?? 0),
                'pageviews' => (int) ($row['screen_page_views'] ?? 0),
                'active_posts' => 0,
            ];
        }

        return $series;
    }

    private function buildPrimaryTrafficSources($source, array $internal, array $ga4Snapshot)
    {
        if ($source !== 'ga4') {
            return array_merge($internal, ['source' => 'internal']);
        }

        $channels = $ga4Snapshot['channels'] ?? [];
        if (empty($channels)) {
            return array_merge($internal, ['source' => 'internal']);
        }

        $totalSessions = array_sum(array_map(static function ($row) {
            return (int) ($row['sessions'] ?? 0);
        }, $channels));

        $sources = array_map(static function ($row) use ($totalSessions) {
            $sessions = (int) ($row['sessions'] ?? 0);
            return [
                'source' => (string) ($row['channel'] ?? 'Não definido'),
                'visitors' => (int) ($row['active_users'] ?? 0),
                'pageviews' => $sessions,
                'percentage' => $totalSessions > 0 ? round(($sessions / $totalSessions) * 100, 2) : 0,
            ];
        }, $channels);

        return [
            'source' => 'ga4',
            'tracked' => true,
            'coverage_pct' => 100,
            'sources' => $sources,
        ];
    }

    private function buildPrimaryDeviceAnalytics($source, array $internal, array $ga4Snapshot)
    {
        if ($source !== 'ga4') {
            return array_merge($internal, ['source' => 'internal']);
        }

        $devices = $ga4Snapshot['devices'] ?? [];
        $browsers = $ga4Snapshot['browsers'] ?? [];
        if (empty($devices) && empty($browsers)) {
            return array_merge($internal, ['source' => 'internal']);
        }

        $deviceTotal = array_sum(array_map(static function ($row) {
            return (int) ($row['sessions'] ?? 0);
        }, $devices));
        $devicesPayload = array_map(static function ($row) use ($deviceTotal) {
            $count = (int) ($row['sessions'] ?? 0);
            return [
                'type' => (string) ($row['type'] ?? 'Não identificado'),
                'count' => $count,
                'percentage' => $deviceTotal > 0 ? round(($count / $deviceTotal) * 100, 2) : 0,
            ];
        }, $devices);

        $browserTotal = array_sum(array_map(static function ($row) {
            return (int) ($row['sessions'] ?? 0);
        }, $browsers));
        $browsersPayload = array_map(static function ($row) use ($browserTotal) {
            $count = (int) ($row['sessions'] ?? 0);
            return [
                'name' => (string) ($row['name'] ?? 'Não identificado'),
                'count' => $count,
                'percentage' => $browserTotal > 0 ? round(($count / $browserTotal) * 100, 2) : 0,
            ];
        }, $browsers);

        return [
            'source' => 'ga4',
            'tracked' => true,
            'coverage_pct' => 100,
            'devices' => $devicesPayload,
            'browsers' => $browsersPayload,
        ];
    }

    private function getDashboardUserId()
    {
        $currentUser = user();
        return $currentUser ? (int) $currentUser->id : 1;
    }

    private function resolveExportPayload($type, $days)
    {
        switch ($type) {
            case 'visitors':
                return [$this->dashboardModel->getDailyVisitors($days), 'visitantes_diarios'];
            case 'retention':
                return [$this->dashboardModel->getVisitorSegments($days), 'retencao_visitantes'];
            case 'top_posts':
                return [$this->dashboardModel->getTopPosts(50, $days), 'posts_mais_lidos'];
            case 'engagement':
                return [$this->dashboardModel->getMostEngagedPosts(50, $days), 'engajamento_editorial'];
            case 'categories':
                return [$this->dashboardModel->getCategoryPerformance($days), 'performance_categorias'];
            case 'traffic_sources':
                return [$this->dashboardModel->getTrafficSources($days)['sources'] ?? [], 'origem_trafego'];
            case 'devices':
            case 'device_analytics':
                return [$this->dashboardModel->getDeviceAnalytics($days)['devices'] ?? [], 'dispositivos'];
            case 'conversions':
                $convData = $this->dashboardModel->getConversionMetrics($days);
                $exportRows = [];
                foreach ($convData['daily_leads'] as $i => $row) {
                    $exportRows[] = [
                        'date' => $row['date'],
                        'leads' => $row['leads'],
                        'contacts' => $convData['daily_contacts'][$i]['contacts'] ?? 0,
                    ];
                }
                return [$exportRows, 'conversoes'];
            case 'lead_sources':
                $sourceData = $this->dashboardModel->getConversionMetrics($days);
                return [$sourceData['leads_by_source'] ?? [], 'leads_por_origem'];
            case 'ga4_channels':
                $ga4Payload = $this->getGoogleAnalyticsDashboardPayload($days);
                if (empty($ga4Payload['snapshot']['success'])) {
                    throw new \Exception($ga4Payload['snapshot']['error'] ?? 'Google Analytics 4 não está conectado.');
                }
                return [$ga4Payload['snapshot']['channels'] ?? [], 'ga4_canais'];
            case 'ga4_pages':
                $ga4Payload = $this->getGoogleAnalyticsDashboardPayload($days);
                if (empty($ga4Payload['snapshot']['success'])) {
                    throw new \Exception($ga4Payload['snapshot']['error'] ?? 'Google Analytics 4 não está conectado.');
                }
                return [$ga4Payload['snapshot']['pages'] ?? [], 'ga4_paginas'];
            default:
                throw new \Exception('Tipo de exportação inválido');
        }
    }

    private function getGoogleAnalyticsDashboardPayload($days)
    {
        $connection = $this->dashboardIntegrationModel->getGoogleAnalyticsStatus();
        $snapshot = ['success' => false];

        if (!$connection['is_ready']) {
            return [
                'connection' => $connection,
                'snapshot' => $snapshot,
            ];
        }

        $fullConnection = $this->dashboardIntegrationModel->getGoogleAnalyticsConnection();
        $cacheKey = $this->buildGoogleAnalyticsCacheKey('snapshot_' . (int) $days, $fullConnection);
        $cachedSnapshot = cache($cacheKey);
        if (is_array($cachedSnapshot) && !empty($cachedSnapshot['success'])) {
            return [
                'connection' => $connection,
                'snapshot' => $cachedSnapshot,
            ];
        }

        $client = $this->getGoogleAnalyticsClient();
        $snapshot = $client->getDashboardSnapshot($fullConnection, $days);
        $this->syncGoogleAnalyticsTokenState($snapshot);

        if (!empty($snapshot['success'])) {
            cache()->save($cacheKey, $this->stripGoogleAnalyticsTokenFromPayload($snapshot), self::GA4_SNAPSHOT_CACHE_TTL);
            $this->dashboardIntegrationModel->clearGoogleAnalyticsLastError();
        } elseif (!empty($snapshot['error'])) {
            $this->dashboardIntegrationModel->setGoogleAnalyticsLastError($snapshot['error']);
        }

        return [
            'connection' => $this->dashboardIntegrationModel->getGoogleAnalyticsStatus(),
            'snapshot' => $snapshot,
        ];
    }

    private function getGoogleAnalyticsRealtimePayload()
    {
        $connection = $this->dashboardIntegrationModel->getGoogleAnalyticsStatus();
        if (!$connection['is_ready']) {
            return ['success' => false];
        }

        $fullConnection = $this->dashboardIntegrationModel->getGoogleAnalyticsConnection();
        $cacheKey = $this->buildGoogleAnalyticsCacheKey('realtime', $fullConnection);
        $cachedPayload = cache($cacheKey);
        if (is_array($cachedPayload) && !empty($cachedPayload['success'])) {
            return $cachedPayload;
        }

        $payload = $this->getGoogleAnalyticsClient()->getRealtimeSummary($fullConnection);
        $this->syncGoogleAnalyticsTokenState($payload);

        if (!empty($payload['success'])) {
            cache()->save($cacheKey, $this->stripGoogleAnalyticsTokenFromPayload($payload), self::GA4_REALTIME_CACHE_TTL);
            $this->dashboardIntegrationModel->clearGoogleAnalyticsLastError();
        } elseif (!empty($payload['error'])) {
            $this->dashboardIntegrationModel->setGoogleAnalyticsLastError($payload['error']);
        }

        return $payload;
    }

    private function syncGoogleAnalyticsTokenState(array $payload)
    {
        if (!empty($payload['token']) && is_array($payload['token'])) {
            $this->dashboardIntegrationModel->saveGoogleAnalyticsToken($payload['token']);
        }
    }

    private function getGoogleAnalyticsClient()
    {
        $connection = $this->dashboardIntegrationModel->getGoogleAnalyticsConnection();

        return new GoogleAnalyticsDashboardClient([
            'client_id' => $connection['client_id'] ?? '',
            'client_secret' => $connection['client_secret'] ?? '',
        ], $this->getGoogleAnalyticsRedirectUri());
    }

    private function getGoogleAnalyticsRedirectUri()
    {
        return adminUrl('dashboard/google-analytics/callback');
    }

    private function getGoogleAnalyticsPropertyResult($forceRefresh = false)
    {
        $connection = $this->dashboardIntegrationModel->getGoogleAnalyticsConnection();
        $cacheKey = $this->buildGoogleAnalyticsCacheKey('properties', $connection);

        if (!$forceRefresh) {
            $cachedResult = cache($cacheKey);
            if (is_array($cachedResult) && !empty($cachedResult['success'])) {
                return $cachedResult;
            }
        }

        $propertyResult = $this->getGoogleAnalyticsClient()->listAccessibleProperties($connection);
        $this->syncGoogleAnalyticsTokenState($propertyResult);

        if (!empty($propertyResult['success'])) {
            cache()->save($cacheKey, $this->stripGoogleAnalyticsTokenFromPayload($propertyResult), self::GA4_PROPERTIES_CACHE_TTL);
        }

        return $propertyResult;
    }

    private function purgeGoogleAnalyticsCache(array $connection)
    {
        if (empty($connection)) {
            return;
        }

        cache()->delete($this->buildGoogleAnalyticsCacheKey('properties', $connection));
        cache()->delete($this->buildGoogleAnalyticsCacheKey('realtime', $connection));

        foreach ($this->dashboardModel->getAllowedWindows() as $days) {
            cache()->delete($this->buildGoogleAnalyticsCacheKey('snapshot_' . (int) $days, $connection));
        }
    }

    private function buildGoogleAnalyticsCacheKey($segment, array $connection)
    {
        $fingerprint = implode('|', [
            (string) ($connection['client_id'] ?? ''),
            (string) ($connection['connected_email'] ?? ''),
            (string) ($connection['property_id'] ?? ''),
            (string) ($connection['refresh_token'] ?? ''),
        ]);

        return 'dashboard_ga4_' . $segment . '_' . sha1($fingerprint);
    }

    private function stripGoogleAnalyticsTokenFromPayload(array $payload)
    {
        unset($payload['token']);
        return $payload;
    }
}
