<div class="row">
    <div class="col-sm-12">
        <section class="content-header">
            <h1>Wealth Manager</h1>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Visão Geral</h3>
                <div class="box-tools">
                    <a href="<?= adminUrl('wealth/settings'); ?>" class="btn btn-sm btn-default">Configurações</a>
                    <a href="<?= adminUrl('wealth/tokens'); ?>" class="btn btn-sm btn-default">Tokens</a>
                    <a href="<?= adminUrl('wealth/appointments'); ?>" class="btn btn-sm btn-default">Agendamentos</a>
                    <a href="<?= adminUrl('wealth/cms'); ?>" class="btn btn-sm btn-default">CMS</a>
                    <a href="<?= adminUrl('wealth/export'); ?>" class="btn btn-sm btn-default">Exportação</a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-3"><strong>Landing Views:</strong> <?= esc($wmCounters['view_landing'] ?? 0); ?></div>
                    <div class="col-sm-3"><strong>Inícios de sessão:</strong> <?= esc($wmCounters['start_session'] ?? 0); ?></div>
                    <div class="col-sm-3"><strong>Fins de sessão:</strong> <?= esc($wmCounters['end_session'] ?? 0); ?></div>
                    <div class="col-sm-3"><strong>Resultados vistos:</strong> <?= esc($wmCounters['view_results'] ?? 0); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

