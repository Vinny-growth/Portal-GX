<div class="row">
    <div class="col-sm-12">
        <section class="content-header">
            <h1>Wealth Manager - Exportação</h1>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Exportar Dossiê (CSV)</h3></div>
            <div class="box-body">
                <form action="<?= base_url('WealthManager/adminExportCsv'); ?>" method="post" class="form-inline">
                    <?= csrf_field(); ?>
                    <input type="number" class="form-control" name="user_id" placeholder="ID do usuário" required>
                    <button class="btn btn-primary">Exportar CSV</button>
                </form>
            </div>
        </div>
    </div>
</div>

