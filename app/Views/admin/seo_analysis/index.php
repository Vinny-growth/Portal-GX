<?php
$overview  = $overview ?? [];
$evolution = $evolution ?? [];
$providers = $providers ?? ['gsc' => false, 'serp' => false, 'gsc_site' => ''];
$topMovers = $topMovers ?? [];
$days      = $days ?? 30;

$avg = $overview['avg_position'] ?? null;

// Evolution chart series (average tracked position per day — lower is better)
$chartLabels = [];
$chartPositions = [];
foreach ($evolution as $row) {
    $chartLabels[]    = date('d/m', strtotime($row->checked_date));
    $chartPositions[] = round((float) $row->avg_position, 1);
}

// Movers: only keywords that have a measured delta, biggest absolute change first
$movers = array_filter($topMovers, function ($k) {
    return isset($k->delta) && $k->delta !== null;
});
usort($movers, function ($a, $b) {
    return abs($b->delta) <=> abs($a->delta);
});
$movers = array_slice($movers, 0, 10);
?>

<div class="row">
    <div class="col-sm-8">
        <h3 style="margin-top:5px;"><i class="fa fa-search"></i> Análise de SEO <small>posicionamento de palavras-chave</small></h3>
    </div>
    <div class="col-sm-4 text-right">
        <form action="<?= adminUrl('seo-analysis/fetch-now'); ?>" method="post" style="display:inline;"
              onsubmit="return confirm('Coletar agora os rankings das palavras-chave ativas?');">
            <?= csrf_field(); ?>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i> Coletar agora</button>
        </form>
        <a href="<?= adminUrl('seo-analysis/keywords'); ?>" class="btn btn-default btn-sm"><i class="fa fa-key"></i> Palavras-chave</a>
    </div>
</div>

<!-- Integration status -->
<div class="row">
    <div class="col-sm-12">
        <?php if (!$providers['gsc'] && !$providers['serp']): ?>
            <div class="callout callout-warning">
                <h4><i class="fa fa-plug"></i> Conecte uma fonte de dados para começar</h4>
                <p>Nenhuma integração está configurada. Para ver posições reais, defina no <code>.env</code>:</p>
                <ul style="margin-bottom:0;">
                    <li><strong>Google Search Console</strong> (recomendado, grátis): <code>GSC_SITE_URL</code> e <code>GSC_SERVICE_ACCOUNT_JSON</code> — a conta de serviço precisa ser adicionada como usuário da propriedade no Search Console.</li>
                    <li><strong>openserp</strong> (opcional, concorrentes/keywords fora do GSC): <code>OPENSERP_ENABLED=true</code> e <code>OPENSERP_URL</code>.</li>
                </ul>
            </div>
        <?php else: ?>
            <div class="callout callout-info" style="padding:10px 15px;">
                <strong>Integrações:</strong>
                <span class="label <?= $providers['gsc'] ? 'label-success' : 'label-default'; ?>">
                    Google Search Console <?= $providers['gsc'] ? 'ON' : 'off'; ?></span>
                <?php if (!empty($providers['gsc_site'])): ?>
                    <small class="text-muted"><?= esc($providers['gsc_site']); ?></small>
                <?php endif; ?>
                &nbsp;
                <span class="label <?= $providers['serp'] ? 'label-success' : 'label-default'; ?>">
                    openserp <?= $providers['serp'] ? 'ON' : 'off'; ?></span>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- KPI cards -->
<div class="row">
    <div class="col-lg-2 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?= (int) ($totalKeywords ?? 0); ?></h3>
                <p>Palavras monitoradas<?php if (!empty($overview['with_position'])): ?> <small>(<?= (int) $overview['with_position']; ?> com posição)</small><?php endif; ?></p>
            </div>
            <div class="icon"><i class="fa fa-key"></i></div>
        </div>
    </div>
    <div class="col-lg-2 col-xs-6">
        <div class="small-box bg-blue">
            <div class="inner"><h3><?= $avg !== null ? number_format($avg, 1, ',', '.') : '—'; ?></h3><p>Posição média</p></div>
            <div class="icon"><i class="fa fa-line-chart"></i></div>
        </div>
    </div>
    <div class="col-lg-2 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner"><h3><?= (int) ($overview['top3'] ?? 0); ?></h3><p>No Top 3</p></div>
            <div class="icon"><i class="fa fa-trophy"></i></div>
        </div>
    </div>
    <div class="col-lg-2 col-xs-6">
        <div class="small-box bg-teal">
            <div class="inner"><h3><?= (int) ($overview['top10'] ?? 0); ?></h3><p>No Top 10</p></div>
            <div class="icon"><i class="fa fa-star"></i></div>
        </div>
    </div>
    <div class="col-lg-2 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner"><h3><?= number_format((int) ($overview['clicks'] ?? 0), 0, ',', '.'); ?></h3><p>Cliques (período GSC)</p></div>
            <div class="icon"><i class="fa fa-mouse-pointer"></i></div>
        </div>
    </div>
    <div class="col-lg-2 col-xs-6">
        <div class="small-box bg-purple">
            <div class="inner"><h3><?= number_format((int) ($overview['impressions'] ?? 0), 0, ',', '.'); ?></h3><p>Impressões</p></div>
            <div class="icon"><i class="fa fa-eye"></i></div>
        </div>
    </div>
</div>

<!-- Evolution chart -->
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-area-chart"></i> Evolução da posição média</h3>
                <div class="box-tools pull-right">
                    <div class="btn-group" id="table_dropdown">
                        <?php foreach ([7, 30, 90] as $d): ?>
                            <a href="<?= adminUrl('seo-analysis'); ?>?days=<?= $d; ?>"
                               class="btn btn-xs <?= $days === $d ? 'btn-primary' : 'btn-default'; ?>"><?= $d; ?>d</a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <?php if (!empty($chartPositions)): ?>
                    <canvas id="seoPositionChart" height="90"></canvas>
                    <p class="text-muted" style="margin-top:8px;font-size:12px;">
                        Eixo invertido: quanto <strong>mais alto</strong> no gráfico, <strong>melhor</strong> a posição (1º lugar no topo).
                    </p>
                <?php else: ?>
                    <p class="text-muted text-center" style="padding:40px;">
                        Sem dados de ranking ainda. Cadastre palavras-chave, configure uma integração e clique em <strong>Coletar agora</strong>.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Movers -->
<div class="row">
    <div class="col-sm-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-exchange"></i> Maiores variações (7 dias)</h3>
            </div>
            <div class="box-body">
                <?php if (!empty($movers)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" style="font-size:13px;">
                            <thead>
                            <tr>
                                <th>Palavra-chave</th>
                                <th class="text-center">Posição atual</th>
                                <th class="text-center">Variação (7d)</th>
                                <th class="text-center">Cliques</th>
                                <th class="text-center">Impressões</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($movers as $k): ?>
                                <tr>
                                    <td><a href="<?= adminUrl('seo-analysis/keyword/' . $k->id); ?>"><?= esc($k->keyword); ?></a></td>
                                    <td class="text-center"><?= $k->position !== null ? number_format((float) $k->position, 1, ',', '.') : '—'; ?></td>
                                    <td class="text-center">
                                        <?php if ($k->delta > 0): ?>
                                            <span class="text-green"><i class="fa fa-arrow-up"></i> <?= number_format($k->delta, 1, ',', '.'); ?></span>
                                        <?php elseif ($k->delta < 0): ?>
                                            <span class="text-red"><i class="fa fa-arrow-down"></i> <?= number_format(abs($k->delta), 1, ',', '.'); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= number_format((int) $k->clicks, 0, ',', '.'); ?></td>
                                    <td class="text-center"><?= number_format((int) $k->impressions, 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center" style="padding:30px;">
                        Ainda sem histórico suficiente para calcular variações. As variações aparecem após algumas coletas.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/admin/plugins/chart/chart.min.js'); ?>"></script>
<script>
(function () {
    var canvas = document.getElementById('seoPositionChart');
    if (!canvas || typeof Chart === 'undefined') return;
    new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels); ?>,
            datasets: [{
                label: 'Posição média',
                data: <?= json_encode($chartPositions); ?>,
                borderColor: '#1f78ff',
                backgroundColor: 'rgba(31,120,255,0.12)',
                borderWidth: 2,
                pointRadius: 3,
                fill: true,
                lineTension: 0.25
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            legend: { display: false },
            scales: {
                yAxes: [{
                    ticks: { reverse: true, beginAtZero: false, precision: 0 },
                    scaleLabel: { display: true, labelString: 'Posição no Google (menor = melhor)' }
                }]
            }
        }
    });
})();
</script>
