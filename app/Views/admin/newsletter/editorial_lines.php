<div class="row">
    <div class="col-sm-12 title-section">
        <h3>Linhas Editoriais <small>(Newsletter IA)</small></h3>
        <a href="<?= adminUrl('newsletter/editorial-lines/new'); ?>" class="btn btn-primary pull-right">
            <i class="fa fa-plus"></i> Nova linha
        </a>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-body">
                <?php if (empty($lines)): ?>
                    <p class="text-muted">Nenhuma linha editorial cadastrada. Crie a primeira para começar a programar envios automáticos.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Slug</th>
                                <th>Categorias</th>
                                <th>Horários</th>
                                <th>Posts/edição</th>
                                <th>IA Auto</th>
                                <th>Status</th>
                                <th>Último envio</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lines as $line): ?>
                                <?php
                                $catIds = $line->category_ids ? json_decode($line->category_ids, true) : [];
                                if (!is_array($catIds)) $catIds = [];
                                $sendTimes = $line->send_times ? json_decode($line->send_times, true) : [];
                                if (!is_array($sendTimes)) $sendTimes = [];
                                $catNames = [];
                                foreach (($categories ?? []) as $c) {
                                    if (in_array((int) $c->id, array_map('intval', $catIds), true)) {
                                        $catNames[] = $c->name;
                                    }
                                }
                                ?>
                                <tr>
                                    <td><strong><?= esc($line->name); ?></strong>
                                        <?php if (!empty($line->description)): ?>
                                            <br><small class="text-muted"><?= esc(mb_substr($line->description, 0, 80)); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><code><?= esc($line->slug); ?></code></td>
                                    <td><?= esc(implode(', ', $catNames) ?: '—'); ?></td>
                                    <td><?= esc(implode(', ', $sendTimes) ?: '—'); ?></td>
                                    <td><?= (int) $line->posts_per_edition; ?></td>
                                    <td>
                                        <?php if ((int) $line->ai_auto_publish === 1): ?>
                                            <span class="label label-success">Auto-publica</span>
                                        <?php else: ?>
                                            <span class="label label-warning">Requer aprovação</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ((int) $line->enabled === 1): ?>
                                            <span class="label label-success">Ativa</span>
                                        <?php else: ?>
                                            <span class="label label-default">Desativada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $line->last_sent_at ? esc($line->last_sent_at) : '—'; ?></td>
                                    <td>
                                        <a href="<?= adminUrl('newsletter/editorial-lines/edit/' . $line->id); ?>" class="btn btn-xs btn-default" title="Editar"><i class="fa fa-pencil"></i></a>
                                        <form action="<?= adminUrl('newsletter/editorial-lines/generate/' . $line->id); ?>" method="post" style="display:inline" onsubmit="return confirm('Gerar uma edição agora como rascunho?');">
                                            <?= csrf_field(); ?>
                                            <button type="submit" class="btn btn-xs btn-info" title="Gerar edição agora"><i class="fa fa-magic"></i></button>
                                        </form>
                                        <form action="<?= adminUrl('newsletter/editorial-lines/delete/' . $line->id); ?>" method="post" style="display:inline" onsubmit="return confirm('Remover linha editorial?');">
                                            <?= csrf_field(); ?>
                                            <button type="submit" class="btn btn-xs btn-danger" title="Remover"><i class="fa fa-trash"></i></button>
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
