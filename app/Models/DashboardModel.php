<?php namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends BaseModel
{
    protected $table;
    protected $primaryKey;
    protected $allowedFields;

    public function __construct()
    {
        parent::__construct();
    }

    // Analytics e estatísticas
    
    /**
     * Get total visitors count
     */
    public function getTotalVisitors($days = 30)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->db->table('post_pageviews_month')
            ->select('COUNT(DISTINCT ip_address) as total')
            ->where('created_at >=', $startDate)
            ->get()
            ->getRow()
            ->total ?? 0;
    }

    /**
     * Get total page views
     */
    public function getTotalPageViews($days = 30)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->db->table('post_pageviews_month')
            ->where('created_at >=', $startDate)
            ->countAllResults();
    }

    /**
     * Get daily visitors for chart
     */
    public function getDailyVisitors($days = 30)
    {
        $startDate = date('Y-m-d', strtotime("-{$days} days"));
        
        $query = $this->db->query("
            SELECT 
                DATE(created_at) as date,
                COUNT(DISTINCT ip_address) as visitors,
                COUNT(*) as pageviews
            FROM post_pageviews_month 
            WHERE DATE(created_at) >= ? 
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ", [$startDate]);
        
        return $query->getResult();
    }

    /**
     * Get top performing posts
     */
    public function getTopPosts($limit = 10, $days = 30)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $query = $this->db->query("
            SELECT 
                p.id,
                p.title,
                p.slug,
                COUNT(pv.id) as pageviews,
                COUNT(DISTINCT pv.ip_address) as unique_visitors,
                c.name as category_name
            FROM posts p
            LEFT JOIN post_pageviews_month pv ON p.id = pv.post_id AND pv.created_at >= ?
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 1 AND p.visibility = 1
            GROUP BY p.id
            ORDER BY pageviews DESC
            LIMIT ?
        ", [$startDate, $limit]);
        
        return $query->getResult();
    }

    /**
     * Get posts with most engagement (comments + reactions)
     */
    public function getMostEngagedPosts($limit = 10, $days = 30)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $query = $this->db->query("
            SELECT 
                p.id,
                p.title,
                p.slug,
                p.comment_count,
                COALESCE(r.total_reactions, 0) as reaction_count,
                (p.comment_count + COALESCE(r.total_reactions, 0)) as total_engagement,
                c.name as category_name
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN (
                SELECT 
                    post_id, 
                    (re_like + re_dislike + re_love + re_funny + re_angry + re_sad + re_wow) as total_reactions
                FROM reactions 
            ) r ON p.id = r.post_id
            WHERE p.status = 1 AND p.visibility = 1 AND p.created_at >= ?
            ORDER BY total_engagement DESC
            LIMIT ?
        ", [$startDate, $limit]);
        
        return $query->getResult();
    }

    /**
     * Get traffic sources (referrer data)
     */
    public function getTrafficSources($days = 30)
    {
        // Simulação de dados de tráfego - em produção seria integrado com Google Analytics
        return [
            ['source' => 'Direto', 'visitors' => rand(1000, 5000), 'percentage' => rand(30, 50)],
            ['source' => 'Google', 'visitors' => rand(500, 2000), 'percentage' => rand(20, 35)],
            ['source' => 'Facebook', 'visitors' => rand(100, 800), 'percentage' => rand(5, 15)],
            ['source' => 'Twitter', 'visitors' => rand(50, 400), 'percentage' => rand(2, 10)],
            ['source' => 'LinkedIn', 'visitors' => rand(20, 200), 'percentage' => rand(1, 5)],
            ['source' => 'Outros', 'visitors' => rand(10, 100), 'percentage' => rand(1, 5)]
        ];
    }

    /**
     * Get category performance
     */
    public function getCategoryPerformance($days = 30)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $query = $this->db->query("
            SELECT 
                c.id,
                c.name,
                COUNT(DISTINCT p.id) as post_count,
                COALESCE(SUM(p.pageviews), 0) as total_views,
                COALESCE(SUM(p.comment_count), 0) as total_comments
            FROM categories c
            LEFT JOIN posts p ON c.id = p.category_id AND p.created_at >= ? AND p.status = 1
            GROUP BY c.id, c.name
            ORDER BY total_views DESC
        ", [$startDate]);
        
        return $query->getResult();
    }

    /**
     * Get user engagement metrics
     */
    public function getUserEngagement($days = 30)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $totalUsers = $this->db->table('users')->countAllResults();
        $activeUsers = $this->db->table('users')
            ->where('last_seen >=', $startDate)
            ->countAllResults();
        
        $newUsers = $this->db->table('users')
            ->where('created_at >=', $startDate)
            ->countAllResults();
        
        $comments = $this->db->table('comments')
            ->where('created_at >=', $startDate)
            ->countAllResults();
        
        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'new_users' => $newUsers,
            'total_comments' => $comments,
            'engagement_rate' => $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 2) : 0
        ];
    }

    /**
     * Get content performance summary
     */
    public function getContentSummary($days = 30)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $totalPosts = $this->db->table('posts')
            ->where('status', 1)
            ->where('visibility', 1)
            ->countAllResults();
        
        $newPosts = $this->db->table('posts')
            ->where('created_at >=', $startDate)
            ->where('status', 1)
            ->countAllResults();
        
        $avgViews = $this->db->query("
            SELECT AVG(pageviews) as avg_views 
            FROM posts 
            WHERE status = 1 AND visibility = 1 AND created_at >= ?
        ", [$startDate])->getRow()->avg_views ?? 0;
        
        $totalComments = $this->db->table('comments')
            ->where('created_at >=', $startDate)
            ->countAllResults();
        
        return [
            'total_posts' => $totalPosts,
            'new_posts' => $newPosts,
            'avg_views' => round($avgViews, 0),
            'total_comments' => $totalComments
        ];
    }

    /**
     * Get device and browser analytics
     */
    public function getDeviceAnalytics($days = 30)
    {
        // Simulação de dados de dispositivos - em produção seria integrado com analytics reais
        return [
            'devices' => [
                ['type' => 'Desktop', 'count' => rand(1000, 3000), 'percentage' => rand(40, 60)],
                ['type' => 'Mobile', 'count' => rand(800, 2500), 'percentage' => rand(35, 50)],
                ['type' => 'Tablet', 'count' => rand(100, 500), 'percentage' => rand(5, 15)]
            ],
            'browsers' => [
                ['name' => 'Chrome', 'count' => rand(1500, 4000), 'percentage' => rand(50, 70)],
                ['name' => 'Firefox', 'count' => rand(300, 800), 'percentage' => rand(10, 20)],
                ['name' => 'Safari', 'count' => rand(200, 600), 'percentage' => rand(8, 15)],
                ['name' => 'Edge', 'count' => rand(100, 400), 'percentage' => rand(5, 12)],
                ['name' => 'Outros', 'count' => rand(50, 200), 'percentage' => rand(2, 8)]
            ]
        ];
    }

    /**
     * Get real-time analytics (last 24 hours)
     */
    public function getRealTimeAnalytics()
    {
        $last24h = date('Y-m-d H:i:s', strtotime('-24 hours'));
        
        $currentVisitors = $this->db->table('post_pageviews_month')
            ->select('COUNT(DISTINCT ip_address) as visitors')
            ->where('created_at >=', $last24h)
            ->get()
            ->getRow()
            ->visitors ?? 0;
        
        $currentViews = $this->db->table('post_pageviews_month')
            ->where('created_at >=', $last24h)
            ->countAllResults();
        
        $topPagesNow = $this->db->query("
            SELECT 
                p.title,
                p.slug,
                COUNT(*) as current_views
            FROM post_pageviews_month pv
            JOIN posts p ON pv.post_id = p.id
            WHERE pv.created_at >= ?
            GROUP BY p.id
            ORDER BY current_views DESC
            LIMIT 5
        ", [$last24h])->getResult();
        
        return [
            'current_visitors' => $currentVisitors,
            'current_views' => $currentViews,
            'top_pages_now' => $topPagesNow
        ];
    }

    /**
     * Get widget configuration for user
     */
    public function getUserWidgetConfig($userId)
    {
        $config = $this->db->table('dashboard_widgets')
            ->where('user_id', $userId)
            ->get()
            ->getRow();
        
        if (!$config) {
            // Default configuration
            return [
                'widgets' => json_encode([
                    'visitors_chart' => ['enabled' => true, 'position' => 1],
                    'top_posts' => ['enabled' => true, 'position' => 2],
                    'engagement' => ['enabled' => true, 'position' => 3],
                    'traffic_sources' => ['enabled' => true, 'position' => 4],
                    'category_performance' => ['enabled' => true, 'position' => 5],
                    'real_time' => ['enabled' => true, 'position' => 6]
                ])
            ];
        }
        
        return ['widgets' => $config->widget_config];
    }

    /**
     * Save widget configuration for user
     */
    public function saveUserWidgetConfig($userId, $config)
    {
        $existing = $this->db->table('dashboard_widgets')
            ->where('user_id', $userId)
            ->get()
            ->getRow();
        
        $data = [
            'user_id' => $userId,
            'widget_config' => json_encode($config),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($existing) {
            return $this->db->table('dashboard_widgets')
                ->where('user_id', $userId)
                ->update($data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->table('dashboard_widgets')
                ->insert($data);
        }
    }

    /**
     * Create dashboard_widgets table if it doesn't exist
     */
    public function createDashboardWidgetsTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS `dashboard_widgets` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `widget_config` text,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        
        return $this->db->query($query);
    }
}