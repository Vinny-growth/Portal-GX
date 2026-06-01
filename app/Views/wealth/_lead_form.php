<?php
$formId = $formId ?? 'gx-wealth-lead-form';
$formAction = $formAction ?? base_url('wealth/agendar');
$ajaxUrl = $ajaxUrl ?? base_url('wealth/lead');
$sourcePage = $sourcePage ?? 'landing';
$formTitle = $formTitle ?? 'Receba um diagnóstico com um especialista';
$formDescription = $formDescription ?? 'Preencha os dados principais. A equipe da GX Capital retorna com uma leitura consultiva e próximos passos possíveis.';
$submitLabel = $submitLabel ?? 'Quero meu diagnóstico';
$successTitle = $successTitle ?? 'Diagnóstico recebido';
$successText = $successText ?? 'Seu contexto já entrou na fila consultiva. O retorno acontece com próximos passos objetivos, não com mensagem genérica.';
$goalOptions = $goalOptions ?? [
    'Blindar patrimônio e liquidez' => 'Blindar patrimônio e liquidez',
    'Gerar mais renda recorrente' => 'Gerar mais renda recorrente',
    'Organizar crescimento e alocação' => 'Organizar crescimento e alocação',
    'Planejar legado e sucessão' => 'Planejar legado e sucessão',
];
$patrimonyOptions = $patrimonyOptions ?? [
    'Até R$ 300 mil' => 'Até R$ 300 mil',
    'De R$ 300 mil a R$ 1 milhão' => 'De R$ 300 mil a R$ 1 milhão',
    'De R$ 1 milhão a R$ 5 milhões' => 'De R$ 1 milhão a R$ 5 milhões',
    'Acima de R$ 5 milhões' => 'Acima de R$ 5 milhões',
];
$preferredSlots = $preferredSlots ?? [
    'Manhã em dias úteis' => 'Manhã em dias úteis',
    'Tarde em dias úteis' => 'Tarde em dias úteis',
    'Noite' => 'Noite',
    'Prefiro retorno por WhatsApp ou e-mail primeiro' => 'Prefiro retorno por WhatsApp ou e-mail primeiro',
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
                <span>Nome</span>
                <input type="text" name="name" value="<?= esc($prefillName); ?>" maxlength="199" required autocomplete="name">
            </label>

            <label class="gx-wealth-field">
                <span>E-mail</span>
                <input type="email" name="email" value="<?= esc($prefillEmail); ?>" maxlength="199" required autocomplete="email" inputmode="email">
            </label>

            <label class="gx-wealth-field">
                <span>Telefone com DDD</span>
                <input type="tel" name="phone" value="<?= esc(old('phone')); ?>" placeholder="(11) 99999-9999" required autocomplete="tel">
            </label>

            <label class="gx-wealth-field">
                <span>Faixa patrimonial</span>
                <select name="patrimony_range" required>
                    <option value="">Selecione</option>
                    <?php foreach ($patrimonyOptions as $value => $label): ?>
                        <option value="<?= esc($value); ?>" <?= $selectedRange === $value ? 'selected' : ''; ?>><?= esc($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="gx-wealth-field gx-wealth-field-full">
                <span>Objetivo principal</span>
                <select name="goal" required>
                    <option value="">Selecione</option>
                    <?php foreach ($goalOptions as $value => $label): ?>
                        <option value="<?= esc($value); ?>" <?= $selectedGoal === $value ? 'selected' : ''; ?>><?= esc($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="gx-wealth-field gx-wealth-field-full">
                <span>Melhor formato para o primeiro retorno</span>
                <select name="preferred_slot">
                    <option value="">Opcional</option>
                    <?php foreach ($preferredSlots as $value => $label): ?>
                        <option value="<?= esc($value); ?>" <?= $selectedSlot === $value ? 'selected' : ''; ?>><?= esc($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="gx-wealth-field gx-wealth-field-full">
                <span>Contexto adicional</span>
                <textarea name="message" rows="4" placeholder="Ex.: patrimônio hoje concentrado em caixa, necessidade de renda recorrente, liquidez para empresa ou reorganização de carteira."><?= esc(old('message')); ?></textarea>
            </label>
        </div>

        <label class="gx-check">
            <input type="checkbox" required>
            <span>
                Autorizo contato consultivo da GX Capital e concordo com os
                <a href="<?= esc($termsUrl); ?>" target="_blank" rel="noopener">termos e condições</a>.
            </span>
        </label>

        <div class="gx-wealth-form-actions">
            <button type="submit" class="gx-btn gx-btn-primary gx-btn-lg" data-default-label="<?= esc($submitLabel); ?>"><?= esc($submitLabel); ?></button>
            <p class="gx-wealth-form-note">Leitura inicial rápida, com contexto patrimonial e próximos passos possíveis.</p>
        </div>

        <p class="gx-wealth-form-feedback" aria-live="polite"></p>
    </form>

    <div class="gx-wealth-form-success" hidden>
        <strong><?= esc($successTitle); ?></strong>
        <p><?= esc($successText); ?></p>
        <a href="<?= esc($blogUrl ?? langBaseUrl('blog')); ?>" class="gx-btn gx-btn-secondary">Explorar conteúdos enquanto aguardamos</a>
    </div>
</div>
