<?php
$sim = $sim ?? ['simulators' => [], 'dates' => [], 'ga4_used' => false, 'ga4_available' => false, 'ga4_error' => null, 'today' => date('Y-m-d'), 'days' => 30, 'has_leads' => false];
$days = (int) ($days ?? 30);
$availableWindows = $availableWindows ?? [7, 30, 90, 365];
$simulators = $sim['simulators'] ?? [];
$dates = $sim['dates'] ?? [];
$ga4Used = !empty($sim['ga4_used']);
$today = $sim['today'] ?? date('Y-m-d');

$fmtTime = static function ($s) {
    $s = (int) $s;
    if ($s <= 0) {
        return '—';
    }
    if ($s < 60) {
        return $s . 's';
    }
    return intdiv($s, 60) . 'm ' . str_pad((string) ($s % 60), 2, '0', STR_PAD_LEFT) . 's';
};

$totV = 0; $totL = 0; $totVToday = 0; $totLToday = 0;
foreach ($simulators as $s) {
    $totV += $s['period']['visitors'];
    $totL += $s['period']['leads'];
    $totVToday += $s['today']['visitors'];
    $totLToday += $s['today']['leads'];
}
$convPeriod = $totV > 0 ? round($totL / $totV * 100, 1) : 0;
$convToday = $totVToday > 0 ? round($totLToday / $totVToday * 100, 1) : 0;
?>

<style>
.gx-sim-wrap{margin-bottom:18px;}
.gx-sim-note{margin:0 0 16px;font-size:12px;line-height:1.55;color:#6b7280;}
.gx-sim-warn{padding:14px 16px;border:1px solid #f6ddb0;background:#fef7ea;border-radius:8px;color:#8a5a08;font-size:13px;line-height:1.55;margin-bottom:16px;}
.gx-sim-table{width:100%;border-collapse:collapse;font-size:13px;}
.gx-sim-table th,.gx-sim-table td{padding:9px 10px;border-bottom:1px solid #eef2f6;text-align:right;white-space:nowrap;}
.gx-sim-table th{font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:#6b7280;font-weight:700;background:#fafbfc;}
.gx-sim-table td.sim-name,.gx-sim-table th.sim-name{text-align:left;font-weight:600;color:#0a1a3a;}
.gx-sim-table tr:hover td{background:#fafbfc;}
.gx-sim-table .grp{border-left:1px solid #e6ebf1;}
.gx-sim-table tfoot td{font-weight:700;color:#0a1a3a;border-top:2px solid #e6ebf1;background:#fafbfc;}
.gx-pill{display:inline-block;padding:1px 8px;border-radius:10px;font-size:11px;font-weight:700;}
.gx-pill-good{background:#e7f8ef;color:#047857;}
.gx-pill-mid{background:#fef3e2;color:#b45309;}
.gx-pill-zero{background:#eef2f6;color:#94a3b8;}
.gx-sim-scroll{overflow-x:auto;}
.gx-matrix{width:100%;border-collapse:collapse;font-size:12px;}
.gx-matrix th,.gx-matrix td{padding:7px 9px;border-bottom:1px solid #eef2f6;text-align:right;white-space:nowrap;}
.gx-matrix th{font-size:11px;color:#6b7280;font-weight:700;background:#fafbfc;position:sticky;top:0;}
.gx-matrix td.d,.gx-matrix th.d{text-align:left;font-weight:600;color:#0a1a3a;}
.gx-matrix .z{color:#cbd5e1;}
.gx-matrix tr:hover td{background:#fafbfc;}
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="box dashboard-toolbar">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= esc($title); ?></h3>
                    <p class="dashboard-toolbar-copy">Visitantes e tempo médio por simulador (via GA4) e conversão de leads por origem. Visão do dia (<?= esc(date('d/m', strtotime($today))); ?>) e do período.</p>
                </div>
                <div class="right" style="display:flex;gap:10px;align-items:center;">
                    <a href="<?= adminUrl('dashboard'); ?>?days=<?= $days; ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Dashboard</a>
                    <select id="sim_days_filter" class="form-control input-sm" style="width:auto;display:inline-block;">
                        <?php foreach ($availableWindows as $w): ?>
                            <option value="<?= (int) $w; ?>" <?= $w === $days ? 'selected' : ''; ?>>Últimos <?= (int) $w; ?> dias</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!$ga4Used): ?>
    <div class="row"><div class="col-sm-12">
        <div class="gx-sim-warn">
            <i class="fa fa-exclamation-triangle"></i>
            <strong>Visitantes e tempo médio indisponíveis</strong> — dependem do GA4, que <?= empty($sim['ga4_available']) ? 'não está conectado' : 'não retornou dados'; ?>.
            <?php if (!empty($sim['ga4_error'])): ?><br><small><?= esc($sim['ga4_error']); ?></small><?php endif; ?>
            A tabela abaixo mostra os <strong>leads por simulador</strong> (fonte: sim_leads); a conversão fica indisponível sem as visitas.
        </div>
    </div></div>
<?php endif; ?>

<div class="row dashboard-kpi-row">
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="dashboard-kpi-card dashboard-accent-blue">
            <span class="dashboard-kpi-label">Visitantes (período)</span>
            <strong class="dashboard-kpi-value"><?= $ga4Used ? number_format($totV) : '—'; ?></strong>
            <small class="dashboard-kpi-note">Hoje: <?= $ga4Used ? number_format($totVToday) : '—'; ?> · via GA4</small>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="dashboard-kpi-card dashboard-accent-gold">
            <span class="dashboard-kpi-label">Leads (período)</span>
            <strong class="dashboard-kpi-value"><?= number_format($totL); ?></strong>
            <small class="dashboard-kpi-note">Hoje: <?= number_format($totLToday); ?> · sim_leads</small>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="dashboard-kpi-card dashboard-accent-teal">
            <span class="dashboard-kpi-label">Conversão média (período)</span>
            <strong class="dashboard-kpi-value"><?= $ga4Used ? number_format($convPeriod, 1, ',', '.') . '%' : '—'; ?></strong>
            <small class="dashboard-kpi-note">Hoje: <?= $ga4Used ? number_format($convToday, 1, ',', '.') . '%' : '—'; ?> · leads/visitantes</small>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="dashboard-kpi-card dashboard-accent-indigo">
            <span class="dashboard-kpi-label">Simuladores ativos</span>
            <strong class="dashboard-kpi-value"><?= number_format(count($simulators)); ?></strong>
            <small class="dashboard-kpi-note">com visita ou lead no período</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="box dashboard-widget">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-flask"></i> Por simulador — hoje vs. período (<?= $days; ?> dias)</h3>
                <span class="dashboard-widget-description">Visitantes e tempo médio via GA4; leads via sim_leads; conversão = leads ÷ visitantes.</span>
            </div>
            <div class="box-body">
                <div class="gx-sim-scroll">
                    <table class="gx-sim-table">
                        <thead>
                            <tr>
                                <th class="sim-name" rowspan="2">Simulador</th>
                                <th colspan="3" style="text-align:center;">Hoje (<?= esc(date('d/m', strtotime($today))); ?>)</th>
                                <th colspan="4" class="grp" style="text-align:center;">Período (<?= $days; ?> dias)</th>
                            </tr>
                            <tr>
                                <th>Visitantes</th>
                                <th>Leads</th>
                                <th>Conv.</th>
                                <th class="grp">Visitantes</th>
                                <th>Tempo médio</th>
                                <th>Leads</th>
                                <th>Conv.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($simulators)): ?>
                                <tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:22px;">Sem visitas ou leads de simulador no período.</td></tr>
                            <?php else: foreach ($simulators as $s):
                                $pConv = $s['period']['conversion'];
                                $pill = $pConv <= 0 ? 'gx-pill-zero' : ($pConv >= 3 ? 'gx-pill-good' : 'gx-pill-mid');
                                ?>
                                <tr>
                                    <td class="sim-name"><?= esc($s['label']); ?></td>
                                    <td><?= $ga4Used ? number_format($s['today']['visitors']) : '—'; ?></td>
                                    <td><?= number_format($s['today']['leads']); ?></td>
                                    <td><?= ($ga4Used && $s['today']['visitors'] > 0) ? number_format($s['today']['conversion'], 1, ',', '.') . '%' : '—'; ?></td>
                                    <td class="grp"><?= $ga4Used ? number_format($s['period']['visitors']) : '—'; ?></td>
                                    <td><?= $ga4Used ? $fmtTime($s['period']['avg_time']) : '—'; ?></td>
                                    <td><?= number_format($s['period']['leads']); ?></td>
                                    <td><?php if ($ga4Used && $s['period']['visitors'] > 0): ?><span class="gx-pill <?= $pill; ?>"><?= number_format($pConv, 1, ',', '.'); ?>%</span><?php else: ?>—<?php endif; ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                        <?php if (!empty($simulators)): ?>
                        <tfoot>
                            <tr>
                                <td class="sim-name">Total</td>
                                <td><?= $ga4Used ? number_format($totVToday) : '—'; ?></td>
                                <td><?= number_format($totLToday); ?></td>
                                <td><?= ($ga4Used && $totVToday > 0) ? number_format($convToday, 1, ',', '.') . '%' : '—'; ?></td>
                                <td class="grp"><?= $ga4Used ? number_format($totV) : '—'; ?></td>
                                <td>—</td>
                                <td><?= number_format($totL); ?></td>
                                <td><?= ($ga4Used && $totV > 0) ? number_format($convPeriod, 1, ',', '.') . '%' : '—'; ?></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
                <p class="gx-sim-note"><i class="fa fa-info-circle"></i> Visitantes = usuários ativos do GA4 na página do simulador (site inteiro, base usuário). Tempo médio = engajamento por visitante. Leads = sim_leads por origem. "Outros / não atribuído" reúne leads sem origem casada e páginas de simulador fora do registro.</p>
            </div>
        </div>
    </div>
</div>

<?php if ($ga4Used && !empty($dates) && !empty($simulators)): ?>
<div class="row">
    <div class="col-sm-12">
        <div class="box dashboard-widget">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-calendar"></i> Visitantes por dia · por simulador</h3>
                <span class="dashboard-widget-description">Usuários ativos (GA4) em cada simulador, por dia (mais recente no topo).</span>
            </div>
            <div class="box-body">
                <div class="gx-sim-scroll" style="max-height:520px;overflow-y:auto;">
                    <table class="gx-matrix">
                        <thead>
                            <tr>
                                <th class="d">Dia</th>
                                <?php foreach ($simulators as $s): ?>
                                    <th title="<?= esc($s['label']); ?>"><?= esc(mb_strimwidth($s['label'], 0, 16, '…')); ?></th>
                                <?php endforeach; ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_reverse($dates) as $d):
                                $rowTotal = 0; ?>
                                <tr>
                                    <td class="d"><?= esc(date('d/m', strtotime($d))); ?><?= $d === $today ? ' <small style="color:#b45309">hoje</small>' : ''; ?></td>
                                    <?php foreach ($simulators as $s):
                                        $v = (int) ($s['daily'][$d]['visitors'] ?? 0);
                                        $rowTotal += $v; ?>
                                        <td class="<?= $v === 0 ? 'z' : ''; ?>"><?= $v === 0 ? '·' : number_format($v); ?></td>
                                    <?php endforeach; ?>
                                    <td><strong><?= number_format($rowTotal); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.getElementById('sim_days_filter').addEventListener('change', function () {
    window.location.href = '<?= adminUrl('dashboard/simuladores'); ?>?days=' + this.value;
});
</script>
