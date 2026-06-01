<div class="row">
    <div class="col-sm-12 title-section">
        <h3>Lead Magnets</h3>
        <a href="<?= adminUrl('newsletter/magnets/new'); ?>" class="btn btn-primary pull-right">
            <i class="fa fa-plus"></i> Novo magnet
        </a>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-body">
                <?php if (empty($magnets)): ?>
                    <p class="text-muted">Nenhum magnet cadastrado. Crie o primeiro para começar a entregar materiais aos novos inscritos.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Título</th>
                                <th>Slug</th>
                                <th>Downloads</th>
                                <th>Arquivo</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($magnets as $m): ?>
                                <tr>
                                    <td style="width:60px;">
                                        <?php if (!empty($m->cover_image)): ?>
                                            <img src="<?= esc($m->cover_image); ?>" style="width:50px;height:50px;object-fit:cover;">
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= esc($m->title); ?></strong>
                                        <?php if (!empty($m->description)): ?>
                                            <br><small class="text-muted"><?= esc(mb_substr($m->description, 0, 80)); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><code><?= esc($m->slug); ?></code></td>
                                    <td><?= (int) $m->downloads_count; ?></td>
                                    <td>
                                        <?php if (!empty($m->file_path)): ?>
                                            <i class="fa fa-file-pdf-o"></i> <?= esc(basename($m->file_path)); ?>
                                            <?php if (!empty($m->file_size)): ?>
                                                <br><small><?= number_format($m->file_size / 1024, 0); ?> KB</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="label label-warning">sem arquivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ((int) $m->active === 1): ?>
                                            <span class="label label-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="label label-default">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= adminUrl('newsletter/magnets/edit/' . $m->id); ?>" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>
                                        <form action="<?= adminUrl('newsletter/magnets/delete/' . $m->id); ?>" method="post" style="display:inline" onsubmit="return confirm('Remover este magnet?');">
                                            <?= csrf_field(); ?>
                                            <button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                                        </form>
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
