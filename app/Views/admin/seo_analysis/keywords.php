<?php
$keywords   = $keywords ?? [];
$categories = $categories ?? [];
?>

<div class="row">
    <div class="col-sm-8">
        <h3 style="margin-top:5px;"><i class="fa fa-key"></i> Palavras-chave monitoradas</h3>
    </div>
    <div class="col-sm-4 text-right">
        <a href="<?= adminUrl('seo-analysis'); ?>" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Visão geral</a>
        <form action="<?= adminUrl('seo-analysis/keywords/sync'); ?>" method="post" style="display:inline;"
              onsubmit="return confirm('Sincronizar as palavras-chave usadas nos artigos publicados e no calendário?');">
            <?= csrf_field(); ?>
            <button type="submit" class="btn btn-success btn-sm" title="Importa as keywords dos artigos (posts.keywords + tags do calendário)">
                <i class="fa fa-refresh"></i> Sincronizar do conteúdo
            </button>
        </form>
        <button type="button" class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#addKeywordBox">
            <i class="fa fa-plus"></i> Adicionar
        </button>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <p class="text-muted" style="font-size:12px;margin-bottom:8px;">
            <i class="fa fa-info-circle"></i> As palavras-chave marcadas como <span class="label label-primary">CONTEÚDO</span>
            são sincronizadas automaticamente das keywords dos artigos (diariamente, junto da coleta de rankings).
        </p>
    </div>
</div>

<!-- Add form -->
<div class="row collapse" id="addKeywordBox">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Nova palavra-chave</h3></div>
            <form action="<?= adminUrl('seo-analysis/keyword/add'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4 form-group">
                            <label>Palavra-chave *</label>
                            <input type="text" name="keyword" class="form-control" required placeholder="ex: hedge cambial">
                        </div>
                        <div class="col-sm-4 form-group">
                            <label>URL alvo</label>
                            <input type="text" name="target_url" class="form-control" placeholder="/simuladores/cambio">
                        </div>
                        <div class="col-sm-4 form-group">
                            <label>Categoria</label>
                            <select name="category_id" class="form-control">
                                <option value="">— nenhuma —</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= (int) $cat->id; ?>"><?= esc($cat->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 form-group">
                            <label>Locale</label>
                            <input type="text" name="locale" class="form-control" value="pt-BR">
                        </div>
                        <div class="col-sm-3 form-group">
                            <label>País (GSC)</label>
                            <input type="text" name="country" class="form-control" value="bra">
                        </div>
                        <div class="col-sm-3 form-group">
                            <label>Dispositivo</label>
                            <select name="device" class="form-control">
                                <option value="desktop">Desktop</option>
                                <option value="mobile">Mobile</option>
                            </select>
                        </div>
                        <div class="col-sm-3 form-group">
                            <label>Fonte preferida</label>
                            <select name="source" class="form-control">
                                <option value="gsc">Google Search Console</option>
                                <option value="serp">openserp (SERP)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Adicionar ao monitoramento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Keyword table -->
<div class="row">
    <div class="col-sm-12">
        <div class="box box-default">
            <div class="box-body">
                <?php if (!empty($keywords)): ?>
                    <div class="table-responsive">
                        <table id="seoKwTable" class="table table-bordered table-striped" style="font-size:13px;">
                            <thead>
                            <tr>
                                <th>Palavra-chave</th>
                                <th>URL alvo</th>
                                <th class="text-center" title="Em quantos artigos a palavra-chave aparece">Artigos</th>
                                <th class="text-center">Posição</th>
                                <th class="text-center">Var. 7d</th>
                                <th class="text-center">Cliques</th>
                                <th class="text-center">Impr.</th>
                                <th class="text-center">CTR</th>
                                <th class="text-center">Fonte</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($keywords as $k): ?>
                                <tr>
                                    <td>
                                        <a href="<?= adminUrl('seo-analysis/keyword/' . $k->id); ?>"><strong><?= esc($k->keyword); ?></strong></a>
                                        <?php if (($k->origin ?? 'manual') === 'content'): ?>
                                            <span class="label label-primary" title="Sincronizada dos artigos">CONTEÚDO</span>
                                        <?php else: ?>
                                            <span class="label label-default" title="Adicionada manualmente">MANUAL</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small class="text-muted"><?= esc($k->target_url ?? '—'); ?></small></td>
                                    <td class="text-center"><?= (int) ($k->post_count ?? 0); ?></td>
                                    <td class="text-center"><?= $k->position !== null ? number_format((float) $k->position, 1, ',', '.') : '<span class="text-muted">—</span>'; ?></td>
                                    <td class="text-center">
                                        <?php if (isset($k->delta) && $k->delta !== null && $k->delta != 0): ?>
                                            <?php if ($k->delta > 0): ?>
                                                <span class="text-green"><i class="fa fa-arrow-up"></i> <?= number_format($k->delta, 1, ',', '.'); ?></span>
                                            <?php else: ?>
                                                <span class="text-red"><i class="fa fa-arrow-down"></i> <?= number_format(abs($k->delta), 1, ',', '.'); ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= number_format((int) ($k->clicks ?? 0), 0, ',', '.'); ?></td>
                                    <td class="text-center"><?= number_format((int) ($k->impressions ?? 0), 0, ',', '.'); ?></td>
                                    <td class="text-center"><?= isset($k->ctr) ? number_format((float) $k->ctr, 1, ',', '.') . '%' : '—'; ?></td>
                                    <td class="text-center">
                                        <?php $src = $k->ranking_source ?? $k->source; ?>
                                        <span class="label label-<?= $src === 'serp' ? 'warning' : 'info'; ?>"><?= esc(strtoupper($src ?? 'GSC')); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="label label-<?= $k->is_active ? 'success' : 'default'; ?>"><?= $k->is_active ? 'Ativa' : 'Pausada'; ?></span>
                                    </td>
                                    <td class="text-center" style="white-space:nowrap;">
                                        <form action="<?= adminUrl('seo-analysis/keyword/toggle'); ?>" method="post" style="display:inline;">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?= (int) $k->id; ?>">
                                            <button class="btn btn-xs btn-default" title="Ativar/Pausar"><i class="fa fa-power-off"></i></button>
                                        </form>
                                        <form action="<?= adminUrl('seo-analysis/keyword/delete'); ?>" method="post" style="display:inline;"
                                              onsubmit="return confirm('Remover esta palavra-chave e seu histórico?');">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?= (int) $k->id; ?>">
                                            <button class="btn btn-xs btn-danger" title="Remover"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center" style="padding:40px;">
                        Nenhuma palavra-chave cadastrada. Use <strong>Sincronizar do conteúdo</strong> para importar as keywords dos artigos.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && jQuery.fn.DataTable && jQuery('#seoKwTable').length) {
        jQuery('#seoKwTable').DataTable({
            order: [],                                   // keep server order (mais usadas primeiro)
            pageLength: 50,
            lengthMenu: [[25, 50, 100, 250], [25, 50, 100, 250]],
            columnDefs: [{ orderable: false, targets: -1 }],
            language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese-Brasil.json' }
        });
    }
});
</script>
