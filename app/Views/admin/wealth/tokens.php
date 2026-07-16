<div class="row">
    <div class="col-sm-12">
        <section class="content-header">
            <h1>Wealth Manager - Tokens</h1>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <form method="get" class="form-inline">
                    <input type="text" name="q" class="form-control" value="<?= esc($query ?? ''); ?>" placeholder="Buscar por e-mail ou usuário" style="min-width:260px;">
                    <button class="btn btn-default">Buscar</button>
                </form>
            </div>
            <div class="box-body">
                <?php if (!empty($users)): ?>
                    <table class="table table-bordered">
                        <thead><tr><th>Usuário</th><th>Email</th><th>Tokens</th><th>Ajuste</th></tr></thead>
                        <tbody>
                        <?php $wm = new \Modules\Wealth\Models\WealthModel(); foreach ($users as $u): ?>
                            <tr>
                                <td><?= esc($u->username); ?></td>
                                <td><?= esc($u->email); ?></td>
                                <td><?= (int)$wm->getTokens($u->id); ?></td>
                                <td>
                                    <form action="<?= base_url('WealthManager/adminTokensPost'); ?>" method="post" class="form-inline">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="user_id" value="<?= (int)$u->id; ?>">
                                        <input type="number" name="delta" class="form-control" style="width:100px;" placeholder="± N">
                                        <input type="text" name="note" class="form-control" placeholder="Observação" style="min-width:240px;">
                                        <button class="btn btn-primary">Aplicar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Busque um usuário para ajustar tokens.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

