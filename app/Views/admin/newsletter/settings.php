<div class="row">
    <div class="col-sm-12 title-section">
        <h3>Configurações da Newsletter</h3>
    </div>
</div>

<form action="<?= adminUrl('newsletter/settings/save'); ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field(); ?>
    <div class="row">
        <div class="col-md-7">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Confirmação (double opt-in)</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="double_opt_in_enabled" value="1" <?= (int) $settings->double_opt_in_enabled === 1 ? 'checked' : ''; ?>>
                            <strong>Ativar double opt-in</strong>
                        </label>
                        <p class="text-muted" style="margin-left: 22px;">
                            Quando ativo: o inscrito recebe um email para confirmar a inscrição antes de ser ativado. Reduz spam e melhora deliverability.
                        </p>
                    </div>
                    <div class="form-group">
                        <label>Assunto do email de confirmação</label>
                        <input type="text" name="confirmation_subject" class="form-control" value="<?= esc($settings->confirmation_subject); ?>">
                    </div>
                    <div class="form-group">
                        <label>Texto do corpo</label>
                        <textarea name="confirmation_intro" class="form-control" rows="3"><?= esc($settings->confirmation_intro); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Texto do botão</label>
                        <input type="text" name="confirmation_button_text" class="form-control" value="<?= esc($settings->confirmation_button_text); ?>">
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Email de boas-vindas</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <label>Assunto</label>
                        <input type="text" name="welcome_subject" class="form-control" value="<?= esc($settings->welcome_subject); ?>">
                    </div>
                    <div class="form-group">
                        <label>Texto do corpo (antes da lista de magnets)</label>
                        <textarea name="welcome_intro" class="form-control" rows="3"><?= esc($settings->welcome_intro); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Landing /newsletter</h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <label>Eyebrow (label superior)</label>
                        <input type="text" name="landing_eyebrow" class="form-control" value="<?= esc($settings->landing_eyebrow); ?>">
                    </div>
                    <div class="form-group">
                        <label>Headline principal</label>
                        <textarea name="landing_headline" class="form-control" rows="2"><?= esc($settings->landing_headline); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Subheadline</label>
                        <textarea name="landing_subheadline" class="form-control" rows="2"><?= esc($settings->landing_subheadline); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Texto do CTA</label>
                        <input type="text" name="landing_cta_text" class="form-control" value="<?= esc($settings->landing_cta_text); ?>">
                    </div>
                    <div class="form-group">
                        <label>Prova social</label>
                        <input type="text" name="landing_social_proof" class="form-control" value="<?= esc($settings->landing_social_proof); ?>" placeholder="Ex.: Mais de 1.500 executivos recebem">
                    </div>
                    <div class="form-group">
                        <label>Hero image</label>
                        <?php if (!empty($settings->landing_hero_image)): ?>
                            <div style="margin-bottom:8px;">
                                <img src="/<?= esc(ltrim($settings->landing_hero_image, '/')); ?>" style="max-width:100%;border:1px solid #ddd;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="landing_hero_image" accept="image/*">
                        <small class="text-muted d-block">JPG, PNG ou WEBP. Recomendado 1600x1000.</small>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Salvar configurações</button>
                </div>
            </div>
        </div>
    </div>
</form>
