<div class="row">
    <div class="col-sm-12 title-section">
        <h3>Newsletter Analytics <small>(últimos 30 dias)</small></h3>
    </div>
</div>

<div class="row">
    <?php
    $delivered = (int) ($totals['delivered_30d'] ?? 0);
    $opens = (int) ($totals['opens_30d'] ?? 0);
    $clicks = (int) ($totals['clicks_30d'] ?? 0);
    $openRate = $delivered > 0 ? round(($opens / $delivered) * 100, 1) : 0;
    $clickRate = $delivered > 0 ? round(($clicks / $delivered) * 100, 1) : 0;
    $kpis = [
        ['Subscribers ativos', (int) ($totals['subscribers'] ?? 0)],
        ['Envios (30d)', (int) ($totals['sends_30d'] ?? 0)],
        ['Entregues (30d)', $delivered],
        ['Opens', $opens . ' (' . $openRate . '%)'],
        ['Clicks', $clicks . ' (' . $clickRate . '%)'],
    ];
    ?>
    <?php foreach ($kpis as $kpi): ?>
        <div class="col-md-2 col-sm-4">
            <div class="box box-primary" style="text-align:center;padding:14px 8px;">
                <div style="font-size:24px;font-weight:700;color:#0a1a3a;"><?= esc($kpi[1]); ?></div>
                <small style="text-transform:uppercase;letter-spacing:1px;color:#777;"><?= esc($kpi[0]); ?></small>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Performance por linha editorial</h3></div>
            <div class="box-body">
                <?php if (empty($perLine)): ?>
                    <p class="text-muted">Sem dados ainda.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Linha</th>
                                <th>Envios</th>
                                <th>Entregues</th>
                                <th>Opens</th>
                                <th>Clicks</th>
                                <th>Open rate</th>
                                <th>Click rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($perLine as $row): ?>
                                <?php
                                $d = (int) $row->delivered;
                                $o = (int) $row->opens;
                                $c = (int) $row->clicks;
                                $or = $d > 0 ? round(($o / $d) * 100, 1) : 0;
                                $cr = $d > 0 ? round(($c / $d) * 100, 1) : 0;
                                ?>
                                <tr>
                                    <td><?= esc($row->name); ?></td>
                                    <td><?= (int) $row->sends; ?></td>
                                    <td><?= $d; ?></td>
                                    <td><?= $o; ?></td>
                                    <td><?= $c; ?></td>
                                    <td><?= $or; ?>%</td>
                                    <td><?= $cr; ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Últimos envios</h3></div>
            <div class="box-body">
                <?php if (empty($recentSends)): ?>
                    <p class="text-muted">Sem envios recentes.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Linha</th>
                                <th>Subject</th>
                                <th>Enviado em</th>
                                <th>Entregues</th>
                                <th>Opens</th>
                                <th>Clicks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentSends as $r): ?>
                                <tr>
                                    <td>#<?= (int) $r->id; ?></td>
                                    <td><?= esc($r->line_name ?? '—'); ?></td>
                                    <td><?= esc(mb_substr($r->subject ?? '—', 0, 60)); ?></td>
                                    <td><?= esc($r->sent_at); ?></td>
                                    <td><?= (int) $r->delivered_count; ?> / <?= (int) $r->recipients_count; ?></td>
                                    <td><?= (int) $r->opens_count; ?></td>
                                    <td><?= (int) $r->clicks_count; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
