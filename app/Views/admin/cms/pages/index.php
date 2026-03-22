<div class="row">
    <div class="col-sm-12">
        <section class="content-header"><h1>Páginas (CMS)</h1></section>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Criar nova página</h3></div>
            <div class="box-body">
                <form method="post" action="<?= adminUrl('cms-pages/add'); ?>" class="form-inline">
                    <?= csrf_field(); ?>
                    <input type="text" class="form-control" name="title" placeholder="Título" required>
                    <input type="text" class="form-control" name="slug" placeholder="slug-exemplo" required>
                    <button class="btn btn-primary">Criar</button>
                </form>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Páginas</h3></div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead><tr><th>ID</th><th>Título</th><th>Slug</th><th>Status</th><th>Ações</th></tr></thead>
                    <tbody>
                    <?php if (!empty($pages)): foreach ($pages as $p): ?>
                        <tr>
                            <td><?= (int)$p->id; ?></td>
                            <td><?= esc($p->title); ?></td>
                            <td><?= esc($p->slug); ?></td>
                            <td><?= esc($p->status); ?></td>
                            <td>
                                <a class="btn btn-xs btn-default" href="<?= adminUrl('cms-pages/edit/'.(int)$p->id); ?>">editar</a>
                                <a class="btn btn-xs btn-default" href="<?= adminUrl('cms-pages/builder/'.(int)$p->id); ?>">builder</a>
                                <a class="btn btn-xs btn-success" href="<?= adminUrl('cms-pages/publish/'.(int)$p->id); ?>">publicar</a>
                                <?php if (!empty($p->published_json)): ?>
                                    <a class="btn btn-xs btn-default" href="<?= adminUrl('cms-pages/restore/'.(int)$p->id); ?>">restaurar rascunho</a>
                                <?php endif; ?>
                                <a class="btn btn-xs btn-danger" onclick="return confirm('Remover página?');" href="<?= adminUrl('cms-pages/delete/'.(int)$p->id); ?>">remover</a>
                                <?php if (!empty($p->published_json)): ?>
                                    <a class="btn btn-xs btn-info" target="_blank" href="<?= base_url('p/'.$p->slug); ?>">ver</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="5">Sem páginas</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
