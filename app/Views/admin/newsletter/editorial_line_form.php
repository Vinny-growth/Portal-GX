<?php
$line = $line ?? null;
$isEdit = !empty($line);
$catIds = ($isEdit && !empty($line->category_ids)) ? json_decode($line->category_ids, true) : [];
if (!is_array($catIds)) $catIds = [];
$sendTimes = ($isEdit && !empty($line->send_times)) ? json_decode($line->send_times, true) : [];
if (!is_array($sendTimes)) $sendTimes = [];
?>
<div class="row">
    <div class="col-sm-12 title-section">
        <h3><?= $isEdit ? 'Editar' : 'Nova'; ?> Linha Editorial</h3>
        <a href="<?= adminUrl('newsletter/editorial-lines'); ?>" class="btn btn-default pull-right">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<form action="<?= adminUrl('newsletter/editorial-lines/save'); ?>" method="post">
    <?= csrf_field(); ?>
    <input type="hidden" name="id" value="<?= $isEdit ? (int) $line->id : ''; ?>">
    <div class="row">
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="form-group">
                        <label>Nome *</label>
                        <input type="text" name="name" class="form-control" required value="<?= esc($isEdit ? $line->name : old('name')); ?>">
                    </div>
                    <div class="form-group">
                        <label>Slug *</label>
                        <input type="text" name="slug" class="form-control" required value="<?= esc($isEdit ? $line->slug : old('slug')); ?>">
                        <small class="text-muted">apenas letras minúsculas, números e hífen</small>
                    </div>
                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="description" class="form-control" rows="2"><?= esc($isEdit ? $line->description : old('description')); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Categorias relacionadas *</label>
                        <select name="category_ids[]" multiple class="form-control" size="6">
                            <?php foreach (($categories ?? []) as $c): ?>
                                <option value="<?= (int) $c->id; ?>" <?= in_array((int) $c->id, array_map('intval', $catIds), true) ? 'selected' : ''; ?>>
                                    <?= esc($c->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Subscribers que converterem em posts dessas categorias entram nesta linha.</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Horários de envio (HH:MM, separados por vírgula)</label>
                                <input type="text" name="send_times" class="form-control" value="<?= esc(implode(', ', $sendTimes)); ?>" placeholder="08:00, 13:00, 18:00">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Posts por edição</label>
                                <input type="number" name="posts_per_edition" class="form-control" min="1" max="20" value="<?= $isEdit ? (int) $line->posts_per_edition : 5; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Janela (horas)</label>
                                <input type="number" name="lookback_hours" class="form-control" min="1" value="<?= $isEdit ? (int) $line->lookback_hours : 24; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Prompts da IA</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <label>Orientação para subject</label>
                        <textarea name="subject_prompt" class="form-control" rows="2" placeholder="Ex.: Foque em câmbio do dia, máximo 60 caracteres."><?= esc($isEdit ? $line->subject_prompt : ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Orientação para corpo</label>
                        <textarea name="body_prompt" class="form-control" rows="3" placeholder="Ex.: Tom executivo, foco em dólar e juros, sem jargão. Cite números do dia."><?= esc($isEdit ? $line->body_prompt : ''); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">CTA padrão</h3></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Texto do CTA</label>
                                <input type="text" name="cta_text" class="form-control" value="<?= esc($isEdit ? $line->cta_text : ''); ?>" placeholder="Falar com especialista">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>URL do CTA</label>
                                <input type="url" name="cta_url" class="form-control" value="<?= esc($isEdit ? $line->cta_url : ''); ?>" placeholder="https://gx.capital/wealth">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Lead Magnet</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <label>Material entregue aos novos inscritos desta linha</label>
                        <select name="lead_magnet_id" class="form-control">
                            <option value="">— Nenhum —</option>
                            <?php foreach (($magnets ?? []) as $m): ?>
                                <option value="<?= (int) $m->id; ?>" <?= ($isEdit && (int) ($line->lead_magnet_id ?? 0) === (int) $m->id) ? 'selected' : ''; ?>>
                                    <?= esc($m->title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Cadastre magnets em <a href="<?= adminUrl('newsletter/magnets'); ?>">Lead Magnets</a>.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Status</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <label>Frequência</label>
                        <select name="frequency" class="form-control">
                            <?php $f = $isEdit ? $line->frequency : 'daily'; ?>
                            <option value="daily" <?= $f === 'daily' ? 'selected' : ''; ?>>Diária</option>
                            <option value="weekly" <?= $f === 'weekly' ? 'selected' : ''; ?>>Semanal</option>
                            <option value="on_demand" <?= $f === 'on_demand' ? 'selected' : ''; ?>>Sob demanda</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="enabled" value="1" <?= (!$isEdit || (int) $line->enabled === 1) ? 'checked' : ''; ?>>
                            Linha ativa
                        </label>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="ai_auto_publish" value="1" <?= ($isEdit && (int) $line->ai_auto_publish === 1) ? 'checked' : ''; ?>>
                            IA pode publicar sem aprovação
                        </label>
                        <br>
                        <small class="text-muted">Desmarcado: envios geram rascunhos para aprovação manual.</small>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-save"></i> Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
