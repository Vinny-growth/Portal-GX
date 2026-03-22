<div class="row">
    <div class="col-sm-12">
        <section class="content-header">
            <h1>Wealth Manager - CMS (Landing)</h1>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Editar Landing</h3></div>
            <form action="<?= base_url('WealthManager/adminCMSPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <label>Conteúdo (JSON)</label>
                    <textarea class="form-control" name="landing_json" rows="14" placeholder='{"headline":"...","subheadline":"...","faq":[{"q":"...","a":"..."}]}'
                    ><?= esc($landing ?? '{}'); ?></textarea>
                    <small>Estrutura recomendada: headline, subheadline, faq, testimonials, cta</small>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

