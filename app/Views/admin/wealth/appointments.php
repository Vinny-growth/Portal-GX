<div class="row">
    <div class="col-sm-12">
        <section class="content-header">
            <h1>Wealth Manager - Agendamentos</h1>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr><th>ID</th><th>Usuário</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Preferência</th><th>Status</th><th>Ação</th></tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($appointments)): foreach ($appointments as $a): ?>
                        <tr>
                            <td><?= (int)$a->id; ?></td>
                            <td>
                                <?= (int)$a->user_id; ?>
                                <?php if (!empty($a->user_id)): ?>
                                    <a class="btn btn-xs btn-default" target="_blank" href="<?= adminUrl('wealth/view-result/' . (int)$a->user_id); ?>">ver resultado</a>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($a->nome); ?></td>
                            <td><?= esc($a->email); ?></td>
                            <td><?= esc($a->telefone); ?></td>
                            <td><?= esc($a->preferencia_horario); ?></td>
                            <td><?= esc($a->status); ?></td>
                            <td>
                                <form action="<?= base_url('WealthManager/adminAppointmentStatusPost'); ?>" method="post" class="form-inline">
                                    <?= csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?= (int)$a->id; ?>">
                                    <select name="status" class="form-control">
                                        <?php foreach (['novo','confirmado','atendido'] as $s): ?>
                                            <option value="<?= $s; ?>" <?= $a->status==$s?'selected':''; ?>><?= ucfirst($s); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-primary btn-sm">Salvar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="8">Sem agendamentos</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
