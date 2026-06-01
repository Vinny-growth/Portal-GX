<?php
$cardForSync = function ($sync, $label) {
    if (empty($sync)) {
        return '<div class="callout callout-default" style="margin:0;"><h4>' . esc($label) . '</h4><p class="text-muted">Nenhuma sincronização registrada.</p></div>';
    }
    $statusClass = $sync->status === 'success' ? 'callout-success'
        : ($sync->status === 'failed' ? 'callout-danger' : 'callout-warning');
    $age = '';
    if (!empty($sync->finished_at)) {
        $diffSec = max(0, time() - strtotime($sync->finished_at));
        $age = $diffSec < 60 ? 'há ' . $diffSec . 's'
             : ($diffSec < 3600 ? 'há ' . round($diffSec / 60) . ' min'
             : 'há ' . round($diffSec / 3600, 1) . 'h');
    }
    $warn = !empty($sync->finished_at) && (time() - strtotime($sync->finished_at)) > 129600; // > 36h
    ob_start(); ?>
    <div class="callout <?= $statusClass; ?>" style="margin:0;">
        <h4><?= esc($label); ?>
            <small style="font-weight:normal;">
                <?= esc($sync->status); ?> • <?= esc($sync->finished_at ?? $sync->started_at ?? '—'); ?> <?= $age ? '(' . $age . ')' : ''; ?>
            </small>
        </h4>
        <div class="row" style="margin-top:10px;">
            <div class="col-xs-3"><strong><?= (int) $sync->total_received; ?></strong><br><small>recebidos</small></div>
            <div class="col-xs-3"><strong style="color:#00a65a;"><?= (int) $sync->created_count; ?></strong><br><small>criados</small></div>
            <div class="col-xs-3"><strong style="color:#3c8dbc;"><?= (int) $sync->updated_count; ?></strong><br><small>atualizados</small></div>
            <div class="col-xs-3"><strong style="color:#f39c12;"><?= (int) $sync->skipped_unsubscribed; ?></strong><br><small>desinscritos</small></div>
        </div>
        <?php if ($warn): ?>
            <p style="margin-top:8px;"><i class="fa fa-warning"></i> <strong>Último sync foi há mais de 36h.</strong> Verifique o cron.</p>
        <?php endif; ?>
        <?php if (!empty($sync->error_log)): ?>
            <p style="margin-top:8px;"><small><a href="<?= adminUrl('newsletter/crm-sync/view/' . (int) $sync->id); ?>"><i class="fa fa-exclamation-circle"></i> Ver detalhes do erro</a></small></p>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
};
?>

<div class="row">
    <div class="col-sm-12 title-section">
        <h3>Sincronização CRM <small>Leads e clientes → Newsletter</small></h3>
    </div>
</div>

<?php if (empty($config['anon_set']) || empty($config['api_key_set'])): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="callout callout-danger">
                <h4><i class="fa fa-exclamation-triangle"></i> Configuração incompleta</h4>
                <p>Antes de sincronizar, defina no arquivo <code>.env</code>:</p>
                <ul style="margin-bottom:0;">
                    <li><code>CRM_LEAD_API_KEY</code> — <?= $config['api_key_set'] ? '<span class="text-success">✓ configurada</span>' : '<span class="text-danger">faltando</span>'; ?> (reusa a chave do envio de leads)</li>
                    <li><code>CRM_NEWSLETTER_ANON_KEY</code> — <?= $config['anon_set'] ? '<span class="text-success">✓ configurada</span>' : '<span class="text-danger">faltando</span>'; ?> (anon public key do Supabase do CRM)</li>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-sm-6"><?= $cardForSync($lastLeads, 'Leads — último sync'); ?></div>
    <div class="col-sm-6"><?= $cardForSync($lastClients, 'Clientes — último sync'); ?></div>
</div>

<div class="row" style="margin-top:15px;">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Sincronizar agora</h3>
            </div>
            <div class="box-body">
                <form action="<?= adminUrl('newsletter/crm-sync/run'); ?>" method="post" style="display:inline;" onsubmit="return confirm('Disparar sync agora? Pode levar 1-2 minutos dependendo do volume.');">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="source" value="all">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i> Sincronizar tudo (incremental)</button>
                </form>
                <form action="<?= adminUrl('newsletter/crm-sync/run'); ?>" method="post" style="display:inline; margin-left:5px;" onsubmit="return confirm('Sync apenas leads?');">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="source" value="leads">
                    <button type="submit" class="btn btn-default"><i class="fa fa-user-plus"></i> Só leads</button>
                </form>
                <form action="<?= adminUrl('newsletter/crm-sync/run'); ?>" method="post" style="display:inline; margin-left:5px;" onsubmit="return confirm('Sync apenas clientes?');">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="source" value="clients">
                    <button type="submit" class="btn btn-default"><i class="fa fa-briefcase"></i> Só clientes</button>
                </form>
                <form action="<?= adminUrl('newsletter/crm-sync/run'); ?>" method="post" style="display:inline; margin-left:15px;" onsubmit="return confirm('FULL SYNC ignora o cursor incremental e refaz a base inteira. Pode demorar bastante. Continuar?');">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="source" value="all">
                    <input type="hidden" name="full" value="1">
                    <button type="submit" class="btn btn-warning"><i class="fa fa-history"></i> Full sync</button>
                </form>
                <p class="text-muted" style="margin-top:10px; margin-bottom:0;"><small>O cron diário (03:00 UTC) já dispara o sync incremental automaticamente. Use os botões acima para forçar manualmente.</small></p>
            </div>
        </div>
    </div>
</div>

<div class="row" style="margin-top:15px;">
    <div class="col-sm-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Histórico (últimos 30)</h3>
            </div>
            <div class="box-body">
                <?php if (empty($history)): ?>
                    <p class="text-muted">Nenhuma sincronização ainda. Use o botão acima ou aguarde o cron.</p>
                <?php else: ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th style="width:80px;">Fonte</th>
                            <th style="width:160px;">Início</th>
                            <th style="width:100px;">Status</th>
                            <th>Origem</th>
                            <th style="width:70px;">Pgs</th>
                            <th style="width:90px;">Recebidos</th>
                            <th style="width:80px;">Criados</th>
                            <th style="width:90px;">Atualiz.</th>
                            <th style="width:90px;">Unsub.</th>
                            <th style="width:80px;">Inválidos</th>
                            <th style="width:100px;">Opt-out CRM</th>
                            <th style="width:80px;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($history as $h):
                            $clsRow = $h->status === 'failed' ? 'danger' : ($h->status === 'success' ? '' : 'warning');
                            $clsLabel = $h->status === 'success' ? 'success' : ($h->status === 'failed' ? 'danger' : 'warning'); ?>
                            <tr<?= $clsRow ? ' class="' . $clsRow . '"' : ''; ?>>
                                <td>#<?= (int) $h->id; ?></td>
                                <td><?= esc($h->source); ?></td>
                                <td><small><?= esc($h->started_at); ?></small></td>
                                <td><span class="label label-<?= $clsLabel; ?>"><?= esc($h->status); ?></span></td>
                                <td><small><?= esc($h->trigger_type); ?></small></td>
                                <td><?= (int) $h->pages_fetched; ?></td>
                                <td><?= (int) $h->total_received; ?></td>
                                <td><?= (int) $h->created_count; ?></td>
                                <td><?= (int) $h->updated_count; ?></td>
                                <td><?= (int) $h->skipped_unsubscribed; ?></td>
                                <td><?= (int) $h->skipped_invalid; ?></td>
                                <td><?= (int) $h->filtered_opt_out_total; ?></td>
                                <td>
                                    <a href="<?= adminUrl('newsletter/crm-sync/view/' . (int) $h->id); ?>" class="btn btn-xs btn-default">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
