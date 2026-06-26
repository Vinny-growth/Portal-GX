<?php
$overview = $overview ?? [];
$retention = $retention ?? [];
$trackingAvailability = $trackingAvailability ?? [];
$trafficSources = $trafficSources ?? ['tracked' => false, 'coverage_pct' => 0, 'sources' => []];
$deviceAnalytics = $deviceAnalytics ?? ['tracked' => false, 'coverage_pct' => 0, 'devices' => [], 'browsers' => []];
$realTime = $realTime ?? ['current_visitors' => 0, 'views_last_hour' => 0, 'current_views' => 0, 'last_event_at' => null, 'top_pages_now' => []];
$dashboardWidgets = $dashboardWidgets ?? [];
$dailyVisitors = $dailyVisitors ?? [];
$visitorSegments = $visitorSegments ?? [];
$ga4Connection = $ga4Connection ?? [];
$ga4Data = $ga4Data ?? ['success' => false];
$analyticsSource = $analyticsSource ?? 'internal';
$overviewInternal = $overviewInternal ?? $overview;
$isGa4Primary = ($analyticsSource === 'ga4');
$availableWindows = $availableWindows ?? [7, 30, 90, 365];
$availableDays = (int) ($trackingAvailability['available_days'] ?? 0);
$firstSeenAt = $trackingAvailability['first_seen_at'] ?? null;
$dimensionStartedAt = $trackingAvailability['dimension_started_at'] ?? null;
$formatDuration = static function ($seconds) {
    $seconds = (int) round((float) $seconds);
    if ($seconds <= 0) {
        return '0s';
    }

    $hours = (int) floor($seconds / 3600);
    $minutes = (int) floor(($seconds % 3600) / 60);
    $remainingSeconds = $seconds % 60;

    if ($hours > 0) {
        return sprintf('%02dh %02dm', $hours, $minutes);
    }

    if ($minutes > 0) {
        return sprintf('%02dm %02ds', $minutes, $remainingSeconds);
    }

    return $remainingSeconds . 's';
};
$dashboardPayload = [
    'dailyVisitors' => $dailyVisitors,
    'visitorSegments' => $visitorSegments,
    'trafficSources' => $trafficSources['sources'] ?? [],
    'deviceAnalytics' => [
        'devices' => $deviceAnalytics['devices'] ?? [],
        'browsers' => $deviceAnalytics['browsers'] ?? [],
    ],
    'ga4' => [
        'channels' => $ga4Data['channels'] ?? [],
    ],
    'conversions' => [
        'daily_leads' => ($conversions ?? [])['daily_leads'] ?? [],
        'daily_contacts' => ($conversions ?? [])['daily_contacts'] ?? [],
    ],
];
$summaryCards = [
    [
        'label' => $isGa4Primary ? 'Usuários ativos (GA4)' : 'Visitantes únicos (blog)',
        'value' => number_format((int) ($overview['total_visitors'] ?? 0)),
        'note' => $isGa4Primary ? 'Site inteiro, medido no GA4' : 'IPs distintos com leitura de post',
        'accent' => 'dashboard-accent-blue',
    ],
    [
        'label' => $isGa4Primary ? 'Pageviews (GA4)' : 'Pageviews (blog)',
        'value' => number_format((int) ($overview['total_pageviews'] ?? 0)),
        'note' => $isGa4Primary ? 'screenPageViews no período' : 'Visualizações de posts no período',
        'accent' => 'dashboard-accent-teal',
    ],
    [
        'label' => 'Páginas / visitante',
        'value' => number_format((float) ($overview['avg_pages_per_visitor'] ?? 0), 2, ',', '.'),
        'note' => 'Profundidade média de consumo',
        'accent' => 'dashboard-accent-indigo',
    ],
    [
        'label' => 'Retorno',
        'value' => number_format((float) ($overview['returning_rate'] ?? 0), 2, ',', '.') . '%',
        'note' => number_format((int) ($overview['returning_visitors'] ?? 0)) . ' visitantes recorrentes',
        'accent' => 'dashboard-accent-gold',
    ],
    [
        'label' => 'Posts ativos',
        'value' => number_format((int) ($overview['active_posts'] ?? 0)),
        'note' => 'Posts com views na janela',
        'accent' => 'dashboard-accent-orange',
    ],
    [
        'label' => 'Comentários aprovados',
        'value' => number_format((int) ($overview['approved_comments'] ?? 0)),
        'note' => 'Comentários publicados no período',
        'accent' => 'dashboard-accent-red',
    ],
];
$exportLinks = [
    ['label' => 'Visitantes (CSV)', 'url' => adminUrl('dashboard/export-data?type=visitors&format=csv&days=' . $days)],
    ['label' => 'Retenção (CSV)', 'url' => adminUrl('dashboard/export-data?type=retention&format=csv&days=' . $days)],
    ['label' => 'Posts mais lidos (CSV)', 'url' => adminUrl('dashboard/export-data?type=top_posts&format=csv&days=' . $days)],
    ['label' => 'Categorias (CSV)', 'url' => adminUrl('dashboard/export-data?type=categories&format=csv&days=' . $days)],
    ['label' => 'Conversões (CSV)', 'url' => adminUrl('dashboard/export-data?type=conversions&format=csv&days=' . $days)],
    ['label' => 'Leads por origem (CSV)', 'url' => adminUrl('dashboard/export-data?type=lead_sources&format=csv&days=' . $days)],
];
if (!empty($ga4Connection['is_ready'])) {
    $exportLinks[] = ['label' => 'GA4 Canais (CSV)', 'url' => adminUrl('dashboard/export-data?type=ga4_channels&format=csv&days=' . $days)];
    $exportLinks[] = ['label' => 'GA4 Páginas (CSV)', 'url' => adminUrl('dashboard/export-data?type=ga4_pages&format=csv&days=' . $days)];
}
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box dashboard-toolbar">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= esc($title); ?></h3>
                    <p class="dashboard-toolbar-copy">
                        <?php if ($isGa4Primary): ?>
                            Métricas globais via Google Analytics 4 (site inteiro). Widgets editoriais usam o tracking interno de posts.
                        <?php else: ?>
                            Métricas baseadas no tracking interno de posts. Conecte o Google Analytics para cobrir o site inteiro.
                        <?php endif; ?>
                    </p>
                    <span class="dashboard-meta-chip <?= $isGa4Primary ? 'dashboard-badge-green' : 'dashboard-badge-gold'; ?>" style="margin-top:6px;display:inline-block">
                        <i class="fa <?= $isGa4Primary ? 'fa-google' : 'fa-database'; ?>"></i>
                        Fonte primária: <?= $isGa4Primary ? 'Google Analytics 4' : 'Tracking interno (blog)'; ?>
                    </span>
                </div>
                <div class="right dashboard-toolbar-actions">
                    <div class="form-group">
                        <select id="days_filter" class="form-control dashboard-toolbar-select">
                            <?php foreach ($availableWindows as $window): ?>
                                <option value="<?= esc($window); ?>" <?= (int) $days === (int) $window ? 'selected' : ''; ?>>
                                    Últimos <?= esc($window); ?> dias
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-download"></i> Exportar <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <?php foreach ($exportLinks as $exportLink): ?>
                                <li><a href="<?= esc($exportLink['url']); ?>"><?= esc($exportLink['label']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <a href="<?= adminUrl('dashboard/widgets'); ?>" class="btn btn-primary btn-sm">
                        <i class="fa fa-sliders"></i> Personalizar
                    </a>
                    <a href="<?= adminUrl('dashboard/google-analytics'); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-google"></i> Google Analytics
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!$isGa4Primary && empty($firstSeenAt)): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="callout callout-warning">
                <h4>Sem base comportamental suficiente</h4>
                <p>O dashboard já está pronto, mas ainda não há pageviews registradas em <code>post_pageviews_month</code> para preencher os widgets de audiência.</p>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!$isGa4Primary && !empty($firstSeenAt) && $availableDays > 0 && $availableDays < (int) $days): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="callout callout-info">
                <strong>Janela comportamental disponível:</strong>
                os dados de navegação hoje cobrem aproximadamente <?= esc($availableDays); ?> dias, a partir de <?= esc(date('d/m/Y H:i', strtotime($firstSeenAt))); ?>.
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!$isGa4Primary && empty($dimensionStartedAt)): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="callout callout-warning">
                <strong>Origem, dispositivo e navegador</strong>
                começaram a ser coletados nesta revisão do módulo. Esses widgets passam a ganhar cobertura conforme novas visitas entram no portal.
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row dashboard-kpi-row">
    <?php foreach ($summaryCards as $card): ?>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="dashboard-kpi-card <?= esc($card['accent']); ?>">
                <span class="dashboard-kpi-label"><?= esc($card['label']); ?></span>
                <strong class="dashboard-kpi-value"><?= esc($card['value']); ?></strong>
                <small class="dashboard-kpi-note"><?= esc($card['note']); ?></small>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($ga4Connection['credentials_configured'])): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="callout callout-info">
                <strong>Google Analytics 4:</strong>
                as credenciais OAuth ainda não foram configuradas.
                <a href="<?= adminUrl('dashboard/google-analytics'); ?>">Configurar integração</a>.
            </div>
        </div>
    </div>
<?php elseif (empty($ga4Connection['is_connected'])): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="callout callout-warning">
                <strong>Google Analytics 4:</strong>
                as credenciais já estão salvas, mas a conta Google ainda não foi autorizada.
                <a href="<?= adminUrl('dashboard/google-analytics'); ?>">Conectar conta</a>.
            </div>
        </div>
    </div>
<?php elseif (empty($ga4Connection['property_selected'])): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="callout callout-warning">
                <strong>Google Analytics 4:</strong>
                a conta está conectada, mas nenhuma propriedade foi selecionada.
                <a href="<?= adminUrl('dashboard/google-analytics'); ?>">Escolher propriedade</a>.
            </div>
        </div>
    </div>
<?php elseif (!empty($ga4Data['success'])): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="box dashboard-widget dashboard-ga-box">
                <div class="box-header with-border">
                    <div class="left">
                        <h3 class="box-title"><i class="fa fa-google"></i> Google Analytics 4</h3>
                        <span class="dashboard-widget-description">Audiência, sessões e páginas medidas diretamente na propriedade conectada do GA4.</span>
                    </div>
                    <div class="right dashboard-meta-chips">
                        <?php if (!empty($ga4Connection['account_name'])): ?>
                            <span class="dashboard-meta-chip"><?= esc($ga4Connection['account_name']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($ga4Connection['property_name'])): ?>
                            <span class="dashboard-meta-chip"><?= esc($ga4Connection['property_name']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($ga4Connection['connected_email'])): ?>
                            <span class="dashboard-meta-chip"><?= esc($ga4Connection['connected_email']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($ga4Data['fetched_at'])): ?>
                            <span class="dashboard-meta-chip">Sincronizado <?= esc(date('d/m H:i', strtotime($ga4Data['fetched_at']))); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row dashboard-kpi-row dashboard-ga-kpis">
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="dashboard-kpi-card dashboard-accent-blue">
                                <span class="dashboard-kpi-label">GA Usuários Ativos</span>
                                <strong class="dashboard-kpi-value"><?= number_format((int) ($ga4Data['overview']['active_users'] ?? 0)); ?></strong>
                                <small class="dashboard-kpi-note">usuários ativos no período</small>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="dashboard-kpi-card dashboard-accent-teal">
                                <span class="dashboard-kpi-label">GA Sessões</span>
                                <strong class="dashboard-kpi-value"><?= number_format((int) ($ga4Data['overview']['sessions'] ?? 0)); ?></strong>
                                <small class="dashboard-kpi-note">sessões registradas no GA4</small>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="dashboard-kpi-card dashboard-accent-indigo">
                                <span class="dashboard-kpi-label">GA Visualizações</span>
                                <strong class="dashboard-kpi-value"><?= number_format((int) ($ga4Data['overview']['screen_page_views'] ?? 0)); ?></strong>
                                <small class="dashboard-kpi-note">pageviews/screens no período</small>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="dashboard-kpi-card dashboard-accent-gold">
                                <span class="dashboard-kpi-label">Engajamento</span>
                                <strong class="dashboard-kpi-value"><?= number_format((float) ($ga4Data['overview']['engagement_rate_pct'] ?? 0), 2, ',', '.'); ?>%</strong>
                                <small class="dashboard-kpi-note">taxa de engajamento GA4</small>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="dashboard-kpi-card dashboard-accent-orange">
                                <span class="dashboard-kpi-label">Duração Média</span>
                                <strong class="dashboard-kpi-value"><?= esc($formatDuration($ga4Data['overview']['average_session_duration_sec'] ?? 0)); ?></strong>
                                <small class="dashboard-kpi-note">média por sessão</small>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="dashboard-kpi-card dashboard-accent-red">
                                <span class="dashboard-kpi-label">Tempo Real</span>
                                <strong class="dashboard-kpi-value" id="ga4-active-users"><?= number_format((int) ($ga4Data['realtime']['active_users'] ?? 0)); ?></strong>
                                <small class="dashboard-kpi-note">usuários ativos agora no GA4</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-5 col-md-12">
                            <div class="dashboard-chart-shell dashboard-chart-shell-sm">
                                <canvas id="gaChannelsChart" height="210"></canvas>
                            </div>
                        </div>
                        <div class="col-lg-7 col-md-12">
                            <?php if (!empty($ga4Data['pages'])): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover dashboard-table">
                                        <thead>
                                        <tr>
                                            <th>Página</th>
                                            <th>Views</th>
                                            <th>Usuários</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($ga4Data['pages'] as $page): ?>
                                            <tr>
                                                <td><?= esc(characterLimiter($page['path'] ?? '/', 70, '...')); ?></td>
                                                <td><span class="dashboard-badge dashboard-badge-blue"><?= number_format((int) ($page['views'] ?? 0)); ?></span></td>
                                                <td><span class="dashboard-badge dashboard-badge-green"><?= number_format((int) ($page['active_users'] ?? 0)); ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="dashboard-empty">
                                    <i class="fa fa-file-text-o"></i>
                                    <p>O GA4 não retornou páginas relevantes para este período.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php elseif (!empty($ga4Connection['last_error'])): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="callout callout-danger">
                <strong>Google Analytics 4:</strong>
                <?= esc($ga4Connection['last_error']); ?>
                <a href="<?= adminUrl('dashboard/google-analytics'); ?>">Revisar conexão</a>.
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row dashboard-widget-grid">
    <?php if (empty($dashboardWidgets)): ?>
        <div class="col-sm-12">
            <div class="box dashboard-widget">
                <div class="box-body">
                    <div class="dashboard-empty">
                        <i class="fa fa-sliders"></i>
                        <p>Nenhum widget está ativo. Reative a visualização em <a href="<?= adminUrl('dashboard/widgets'); ?>">Personalizar Dashboard</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php foreach ($dashboardWidgets as $widget): ?>
        <div class="<?= esc($widget['column_class']); ?>">
            <?php switch ($widget['key']):
                case 'visitors_chart': ?>
                    <div class="box dashboard-widget" id="visitors-chart-widget">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa <?= esc($widget['icon']); ?>"></i> <?= esc($widget['name']); ?></h3>
                            <span class="dashboard-widget-description"><?= esc($widget['description']); ?></span>
                        </div>
                        <div class="box-body">
                            <div class="dashboard-chart-shell">
                                <canvas id="visitorsChart" height="120"></canvas>
                            </div>
                            <div class="dashboard-inline-metrics">
                                <div>
                                    <strong><?= number_format((int) ($overview['total_visitors'] ?? 0)); ?></strong>
                                    <span>visitantes</span>
                                </div>
                                <div>
                                    <strong><?= number_format((int) ($overview['total_pageviews'] ?? 0)); ?></strong>
                                    <span>pageviews</span>
                                </div>
                                <div>
                                    <strong><?= number_format((float) ($overview['avg_pages_per_visitor'] ?? 0), 2, ',', '.'); ?></strong>
                                    <span>páginas por visitante</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'retention': ?>
                    <div class="box dashboard-widget" id="retention-widget">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa <?= esc($widget['icon']); ?>"></i> <?= esc($widget['name']); ?></h3>
                            <span class="dashboard-widget-description"><?= esc($widget['description']); ?></span>
                        </div>
                        <div class="box-body">
                            <div class="dashboard-stat-grid">
                                <div class="dashboard-stat-card">
                                    <span>Recorrentes</span>
                                    <strong><?= number_format((int) ($retention['returning_visitors'] ?? 0)); ?></strong>
                                    <small><?= number_format((float) ($retention['returning_rate'] ?? 0), 2, ',', '.'); ?>% da base do período</small>
                                </div>
                                <div class="dashboard-stat-card">
                                    <span>Novos</span>
                                    <strong><?= number_format((int) ($retention['new_visitors'] ?? 0)); ?></strong>
                                    <small>primeira aparição na janela disponível</small>
                                </div>
                                <div class="dashboard-stat-card">
                                    <span>Repetição</span>
                                    <strong><?= number_format((float) ($retention['repeat_rate'] ?? 0), 2, ',', '.'); ?>%</strong>
                                    <small><?= number_format((int) ($retention['repeat_visitors'] ?? 0)); ?> visitantes com 2+ acessos</small>
                                </div>
                                <div class="dashboard-stat-card">
                                    <span>Stickiness</span>
                                    <strong><?= number_format((float) ($retention['stickiness'] ?? 0), 2, ',', '.'); ?>%</strong>
                                    <small><?= number_format((int) ($retention['loyal_visitors'] ?? 0)); ?> visitantes com 3+ dias ativos</small>
                                </div>
                            </div>
                            <div class="dashboard-chart-shell dashboard-chart-shell-sm">
                                <canvas id="retentionChart" height="110"></canvas>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'top_posts': ?>
                    <div class="box dashboard-widget" id="top-posts-widget">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa <?= esc($widget['icon']); ?>"></i> <?= esc($widget['name']); ?></h3>
                            <span class="dashboard-widget-description"><?= esc($widget['description']); ?></span>
                        </div>
                        <div class="box-body">
                            <?php if (!empty($topPosts)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover dashboard-table">
                                        <thead>
                                        <tr>
                                            <th>Post</th>
                                            <th>Categoria</th>
                                            <th>Views</th>
                                            <th>Visitantes</th>
                                            <th>P/V</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($topPosts as $post): ?>
                                            <tr>
                                                <td>
                                                    <a href="<?= base_url($post->slug); ?>" target="_blank" rel="noopener">
                                                        <?= esc(characterLimiter($post->title, 56, '...')); ?>
                                                    </a>
                                                </td>
                                                <td><?= esc($post->category_name ?: 'Sem categoria'); ?></td>
                                                <td><span class="dashboard-badge dashboard-badge-blue"><?= number_format((int) $post->pageviews); ?></span></td>
                                                <td><span class="dashboard-badge dashboard-badge-green"><?= number_format((int) $post->unique_visitors); ?></span></td>
                                                <td><?= number_format((float) $post->views_per_visitor, 2, ',', '.'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="dashboard-empty">
                                    <i class="fa fa-file-text-o"></i>
                                    <p>Não há posts com pageviews dentro desta janela.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php break;

                case 'engagement': ?>
                    <div class="box dashboard-widget" id="engagement-widget">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa <?= esc($widget['icon']); ?>"></i> <?= esc($widget['name']); ?></h3>
                            <span class="dashboard-widget-description"><?= esc($widget['description']); ?></span>
                        </div>
                        <div class="box-body">
                            <?php if (!empty($engagedPosts)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover dashboard-table">
                                        <thead>
                                        <tr>
                                            <th>Post</th>
                                            <th>Comentários</th>
                                            <th>Reações</th>
                                            <th>Interações</th>
                                            <th>Taxa</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($engagedPosts as $post): ?>
                                            <tr>
                                                <td>
                                                    <a href="<?= base_url($post->slug); ?>" target="_blank" rel="noopener">
                                                        <?= esc(characterLimiter($post->title, 54, '...')); ?>
                                                    </a>
                                                </td>
                                                <td><span class="dashboard-badge dashboard-badge-blue"><?= number_format((int) $post->period_comments); ?></span></td>
                                                <td><span class="dashboard-badge dashboard-badge-green"><?= number_format((int) $post->reaction_count); ?></span></td>
                                                <td><span class="dashboard-badge dashboard-badge-gold"><?= number_format((int) $post->total_interactions); ?></span></td>
                                                <td><?= $post->interaction_rate !== null ? number_format((float) $post->interaction_rate, 2, ',', '.') . '%' : '0,00%'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="dashboard-empty">
                                    <i class="fa fa-comments-o"></i>
                                    <p>Ainda não há interações suficientes no período para ranquear engajamento.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php break;

                case 'traffic_sources': ?>
                    <div class="box dashboard-widget" id="traffic-sources-widget">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa <?= esc($widget['icon']); ?>"></i> <?= esc($widget['name']); ?></h3>
                            <span class="dashboard-widget-description"><?= esc($widget['description']); ?></span>
                            <?php if (!empty($trafficSources['tracked'])): ?>
                                <span class="dashboard-coverage">Cobertura rastreada: <?= number_format((float) ($trafficSources['coverage_pct'] ?? 0), 2, ',', '.'); ?>%</span>
                            <?php endif; ?>
                        </div>
                        <div class="box-body">
                            <?php if (!empty($trafficSources['tracked']) && !empty($trafficSources['sources'])): ?>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="dashboard-chart-shell dashboard-chart-shell-sm">
                                            <canvas id="trafficChart" height="180"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="dashboard-source-list">
                                            <?php foreach ($trafficSources['sources'] as $source): ?>
                                                <div class="dashboard-source-row">
                                                    <div>
                                                        <strong><?= esc($source['source']); ?></strong>
                                                        <small><?= number_format((int) ($source['visitors'] ?? 0)); ?> visitantes</small>
                                                    </div>
                                                    <span><?= number_format((float) ($source['percentage'] ?? 0), 2, ',', '.'); ?>%</span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="dashboard-empty">
                                    <i class="fa fa-compass"></i>
                                    <p>Sem massa crítica para origem de tráfego ainda. O tracking detalhado passa a preencher este widget com as novas pageviews.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php break;

                case 'category_performance': ?>
                    <div class="box dashboard-widget" id="category-performance-widget">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa <?= esc($widget['icon']); ?>"></i> <?= esc($widget['name']); ?></h3>
                            <span class="dashboard-widget-description"><?= esc($widget['description']); ?></span>
                        </div>
                        <div class="box-body">
                            <?php if (!empty($categoryPerformance)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover dashboard-table">
                                        <thead>
                                        <tr>
                                            <th>Categoria</th>
                                            <th>Posts ativos</th>
                                            <th>Views</th>
                                            <th>Visitantes</th>
                                            <th>Comentários</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($categoryPerformance as $category): ?>
                                            <tr>
                                                <td><?= esc($category->name); ?></td>
                                                <td><?= number_format((int) $category->active_posts); ?></td>
                                                <td><?= number_format((int) $category->period_views); ?></td>
                                                <td><?= number_format((int) $category->unique_visitors); ?></td>
                                                <td><?= number_format((int) $category->period_comments); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="dashboard-empty">
                                    <i class="fa fa-folder-open-o"></i>
                                    <p>Nenhuma categoria teve atividade suficiente nesta janela.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php break;

                case 'user_stats': ?>
                    <div class="box dashboard-widget" id="user-stats-widget">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa <?= esc($widget['icon']); ?>"></i> <?= esc($widget['name']); ?></h3>
                            <span class="dashboard-widget-description"><?= esc($widget['description']); ?></span>
                        </div>
                        <div class="box-body">
                            <div class="dashboard-stat-grid">
                                <div class="dashboard-stat-card">
                                    <span>Usuários totais</span>
                                    <strong><?= number_format((int) ($userEngagement['total_users'] ?? 0)); ?></strong>
                                    <small>base cadastrada</small>
                                </div>
                                <div class="dashboard-stat-card">
                                    <span>Ativos no período</span>
                                    <strong><?= number_format((int) ($userEngagement['active_users'] ?? 0)); ?></strong>
                                    <small><?= number_format((float) ($userEngagement['engagement_rate'] ?? 0), 2, ',', '.'); ?>% da base</small>
                                </div>
                                <div class="dashboard-stat-card">
                                    <span>Novos usuários</span>
                                    <strong><?= number_format((int) ($userEngagement['new_users'] ?? 0)); ?></strong>
                                    <small>cadastros criados na janela</small>
                                </div>
                                <div class="dashboard-stat-card">
                                    <span>Autores ativos</span>
                                    <strong><?= number_format((int) ($userEngagement['active_authors'] ?? 0)); ?></strong>
                                    <small><?= number_format((float) ($userEngagement['author_activity_rate'] ?? 0), 2, ',', '.'); ?>% dos autores publicados</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'content_summary': ?>
                    <div class="box dashboard-widget" id="content-summary-widget">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa <?= esc($widget['icon']); ?>"></i> <?= esc($widget['name']); ?></h3>
                            <span class="dashboard-widget-description"><?= esc($widget['description']); ?></span>
                        </div>
                        <div class="box-body">
                            <div class="dashboard-stat-grid">
                                <div class="dashboard-stat-card">
                                    <span>Posts publicados</span>
                                    <strong><?= number_format((int) ($contentSummary['total_posts'] ?? 0)); ?></strong>
                                    <small>acervo ativo</small>
                                </div>
                                <div class="dashboard-stat-card">
                                    <span>Novos posts</span>
                                    <strong><?= number_format((int) ($contentSummary['new_posts'] ?? 0)); ?></strong>
                                    <small>publicados no período</small>
                                </div>
                                <div class="dashboard-stat-card">
                                    <span>Posts sem tráfego</span>
                                    <strong><?= number_format((int) ($contentSummary['inactive_posts'] ?? 0)); ?></strong>
                                    <small>sem views na janela</small>
                                </div>
                                <div class="dashboard-stat-card">
                                    <span>Views por post ativo</span>
                                    <strong><?= number_format((float) ($contentSummary['avg_views_per_active_post'] ?? 0), 2, ',', '.'); ?></strong>
                                    <small>intensidade média de consumo</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'device_analytics': ?>
                    <div class="box dashboard-widget" id="device-analytics-widget">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa <?= esc($widget['icon']); ?>"></i> <?= esc($widget['name']); ?></h3>
                            <span class="dashboard-widget-description"><?= esc($widget['description']); ?></span>
                            <?php if (!empty($deviceAnalytics['tracked'])): ?>
                                <span class="dashboard-coverage">Cobertura rastreada: <?= number_format((float) ($deviceAnalytics['coverage_pct'] ?? 0), 2, ',', '.'); ?>%</span>
                            <?php endif; ?>
                        </div>
                        <div class="box-body">
                            <?php if (!empty($deviceAnalytics['tracked']) && (!empty($deviceAnalytics['devices']) || !empty($deviceAnalytics['browsers']))): ?>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="dashboard-chart-shell dashboard-chart-shell-sm">
                                            <canvas id="deviceChart" height="180"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="dashboard-chart-shell dashboard-chart-shell-sm">
                                            <canvas id="browserChart" height="180"></canvas>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="dashboard-empty">
                                    <i class="fa fa-desktop"></i>
                                    <p>Os recortes de dispositivo e navegador começam a aparecer quando as novas visitas já entram com tracking detalhado.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php break;

                case 'conversions': ?>
                    <?php $conversions = $conversions ?? ['total_leads' => 0, 'total_contacts' => 0, 'total_conversions' => 0, 'converted_leads' => 0, 'conversion_rate' => 0, 'funnel' => [], 'leads_by_source' => [], 'daily_leads' => [], 'daily_contacts' => [], 'recent_leads' => []]; ?>
                    <?php $conversions['leads_by_source'] = $conversions['leads_by_source'] ?? []; ?>
                    <div class="box dashboard-widget" id="conversions-widget">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa <?= esc($widget['icon']); ?>"></i> <?= esc($widget['name']); ?></h3>
                            <span class="dashboard-widget-description"><?= esc($widget['description']); ?></span>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-6 col-md-12">
                                    <div class="dashboard-stat-grid">
                                        <div class="dashboard-stat-card">
                                            <span>Leads simulador</span>
                                            <strong><?= number_format((int) $conversions['total_leads']); ?></strong>
                                            <small>captados via simuladores no período</small>
                                        </div>
                                        <div class="dashboard-stat-card">
                                            <span>Contatos recebidos</span>
                                            <strong><?= number_format((int) $conversions['total_contacts']); ?></strong>
                                            <small>formulários de contato no período</small>
                                        </div>
                                        <div class="dashboard-stat-card">
                                            <span>Total conversões</span>
                                            <strong><?= number_format((int) $conversions['total_conversions']); ?></strong>
                                            <small>leads + contatos combinados</small>
                                        </div>
                                        <div class="dashboard-stat-card">
                                            <span>Taxa de conversão</span>
                                            <strong><?= number_format((float) $conversions['conversion_rate'], 2, ',', '.'); ?>%</strong>
                                            <small><?= number_format((int) $conversions['converted_leads']); ?> leads convertidos</small>
                                        </div>
                                    </div>
                                    <div class="dashboard-chart-shell dashboard-chart-shell-sm" style="margin-top:15px">
                                        <canvas id="conversionsChart" height="160"></canvas>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <h5 style="margin-bottom:12px">Funil de leads</h5>
                                    <?php if (!empty($conversions['funnel'])): ?>
                                        <?php
                                        $funnelColors = [
                                            'new' => '#0ea5e9',
                                            'contacted' => '#8b5cf6',
                                            'qualified' => '#f59e0b',
                                            'proposal' => '#1f78ff',
                                            'converted' => '#11a683',
                                            'lost' => '#ef4444',
                                        ];
                                        $maxFunnel = max(1, max(array_column($conversions['funnel'], 'count')));
                                        ?>
                                        <?php foreach ($conversions['funnel'] as $step): ?>
                                            <?php if ((int) $step['count'] > 0): ?>
                                                <div style="margin-bottom:8px">
                                                    <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:2px">
                                                        <span><?= esc($step['label']); ?></span>
                                                        <strong><?= number_format((int) $step['count']); ?></strong>
                                                    </div>
                                                    <div style="background:#f1f5f9;border-radius:4px;height:8px;overflow:hidden">
                                                        <div style="height:100%;border-radius:4px;width:<?= round(((int) $step['count'] / $maxFunnel) * 100); ?>%;background:<?= $funnelColors[$step['status']] ?? '#64748b'; ?>"></div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="dashboard-empty dashboard-empty-inline">
                                            <p>Nenhum lead no período.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <h5 style="margin-bottom:12px">Últimos leads</h5>
                                    <?php if (!empty($conversions['recent_leads'])): ?>
                                        <?php
                                        $statusBadgeClass = [
                                            'new' => 'dashboard-badge-blue',
                                            'contacted' => 'dashboard-badge-purple',
                                            'qualified' => 'dashboard-badge-gold',
                                            'proposal' => 'dashboard-badge-blue',
                                            'converted' => 'dashboard-badge-green',
                                            'lost' => 'dashboard-badge-red',
                                        ];
                                        $statusLabel = [
                                            'new' => 'Novo',
                                            'contacted' => 'Contactado',
                                            'qualified' => 'Qualificado',
                                            'proposal' => 'Proposta',
                                            'converted' => 'Convertido',
                                            'lost' => 'Perdido',
                                        ];
                                        ?>
                                        <?php foreach ($conversions['recent_leads'] as $lead): ?>
                                            <div class="dashboard-list-row">
                                                <div>
                                                    <strong><?= esc(characterLimiter($lead->name ?? '', 25, '...')); ?></strong>
                                                    <small><?= esc(date('d/m H:i', strtotime($lead->created_at))); ?><?php if (!empty($lead->origem)): ?> · <?= esc(characterLimiter($lead->origem, 34, '...')); ?><?php endif; ?></small>
                                                </div>
                                                <span class="dashboard-badge <?= $statusBadgeClass[$lead->status] ?? 'dashboard-badge-blue'; ?>">
                                                    <?= esc($statusLabel[$lead->status] ?? $lead->status); ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="dashboard-empty dashboard-empty-inline">
                                            <p>Nenhum lead recente.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row" style="margin-top:20px">
                                <div class="col-sm-12">
                                    <h5 style="margin-bottom:12px"><i class="fa fa-compass"></i> Leads por origem <small class="text-muted">— de onde vêm os leads dos simuladores</small></h5>
                                    <?php if (!empty($conversions['leads_by_source'])): ?>
                                        <?php $maxSource = max(1, max(array_column($conversions['leads_by_source'], 'count'))); ?>
                                        <div class="lead-source-list">
                                            <?php foreach ($conversions['leads_by_source'] as $source): ?>
                                                <div class="lead-source-row">
                                                    <div class="lead-source-head">
                                                        <span class="lead-source-name" title="<?= esc($source['origem']); ?>"><?= esc($source['origem']); ?></span>
                                                        <span class="lead-source-count"><strong><?= number_format((int) $source['count']); ?></strong> <small><?= number_format((float) $source['percentage'], 1, ',', '.'); ?>%</small></span>
                                                    </div>
                                                    <div class="lead-source-track">
                                                        <div class="lead-source-bar" style="width:<?= round(((int) $source['count'] / $maxSource) * 100); ?>%"></div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="dashboard-empty dashboard-empty-inline">
                                            <p>Nenhum lead com origem registrada no período. Novos leads dos simuladores passam a aparecer aqui automaticamente.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'real_time': ?>
                    <div class="box dashboard-widget" id="realtime-widget">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa <?= esc($widget['icon']); ?>"></i> <?= esc($widget['name']); ?></h3>
                            <span class="dashboard-widget-description"><?= esc($widget['description']); ?></span>
                            <span class="dashboard-live-badge" id="live-indicator">LIVE</span>
                        </div>
                        <div class="box-body">
                            <div class="dashboard-stat-grid">
                                <div class="dashboard-stat-card">
                                    <span>IPs ativos 30 min</span>
                                    <strong id="current-visitors"><?= number_format((int) ($realTime['current_visitors'] ?? 0)); ?></strong>
                                    <small>presença imediata</small>
                                </div>
                                <div class="dashboard-stat-card">
                                    <span>Views 1h</span>
                                    <strong id="views-last-hour"><?= number_format((int) ($realTime['views_last_hour'] ?? 0)); ?></strong>
                                    <small>ritmo recente</small>
                                </div>
                                <div class="dashboard-stat-card">
                                    <span>Views 24h</span>
                                    <strong id="current-views"><?= number_format((int) ($realTime['current_views'] ?? 0)); ?></strong>
                                    <small>janela operacional</small>
                                </div>
                                <div class="dashboard-stat-card">
                                    <span>Último evento</span>
                                    <strong id="last-event-at"><?= !empty($realTime['last_event_at']) ? esc(date('d/m H:i', strtotime($realTime['last_event_at']))) : 'N/D'; ?></strong>
                                    <small>atualização mais recente</small>
                                </div>
                            </div>

                            <div class="dashboard-list">
                                <h5>Páginas quentes nos últimos 30 minutos</h5>
                                <div id="top-pages-now">
                                    <?php if (!empty($realTime['top_pages_now'])): ?>
                                        <?php foreach ($realTime['top_pages_now'] as $page): ?>
                                            <div class="dashboard-list-row">
                                                <div>
                                                    <strong><?= esc(characterLimiter($page->title, 52, '...')); ?></strong>
                                                    <small><?= number_format((int) ($page->unique_visitors ?? 0)); ?> visitantes únicos</small>
                                                </div>
                                                <span><?= number_format((int) ($page->current_views ?? 0)); ?> views</span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="dashboard-empty dashboard-empty-inline">
                                            <i class="fa fa-bolt"></i>
                                            <p>Nenhuma página visitada nos últimos 30 minutos.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php break;
            endswitch; ?>
        </div>
    <?php endforeach; ?>
</div>

<script src="<?= base_url('assets/admin/plugins/chart/chart.min.js'); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var dashboardPayload = <?= json_encode($dashboardPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var numberFormatter = new Intl.NumberFormat('pt-BR');

    document.getElementById('days_filter').addEventListener('change', function () {
        window.location.href = '<?= adminUrl('dashboard'); ?>?days=' + this.value;
    });

    renderVisitorsChart();
    renderRetentionChart();
    renderTrafficChart();
    renderDeviceCharts();
    renderGaChannelsChart();
    renderConversionsChart();

    setInterval(updateRealTimeData, 30000);

    function renderVisitorsChart() {
        var canvas = document.getElementById('visitorsChart');
        if (!canvas) {
            return;
        }

        var rows = dashboardPayload.dailyVisitors || [];
        new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: rows.map(function (item) { return item.label; }),
                datasets: [{
                    label: 'Visitantes únicos',
                    data: rows.map(function (item) { return item.visitors; }),
                    borderColor: '#1f78ff',
                    backgroundColor: 'rgba(31, 120, 255, 0.12)',
                    borderWidth: 2,
                    pointRadius: 3,
                    fill: true,
                    lineTension: 0.25
                }, {
                    label: 'Pageviews',
                    data: rows.map(function (item) { return item.pageviews; }),
                    borderColor: '#11a683',
                    backgroundColor: 'rgba(17, 166, 131, 0.10)',
                    borderWidth: 2,
                    pointRadius: 3,
                    fill: true,
                    lineTension: 0.25
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    }

    function renderRetentionChart() {
        var canvas = document.getElementById('retentionChart');
        if (!canvas) {
            return;
        }

        var rows = dashboardPayload.visitorSegments || [];
        new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: rows.map(function (item) { return item.label; }),
                datasets: [{
                    label: 'Novos',
                    data: rows.map(function (item) { return item.new_visitors; }),
                    backgroundColor: '#4db6ac'
                }, {
                    label: 'Recorrentes',
                    data: rows.map(function (item) { return item.returning_visitors; }),
                    backgroundColor: '#1f78ff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                },
                scales: {
                    xAxes: [{
                        stacked: true,
                        gridLines: {
                            display: false
                        }
                    }],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    }

    function renderTrafficChart() {
        var canvas = document.getElementById('trafficChart');
        if (!canvas) {
            return;
        }

        var rows = dashboardPayload.trafficSources || [];
        if (!rows.length) {
            return;
        }

        new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: rows.map(function (item) { return item.source; }),
                datasets: [{
                    data: rows.map(function (item) { return item.visitors; }),
                    backgroundColor: ['#1f78ff', '#11a683', '#f59e0b', '#7c4dff', '#ef4444', '#64748b', '#0ea5e9', '#8b5cf6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                }
            }
        });
    }

    function renderDeviceCharts() {
        var deviceCanvas = document.getElementById('deviceChart');
        var browserCanvas = document.getElementById('browserChart');
        var devices = dashboardPayload.deviceAnalytics ? dashboardPayload.deviceAnalytics.devices || [] : [];
        var browsers = dashboardPayload.deviceAnalytics ? dashboardPayload.deviceAnalytics.browsers || [] : [];

        if (deviceCanvas && devices.length) {
            new Chart(deviceCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: devices.map(function (item) { return item.type; }),
                    datasets: [{
                        data: devices.map(function (item) { return item.count; }),
                        backgroundColor: ['#1f78ff', '#11a683', '#f59e0b', '#64748b']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position: 'bottom'
                    }
                }
            });
        }

        if (browserCanvas && browsers.length) {
            new Chart(browserCanvas.getContext('2d'), {
                type: 'horizontalBar',
                data: {
                    labels: browsers.map(function (item) { return item.name; }),
                    datasets: [{
                        data: browsers.map(function (item) { return item.count; }),
                        backgroundColor: '#1f78ff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                display: false
                            }
                        }]
                    }
                }
            });
        }
    }

    function renderGaChannelsChart() {
        var canvas = document.getElementById('gaChannelsChart');
        if (!canvas) {
            return;
        }

        var rows = dashboardPayload.ga4 ? dashboardPayload.ga4.channels || [] : [];
        if (!rows.length) {
            return;
        }

        new Chart(canvas.getContext('2d'), {
            type: 'horizontalBar',
            data: {
                labels: rows.map(function (item) { return item.channel; }),
                datasets: [{
                    data: rows.map(function (item) { return item.sessions; }),
                    backgroundColor: '#ea4335'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display: false
                        }
                    }]
                }
            }
        });
    }

    function renderConversionsChart() {
        var canvas = document.getElementById('conversionsChart');
        if (!canvas) {
            return;
        }

        var leads = dashboardPayload.conversions.daily_leads || [];
        var contacts = dashboardPayload.conversions.daily_contacts || [];
        new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: leads.map(function (item) { return item.label; }),
                datasets: [{
                    label: 'Leads simulador',
                    data: leads.map(function (item) { return item.leads; }),
                    backgroundColor: '#1f78ff'
                }, {
                    label: 'Contatos',
                    data: contacts.map(function (item) { return item.contacts; }),
                    backgroundColor: '#11a683'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { position: 'bottom' },
                scales: {
                    xAxes: [{ stacked: true, gridLines: { display: false } }],
                    yAxes: [{ stacked: true, ticks: { beginAtZero: true } }]
                }
            }
        });
    }

    function updateRealTimeData() {
        fetch('<?= adminUrl('dashboard/live-data'); ?>')
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Falha na resposta do endpoint');
                }
                return response.json();
            })
            .then(function (data) {
                if (!data || data.success === false || !data.realTime) {
                    throw new Error('Payload inválido');
                }

                updateText('current-visitors', numberFormatter.format(data.realTime.current_visitors || 0));
                updateText('views-last-hour', numberFormatter.format(data.realTime.views_last_hour || 0));
                updateText('current-views', numberFormatter.format(data.realTime.current_views || 0));
                updateText('last-event-at', data.realTime.last_event_at ? formatDateTime(data.realTime.last_event_at) : 'N/D');
                renderTopPages(data.realTime.top_pages_now || []);
                if (data.googleAnalytics && typeof data.googleAnalytics.active_users !== 'undefined') {
                    updateText('ga4-active-users', numberFormatter.format(data.googleAnalytics.active_users || 0));
                }

                var indicator = document.getElementById('live-indicator');
                if (indicator) {
                    indicator.classList.remove('is-warning');
                    indicator.removeAttribute('title');
                    indicator.classList.add('is-ping');
                    setTimeout(function () {
                        indicator.classList.remove('is-ping');
                    }, 350);
                }
            })
            .catch(function () {
                var indicator = document.getElementById('live-indicator');
                if (indicator) {
                    indicator.classList.add('is-warning');
                    indicator.title = 'Erro ao atualizar o widget em tempo real';
                }
            });
    }

    function renderTopPages(rows) {
        var container = document.getElementById('top-pages-now');
        if (!container) {
            return;
        }

        if (!rows.length) {
            container.innerHTML = '<div class="dashboard-empty dashboard-empty-inline"><i class="fa fa-bolt"></i><p>Nenhuma página visitada nas últimas 24 horas.</p></div>';
            return;
        }

        container.innerHTML = rows.map(function (page) {
            return '' +
                '<div class="dashboard-list-row">' +
                    '<div>' +
                        '<strong>' + escapeHtml(trimTitle(page.title || "", 52)) + '</strong>' +
                        '<small>' + numberFormatter.format(page.unique_visitors || 0) + ' visitantes únicos</small>' +
                    '</div>' +
                    '<span>' + numberFormatter.format(page.current_views || 0) + ' views</span>' +
                '</div>';
        }).join('');
    }

    function updateText(id, value) {
        var el = document.getElementById(id);
        if (el) {
            el.textContent = value;
        }
    }

    function trimTitle(text, limit) {
        if (!text || text.length <= limit) {
            return text;
        }
        return text.substring(0, limit - 3) + '...';
    }

    function formatDateTime(value) {
        var date = new Date(value.replace(' ', 'T'));
        if (isNaN(date.getTime())) {
            return value;
        }

        return date.toLocaleString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});
</script>

<style>
.dashboard-toolbar .box-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.dashboard-toolbar-copy {
    margin: 6px 0 0;
    color: #6b7280;
    font-size: 13px;
}

.dashboard-toolbar-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.dashboard-toolbar-select {
    min-width: 160px;
}

.dashboard-kpi-row {
    margin-bottom: 8px;
}

.dashboard-ga-kpis {
    margin-bottom: 18px;
}

.dashboard-kpi-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px 18px;
    min-height: 122px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    position: relative;
    overflow: hidden;
}

.dashboard-kpi-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
}

.dashboard-accent-blue::before { background: #1f78ff; }
.dashboard-accent-teal::before { background: #11a683; }
.dashboard-accent-indigo::before { background: #5b6bff; }
.dashboard-accent-gold::before { background: #d4a11d; }
.dashboard-accent-orange::before { background: #f97316; }
.dashboard-accent-red::before { background: #ef4444; }

.dashboard-kpi-label {
    display: block;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #6b7280;
    margin-bottom: 12px;
}

.dashboard-kpi-value {
    display: block;
    font-size: 30px;
    line-height: 1.1;
    color: #0f172a;
    margin-bottom: 10px;
}

.dashboard-kpi-note {
    display: block;
    color: #6b7280;
    font-size: 12px;
}

.dashboard-ga-box .box-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}

.dashboard-meta-chips {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 8px;
}

.dashboard-meta-chip {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    background: #f1f5f9;
    color: #334155;
    font-size: 12px;
    font-weight: 600;
}

.dashboard-widget {
    border-radius: 12px;
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
    border: 1px solid #e5e7eb;
}

.dashboard-widget .box-header {
    padding-bottom: 10px;
}

.dashboard-widget .box-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}

.dashboard-widget-description {
    display: block;
    margin-top: 6px;
    color: #6b7280;
    font-size: 12px;
    line-height: 1.5;
}

.dashboard-coverage {
    display: inline-block;
    margin-top: 10px;
    padding: 4px 10px;
    border-radius: 999px;
    background: #eef6ff;
    color: #1f78ff;
    font-size: 11px;
    font-weight: 600;
}

.dashboard-live-badge {
    float: right;
    padding: 5px 10px;
    border-radius: 999px;
    background: #dcfce7;
    color: #166534;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .05em;
}

.dashboard-live-badge.is-ping {
    opacity: .55;
}

.dashboard-live-badge.is-warning {
    background: #fef3c7;
    color: #92400e;
}

.dashboard-chart-shell {
    position: relative;
    min-height: 280px;
}

.dashboard-chart-shell-sm {
    min-height: 220px;
}

.dashboard-inline-metrics {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-top: 16px;
}

.dashboard-inline-metrics div,
.dashboard-stat-card {
    padding: 14px;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
}

.dashboard-inline-metrics strong,
.dashboard-stat-card strong {
    display: block;
    font-size: 24px;
    color: #0f172a;
}

.dashboard-inline-metrics span,
.dashboard-stat-card span {
    display: block;
    color: #475569;
    font-size: 12px;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.dashboard-inline-metrics small,
.dashboard-stat-card small {
    color: #6b7280;
    font-size: 12px;
}

.dashboard-stat-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

.dashboard-table th {
    border-top: none !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #64748b;
}

.dashboard-table td {
    vertical-align: middle !important;
    font-size: 13px;
}

.dashboard-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
}

.dashboard-badge-blue {
    background: #dbeafe;
    color: #1d4ed8;
}

.dashboard-badge-green {
    background: #dcfce7;
    color: #166534;
}

.dashboard-badge-gold {
    background: #fef3c7;
    color: #92400e;
}

.dashboard-source-list,
.dashboard-list {
    display: grid;
    gap: 10px;
}

.dashboard-source-row,
.dashboard-list-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 14px;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
}

.dashboard-source-row strong,
.dashboard-list-row strong {
    display: block;
    color: #0f172a;
}

.dashboard-source-row small,
.dashboard-list-row small {
    display: block;
    color: #64748b;
    margin-top: 4px;
}

.dashboard-source-row span,
.dashboard-list-row span {
    color: #0f172a;
    font-weight: 700;
    white-space: nowrap;
}

.dashboard-empty {
    min-height: 160px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-align: center;
    color: #6b7280;
}

.dashboard-empty i {
    font-size: 24px;
    color: #94a3b8;
}

.dashboard-empty-inline {
    min-height: auto;
    padding: 12px 0;
}

@media (max-width: 1199px) {
    .dashboard-kpi-row > div {
        margin-bottom: 15px;
    }
}

@media (max-width: 991px) {
    .dashboard-toolbar .box-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .dashboard-toolbar-actions {
        width: 100%;
        flex-wrap: wrap;
    }

    .dashboard-inline-metrics,
    .dashboard-stat-grid {
        grid-template-columns: 1fr;
    }

    .dashboard-ga-box .box-header {
        flex-direction: column;
    }

    .dashboard-meta-chips {
        justify-content: flex-start;
    }
}

.lead-source-list {
    display: grid;
    gap: 12px;
}
.lead-source-row {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 10px 14px;
}
.lead-source-head {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 6px;
}
.lead-source-name {
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.lead-source-count {
    flex: none;
    font-size: 13px;
    color: #334155;
}
.lead-source-count small {
    color: #94a3b8;
    margin-left: 4px;
}
.lead-source-track {
    background: #eef2f7;
    border-radius: 4px;
    height: 8px;
    overflow: hidden;
}
.lead-source-bar {
    height: 100%;
    border-radius: 4px;
    background: linear-gradient(90deg, #d4a11d, #b8860b);
}
</style>
