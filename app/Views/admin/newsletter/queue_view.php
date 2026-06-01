<?php $payload = $payload ?: []; ?>
<div class="row">
    <div class="col-sm-12 title-section">
        <h3>Envio #<?= (int) $send->id; ?>
            <small>
                <?php
                $label = [
                    'draft' => 'default',
                    'approved' => 'info',
                    'sending' => 'warning',
                    'sent' => 'success',
                    'failed' => 'danger',
                    'canceled' => 'default',
                ][$send->status] ?? 'default';
                ?>
                <span class="label label-<?= $label; ?>"><?= esc($send->status); ?></span>
            </small>
        </h3>
        <a href="<?= adminUrl('newsletter/queue'); ?>" class="btn btn-default pull-right">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <form action="<?= adminUrl('newsletter/queue/update/' . $send->id); ?>" method="post">
            <?= csrf_field(); ?>
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Conteúdo da edição</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" class="form-control" value="<?= esc($payload['subject'] ?? $send->subject); ?>" maxlength="500" <?= in_array($send->status, ['sent','sending'], true) ? 'readonly' : ''; ?>>
                    </div>
                    <div class="form-group">
                        <label>Preheader (preview que aparece no inbox)</label>
                        <input type="text" name="preheader" class="form-control" value="<?= esc($payload['preheader'] ?? $send->preheader); ?>" maxlength="500" <?= in_array($send->status, ['sent','sending'], true) ? 'readonly' : ''; ?>>
                    </div>
                    <div class="form-group">
                        <label>Intro</label>
                        <textarea name="intro" class="form-control" rows="2" <?= in_array($send->status, ['sent','sending'], true) ? 'readonly' : ''; ?>><?= esc($payload['intro'] ?? ''); ?></textarea>
                    </div>

                    <?php foreach (($payload['posts'] ?? []) as $idx => $p): ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Post #<?= (int) ($p['post_id'] ?? 0); ?>
                                <input type="hidden" name="posts[<?= $idx; ?>][post_id]" value="<?= (int) ($p['post_id'] ?? 0); ?>">
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label>Título</label>
                                    <input type="text" name="posts[<?= $idx; ?>][title]" class="form-control" value="<?= esc($p['title'] ?? ''); ?>" maxlength="200">
                                </div>
                                <div class="form-group">
                                    <label>Resumo</label>
                                    <textarea name="posts[<?= $idx; ?>][summary]" class="form-control" rows="2"><?= esc($p['summary'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Texto do CTA</label>
                                    <input type="text" name="posts[<?= $idx; ?>][cta_label]" class="form-control" value="<?= esc($p['cta_label'] ?? 'Leia mais'); ?>" maxlength="50">
                                </div>
                                <small class="text-muted">URL: <?= esc($p['url'] ?? ''); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>CTA final — texto</label>
                                <input type="text" name="cta_text" class="form-control" value="<?= esc($payload['cta_text'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group">
                                <label>CTA final — URL</label>
                                <input type="url" name="cta_url" class="form-control" value="<?= esc($payload['cta_url'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <?php if (!in_array($send->status, ['sent','sending'], true)): ?>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Salvar alterações</button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Ações</h3></div>
            <div class="box-body">
                <?php if ($send->status === 'draft'): ?>
                    <form action="<?= adminUrl('newsletter/queue/approve/' . $send->id); ?>" method="post" onsubmit="return confirm('Aprovar este envio? Ele será disparado no próximo ciclo do cron (ou imediatamente se você usar o Disparar Agora).');">
                        <?= csrf_field(); ?>
                        <button type="submit" class="btn btn-success btn-block"><i class="fa fa-check"></i> Aprovar</button>
                    </form>
                <?php endif; ?>
                <?php if (in_array($send->status, ['approved'], true)): ?>
                    <form action="<?= adminUrl('newsletter/queue/dispatch/' . $send->id); ?>" method="post" onsubmit="return confirm('Disparar AGORA para todos os subscribers da linha?');" style="margin-top:8px;">
                        <?= csrf_field(); ?>
                        <button type="submit" class="btn btn-warning btn-block"><i class="fa fa-paper-plane"></i> Disparar agora</button>
                    </form>
                <?php endif; ?>
                <?php if (!in_array($send->status, ['sent','canceled'], true)): ?>
                    <form action="<?= adminUrl('newsletter/queue/cancel/' . $send->id); ?>" method="post" onsubmit="return confirm('Cancelar este envio? Não será disparado.');" style="margin-top:8px;">
                        <?= csrf_field(); ?>
                        <button type="submit" class="btn btn-danger btn-block"><i class="fa fa-ban"></i> Cancelar</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Métricas</h3></div>
            <div class="box-body">
                <p><strong>Destinatários:</strong> <?= (int) $send->recipients_count; ?></p>
                <p><strong>Entregues:</strong> <?= (int) $send->delivered_count; ?></p>
                <p><strong>Opens:</strong> <?= (int) $send->opens_count; ?></p>
                <p><strong>Clicks:</strong> <?= (int) $send->clicks_count; ?></p>
                <p><strong>Gerado em:</strong> <?= esc($send->generated_at ?: '—'); ?></p>
                <p><strong>Aprovado em:</strong> <?= esc($send->approved_at ?: '—'); ?></p>
                <p><strong>Enviado em:</strong> <?= esc($send->sent_at ?: '—'); ?></p>
                <?php if (!empty($send->error)): ?>
                    <p class="text-danger"><strong>Erro:</strong> <?= esc(mb_substr($send->error, 0, 300)); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
