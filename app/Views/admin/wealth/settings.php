<div class="row">
    <div class="col-sm-12">
        <section class="content-header">
            <h1>Wealth Manager - Configurações</h1>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Parâmetros</h3>
            </div>
            <form action="<?= base_url('WealthManager/adminSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Modelo de IA (default)</label>
                            <select name="model" class="form-control">
                                <?php $m = $model ?? 'GPT-5'; ?>
                                <option value="GPT-5" <?= $m=='GPT-5'?'selected':''; ?>>GPT-5</option>
                                <option value="Local" <?= $m=='Local'?'selected':''; ?>>Local</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Inflação (%)</label>
                            <input type="text" class="form-control" name="inflacao" value="<?= esc($inflacao ?? '4.0'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label>Crescimento de renda (%)</label>
                            <input type="text" class="form-control" name="crescimento_renda" value="<?= esc($crescimento_renda ?? '2.0'); ?>">
                        </div>
                    </div>
                    <div class="row" style="margin-top:15px;">
                        <div class="col-md-4">
                            <label>Limite de patrimônio (CTA Sênior)</label>
                            <input type="text" class="form-control" name="limit_senior" value="<?= esc($limit_senior ?? '1000000'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label>Creditar tokens após confirmação?</label>
                            <select name="credit_after_confirm" class="form-control">
                                <option value="1" <?= ($credit_after_confirm ?? '1')=='1'?'selected':''; ?>>Sim</option>
                                <option value="0" <?= ($credit_after_confirm ?? '1')=='0'?'selected':''; ?>>Não</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Valor do crédito (tokens)</label>
                            <input type="number" class="form-control" name="credit_amount" value="<?= esc($credit_amount ?? '1'); ?>">
                        </div>
                    </div>
                    <div class="row" style="margin-top:15px;">
                        <div class="col-md-12">
                            <label>Retornos nominais por classe (JSON)</label>
                            <textarea class="form-control" name="returns_by_class" rows="5" placeholder='{"caixa":1.5,"CDB":3.0,"fundos":4.0,"ações":6.0,"previdência":4.0}'><?= esc($returns_by_class ?? ''); ?></textarea>
                            <small>Usado para ajustar a projeção de patrimônio (retorno real calculado com inflação).</small>
                        </div>
                    </div>
                    <div class="row" style="margin-top:15px;">
                        <div class="col-md-12">
                            <label>Textos/labels da interface (JSON)</label>
                            <textarea class="form-control" name="copy_json" rows="6" placeholder='{"headline":".."}'><?= esc($copy_json ?? '{}'); ?></textarea>
                            <small>Edite rótulos e mensagens exibidas ao usuário.</small>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
