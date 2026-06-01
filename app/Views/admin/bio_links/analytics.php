<?= view('admin/includes/_header'); ?>
<div class="row">
    <div class="col-sm-12">
        <?= view('admin/includes/_messages'); ?>
    </div>
</div>

<?php
$clicksByDay = $clicksByDay ?? [];
$clicksByLink = $clicksByLink ?? [];
$clicksByLinkDay = $clicksByLinkDay ?? [];
$recentClicks = $recentClicks ?? [];
$analytics = $analytics ?? ['total_clicks' => 0, 'unique_ips' => 0, 'top_referrers' => []];
$stats = $stats ?? ['total_links' => 0, 'active_links' => 0, 'total_clicks' => 0];

// Build per-link daily series for chart
$linkTitles = [];
foreach ($clicksByLink as $row) {
    $linkTitles[$row['link_id']] = $row['title'] ?? ('Link #' . $row['link_id']);
}
$dailyByLink = [];
foreach ($clicksByLinkDay as $row) {
    $dailyByLink[$row['link_id']][$row['day']] = (int) $row['clicks'];
}

// Build complete date range
$dateLabels = [];
for ($i = $days - 1; $i >= 0; $i--) {
    $dateLabels[] = date('Y-m-d', strtotime("-{$i} days"));
}

$chartColors = ['#1f78ff', '#11a683', '#f59e0b', '#7c4dff', '#ef4444', '#0ea5e9', '#8b5cf6', '#f97316', '#14b8a6', '#e11d48'];
$datasets = [];
$colorIdx = 0;
foreach ($linkTitles as $linkId => $title) {
    $data = [];
    foreach ($dateLabels as $d) {
        $data[] = $dailyByLink[$linkId][$d] ?? 0;
    }
    $color = $chartColors[$colorIdx % count($chartColors)];
    $datasets[] = [
        'label' => $title,
        'data' => $data,
        'borderColor' => $color,
        'backgroundColor' => $color . '20',
        'borderWidth' => 2,
        'pointRadius' => 3,
        'fill' => false,
        'lineTension' => 0.25,
    ];
    $colorIdx++;
}

$chartLabels = array_map(function ($d) {
    return date('d/m', strtotime($d));
}, $dateLabels);

$chartPayload = [
    'labels' => $chartLabels,
    'datasets' => $datasets,
    'totals' => array_map(function ($d) use ($clicksByDay) {
        foreach ($clicksByDay as $row) {
            if ($row['day'] === $d) return (int) $row['clicks'];
        }
        return 0;
    }, $dateLabels),
];
?>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><i class="fa fa-bar-chart"></i> Analytics - Bio Links</h3>
                </div>
                <div class="right" style="display:flex;align-items:center;gap:10px;">
                    <select id="days_filter" class="form-control" style="width:auto;">
                        <?php foreach ([7, 30, 60, 90] as $w): ?>
                            <option value="<?= $w; ?>" <?= $days === $w ? 'selected' : ''; ?>>
                                Ultimos <?= $w; ?> dias
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <a href="<?= adminUrl('bio-links'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <div class="box-body">
                <!-- KPI Cards -->
                <div class="row" style="margin-bottom:20px;">
                    <div class="col-lg-3 col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-mouse-pointer"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Cliques no Periodo</span>
                                <span class="info-box-number"><?= number_format($analytics['total_clicks']); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">IPs Unicos</span>
                                <span class="info-box-number"><?= number_format($analytics['unique_ips']); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-yellow"><i class="fa fa-link"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Links Ativos</span>
                                <span class="info-box-number"><?= number_format($stats['active_links']); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-red"><i class="fa fa-area-chart"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Cliques Totais (historico)</span>
                                <span class="info-box-number"><?= number_format($stats['total_clicks']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clicks Chart -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h4 class="box-title"><i class="fa fa-line-chart"></i> Cliques por Dia</h4>
                            </div>
                            <div class="box-body">
                                <div style="position:relative;min-height:300px;">
                                    <canvas id="bioClicksChart" height="120"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Per-link Performance -->
                    <div class="col-lg-7 col-md-12">
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h4 class="box-title"><i class="fa fa-trophy"></i> Performance por Link</h4>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($clicksByLink)): ?>
                                    <?php
                                    $maxClicks = max(1, max(array_column($clicksByLink, 'clicks')));
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                            <tr>
                                                <th>Link</th>
                                                <th>Cliques</th>
                                                <th style="width:40%;">Proporção</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($clicksByLink as $idx => $row): ?>
                                                <tr>
                                                    <td>
                                                        <?php if (!empty($row['icon'])): ?>
                                                            <i class="<?= esc($row['icon']); ?>" style="margin-right:6px;"></i>
                                                        <?php endif; ?>
                                                        <strong><?= esc($row['title'] ?? 'Link #' . $row['link_id']); ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-blue"><?= number_format((int) $row['clicks']); ?></span>
                                                    </td>
                                                    <td>
                                                        <div style="background:#f1f5f9;border-radius:4px;height:10px;overflow:hidden;">
                                                            <div style="height:100%;border-radius:4px;width:<?= round(((int) $row['clicks'] / $maxClicks) * 100); ?>%;background:<?= $chartColors[$idx % count($chartColors)]; ?>;"></div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center" style="padding:30px;">Nenhum clique registrado no periodo.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Top Referrers -->
                    <div class="col-lg-5 col-md-12">
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h4 class="box-title"><i class="fa fa-compass"></i> Origem dos Cliques</h4>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($analytics['top_referrers'])): ?>
                                    <?php
                                    $maxRef = max(1, max(array_column($analytics['top_referrers'], 'clicks')));
                                    ?>
                                    <?php foreach ($analytics['top_referrers'] as $ref): ?>
                                        <div style="margin-bottom:10px;">
                                            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:3px;">
                                                <span><?= esc(mb_substr($ref['source'], 0, 60)); ?></span>
                                                <strong><?= number_format((int) $ref['clicks']); ?></strong>
                                            </div>
                                            <div style="background:#f1f5f9;border-radius:4px;height:8px;overflow:hidden;">
                                                <div style="height:100%;border-radius:4px;width:<?= round(((int) $ref['clicks'] / $maxRef) * 100); ?>%;background:#1f78ff;"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted text-center" style="padding:30px;">Sem dados de referrer no periodo.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Clicks -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h4 class="box-title"><i class="fa fa-clock-o"></i> Cliques Recentes</h4>
                            </div>
                            <div class="box-body">
                                <?php if (!empty($recentClicks)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" style="font-size:13px;">
                                            <thead>
                                            <tr>
                                                <th>Data/Hora</th>
                                                <th>Link</th>
                                                <th>IP</th>
                                                <th>Referrer</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($recentClicks as $click): ?>
                                                <tr>
                                                    <td><?= date('d/m/Y H:i', strtotime($click['clicked_at'])); ?></td>
                                                    <td>
                                                        <strong><?= esc($click['title'] ?? 'N/A'); ?></strong>
                                                    </td>
                                                    <td><code><?= esc($click['ip_address'] ?? '-'); ?></code></td>
                                                    <td><?= esc(mb_substr($click['referrer'] ?? 'Direto', 0, 80)); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center" style="padding:30px;">Nenhum clique registrado ainda. Os dados aparecerao conforme visitantes clicam nos links da pagina /bio.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/admin/plugins/chart/chart.min.js'); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Days filter
    document.getElementById('days_filter').addEventListener('change', function () {
        window.location.href = '<?= adminUrl('bio-links/analytics'); ?>?days=' + this.value;
    });

    // Main chart
    var chartPayload = <?= json_encode($chartPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var canvas = document.getElementById('bioClicksChart');
    if (!canvas || !chartPayload.labels.length) return;

    // Add totals dataset
    var allDatasets = [{
        label: 'Total',
        data: chartPayload.totals,
        borderColor: '#94a3b8',
        backgroundColor: 'rgba(148, 163, 184, 0.10)',
        borderWidth: 2,
        borderDash: [5, 5],
        pointRadius: 2,
        fill: true,
        lineTension: 0.25
    }].concat(chartPayload.datasets);

    new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels: chartPayload.labels,
            datasets: allDatasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { position: 'bottom' },
            tooltips: { mode: 'index', intersect: false },
            hover: { mode: 'nearest', intersect: true },
            scales: {
                xAxes: [{ gridLines: { display: false } }],
                yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }]
            }
        }
    });
});
</script>

<?= view('admin/includes/_footer'); ?>
