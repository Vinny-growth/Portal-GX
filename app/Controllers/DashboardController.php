<?php

namespace App\Controllers;

use App\Models\DashboardModel;

class DashboardController extends BaseAdminController
{
    protected $dashboardModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->dashboardModel = new DashboardModel();
    }

    /**
     * Main dashboard page
     */
    public function index()
    {
        checkPermission('admin_panel');
        
        $data['title'] = 'Dashboard de Analytics';
        $data['panelSettings'] = (object)['theme' => 'default'];
        
        // Get days filter from request
        $days = inputGet('days') ?: 30;
        
        // Get real analytics data from database
        $data['totalVisitors'] = $this->dashboardModel->getTotalVisitors($days);
        $data['totalPageViews'] = $this->dashboardModel->getTotalPageViews($days);
        $data['topPosts'] = $this->dashboardModel->getTopPosts(10, $days);
        $data['engagedPosts'] = $this->dashboardModel->getMostEngagedPosts(10, $days);
        $data['trafficSources'] = $this->dashboardModel->getTrafficSources($days);
        $data['categoryPerformance'] = $this->dashboardModel->getCategoryPerformance($days);
        $data['userEngagement'] = $this->dashboardModel->getUserEngagement($days);
        $data['contentSummary'] = $this->dashboardModel->getContentSummary($days);
        $data['deviceAnalytics'] = $this->dashboardModel->getDeviceAnalytics($days);
        $data['realTime'] = $this->dashboardModel->getRealTimeAnalytics();
        $data['dailyVisitors'] = $this->dashboardModel->getDailyVisitors($days);
        $data['days'] = $days;

        echo view('admin/includes/_header', $data);
        echo view('admin/dashboard/index', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Get analytics data via AJAX
     */
    public function getAnalyticsData()
    {
        checkPermission('admin_panel');
        
        $type = inputPost('type');
        $days = inputPost('days') ?: 30;
        
        $response = ['success' => false, 'data' => []];
        
        try {
            switch ($type) {
                case 'visitors':
                    $response['data'] = [
                        'total' => $this->dashboardModel->getTotalVisitors($days),
                        'daily' => $this->dashboardModel->getDailyVisitors($days)
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
                    $response['data'] = $this->dashboardModel->getDeviceAnalytics($days);
                    break;
                    
                case 'real_time':
                    $response['data'] = $this->dashboardModel->getRealTimeAnalytics();
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
            // Validate config input
            if (empty($config)) {
                throw new \Exception('Configuração não pode estar vazia');
            }
            
            $currentUser = user();
            $userId = $currentUser ? $currentUser->id : 1; // Fallback to admin user
            
            if ($this->dashboardModel->saveUserWidgetConfig($userId, $config)) {
                $response['success'] = true;
                $response['message'] = 'Configuração salva com sucesso';
            } else {
                $response['error'] = 'Erro ao salvar configuração';
            }
        } catch (\Exception $e) {
            error_log("Dashboard saveWidgetConfig error: " . $e->getMessage());
            $response['error'] = $e->getMessage();
        }
        
        // Set JSON response headers
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
        $days = inputGet('days') ?: 30;
        $format = inputGet('format') ?: 'csv';
        
        try {
            $data = [];
            $filename = '';
            
            switch ($type) {
                case 'visitors':
                    $data = $this->dashboardModel->getDailyVisitors($days);
                    $filename = 'visitantes_diarios';
                    break;
                    
                case 'top_posts':
                    $data = $this->dashboardModel->getTopPosts(50, $days);
                    $filename = 'posts_mais_lidos';
                    break;
                    
                case 'engagement':
                    $data = $this->dashboardModel->getMostEngagedPosts(50, $days);
                    $filename = 'posts_mais_engajados';
                    break;
                    
                case 'categories':
                    $data = $this->dashboardModel->getCategoryPerformance($days);
                    $filename = 'performance_categorias';
                    break;
                    
                default:
                    throw new \Exception('Tipo de exportação inválido');
            }
            
            if ($format === 'csv') {
                return $this->exportToCsv($data, $filename);
            } elseif ($format === 'json') {
                return $this->exportToJson($data, $filename);
            }
            
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
        $data['panelSettings'] = (object)['theme' => 'default'];
        $currentUser = user();
        $userId = $currentUser ? $currentUser->id : 1; // Fallback to admin user
        $data['widgetConfig'] = $this->dashboardModel->getUserWidgetConfig($userId);
        
        // Available widgets
        $data['availableWidgets'] = [
            'visitors_chart' => [
                'name' => 'Gráfico de Visitantes',
                'description' => 'Mostra o número de visitantes únicos ao longo do tempo'
            ],
            'top_posts' => [
                'name' => 'Posts Mais Lidos',
                'description' => 'Lista dos artigos com maior número de visualizações'
            ],
            'engagement' => [
                'name' => 'Posts com Maior Engajamento',
                'description' => 'Artigos com mais comentários e reações'
            ],
            'traffic_sources' => [
                'name' => 'Fontes de Tráfego',
                'description' => 'De onde vêm os visitantes do site'
            ],
            'category_performance' => [
                'name' => 'Performance por Categoria',
                'description' => 'Desempenho das diferentes categorias de conteúdo'
            ],
            'real_time' => [
                'name' => 'Analytics em Tempo Real',
                'description' => 'Dados de visitação das últimas 24 horas'
            ],
            'user_stats' => [
                'name' => 'Estatísticas de Usuários',
                'description' => 'Informações sobre registro e engajamento de usuários'
            ],
            'content_summary' => [
                'name' => 'Resumo de Conteúdo',
                'description' => 'Estatísticas gerais sobre publicações'
            ],
            'device_analytics' => [
                'name' => 'Analytics de Dispositivos',
                'description' => 'Tipos de dispositivos e navegadores dos visitantes'
            ]
        ];

        echo view('admin/includes/_header', $data);
        echo view('admin/dashboard/widgets', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Real-time data endpoint for live updates
     */
    public function liveData()
    {
        checkPermission('admin_panel');
        
        try {
            // Get real-time analytics data
            $realTimeData = $this->dashboardModel->getRealTimeAnalytics();
            $latestViews = $this->dashboardModel->getTotalPageViews(1);
            
            // Ensure data is properly formatted and not null
            if (!$realTimeData || !is_array($realTimeData)) {
                $realTimeData = [
                    'current_visitors' => 0,
                    'current_views' => 0,
                    'top_pages_now' => []
                ];
            }
            
            // Ensure required keys exist
            $realTimeData = array_merge([
                'current_visitors' => 0,
                'current_views' => 0,
                'top_pages_now' => []
            ], $realTimeData);
            
            $data = [
                'timestamp' => time(),
                'realTime' => $realTimeData,
                'latestViews' => $latestViews ?: 0,
                'success' => true
            ];
            
            // Set JSON response headers and ensure proper encoding
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
                'error' => true,
                'message' => 'Erro ao carregar dados em tempo real',
                'realTime' => [
                    'current_visitors' => 0,
                    'current_views' => 0,
                    'top_pages_now' => []
                ],
                'latestViews' => 0,
                'timestamp' => time()
            ];
            
            $this->response->setHeader('Content-Type', 'application/json; charset=utf-8');
            return $this->response->setJSON($errorData);
        }
    }
}