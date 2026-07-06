<?php
$formId = $formId ?? 'gx-specialist-form';
$heading = $heading ?? lang('Marketing.form_heading');
$description = $description ?? lang('Marketing.form_description');
$buttonLabel = $buttonLabel ?? lang('Marketing.form_button');
$messagePlaceholder = $messagePlaceholder ?? lang('Marketing.form_placeholder');
$leadOrigin = $leadOrigin ?? (lang('Marketing.form_origin_prefix') . ((string) parse_url(current_url(), PHP_URL_PATH) ?: '/'));
?>
<div class="gx-form-shell">
    <div class="gx-form-intro">
        <h3 class="gx-form-title"><?= esc($heading); ?></h3>
        <p class="gx-form-copy"><?= esc($description); ?></p>
    </div>
    <?= loadView('partials/_messages'); ?>
    <form id="<?= esc($formId); ?>" action="<?= base_url('contact-post'); ?>" method="post" class="gx-form-grid">
        <?= csrf_field(); ?>
        <input type="hidden" name="landing_page" value="<?= esc(current_url()); ?>">
        <input type="hidden" name="lead_origin" value="<?= esc($leadOrigin); ?>">
        <input type="hidden" name="form_ts" value="<?= time(); ?>">
        <input type="hidden" name="utm_source" value="">
        <input type="hidden" name="utm_medium" value="">
        <input type="hidden" name="utm_campaign" value="">
        <input type="hidden" name="utm_term" value="">
        <input type="hidden" name="utm_content" value="">
        <div class="gx-form-field">
            <label for="<?= esc($formId); ?>-name"><?= lang('Marketing.form_nome'); ?></label>
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
            <label for="<?= esc($formId); ?>-email"><?= lang('Marketing.form_email'); ?></label>
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
        <?= view('partials/_lead_phone_field', [
            'fieldIdPrefix' => $formId . '-phone',
            'wrapperClass' => 'gx-form-field',
            'countryValue' => old('phone_country'),
            'phoneValue' => old('phone'),
        ]); ?>
        <div class="gx-form-field gx-form-field-full">
            <label for="<?= esc($formId); ?>-message"><?= lang('Marketing.form_contexto'); ?></label>
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
                <?= lang('Marketing.form_terms_pre'); ?>
                <a href="<?= getPageLinkByDefaultName('terms_conditions', $activeLang->id); ?>" target="_blank" rel="noopener">
                    <?= lang('Marketing.form_terms_link'); ?>
                </a>.
            </span>
        </label>
        <?php if (isRecaptchaEnabled($generalSettings)): ?>
            <div class="gx-form-field-full gx-captcha-row">
                <div
                    class="gx-captcha-shell"
                    data-gx-recaptcha
                    data-sitekey="<?= esc($generalSettings->recaptcha_site_key); ?>"
                    data-theme="<?= $darkMode ? 'dark' : 'light'; ?>"
                    data-lang="<?= esc($activeLang->short_form); ?>">
                    <div class="gx-captcha-placeholder"><?= lang('Marketing.form_captcha_hint'); ?></div>
                </div>
            </div>
        <?php endif; ?>
        <div class="gx-form-actions">
            <button type="submit" class="gx-btn gx-btn-primary gx-form-submit"><?= esc($buttonLabel); ?></button>
            <p class="gx-form-note"><?= lang('Marketing.form_note_mobile'); ?></p>
        </div>
    </form>
</div>
<script>
(function() {
    var form = document.getElementById(<?= json_encode($formId); ?>);
    if (!form) return;

    /* Hydrate UTM hidden fields from URL/sessionStorage */
    if (typeof gxHydrateUtmFields === 'function') gxHydrateUtmFields(form);

    /* Fire conversion events on submit */
    form.addEventListener('submit', function() {
        var origin = <?= json_encode($leadOrigin); ?>;
        if (typeof gxFbq === 'function') gxFbq('track', 'Lead', { content_name: origin, currency: 'BRL', value: 1 });
        if (typeof gxGtag === 'function') gxGtag('event', 'generate_lead', { event_category: 'conversion', event_label: origin, currency: 'BRL', value: 1 });
    });
})();
</script>
