<div class="row">
    <div class="col-sm-12">
        <section class="content-header">
            <h1>Wealth Manager - Sessões</h1>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-body">
                <table class="table table-striped">
                    <thead><tr><th>ID</th><th>Usuário</th><th>Início</th><th>Fim</th><th>Status</th><th>Msgs</th><th>Ação</th></tr></thead>
                    <tbody>
                    <?php if (!empty($sessions)): foreach ($sessions as $s): ?>
                        <tr>
                            <td><?= (int)$s->id; ?></td>
                            <td><?= (int)$s->user_id; ?></td>
                            <td><?= esc($s->started_at); ?></td>
                            <td><?= esc($s->ended_at); ?></td>
                            <td><?= esc($s->status); ?></td>
                            <td><?= (int)$s->messages_count; ?></td>
                            <td><a class="btn btn-xs btn-default" href="<?= adminUrl('wealth/session/' . (int)$s->id); ?>">ver</a></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="7">Sem sessões</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

