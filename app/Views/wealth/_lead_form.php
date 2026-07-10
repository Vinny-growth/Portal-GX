<?php
$formId = $formId ?? 'gx-wealth-lead-form';
$formAction = $formAction ?? base_url('wealth/agendar');
$ajaxUrl = $ajaxUrl ?? base_url('wealth/lead');
$sourcePage = $sourcePage ?? 'landing';
$formTitle = $formTitle ?? lang('Wealth.lf_title');
$formDescription = $formDescription ?? brandLang('Wealth.lf_desc');
$submitLabel = $submitLabel ?? lang('Wealth.lf_submit');
$successTitle = $successTitle ?? lang('Wealth.lf_success_title');
$successText = $successText ?? lang('Wealth.lf_success_text');
$goalOptions = $goalOptions ?? [
    'Blindar patrimônio e liquidez' => lang('Wealth.lf_goal1'),
    'Gerar mais renda recorrente' => lang('Wealth.lf_goal2'),
    'Organizar crescimento e alocação' => lang('Wealth.lf_goal3'),
    'Planejar legado e sucessão' => lang('Wealth.lf_goal4'),
];
$patrimonyOptions = $patrimonyOptions ?? [
    'Até R$ 300 mil' => lang('Wealth.lf_patr1'),
    'De R$ 300 mil a R$ 1 milhão' => lang('Wealth.lf_patr2'),
    'De R$ 1 milhão a R$ 5 milhões' => lang('Wealth.lf_patr3'),
    'Acima de R$ 5 milhões' => lang('Wealth.lf_patr4'),
];
$preferredSlots = $preferredSlots ?? [
    'Manhã em dias úteis' => lang('Wealth.lf_slot1'),
    'Tarde em dias úteis' => lang('Wealth.lf_slot2'),
    'Noite' => lang('Wealth.lf_slot3'),
    'Prefiro retorno por WhatsApp ou e-mail primeiro' => lang('Wealth.lf_slot4'),
];
$prefillName = old('name');
if (empty($prefillName) && authCheck()) {
    $prefillName = user()->username ?? '';
}
$prefillEmail = old('email');
if (empty($prefillEmail) && authCheck()) {
    $prefillEmail = user()->email ?? '';
}
$selectedGoal = old('goal');
$selectedRange = old('patrimony_range');
$selectedSlot = old('preferred_slot');
$termsUrl = getPageLinkByDefaultName('terms_conditions', $activeLang->id);
?>
<div class="gx-wealth-form-shell" id="<?= esc($formId); ?>">
    <div class="gx-wealth-form-copy">
        <strong class="gx-wealth-form-title"><?= esc($formTitle); ?></strong>
        <p><?= esc($formDescription); ?></p>
    </div>

    <?= loadView('partials/_messages'); ?>

    <form
        id="<?= esc($formId); ?>-form"
        class="gx-wealth-form js-wealth-lead-form"
        action="<?= esc($formAction); ?>"
        method="post"
        data-ajax-url="<?= esc($ajaxUrl); ?>"
        novalidate>
        <?= csrf_field(); ?>
        <input type="hidden" name="source_page" value="<?= esc($sourcePage); ?>">
        <input type="hidden" name="landing_page" value="<?= esc(current_url()); ?>">
        <input type="hidden" name="diagnosis_invested" value="">
        <input type="hidden" name="diagnosis_monthly_invest" value="">
        <input type="hidden" name="diagnosis_monthly_cost" value="">
        <input type="hidden" name="diagnosis_target_capital" value="">
        <input type="hidden" name="diagnosis_projection_10y" value="">
        <input type="hidden" name="diagnosis_gap" value="">
        <input type="hidden" name="diagnosis_coverage_pct" value="">
        <input type="hidden" name="diagnosis_objective" value="">
        <input type="hidden" name="utm_source" value="">
        <input type="hidden" name="utm_medium" value="">
        <input type="hidden" name="utm_campaign" value="">
        <input type="hidden" name="utm_term" value="">
        <input type="hidden" name="utm_content" value="">
        <input type="text" name="company_website" class="gx-hp" tabindex="-1" autocomplete="off">

        <div class="gx-wealth-form-grid">
            <label class="gx-wealth-field">
                <span><?= lang('Wealth.lf_field_nome'); ?></span>
                <input type="text" name="name" value="<?= esc($prefillName); ?>" maxlength="199" required autocomplete="name">
            </label>

            <label class="gx-wealth-field">
                <span><?= lang('Wealth.lf_field_email'); ?></span>
                <input type="email" name="email" value="<?= esc($prefillEmail); ?>" maxlength="199" required autocomplete="email" inputmode="email">
            </label>

            <label class="gx-wealth-field">
                <span><?= lang('Wealth.lf_field_phone'); ?></span>
                <input type="tel" name="phone" value="<?= esc(old('phone')); ?>" placeholder="(11) 99999-9999" required autocomplete="tel">
            </label>

            <label class="gx-wealth-field">
                <span><?= lang('Wealth.lf_field_patr'); ?></span>
                <select name="patrimony_range" required>
                    <option value=""><?= lang('Wealth.lf_select'); ?></option>
                    <?php foreach ($patrimonyOptions as $value => $label): ?>
                        <option value="<?= esc($value); ?>" <?= $selectedRange === $value ? 'selected' : ''; ?>><?= esc($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="gx-wealth-field gx-wealth-field-full">
                <span><?= lang('Wealth.lf_field_goal'); ?></span>
                <select name="goal" required>
                    <option value=""><?= lang('Wealth.lf_select'); ?></option>
                    <?php foreach ($goalOptions as $value => $label): ?>
                        <option value="<?= esc($value); ?>" <?= $selectedGoal === $value ? 'selected' : ''; ?>><?= esc($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="gx-wealth-field gx-wealth-field-full">
                <span><?= lang('Wealth.lf_field_slot'); ?></span>
                <select name="preferred_slot">
                    <option value=""><?= lang('Wealth.lf_opcional'); ?></option>
                    <?php foreach ($preferredSlots as $value => $label): ?>
                        <option value="<?= esc($value); ?>" <?= $selectedSlot === $value ? 'selected' : ''; ?>><?= esc($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="gx-wealth-field gx-wealth-field-full">
                <span><?= lang('Wealth.lf_field_msg'); ?></span>
                <textarea name="message" rows="4" placeholder="<?= esc(lang('Wealth.lf_msg_ph')); ?>"><?= esc(old('message')); ?></textarea>
            </label>
        </div>

        <label class="gx-check">
            <input type="checkbox" required>
            <span>
                <?= brandLang('Wealth.lf_consent_pre'); ?>

                <a href="<?= esc($termsUrl); ?>" target="_blank" rel="noopener"><?= lang('Wealth.lf_consent_link'); ?></a>.
            </span>
        </label>

        <div class="gx-wealth-form-actions">
            <button type="submit" class="gx-btn gx-btn-primary gx-btn-lg" data-default-label="<?= esc($submitLabel); ?>"><?= esc($submitLabel); ?></button>
            <p class="gx-wealth-form-note"><?= lang('Wealth.lf_note'); ?></p>
        </div>

        <p class="gx-wealth-form-feedback" aria-live="polite"></p>
    </form>

    <div class="gx-wealth-form-success" hidden>
        <strong><?= esc($successTitle); ?></strong>
        <p><?= esc($successText); ?></p>
        <a href="<?= esc($blogUrl ?? langBaseUrl('blog')); ?>" class="gx-btn gx-btn-secondary"><?= lang('Wealth.lf_success_cta'); ?></a>
    </div>
</div>
