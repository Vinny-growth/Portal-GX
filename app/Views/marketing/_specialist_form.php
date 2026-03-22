<?php
$formId = $formId ?? 'gx-specialist-form';
$heading = $heading ?? 'Fale com um especialista';
$description = $description ?? 'Conte o contexto da operacao e retornamos com a frente mais aderente.';
$buttonLabel = $buttonLabel ?? 'Enviar mensagem';
$messagePlaceholder = $messagePlaceholder ?? 'Descreva brevemente sua necessidade, objetivo ou estrutura atual.';
?>
<div class="gx-form-shell">
    <div class="gx-form-intro">
        <h3 class="gx-form-title"><?= esc($heading); ?></h3>
        <p class="gx-form-copy"><?= esc($description); ?></p>
    </div>
    <?= loadView('partials/_messages'); ?>
    <form id="<?= esc($formId); ?>" action="<?= base_url('contact-post'); ?>" method="post" class="gx-form-grid">
        <?= csrf_field(); ?>
        <div class="gx-form-field">
            <label for="<?= esc($formId); ?>-name">Nome</label>
            <input
                id="<?= esc($formId); ?>-name"
                type="text"
                name="name"
                maxlength="199"
                minlength="1"
                pattern=".*\S+.*"
                autocomplete="name"
                enterkeyhint="next"
                value="<?= old('name'); ?>"
                required>
        </div>
        <div class="gx-form-field">
            <label for="<?= esc($formId); ?>-email">E-mail</label>
            <input
                id="<?= esc($formId); ?>-email"
                type="email"
                name="email"
                maxlength="199"
                autocomplete="email"
                inputmode="email"
                enterkeyhint="next"
                value="<?= old('email'); ?>"
                required>
        </div>
        <div class="gx-form-field gx-form-field-full">
            <label for="<?= esc($formId); ?>-message">Contexto</label>
            <textarea
                id="<?= esc($formId); ?>-message"
                name="message"
                maxlength="4970"
                minlength="5"
                rows="4"
                enterkeyhint="send"
                placeholder="<?= esc($messagePlaceholder); ?>"
                required><?= old('message'); ?></textarea>
        </div>
        <input type="text" name="message_content" class="gx-hp" tabindex="-1" autocomplete="off">
        <label class="gx-check">
            <input type="checkbox" required>
            <span>
                Li e concordo com os
                <a href="<?= getPageLinkByDefaultName('terms_conditions', $activeLang->id); ?>" target="_blank" rel="noopener">
                    termos e condicoes
                </a>.
            </span>
        </label>
        <div class="gx-form-field-full gx-captcha-row">
            <?php reCaptcha('generate', $generalSettings); ?>
        </div>
        <div class="gx-form-actions">
            <button type="submit" class="gx-btn gx-btn-primary gx-form-submit"><?= esc($buttonLabel); ?></button>
            <p class="gx-form-note">Formulario otimizado para preenchimento rapido no celular.</p>
        </div>
    </form>
</div>
