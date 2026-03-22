<?php
$allowedCategoryIds = [];
if (!empty($settings->allowed_category_ids)) {
    $decoded = json_decode($settings->allowed_category_ids, true);
    if (is_array($decoded)) {
        $allowedCategoryIds = $decoded;
    }
}
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Central de Conteudos IA</h1>
    </section>

    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_calendar" data-toggle="tab">Calendario</a></li>
                <li><a href="#tab_trends" data-toggle="tab">Tendencias</a></li>
                <li><a href="#tab_settings" data-toggle="tab">Configuracoes</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_calendar">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Adicionar item ao calendario</h3>
                        </div>
                        <div class="box-body">
                            <form action="<?= adminUrl('content-ai/calendar/add'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Titulo base</label>
                                            <input type="text" class="form-control" name="title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Categoria</label>
                                            <select class="form-control select2" name="category_id">
                                                <option value="">Selecione</option>
                                                <?php if (!empty($categories)):
                                                    foreach ($categories as $cat): ?>
                                                        <option value="<?= $cat->id; ?>"><?= esc($cat->name); ?></option>
                                                    <?php endforeach;
                                                endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Instrucoes para a IA</label>
                                    <textarea class="form-control" name="instructions" rows="3" placeholder="Ex.: foco em cambio comercial, publico C-level, incluir dados recentes."></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tom</label>
                                            <select name="tone" class="form-control">
                                                <option value="">Padrao</option>
                                                <option value="professional">Profissional</option>
                                                <option value="formal">Formal</option>
                                                <option value="inspirational">Inspiracional</option>
                                                <option value="persuasive">Persuasivo</option>
                                                <option value="academic">Academico</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tamanho</label>
                                            <select name="length" class="form-control">
                                                <option value="">Padrao</option>
                                                <option value="short">Curto</option>
                                                <option value="medium">Medio</option>
                                                <option value="long">Longo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Publicar em</label>
                                            <input type="datetime-local" class="form-control" name="publish_at">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Gerar em</label>
                                            <input type="datetime-local" class="form-control" name="generate_at">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Adicionar</button>
                            </form>
                        </div>
                    </div>

                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Itens do calendario</h3>
                            <div class="pull-right">
                                <form action="<?= adminUrl('content-ai/run-now'); ?>" method="post" style="display:inline;">
                                    <?= csrf_field(); ?>
                                    <button class="btn btn-primary btn-sm">Gerar agora</button>
                                </form>
                            </div>
                        </div>
                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titulo</th>
                                    <th>Status</th>
                                    <th>Publicar em</th>
                                    <th>Origem</th>
                                    <th>Post</th>
                                    <th>Acoes</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($calendarItems)):
                                    foreach ($calendarItems as $item): ?>
                                        <tr>
                                            <td><?= $item->id; ?></td>
                                            <td><?= esc($item->title); ?></td>
                                            <td><?= esc($item->status); ?></td>
                                            <td><?= esc($item->publish_at); ?></td>
                                            <td><?= esc($item->source_type); ?></td>
                                            <td><?= !empty($item->post_id) ? (int) $item->post_id : '-'; ?></td>
                                            <td>
                                                <?php if ($item->status === 'needs_review'): ?>
                                                    <form action="<?= adminUrl('content-ai/calendar/approve'); ?>" method="post" style="display:inline;">
                                                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="id" value="<?= $item->id; ?>">
                                                        <button type="submit" class="btn btn-xs btn-warning">Aprovar</button>
                                                    </form>
                                                <?php endif; ?>
                                                <form action="<?= adminUrl('content-ai/calendar/delete'); ?>" method="post" style="display:inline;">
                                                    <?= csrf_field(); ?>
                                                    <input type="hidden" name="id" value="<?= $item->id; ?>">
                                                    <button type="submit" class="btn btn-xs btn-danger">Excluir</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_trends">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Tendencias</h3>
                            <div class="pull-right">
                                <form action="<?= adminUrl('content-ai/trends/fetch'); ?>" method="post" style="display:inline;">
                                    <?= csrf_field(); ?>
                                    <button class="btn btn-info btn-sm">Atualizar tendencias</button>
                                </form>
                            </div>
                        </div>
                        <div class="box-body table-responsive">
                            <form action="<?= adminUrl('content-ai/trends/update'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="mode" value="select">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Titulo</th>
                                        <th>Origem</th>
                                        <th>Selecionado</th>
                                        <th>Auto</th>
                                        <th>Usado</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($trendItems)):
                                        foreach ($trendItems as $trend): ?>
                                            <tr>
                                                <td><input type="checkbox" name="trend_ids[]" value="<?= $trend->id; ?>"></td>
                                                <td><?= esc($trend->title); ?></td>
                                                <td><?= esc($trend->source); ?></td>
                                                <td><?= !empty($trend->selected) ? 'Sim' : 'Nao'; ?></td>
                                                <td><?= !empty($trend->auto_add) ? 'Sim' : 'Nao'; ?></td>
                                                <td><?= !empty($trend->used) ? 'Sim' : 'Nao'; ?></td>
                                            </tr>
                                        <?php endforeach;
                                    endif; ?>
                                    </tbody>
                                </table>
                                <div class="m-t-10">
                                    <button type="submit" class="btn btn-primary btn-sm">Marcar selecionados</button>
                                </div>
                            </form>
                            <form action="<?= adminUrl('content-ai/trends/update'); ?>" method="post" class="m-t-10">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="mode" value="auto_add">
                                <?php if (!empty($trendItems)): ?>
                                    <?php foreach ($trendItems as $trend): ?>
                                        <input type="hidden" name="trend_ids[]" value="<?= $trend->id; ?>">
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-warning btn-sm">Marcar todos como auto</button>
                            </form>
                            <form action="<?= adminUrl('content-ai/trends/add'); ?>" method="post" class="m-t-10">
                                <?= csrf_field(); ?>
                                <?php if (!empty($trendItems)): ?>
                                    <?php foreach ($trendItems as $trend): ?>
                                        <?php if (!empty($trend->selected)): ?>
                                            <input type="hidden" name="trend_ids[]" value="<?= $trend->id; ?>">
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-success btn-sm">Adicionar selecionadas ao calendario</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_settings">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Configuracoes da rotina</h3>
                        </div>
                        <div class="box-body">
                            <form action="<?= adminUrl('content-ai/settings'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Publicacao direta</label>
                                            <select class="form-control" name="auto_publish">
                                                <option value="1" <?= !empty($settings->auto_publish) ? 'selected' : ''; ?>>Sim</option>
                                                <option value="0" <?= empty($settings->auto_publish) ? 'selected' : ''; ?>>Nao (revisao)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Posts por dia</label>
                                            <input type="number" class="form-control" name="posts_per_day" min="1" value="<?= (int) ($settings->posts_per_day ?? 1); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Usuario padrao (ID)</label>
                                            <input type="number" class="form-control" name="default_user_id" min="1" value="<?= (int) ($settings->default_user_id ?? 1); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Horario 1</label>
                                            <input type="time" class="form-control" name="run_time_1" value="<?= !empty($settings->run_time_1) ? substr($settings->run_time_1, 0, 5) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Horario 2</label>
                                            <input type="time" class="form-control" name="run_time_2" value="<?= !empty($settings->run_time_2) ? substr($settings->run_time_2, 0, 5) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Horario 3</label>
                                            <input type="time" class="form-control" name="run_time_3" value="<?= !empty($settings->run_time_3) ? substr($settings->run_time_3, 0, 5) : ''; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tom padrao</label>
                                            <select name="default_tone" class="form-control">
                                                <option value="professional" <?= ($settings->default_tone ?? '') == 'professional' ? 'selected' : ''; ?>>Profissional</option>
                                                <option value="formal" <?= ($settings->default_tone ?? '') == 'formal' ? 'selected' : ''; ?>>Formal</option>
                                                <option value="inspirational" <?= ($settings->default_tone ?? '') == 'inspirational' ? 'selected' : ''; ?>>Inspiracional</option>
                                                <option value="persuasive" <?= ($settings->default_tone ?? '') == 'persuasive' ? 'selected' : ''; ?>>Persuasivo</option>
                                                <option value="academic" <?= ($settings->default_tone ?? '') == 'academic' ? 'selected' : ''; ?>>Academico</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tamanho padrao</label>
                                            <select name="default_length" class="form-control">
                                                <option value="short" <?= ($settings->default_length ?? '') == 'short' ? 'selected' : ''; ?>>Curto</option>
                                                <option value="medium" <?= ($settings->default_length ?? '') == 'medium' ? 'selected' : ''; ?>>Medio</option>
                                                <option value="long" <?= ($settings->default_length ?? '') == 'long' ? 'selected' : ''; ?>>Longo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Idioma</label>
                                            <select name="lang_id" class="form-control">
                                                <?php if (!empty($activeLanguages)):
                                                    foreach ($activeLanguages as $lang): ?>
                                                        <option value="<?= $lang->id; ?>" <?= ($settings->lang_id ?? $activeLang->id) == $lang->id ? 'selected' : ''; ?>><?= esc($lang->name); ?></option>
                                                    <?php endforeach;
                                                endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Palavras (curto)</label>
                                            <input type="number" class="form-control" name="length_short_words" min="200" value="<?= (int) ($settings->length_short_words ?? 900); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Palavras (medio)</label>
                                            <input type="number" class="form-control" name="length_medium_words" min="400" value="<?= (int) ($settings->length_medium_words ?? 1400); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Palavras (longo)</label>
                                            <input type="number" class="form-control" name="length_long_words" min="600" value="<?= (int) ($settings->length_long_words ?? 2000); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Categorias permitidas</label>
                                    <select name="allowed_category_ids[]" class="form-control select2" multiple>
                                        <?php if (!empty($categories)):
                                            foreach ($categories as $cat): ?>
                                                <option value="<?= $cat->id; ?>" <?= in_array($cat->id, $allowedCategoryIds, true) ? 'selected' : ''; ?>><?= esc($cat->name); ?></option>
                                            <?php endforeach;
                                        endif; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Guidelines de voz</label>
                                    <textarea class="form-control" name="voice_guidelines" rows="3"><?= esc($settings->voice_guidelines ?? ''); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Guidelines de SEO</label>
                                    <textarea class="form-control" name="seo_guidelines" rows="3"><?= esc($settings->seo_guidelines ?? ''); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Prompt template (com placeholders)</label>
                                    <textarea class="form-control" name="prompt_template" rows="8"><?= esc($settings->prompt_template ?? ''); ?></textarea>
                                    <p class="help-block">Use {category_name}, {category_guidelines}, {title}, {instructions}, {tone}, {length_words}, {voice}, {seo}, {rules}.</p>
                                </div>
                                <div class="form-group">
                                    <label>Guidelines de imagem</label>
                                    <textarea class="form-control" name="image_guidelines" rows="3"><?= esc($settings->image_guidelines ?? ''); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Prompt template de imagem</label>
                                    <textarea class="form-control" name="image_prompt_template" rows="6"><?= esc($settings->image_prompt_template ?? ''); ?></textarea>
                                    <p class="help-block">Use {title}, {summary}, {category_name}, {category_guidelines}, {image_prompt}, {image_guidelines}.</p>
                                </div>
                                <div class="form-group">
                                    <label>Regras de categoria (JSON)</label>
                                    <textarea class="form-control" name="category_rules_json" rows="5"><?= esc($settings->category_rules_json ?? ''); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Guidelines por categoria (JSON)</label>
                                    <textarea class="form-control" name="category_guidelines_json" rows="5"><?= esc($settings->category_guidelines_json ?? ''); ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Auto adicionar tendencias</label>
                                            <select class="form-control" name="auto_add_trends">
                                                <option value="1" <?= !empty($settings->auto_add_trends) ? 'selected' : ''; ?>>Sim</option>
                                                <option value="0" <?= empty($settings->auto_add_trends) ? 'selected' : ''; ?>>Nao</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tendencias por dia</label>
                                            <input type="number" class="form-control" name="trends_per_day" min="1" value="<?= (int) ($settings->trends_per_day ?? 3); ?>">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success">Salvar configuracoes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
