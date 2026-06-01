<?php $statusFilter = $status ?? null; ?>
<div class="row">
    <div class="col-sm-12 title-section">
        <h3>Fila de Envios <small>(Newsletter IA)</small></h3>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-pills" style="margin-bottom: 15px;">
            <li class="<?= !$statusFilter ? 'active' : ''; ?>"><a href="<?= adminUrl('newsletter/queue'); ?>">Todos</a></li>
            <li class="<?= $statusFilter === 'draft' ? 'active' : ''; ?>"><a href="<?= adminUrl('newsletter/queue?status=draft'); ?>">Rascunhos (<?= (int) ($counts['draft'] ?? 0); ?>)</a></li>
            <li class="<?= $statusFilter === 'approved' ? 'active' : ''; ?>"><a href="<?= adminUrl('newsletter/queue?status=approved'); ?>">Aprovados (<?= (int) ($counts['approved'] ?? 0); ?>)</a></li>
            <li class="<?= $statusFilter === 'sent' ? 'active' : ''; ?>"><a href="<?= adminUrl('newsletter/queue?status=sent'); ?>">Enviados (<?= (int) ($counts['sent'] ?? 0); ?>)</a></li>
            <li class="<?= $statusFilter === 'failed' ? 'active' : ''; ?>"><a href="<?= adminUrl('newsletter/queue?status=failed'); ?>">Falhas (<?= (int) ($counts['failed'] ?? 0); ?>)</a></li>
        </ul>

        <div class="box box-primary">
            <div class="box-body">
                <?php if (empty($sends)): ?>
                    <p class="text-muted">Nenhum envio nesta visão.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Linha</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Agendado p/</th>
                                <th>Enviado em</th>
                                <th>Destinatários</th>
                                <th>Opens / Clicks</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sends as $s): ?>
                                <tr>
                                    <td>#<?= (int) $s->id; ?></td>
                                    <td><?= esc($s->line_name ?? '—'); ?></td>
                                    <td><?= esc($s->subject ?? '—'); ?></td>
                                    <td>
                                        <?php
                                        $label = [
                                            'draft' => 'default',
                                            'approved' => 'info',
                                            'sending' => 'warning',
                                            'sent' => 'success',
                                            'failed' => 'danger',
                                            'canceled' => 'default',
                                        ][$s->status] ?? 'default';
                                        ?>
                                        <span class="label label-<?= $label; ?>"><?= esc($s->status); ?></span>
                                    </td>
                                    <td><?= esc($s->scheduled_for ?? '—'); ?></td>
                                    <td><?= esc($s->sent_at ?? '—'); ?></td>
                                    <td><?= (int) $s->delivered_count; ?> / <?= (int) $s->recipients_count; ?></td>
                                    <td><?= (int) $s->opens_count; ?> / <?= (int) $s->clicks_count; ?></td>
                                    <td>
                                        <a href="<?= adminUrl('newsletter/queue/view/' . $s->id); ?>" class="btn btn-xs btn-primary">
                                            <i class="fa fa-eye"></i> Ver
                                        </a>
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
