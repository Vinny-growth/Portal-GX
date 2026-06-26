<?php namespace App\Models;

class DashboardModel extends BaseModel
{
    private const ALLOWED_WINDOWS = [7, 30, 90, 365];
    private const LIVE_VISITOR_WINDOW_MINUTES = 30;

    protected $hasDetailedPageviewColumns = null;

    public function normalizeDays($days)
    {
        $days = (int) $days;
        if (!in_array($days, self::ALLOWED_WINDOWS, true)) {
            return 30;
        }

        return $days;
    }

    public function getAllowedWindows()
    {
        return self::ALLOWED_WINDOWS;
    }

    public function getWidgetDefinitions()
    {
        return [
            'visitors_chart' => [
                'name' => 'Fluxo de audiência',
                'description' => 'Visitantes únicos, pageviews e volume diário de consumo.',
                'column_class' => 'col-lg-8 col-md-12',
                'icon' => 'fa-line-chart',
            ],
            'retention' => [
                'name' => 'Retenção e recorrência',
                'description' => 'Novos vs recorrentes, stickiness e profundidade de retorno.',
                'column_class' => 'col-lg-4 col-md-12',
                'icon' => 'fa-repeat',
            ],
            'top_posts' => [
                'name' => 'Posts mais lidos',
                'description' => 'Conteúdos líderes em views e visitantes únicos no período.',
                'column_class' => 'col-lg-6 col-md-12',
                'icon' => 'fa-file-text-o',
            ],
            'engagement' => [
                'name' => 'Engajamento editorial',
                'description' => 'Comentários recentes e reações acumuladas nos posts ativos.',
                'column_class' => 'col-lg-6 col-md-12',
                'icon' => 'fa-comments-o',
            ],
            'traffic_sources' => [
                'name' => 'Origem de tráfego',
                'description' => 'Distribuição de visitas por canal rastreado.',
                'column_class' => 'col-lg-6 col-md-12',
                'icon' => 'fa-compass',
            ],
            'category_performance' => [
                'name' => 'Performance por categoria',
                'description' => 'Audiência e atividade editorial por taxonomia.',
                'column_class' => 'col-lg-6 col-md-12',
                'icon' => 'fa-folder-open-o',
            ],
            'user_stats' => [
                'name' => 'Base de usuários',
                'description' => 'Atividade, crescimento e participação da comunidade.',
                'column_class' => 'col-lg-6 col-md-12',
                'icon' => 'fa-users',
            ],
            'content_summary' => [
                'name' => 'Saúde do conteúdo',
                'description' => 'Publicação, cobertura e produtividade do acervo.',
                'column_class' => 'col-lg-6 col-md-12',
                'icon' => 'fa-bar-chart',
            ],
            'device_analytics' => [
                'name' => 'Tecnologia do tráfego',
                'description' => 'Dispositivos e navegadores capturados no tracking.',
                'column_class' => 'col-lg-6 col-md-12',
                'icon' => 'fa-desktop',
            ],
            'real_time' => [
                'name' => 'Pulso em tempo quase real',
                'description' => 'IPs ativos nos últimos 30 minutos e páginas quentes.',
                'column_class' => 'col-lg-6 col-md-12',
                'icon' => 'fa-bolt',
            ],
            'conversions' => [
                'name' => 'Conversões e leads',
                'description' => 'Leads de simuladores, contatos recebidos e funil de conversão no período.',
                'column_class' => 'col-lg-12 col-md-12',
                'icon' => 'fa-bullseye',
            ],
        ];
    }

    public function getDefaultWidgetConfig()
    {
        $config = [];
        $position = 1;

        foreach ($this->getWidgetDefinitions() as $key => $widget) {
            $config[$key] = [
                'enabled' => true,
                'position' => $position++,
            ];
        }

        return $config;
    }

    public function normalizeWidgetConfig($config)
    {
        if (is_string($config)) {
            $decoded = json_decode($config, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $config = $decoded;
                if (is_string($config)) {
                    $decodedNested = json_decode($config, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $config = $decodedNested;
                    }
                }
            }
        }

        if (!is_array($config)) {
            $config = [];
        }

        $definitions = $this->getWidgetDefinitions();
        $defaults = $this->getDefaultWidgetConfig();
        $normalized = [];

        foreach ($definitions as $key => $widget) {
            $current = $config[$key] ?? [];
            $normalized[$key] = [
                'enabled' => isset($current['enabled']) ? (bool) $current['enabled'] : $defaults[$key]['enabled'],
                'position' => isset($current['position']) ? max(1, (int) $current['position']) : $defaults[$key]['position'],
            ];
        }

        uasort($normalized, static function ($a, $b) {
            return $a['position'] <=> $b['position'];
        });

        $position = 1;
        foreach ($normalized as $key => $value) {
            $normalized[$key]['position'] = $position++;
        }

        return $normalized;
    }

    public function getUserWidgetConfig($userId)
    {
        $config = $this->db->table('dashboard_widgets')
            ->where('user_id', (int) $userId)
            ->orderBy('updated_at DESC')
            ->get()
            ->getRow();

        return $this->normalizeWidgetConfig($config->widget_config ?? null);
    }

    public function getEnabledWidgets($userId)
    {
        $definitions = $this->getWidgetDefinitions();
        $config = $this->getUserWidgetConfig($userId);
        $widgets = [];

        foreach ($config as $key => $settings) {
            if (!isset($definitions[$key]) || empty($settings['enabled'])) {
                continue;
            }

            $widgets[] = array_merge($definitions[$key], [
                'key' => $key,
                'position' => $settings['position'],
            ]);
        }

        usort($widgets, static function ($a, $b) {
            return $a['position'] <=> $b['position'];
        });

        return $widgets;
    }

    public function saveUserWidgetConfig($userId, $config)
    {
        $normalizedConfig = $this->normalizeWidgetConfig($config);
        $existing = $this->db->table('dashboard_widgets')
            ->where('user_id', (int) $userId)
            ->get()
            ->getRow();

        $data = [
            'user_id' => (int) $userId,
            'widget_config' => json_encode($normalizedConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            return $this->db->table('dashboard_widgets')
                ->where('user_id', (int) $userId)
                ->update($data);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->table('dashboard_widgets')->insert($data);
    }

    public function getTrackingAvailability()
    {
        $row = $this->db->table('post_pageviews_month')
            ->select('MIN(created_at) as first_seen_at, MAX(created_at) as last_seen_at, COUNT(*) as total_rows')
            ->get()
            ->getRow();

        $firstSeenAt = $row->first_seen_at ?? null;
        $lastSeenAt = $row->last_seen_at ?? null;
        $availableDays = 0;

        if (!empty($firstSeenAt)) {
            $availableDays = max(1, (int) floor((time() - strtotime($firstSeenAt)) / 86400) + 1);
        }

        $dimensionStartedAt = null;
        if ($this->hasDetailedPageviewColumns()) {
            $dimensionRow = $this->db->query("
                SELECT MIN(created_at) AS first_dimension_at
                FROM post_pageviews_month
                WHERE source_group IS NOT NULL
                   OR browser_name IS NOT NULL
                   OR device_type IS NOT NULL
            ")->getRow();
            $dimensionStartedAt = $dimensionRow->first_dimension_at ?? null;
        }

        return [
            'first_seen_at' => $firstSeenAt,
            'last_seen_at' => $lastSeenAt,
            'available_days' => $availableDays,
            'total_rows' => (int) ($row->total_rows ?? 0),
            'detailed_dimensions_available' => $this->hasDetailedPageviewColumns(),
            'dimension_started_at' => $dimensionStartedAt,
        ];
    }

    public function getOverview($days = 30)
    {
        $days = $this->normalizeDays($days);
        $period = $this->getPeriodBounds($days);
        $totalVisitors = $this->getTotalVisitors($days);
        $totalPageViews = $this->getTotalPageViews($days);
        $retention = $this->getRetentionMetrics($days);
        $activePosts = (int) ($this->db->query("
            SELECT COUNT(DISTINCT post_id) AS total
            FROM post_pageviews_month
            WHERE created_at >= ?
        ", [$period['start_datetime']])->getRow()->total ?? 0);
        $approvedComments = (int) $this->db->table('comments')
            ->where('status', 1)
            ->where('created_at >=', $period['start_datetime'])
            ->countAllResults();

        return [
            'total_visitors' => (int) $totalVisitors,
            'total_pageviews' => (int) $totalPageViews,
            'avg_pages_per_visitor' => $totalVisitors > 0 ? round($totalPageViews / $totalVisitors, 2) : 0,
            'returning_rate' => $retention['returning_rate'],
            'returning_visitors' => $retention['returning_visitors'],
            'active_posts' => $activePosts,
            'approved_comments' => $approvedComments,
        ];
    }

    public function getTotalVisitors($days = 30)
    {
        return (int) ($this->db->table('post_pageviews_month')
            ->select('COUNT(DISTINCT ip_address) as total')
            ->where('created_at >=', $this->getPeriodBounds($days)['start_datetime'])
            ->get()
            ->getRow()
            ->total ?? 0);
    }

    public function getTotalPageViews($days = 30)
    {
        return (int) $this->db->table('post_pageviews_month')
            ->where('created_at >=', $this->getPeriodBounds($days)['start_datetime'])
            ->countAllResults();
    }

    public function getDailyVisitors($days = 30)
    {
        $period = $this->getPeriodBounds($days);
        $rows = $this->db->query("
            SELECT
                DATE(created_at) AS event_date,
                COUNT(DISTINCT ip_address) AS visitors,
                COUNT(*) AS pageviews,
                COUNT(DISTINCT post_id) AS active_posts
            FROM post_pageviews_month
            WHERE created_at >= ?
            GROUP BY DATE(created_at)
            ORDER BY event_date ASC
        ", [$period['start_datetime']])->getResultArray();

        return $this->fillDateSeries($days, $rows, [
            'visitors' => 0,
            'pageviews' => 0,
            'active_posts' => 0,
        ]);
    }

    public function getVisitorSegments($days = 30)
    {
        $period = $this->getPeriodBounds($days);
        $rows = $this->db->query("
            SELECT
                DATE(pv.created_at) AS event_date,
                COUNT(*) AS pageviews,
                COUNT(DISTINCT pv.ip_address) AS visitors,
                COUNT(DISTINCT CASE WHEN first_touch.first_seen_date < DATE(pv.created_at) THEN pv.ip_address END) AS returning_visitors,
                COUNT(DISTINCT CASE WHEN first_touch.first_seen_date = DATE(pv.created_at) THEN pv.ip_address END) AS new_visitors
            FROM post_pageviews_month pv
            INNER JOIN (
                SELECT ip_address, MIN(DATE(created_at)) AS first_seen_date
                FROM post_pageviews_month
                GROUP BY ip_address
            ) first_touch ON first_touch.ip_address = pv.ip_address
            WHERE pv.created_at >= ?
            GROUP BY DATE(pv.created_at)
            ORDER BY event_date ASC
        ", [$period['start_datetime']])->getResultArray();

        return $this->fillDateSeries($days, $rows, [
            'visitors' => 0,
            'pageviews' => 0,
            'returning_visitors' => 0,
            'new_visitors' => 0,
        ]);
    }

    public function getRetentionMetrics($days = 30)
    {
        $period = $this->getPeriodBounds($days);
        $dailySeries = $this->getDailyVisitors($days);
        $totalVisitors = $this->getTotalVisitors($days);

        $returningVisitors = (int) ($this->db->query("
            SELECT COUNT(DISTINCT current_period.ip_address) AS total
            FROM post_pageviews_month current_period
            INNER JOIN (
                SELECT ip_address, MIN(created_at) AS first_seen_at
                FROM post_pageviews_month
                GROUP BY ip_address
            ) history ON history.ip_address = current_period.ip_address
            WHERE current_period.created_at >= ?
              AND history.first_seen_at < ?
        ", [$period['start_datetime'], $period['start_datetime']])->getRow()->total ?? 0);

        $repeatVisitors = (int) ($this->db->query("
            SELECT COUNT(*) AS total
            FROM (
                SELECT ip_address
                FROM post_pageviews_month
                WHERE created_at >= ?
                GROUP BY ip_address
                HAVING COUNT(DISTINCT DATE(created_at)) >= 2
            ) repeated_visitors
        ", [$period['start_datetime']])->getRow()->total ?? 0);

        $loyalVisitors = (int) ($this->db->query("
            SELECT COUNT(*) AS total
            FROM (
                SELECT ip_address
                FROM post_pageviews_month
                WHERE created_at >= ?
                GROUP BY ip_address
                HAVING COUNT(DISTINCT DATE(created_at)) >= 3
            ) loyal_visitors
        ", [$period['start_datetime']])->getRow()->total ?? 0);

        $avgDailyVisitors = 0;
        if (!empty($dailySeries)) {
            $avgDailyVisitors = array_sum(array_column($dailySeries, 'visitors')) / count($dailySeries);
        }

        return [
            'total_visitors' => $totalVisitors,
            'returning_visitors' => $returningVisitors,
            'new_visitors' => max(0, $totalVisitors - $returningVisitors),
            'repeat_visitors' => $repeatVisitors,
            'loyal_visitors' => $loyalVisitors,
            'returning_rate' => $totalVisitors > 0 ? round(($returningVisitors / $totalVisitors) * 100, 2) : 0,
            'repeat_rate' => $totalVisitors > 0 ? round(($repeatVisitors / $totalVisitors) * 100, 2) : 0,
            'stickiness' => $totalVisitors > 0 ? round(($avgDailyVisitors / $totalVisitors) * 100, 2) : 0,
        ];
    }

    public function getTopPosts($limit = 10, $days = 30)
    {
        $limit = max(1, (int) $limit);
        $period = $this->getPeriodBounds($days);

        return $this->db->query("
            SELECT
                p.id,
                p.title,
                p.slug,
                COUNT(pv.id) AS pageviews,
                COUNT(DISTINCT pv.ip_address) AS unique_visitors,
                ROUND(COUNT(pv.id) / NULLIF(COUNT(DISTINCT pv.ip_address), 0), 2) AS views_per_visitor,
                c.name AS category_name
            FROM posts p
            INNER JOIN post_pageviews_month pv ON pv.post_id = p.id AND pv.created_at >= ?
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.status = 1 AND p.visibility = 1
            GROUP BY p.id, p.title, p.slug, c.name
            ORDER BY pageviews DESC, unique_visitors DESC, p.id DESC
            LIMIT {$limit}
        ", [$period['start_datetime']])->getResult();
    }

    public function getMostEngagedPosts($limit = 10, $days = 30)
    {
        $limit = max(1, (int) $limit);
        $period = $this->getPeriodBounds($days);

        return $this->db->query("
            SELECT
                p.id,
                p.title,
                p.slug,
                c.name AS category_name,
                COALESCE(views.period_views, 0) AS period_views,
                COALESCE(comments.period_comments, 0) AS period_comments,
                COALESCE(reactions.total_reactions, 0) AS reaction_count,
                (COALESCE(comments.period_comments, 0) + COALESCE(reactions.total_reactions, 0)) AS total_interactions,
                ROUND(((COALESCE(comments.period_comments, 0) + COALESCE(reactions.total_reactions, 0)) / NULLIF(COALESCE(views.period_views, 0), 0)) * 100, 2) AS interaction_rate
            FROM posts p
            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN (
                SELECT post_id, COUNT(*) AS period_views
                FROM post_pageviews_month
                WHERE created_at >= ?
                GROUP BY post_id
            ) views ON views.post_id = p.id
            LEFT JOIN (
                SELECT post_id, COUNT(*) AS period_comments
                FROM comments
                WHERE status = 1 AND created_at >= ?
                GROUP BY post_id
            ) comments ON comments.post_id = p.id
            LEFT JOIN (
                SELECT
                    post_id,
                    (COALESCE(re_like, 0) + COALESCE(re_dislike, 0) + COALESCE(re_love, 0) + COALESCE(re_funny, 0) + COALESCE(re_angry, 0) + COALESCE(re_sad, 0) + COALESCE(re_wow, 0)) AS total_reactions
                FROM reactions
            ) reactions ON reactions.post_id = p.id
            WHERE p.status = 1
              AND p.visibility = 1
              AND (COALESCE(views.period_views, 0) > 0 OR COALESCE(comments.period_comments, 0) > 0)
            ORDER BY total_interactions DESC, period_comments DESC, period_views DESC, p.id DESC
            LIMIT {$limit}
        ", [$period['start_datetime'], $period['start_datetime']])->getResult();
    }

    public function getTrafficSources($days = 30)
    {
        $period = $this->getPeriodBounds($days);
        $totalViews = $this->getTotalPageViews($days);

        if (!$this->hasDetailedPageviewColumns()) {
            return [
                'tracked' => false,
                'coverage_pct' => 0,
                'sources' => [],
            ];
        }

        $trackedViews = (int) ($this->db->query("
            SELECT COUNT(*) AS total
            FROM post_pageviews_month
            WHERE created_at >= ?
              AND source_group IS NOT NULL
              AND source_group != ''
        ", [$period['start_datetime']])->getRow()->total ?? 0);

        if ($trackedViews === 0) {
            return [
                'tracked' => false,
                'coverage_pct' => 0,
                'sources' => [],
            ];
        }

        $rows = $this->db->query("
            SELECT
                source_group AS source,
                COUNT(DISTINCT ip_address) AS visitors,
                COUNT(*) AS pageviews
            FROM post_pageviews_month
            WHERE created_at >= ?
              AND source_group IS NOT NULL
              AND source_group != ''
            GROUP BY source_group
            ORDER BY visitors DESC, pageviews DESC, source ASC
            LIMIT 8
        ", [$period['start_datetime']])->getResultArray();

        return [
            'tracked' => true,
            'coverage_pct' => $totalViews > 0 ? round(($trackedViews / $totalViews) * 100, 2) : 0,
            'sources' => $this->appendPercentages($rows, 'visitors'),
        ];
    }

    public function getCategoryPerformance($days = 30)
    {
        $period = $this->getPeriodBounds($days);

        return $this->db->query("
            SELECT
                c.id,
                c.name,
                COUNT(DISTINCT published_posts.id) AS published_posts,
                COALESCE(views.active_posts, 0) AS active_posts,
                COALESCE(views.period_views, 0) AS period_views,
                COALESCE(views.unique_visitors, 0) AS unique_visitors,
                COALESCE(comments.period_comments, 0) AS period_comments,
                ROUND(COALESCE(views.period_views, 0) / NULLIF(COALESCE(views.active_posts, 0), 0), 2) AS avg_views_per_active_post
            FROM categories c
            LEFT JOIN posts published_posts
                ON published_posts.category_id = c.id
               AND published_posts.status = 1
               AND published_posts.visibility = 1
            LEFT JOIN (
                SELECT
                    p.category_id,
                    COUNT(DISTINCT pv.post_id) AS active_posts,
                    COUNT(*) AS period_views,
                    COUNT(DISTINCT pv.ip_address) AS unique_visitors
                FROM post_pageviews_month pv
                INNER JOIN posts p ON p.id = pv.post_id AND p.status = 1 AND p.visibility = 1
                WHERE pv.created_at >= ?
                GROUP BY p.category_id
            ) views ON views.category_id = c.id
            LEFT JOIN (
                SELECT
                    p.category_id,
                    COUNT(*) AS period_comments
                FROM comments cm
                INNER JOIN posts p ON p.id = cm.post_id AND p.status = 1 AND p.visibility = 1
                WHERE cm.status = 1 AND cm.created_at >= ?
                GROUP BY p.category_id
            ) comments ON comments.category_id = c.id
            GROUP BY c.id, c.name, views.active_posts, views.period_views, views.unique_visitors, comments.period_comments
            HAVING published_posts > 0 OR active_posts > 0 OR period_views > 0 OR period_comments > 0
            ORDER BY period_views DESC, unique_visitors DESC, c.name ASC
        ", [$period['start_datetime'], $period['start_datetime']])->getResult();
    }

    public function getUserEngagement($days = 30)
    {
        $period = $this->getPeriodBounds($days);
        $totalUsers = (int) $this->db->table('users')->countAllResults();
        $activeUsers = (int) $this->db->table('users')
            ->where('last_seen >=', $period['start_datetime'])
            ->countAllResults();
        $newUsers = (int) $this->db->table('users')
            ->where('created_at >=', $period['start_datetime'])
            ->countAllResults();
        $approvedComments = (int) $this->db->table('comments')
            ->where('status', 1)
            ->where('created_at >=', $period['start_datetime'])
            ->countAllResults();
        $publishedAuthors = (int) ($this->db->query("
            SELECT COUNT(DISTINCT user_id) AS total
            FROM posts
            WHERE status = 1 AND visibility = 1 AND user_id IS NOT NULL
        ")->getRow()->total ?? 0);
        $activeAuthors = (int) ($this->db->query("
            SELECT COUNT(DISTINCT post_user_id) AS total
            FROM post_pageviews_month
            WHERE created_at >= ?
              AND post_user_id IS NOT NULL
        ", [$period['start_datetime']])->getRow()->total ?? 0);

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'new_users' => $newUsers,
            'approved_comments' => $approvedComments,
            'published_authors' => $publishedAuthors,
            'active_authors' => $activeAuthors,
            'engagement_rate' => $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 2) : 0,
            'author_activity_rate' => $publishedAuthors > 0 ? round(($activeAuthors / $publishedAuthors) * 100, 2) : 0,
        ];
    }

    public function getContentSummary($days = 30)
    {
        $period = $this->getPeriodBounds($days);
        $totalPosts = (int) $this->db->table('posts')
            ->where('status', 1)
            ->where('visibility', 1)
            ->countAllResults();
        $newPosts = (int) $this->db->table('posts')
            ->where('status', 1)
            ->where('visibility', 1)
            ->where('created_at >=', $period['start_datetime'])
            ->countAllResults();
        $activePosts = (int) ($this->db->query("
            SELECT COUNT(DISTINCT post_id) AS total
            FROM post_pageviews_month
            WHERE created_at >= ?
        ", [$period['start_datetime']])->getRow()->total ?? 0);
        $periodViews = $this->getTotalPageViews($days);
        $approvedComments = (int) $this->db->table('comments')
            ->where('status', 1)
            ->where('created_at >=', $period['start_datetime'])
            ->countAllResults();

        return [
            'total_posts' => $totalPosts,
            'new_posts' => $newPosts,
            'active_posts' => $activePosts,
            'inactive_posts' => max(0, $totalPosts - $activePosts),
            'avg_views_per_active_post' => $activePosts > 0 ? round($periodViews / $activePosts, 2) : 0,
            'approved_comments' => $approvedComments,
        ];
    }

    public function getDeviceAnalytics($days = 30)
    {
        $period = $this->getPeriodBounds($days);
        $totalViews = $this->getTotalPageViews($days);

        if (!$this->hasDetailedPageviewColumns()) {
            return [
                'tracked' => false,
                'coverage_pct' => 0,
                'devices' => [],
                'browsers' => [],
            ];
        }

        $trackedViews = (int) ($this->db->query("
            SELECT COUNT(*) AS total
            FROM post_pageviews_month
            WHERE created_at >= ?
              AND device_type IS NOT NULL
              AND browser_name IS NOT NULL
        ", [$period['start_datetime']])->getRow()->total ?? 0);

        if ($trackedViews === 0) {
            return [
                'tracked' => false,
                'coverage_pct' => 0,
                'devices' => [],
                'browsers' => [],
            ];
        }

        $devices = $this->db->query("
            SELECT
                device_type AS type,
                COUNT(*) AS count
            FROM post_pageviews_month
            WHERE created_at >= ?
              AND device_type IS NOT NULL
              AND device_type != ''
            GROUP BY device_type
            ORDER BY count DESC, type ASC
        ", [$period['start_datetime']])->getResultArray();

        $browsers = $this->db->query("
            SELECT
                browser_name AS name,
                COUNT(*) AS count
            FROM post_pageviews_month
            WHERE created_at >= ?
              AND browser_name IS NOT NULL
              AND browser_name != ''
            GROUP BY browser_name
            ORDER BY count DESC, name ASC
            LIMIT 6
        ", [$period['start_datetime']])->getResultArray();

        return [
            'tracked' => true,
            'coverage_pct' => $totalViews > 0 ? round(($trackedViews / $totalViews) * 100, 2) : 0,
            'devices' => $this->appendPercentages($devices, 'count'),
            'browsers' => $this->appendPercentages($browsers, 'count'),
        ];
    }

    public function getRealTimeAnalytics()
    {
        $last30Minutes = date('Y-m-d H:i:s', strtotime('-' . self::LIVE_VISITOR_WINDOW_MINUTES . ' minutes'));
        $lastHour = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $last24Hours = date('Y-m-d H:i:s', strtotime('-24 hours'));

        $currentVisitors = (int) ($this->db->table('post_pageviews_month')
            ->select('COUNT(DISTINCT ip_address) AS visitors')
            ->where('created_at >=', $last30Minutes)
            ->get()
            ->getRow()
            ->visitors ?? 0);

        $viewsLastHour = (int) $this->db->table('post_pageviews_month')
            ->where('created_at >=', $lastHour)
            ->countAllResults();

        $currentViews = (int) $this->db->table('post_pageviews_month')
            ->where('created_at >=', $last24Hours)
            ->countAllResults();

        $lastEventAt = $this->db->table('post_pageviews_month')
            ->select('MAX(created_at) AS last_event_at')
            ->get()
            ->getRow()
            ->last_event_at ?? null;

        $topPagesNow = $this->db->query("
            SELECT
                p.title,
                p.slug,
                COUNT(*) AS current_views,
                COUNT(DISTINCT pv.ip_address) AS unique_visitors
            FROM post_pageviews_month pv
            INNER JOIN posts p ON p.id = pv.post_id
            WHERE pv.created_at >= ?
              AND p.status = 1
              AND p.visibility = 1
            GROUP BY p.id, p.title, p.slug
            ORDER BY current_views DESC, unique_visitors DESC, p.id DESC
            LIMIT 5
        ", [$last30Minutes])->getResult();

        return [
            'current_visitors' => $currentVisitors,
            'views_last_hour' => $viewsLastHour,
            'current_views' => $currentViews,
            'last_event_at' => $lastEventAt,
            'top_pages_now' => $topPagesNow,
        ];
    }

    public function getConversionMetrics($days = 30)
    {
        $period = $this->getPeriodBounds($days);
        $startDatetime = $period['start_datetime'];

        // --- Simulator leads (sim_leads) ---
        $totalLeads = (int) $this->db->table('sim_leads')
            ->where('created_at >=', $startDatetime)
            ->countAllResults();

        $leadsByStatus = $this->db->query("
            SELECT status, COUNT(*) AS total
            FROM sim_leads
            WHERE created_at >= ?
            GROUP BY status
            ORDER BY total DESC
        ", [$startDatetime])->getResultArray();

        $statusMap = [];
        foreach ($leadsByStatus as $row) {
            $statusMap[$row['status']] = (int) $row['total'];
        }

        $convertedLeads = (int) ($statusMap['converted'] ?? 0);

        $dailyLeads = $this->db->query("
            SELECT
                DATE(created_at) AS event_date,
                COUNT(*) AS leads
            FROM sim_leads
            WHERE created_at >= ?
            GROUP BY DATE(created_at)
            ORDER BY event_date ASC
        ", [$startDatetime])->getResultArray();

        $dailyLeads = $this->fillDateSeries($days, $dailyLeads, ['leads' => 0]);

        // --- Leads por origem (de onde vêm os leads dos simuladores) ---
        $leadsBySourceRows = $this->db->query("
            SELECT
                COALESCE(NULLIF(TRIM(origem), ''), 'Direto / não informado') AS origem,
                COUNT(*) AS total
            FROM sim_leads
            WHERE created_at >= ?
            GROUP BY origem
            ORDER BY total DESC, origem ASC
            LIMIT 12
        ", [$startDatetime])->getResultArray();

        $leadsBySource = [];
        foreach ($leadsBySourceRows as $row) {
            $leadsBySource[] = [
                'origem' => $row['origem'],
                'count' => (int) $row['total'],
                'percentage' => $totalLeads > 0 ? round(((int) $row['total'] / $totalLeads) * 100, 1) : 0,
            ];
        }

        // --- Contact form (contacts) ---
        $totalContacts = (int) $this->db->table('contacts')
            ->where('created_at >=', $startDatetime)
            ->countAllResults();

        $dailyContacts = $this->db->query("
            SELECT
                DATE(created_at) AS event_date,
                COUNT(*) AS contacts
            FROM contacts
            WHERE created_at >= ?
            GROUP BY DATE(created_at)
            ORDER BY event_date ASC
        ", [$startDatetime])->getResultArray();

        $dailyContacts = $this->fillDateSeries($days, $dailyContacts, ['contacts' => 0]);

        // --- Recent leads (last 5) ---
        $recentLeads = $this->db->query("
            SELECT id, name, email, status, origem, created_at
            FROM sim_leads
            WHERE created_at >= ?
            ORDER BY id DESC
            LIMIT 5
        ", [$startDatetime])->getResult();

        // --- Conversion funnel labels ---
        $statusLabels = [
            'new' => 'Novo',
            'contacted' => 'Contactado',
            'qualified' => 'Qualificado',
            'proposal' => 'Proposta',
            'converted' => 'Convertido',
            'lost' => 'Perdido',
        ];

        $funnel = [];
        foreach ($statusLabels as $key => $label) {
            $funnel[] = [
                'status' => $key,
                'label' => $label,
                'count' => (int) ($statusMap[$key] ?? 0),
            ];
        }

        return [
            'total_leads' => $totalLeads,
            'total_contacts' => $totalContacts,
            'total_conversions' => $totalLeads + $totalContacts,
            'converted_leads' => $convertedLeads,
            'conversion_rate' => $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0,
            'funnel' => $funnel,
            'leads_by_source' => $leadsBySource,
            'daily_leads' => $dailyLeads,
            'daily_contacts' => $dailyContacts,
            'recent_leads' => $recentLeads,
        ];
    }

    private function getPeriodBounds($days)
    {
        $days = $this->normalizeDays($days);
        $startDatetime = date('Y-m-d 00:00:00', strtotime('-' . ($days - 1) . ' days'));

        return [
            'days' => $days,
            'start_datetime' => $startDatetime,
            'start_date' => date('Y-m-d', strtotime($startDatetime)),
        ];
    }

    private function fillDateSeries($days, array $rows, array $defaults)
    {
        $days = $this->normalizeDays($days);
        $mappedRows = [];

        foreach ($rows as $row) {
            $mappedRows[$row['event_date']] = $row;
        }

        $series = [];
        for ($index = $days - 1; $index >= 0; $index--) {
            $date = date('Y-m-d', strtotime('-' . $index . ' days'));
            $current = $defaults;
            if (isset($mappedRows[$date])) {
                foreach ($defaults as $field => $defaultValue) {
                    $current[$field] = isset($mappedRows[$date][$field]) ? (float) $mappedRows[$date][$field] : $defaultValue;
                }
            }

            $current['date'] = $date;
            $current['label'] = date('d/m', strtotime($date));
            $series[] = $current;
        }

        return $series;
    }

    private function appendPercentages(array $rows, $field)
    {
        $total = array_sum(array_map(static function ($row) use ($field) {
            return (int) ($row[$field] ?? 0);
        }, $rows));

        return array_map(static function ($row) use ($field, $total) {
            $row['percentage'] = $total > 0 ? round(((int) ($row[$field] ?? 0) / $total) * 100, 2) : 0;
            return $row;
        }, $rows);
    }

    private function hasDetailedPageviewColumns()
    {
        if ($this->hasDetailedPageviewColumns !== null) {
            return $this->hasDetailedPageviewColumns;
        }

        $requiredColumns = ['source_group', 'referrer_host', 'browser_name', 'platform_name', 'device_type'];
        foreach ($requiredColumns as $column) {
            if (!$this->db->fieldExists($column, 'post_pageviews_month')) {
                $this->hasDetailedPageviewColumns = false;
                return false;
            }
        }

        $this->hasDetailedPageviewColumns = true;
        return true;
    }
}
