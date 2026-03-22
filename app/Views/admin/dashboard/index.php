
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= esc($title); ?></h3>
                </div>
                <div class="right">
                    <div class="form-group">
                        <select id="days_filter" class="form-control" style="width: 150px; display: inline-block;">
                            <option value="7" <?= $days == 7 ? 'selected' : '' ?>>Últimos 7 dias</option>
                            <option value="30" <?= $days == 30 ? 'selected' : '' ?>>Últimos 30 dias</option>
                            <option value="90" <?= $days == 90 ? 'selected' : '' ?>>Últimos 90 dias</option>
                            <option value="365" <?= $days == 365 ? 'selected' : '' ?>>Último ano</option>
                        </select>
                    </div>
                    <a href="<?= adminUrl('dashboard/widgets'); ?>" class="btn btn-primary btn-sm">
                        <i class="fa fa-cog"></i> Personalizar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row" id="summary-cards">
    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3 id="total-visitors"><?= number_format($totalVisitors); ?></h3>
                <p>Visitantes Únicos</p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-blue">
            <div class="inner">
                <h3 id="total-pageviews"><?= number_format($totalPageViews); ?></h3>
                <p>Visualizações</p>
            </div>
            <div class="icon">
                <i class="fa fa-eye"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3 id="total-posts"><?= number_format($contentSummary['total_posts']); ?></h3>
                <p>Total de Posts</p>
            </div>
            <div class="icon">
                <i class="fa fa-file-text"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3 id="engagement-rate"><?= $userEngagement['engagement_rate']; ?>%</h3>
                <p>Taxa de Engajamento</p>
            </div>
            <div class="icon">
                <i class="fa fa-heart"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Dashboard Content -->
<div class="row">
    <!-- Visitors Chart -->
    <div class="col-md-8">
        <div class="box" id="visitors-chart-widget">
            <div class="box-header with-border">
                <h3 class="box-title">Visitantes ao Longo do Tempo</h3>
                <div class="box-tools">
                    <button class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <canvas id="visitorsChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Real-time Stats -->
    <div class="col-md-4">
        <div class="box" id="realtime-widget">
            <div class="box-header with-border">
                <h3 class="box-title">Tempo Real (24h)</h3>
                <div class="box-tools">
                    <span class="badge bg-green" id="live-indicator">LIVE</span>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-6">
                        <div class="description-block">
                            <h5 class="description-header" id="current-visitors"><?= $realTime['current_visitors']; ?></h5>
                            <span class="description-text">Visitantes Agora</span>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="description-block">
                            <h5 class="description-header" id="current-views"><?= $realTime['current_views']; ?></h5>
                            <span class="description-text">Views (24h)</span>
                        </div>
                    </div>
                </div>
                
                <h5>Páginas Mais Visitadas Agora:</h5>
                <div id="top-pages-now">
                    <?php if (!empty($realTime['top_pages_now'])): ?>
                        <?php foreach ($realTime['top_pages_now'] as $page): ?>
                            <div class="progress-group">
                                <span class="progress-text"><?= esc(substr($page->title, 0, 30)); ?>...</span>
                                <span class="float-right"><b><?= $page->current_views; ?></b> views</span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary" style="width: <?= min(100, ($page->current_views / max(1, $realTime['top_pages_now'][0]->current_views)) * 100); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Nenhuma página visitada nas últimas 24h</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Posts -->
    <div class="col-md-6">
        <div class="box" id="top-posts-widget">
            <div class="box-header with-border">
                <h3 class="box-title">Posts Mais Lidos</h3>
                <div class="box-tools">
                    <button class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Categoria</th>
                                <th>Views</th>
                                <th>Visitantes</th>
                            </tr>
                        </thead>
                        <tbody id="top-posts-table">
                            <?php foreach ($topPosts as $post): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url($post->slug); ?>" target="_blank">
                                            <?= esc(substr($post->title, 0, 40)); ?>...
                                        </a>
                                    </td>
                                    <td><?= esc($post->category_name); ?></td>
                                    <td><span class="badge bg-blue"><?= number_format($post->pageviews); ?></span></td>
                                    <td><span class="badge bg-green"><?= number_format($post->unique_visitors); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Most Engaged Posts -->
    <div class="col-md-6">
        <div class="box" id="engagement-widget">
            <div class="box-header with-border">
                <h3 class="box-title">Posts com Maior Engajamento</h3>
                <div class="box-tools">
                    <button class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Categoria</th>
                                <th>Comentários</th>
                                <th>Reações</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="engagement-table">
                            <?php foreach ($engagedPosts as $post): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url($post->slug); ?>" target="_blank">
                                            <?= esc(substr($post->title, 0, 30)); ?>...
                                        </a>
                                    </td>
                                    <td><?= esc($post->category_name); ?></td>
                                    <td><span class="badge bg-blue"><?= $post->comment_count; ?></span></td>
                                    <td><span class="badge bg-green"><?= $post->reaction_count; ?></span></td>
                                    <td><span class="badge bg-orange"><?= $post->total_engagement; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Traffic Sources -->
    <div class="col-md-6">
        <div class="box" id="traffic-sources-widget">
            <div class="box-header with-border">
                <h3 class="box-title">Fontes de Tráfego</h3>
                <div class="box-tools">
                    <button class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <canvas id="trafficChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Category Performance -->
    <div class="col-md-6">
        <div class="box" id="category-performance-widget">
            <div class="box-header with-border">
                <h3 class="box-title">Performance por Categoria</h3>
                <div class="box-tools">
                    <button class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Categoria</th>
                                <th>Posts</th>
                                <th>Views</th>
                                <th>Comentários</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categoryPerformance as $category): ?>
                                <tr>
                                    <td><?= esc($category->name); ?></td>
                                    <td><span class="badge bg-blue"><?= $category->post_count; ?></span></td>
                                    <td><span class="badge bg-green"><?= number_format($category->total_views); ?></span></td>
                                    <td><span class="badge bg-orange"><?= $category->total_comments; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Device Analytics -->
<div class="row">
    <div class="col-md-6">
        <div class="box" id="device-analytics-widget">
            <div class="box-header with-border">
                <h3 class="box-title">Dispositivos</h3>
                <div class="box-tools">
                    <button class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <canvas id="deviceChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box" id="browser-analytics-widget">
            <div class="box-header with-border">
                <h3 class="box-title">Navegadores</h3>
                <div class="box-tools">
                    <button class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <canvas id="browserChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize charts
    initializeCharts();
    
    // Auto-refresh real-time data every 30 seconds
    setInterval(updateRealTimeData, 30000);
    
    // Days filter change handler
    document.getElementById('days_filter').addEventListener('change', function() {
        const days = this.value;
        window.location.href = '<?= adminUrl('dashboard'); ?>?days=' + days;
    });
    
    function initializeCharts() {
        // Visitors Chart
        const visitorsCtx = document.getElementById('visitorsChart').getContext('2d');
        const visitorsData = <?= json_encode($dailyVisitors); ?>;
        
        new Chart(visitorsCtx, {
            type: 'line',
            data: {
                labels: visitorsData.map(item => item.date),
                datasets: [{
                    label: 'Visitantes Únicos',
                    data: visitorsData.map(item => item.visitors),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Visualizações',
                    data: visitorsData.map(item => item.pageviews),
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Traffic Sources Chart
        const trafficCtx = document.getElementById('trafficChart').getContext('2d');
        const trafficData = <?= json_encode($trafficSources); ?>;
        
        new Chart(trafficCtx, {
            type: 'doughnut',
            data: {
                labels: trafficData.map(item => item.source),
                datasets: [{
                    data: trafficData.map(item => item.visitors),
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#FF9F40',
                        '#9966FF',
                        '#4BC0C0'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Device Chart
        const deviceCtx = document.getElementById('deviceChart').getContext('2d');
        const deviceData = <?= json_encode($deviceAnalytics['devices']); ?>;
        
        new Chart(deviceCtx, {
            type: 'bar',
            data: {
                labels: deviceData.map(item => item.type),
                datasets: [{
                    label: 'Usuários',
                    data: deviceData.map(item => item.count),
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Browser Chart
        const browserCtx = document.getElementById('browserChart').getContext('2d');
        const browserData = <?= json_encode($deviceAnalytics['browsers']); ?>;
        
        new Chart(browserCtx, {
            type: 'pie',
            data: {
                labels: browserData.map(item => item.name),
                datasets: [{
                    data: browserData.map(item => item.count),
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#FF9F40',
                        '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    function updateRealTimeData() {
        fetch('<?= adminUrl('dashboard/live-data'); ?>')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text().then(text => {
                    if (!text || text.trim() === '') {
                        console.error('Empty response received from live-data endpoint');
                        throw new Error('Empty response');
                    }
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Invalid JSON response:', text);
                        throw new Error('Invalid JSON response');
                    }
                });
            })
            .then(data => {
                // Check if response contains error
                if (data.error) {
                    console.warn('Dashboard API error:', data.message);
                    return;
                }
                
                // Safely update DOM elements
                const currentVisitorsEl = document.getElementById('current-visitors');
                const currentViewsEl = document.getElementById('current-views');
                const topPagesEl = document.getElementById('top-pages-now');
                const liveIndicatorEl = document.getElementById('live-indicator');
                
                if (currentVisitorsEl && data.realTime && typeof data.realTime.current_visitors !== 'undefined') {
                    currentVisitorsEl.textContent = data.realTime.current_visitors;
                }
                
                if (currentViewsEl && data.realTime && typeof data.realTime.current_views !== 'undefined') {
                    currentViewsEl.textContent = data.realTime.current_views;
                }
                
                // Update top pages
                if (topPagesEl && data.realTime && data.realTime.top_pages_now && data.realTime.top_pages_now.length > 0) {
                    let html = '';
                    data.realTime.top_pages_now.forEach(page => {
                        if (page && page.title && typeof page.current_views !== 'undefined') {
                            const percentage = Math.min(100, (page.current_views / Math.max(1, data.realTime.top_pages_now[0].current_views)) * 100);
                            html += `
                                <div class="progress-group">
                                    <span class="progress-text">${page.title.substring(0, 30)}...</span>
                                    <span class="float-right"><b>${page.current_views}</b> views</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-primary" style="width: ${percentage}%"></div>
                                    </div>
                                </div>
                            `;
                        }
                    });
                    topPagesEl.innerHTML = html;
                } else if (topPagesEl) {
                    topPagesEl.innerHTML = '<p class="text-muted">Nenhuma página visitada nas últimas 24h</p>';
                }
                
                // Animate live indicator
                if (liveIndicatorEl) {
                    liveIndicatorEl.style.opacity = '0.3';
                    setTimeout(() => {
                        liveIndicatorEl.style.opacity = '1';
                    }, 200);
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar dados em tempo real:', error);
                // Optionally show a user-friendly message in the UI
                const liveIndicatorEl = document.getElementById('live-indicator');
                if (liveIndicatorEl) {
                    liveIndicatorEl.style.opacity = '0.5';
                    liveIndicatorEl.title = 'Erro ao carregar dados';
                }
            });
    }
});
</script>

<style>
.small-box {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.small-box:hover {
    transform: translateY(-2px);
}

.box {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.progress-group {
    margin-bottom: 10px;
}

.progress-text {
    font-size: 12px;
}

.badge {
    font-size: 11px;
}

#live-indicator {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.description-block {
    text-align: center;
    padding: 10px;
}

.description-header {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.description-text {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
}

.table th {
    border-top: none;
    font-size: 12px;
    font-weight: 600;
}

.table td {
    font-size: 12px;
    vertical-align: middle;
}

.float-right {
    float: right !important;
}
</style>