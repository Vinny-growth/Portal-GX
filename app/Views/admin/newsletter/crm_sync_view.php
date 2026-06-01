<div class="row">
    <div class="col-sm-12 title-section">
        <h3>Sync CRM #<?= (int) $sync->id; ?> <small><?= esc($sync->source); ?> • <?= esc($sync->status); ?></small></h3>
        <p><a href="<?= adminUrl('newsletter/crm-sync'); ?>"><i class="fa fa-arrow-left"></i> Voltar</a></p>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt>ID:</dt><dd>#<?= (int) $sync->id; ?></dd>
                    <dt>Fonte:</dt><dd><?= esc($sync->source); ?></dd>
                    <dt>Origem (trigger):</dt><dd><?= esc($sync->trigger_type); ?></dd>
                    <dt>Status:</dt><dd><span class="label label-<?= $sync->status === 'success' ? 'success' : ($sync->status === 'failed' ? 'danger' : 'warning'); ?>"><?= esc($sync->status); ?></span></dd>
                    <dt>Iniciado em:</dt><dd><?= esc($sync->started_at); ?></dd>
                    <dt>Finalizado em:</dt><dd><?= esc($sync->finished_at ?? '—'); ?></dd>
                    <dt>Cursor incremental (updated_since):</dt><dd><?= esc($sync->updated_since ?? '— (full sync)'); ?></dd>
                </dl>

                <h4 style="margin-top:25px;">Contadores</h4>
                <table class="table table-bordered" style="max-width:600px;">
                    <tr><th style="width:70%;">Páginas consultadas no CRM</th><td><?= (int) $sync->pages_fetched; ?></td></tr>
                    <tr><th>Registros recebidos do CRM</th><td><?= (int) $sync->total_received; ?></td></tr>
                    <tr><th class="success">Subscribers criados</th><td><?= (int) $sync->created_count; ?></td></tr>
                    <tr><th class="info">Subscribers atualizados</th><td><?= (int) $sync->updated_count; ?></td></tr>
                    <tr><th class="warning">Pulados (já estavam unsubscribed)</th><td><?= (int) $sync->skipped_unsubscribed; ?></td></tr>
                    <tr><th class="danger">Pulados (dados inválidos)</th><td><?= (int) $sync->skipped_invalid; ?></td></tr>
                    <tr><th>Opt-out filtrado no CRM</th><td><?= (int) $sync->filtered_opt_out_total; ?></td></tr>
                </table>

                <?php if (!empty($sync->error_log)): ?>
                    <h4 style="margin-top:25px;">Log de erros</h4>
                    <pre style="white-space:pre-wrap; word-break:break-word; background:#f9f2f2; border-left:3px solid #d9534f; padding:10px; max-height:400px; overflow:auto;"><?= esc($sync->error_log); ?></pre>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
