<div class="row">
    <div class="col-sm-12">
        <section class="content-header">
            <h1>Wealth Manager - Logs/Auditoria</h1>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-body">
                <table class="table table-striped">
                    <thead><tr><th>ID</th><th>Admin</th><th>Ação</th><th>Detalhes</th><th>Data</th></tr></thead>
                    <tbody>
                    <?php if (!empty($logs)): foreach ($logs as $l): ?>
                        <tr>
                            <td><?= (int)$l->id; ?></td>
                            <td><?= (int)$l->admin_id; ?></td>
                            <td><?= esc($l->acao); ?></td>
                            <td><small><?= esc($l->detalhes); ?></small></td>
                            <td><?= esc($l->created_at); ?></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="5">Sem registros</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

