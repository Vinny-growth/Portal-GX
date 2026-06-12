<?php
$keyword = $keyword ?? null;
$series  = $series ?? [];

$labels = [];
$positions = [];
$clicks = [];
foreach ($series as $row) {
    $labels[]    = date('d/m', strtotime($row->checked_date));
    $positions[] = $row->position !== null ? round((float) $row->position, 1) : null;
    $clicks[]    = (int) $row->clicks;
}
$reversed = array_reverse($series);
?>

<div class="row">
    <div class="col-sm-8">
        <h3 style="margin-top:5px;"><i class="fa fa-key"></i> <?= esc($keyword->keyword ?? ''); ?></h3>
        <?php if (!empty($keyword->target_url)): ?>
            <p class="text-muted" style="margin:0;">URL alvo: <code><?= esc($keyword->target_url); ?></code></p>
        <?php endif; ?>
    </div>
    <div class="col-sm-4 text-right">
        <a href="<?= adminUrl('seo-analysis/keywords'); ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Palavras-chave</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-line-chart"></i> Evolução da posição</h3></div>
            <div class="box-body">
                <?php if (array_filter($positions, function ($p) { return $p !== null; })): ?>
                    <canvas id="kwChart" height="120"></canvas>
                    <p class="text-muted" style="margin-top:8px;font-size:12px;">Eixo invertido — mais alto no gráfico = melhor posição.</p>
                <?php else: ?>
                    <p class="text-muted text-center" style="padding:40px;">Sem coletas registradas para esta palavra-chave ainda.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box box-default">
            <div class="box-header with-border"><h3 class="box-title">Resumo</h3></div>
            <div class="box-body">
                <table class="table table-condensed" style="font-size:13px;">
                    <tr><td>Posição atual</td><td class="text-right"><strong><?= $keyword->last_position !== null ? number_format((float) $keyword->last_position, 1, ',', '.') : '—'; ?></strong></td></tr>
                    <tr><td>Última coleta</td><td class="text-right"><?= $keyword->last_checked_at ? date('d/m/Y H:i', strtotime($keyword->last_checked_at)) : '—'; ?></td></tr>
                    <tr><td>Locale</td><td class="text-right"><?= esc($keyword->locale); ?></td></tr>
                    <tr><td>Dispositivo</td><td class="text-right"><?= esc($keyword->device); ?></td></tr>
                    <tr><td>Status</td><td class="text-right"><span class="label label-<?= $keyword->is_active ? 'success' : 'default'; ?>"><?= $keyword->is_active ? 'Ativa' : 'Pausada'; ?></span></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-default">
            <div class="box-header with-border"><h3 class="box-title">Histórico de coletas</h3></div>
            <div class="box-body">
                <?php if (!empty($reversed)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" style="font-size:13px;">
                            <thead>
                            <tr><th>Data</th><th class="text-center">Posição</th><th class="text-center">Cliques</th><th class="text-center">Impressões</th><th class="text-center">CTR</th><th class="text-center">Fonte</th><th>URL encontrada</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($reversed as $row): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($row->checked_date)); ?></td>
                                    <td class="text-center"><?= $row->position !== null ? number_format((float) $row->position, 1, ',', '.') : '—'; ?></td>
                                    <td class="text-center"><?= number_format((int) $row->clicks, 0, ',', '.'); ?></td>
                                    <td class="text-center"><?= number_format((int) $row->impressions, 0, ',', '.'); ?></td>
                                    <td class="text-center"><?= number_format((float) $row->ctr, 1, ',', '.'); ?>%</td>
                                    <td class="text-center"><span class="label label-<?= $row->source === 'serp' ? 'warning' : 'info'; ?>"><?= esc(strtoupper($row->source)); ?></span></td>
                                    <td><small class="text-muted"><?= esc($row->url_found ?? '—'); ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center" style="padding:30px;">Nenhuma coleta ainda.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/admin/plugins/chart/chart.min.js'); ?>"></script>
<script>
(function () {
    var canvas = document.getElementById('kwChart');
    if (!canvas || typeof Chart === 'undefined') return;
    new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= json_encode($labels); ?>,
            datasets: [{
                label: 'Posição',
                data: <?= json_encode($positions); ?>,
                borderColor: '#11a683',
                backgroundColor: 'rgba(17,166,131,0.12)',
                borderWidth: 2,
                pointRadius: 3,
                fill: true,
                lineTension: 0.25,
                spanGaps: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            legend: { display: false },
            scales: { yAxes: [{ ticks: { reverse: true, beginAtZero: false, precision: 0 } }] }
        }
    });
})();
</script>
