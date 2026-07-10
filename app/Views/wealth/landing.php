<?php
$l = is_array($landing ?? null) ? $landing : [];
$heroTitle = trim((string)($l['headline'] ?? lang('Wealth.l_hero_title')));
$heroSubtitle = trim((string)($l['subheadline'] ?? lang('Wealth.l_hero_sub')));
$heroBadge = $copy['landing']['hero_badge'] ?? brandLang('Wealth.l_hero_badge');
$primaryCta = $copy['landing']['primary_cta_label'] ?? lang('Wealth.l_cta_primary');
$secondaryCta = $copy['landing']['secondary_cta_label'] ?? lang('Wealth.l_cta_secondary');
$leadFormTitle = $copy['landing']['form_title'] ?? lang('Wealth.l_form_title');
$leadFormDescription = $copy['landing']['form_description'] ?? lang('Wealth.l_form_desc');
$leadFormButton = $copy['landing']['form_button_label'] ?? lang('Wealth.l_form_button');
$faqItems = !empty($l['faq']) && is_array($l['faq']) ? $l['faq'] : lang('Wealth.l_faq');
$signalCards = [
    ['title' => lang('Wealth.l_sig1_t'), 'text' => lang('Wealth.l_sig1_x')],
    ['title' => lang('Wealth.l_sig2_t'), 'text' => lang('Wealth.l_sig2_x')],
    ['title' => lang('Wealth.l_sig3_t'), 'text' => lang('Wealth.l_sig3_x')],
    ['title' => lang('Wealth.l_sig4_t'), 'text' => lang('Wealth.l_sig4_x')],
];
$deliverables = [
    ['title' => lang('Wealth.l_del1_t'), 'text' => lang('Wealth.l_del1_x'), 'chips' => [lang('Wealth.l_del1_c1'), lang('Wealth.l_del1_c2'), lang('Wealth.l_del1_c3')]],
    ['title' => lang('Wealth.l_del2_t'), 'text' => lang('Wealth.l_del2_x'), 'chips' => [lang('Wealth.l_del2_c1'), lang('Wealth.l_del2_c2'), lang('Wealth.l_del2_c3')]],
    ['title' => lang('Wealth.l_del3_t'), 'text' => lang('Wealth.l_del3_x'), 'chips' => [lang('Wealth.l_del3_c1'), lang('Wealth.l_del3_c2'), lang('Wealth.l_del3_c3')]],
];
$contactPhone = !empty($baseSettings->contact_phone) ? preg_replace('/[^0-9+]/', '', (string)$baseSettings->contact_phone) : '';
?>
<main class="gx-marketing gx-wealth">
    <nav class="gx-nav" id="gx-nav">
        <div class="gx-nav-inner">
            <a href="<?= langBaseUrl(); ?>" class="gx-nav-brand" aria-label="<?= esc(brand('display_name')); ?>">
                <img src="<?= getLogo(); ?>" alt="<?= esc(brand('display_name')); ?>" width="<?= getLogoSize('width'); ?>" height="<?= getLogoSize('height'); ?>">
            </a>

            <div class="gx-nav-links" id="gx-nav-links">
                <a href="#diagnostico" class="gx-nav-link"><?= lang('Wealth.l_nav_diag'); ?></a>
                <a href="#metodo" class="gx-nav-link"><?= lang('Wealth.l_nav_metodo'); ?></a>
                <a href="#entregaveis" class="gx-nav-link"><?= lang('Wealth.l_nav_entreg'); ?></a>
                <a href="#faq" class="gx-nav-link"><?= lang('Wealth.l_nav_faq'); ?></a>
                <div class="gx-nav-menu-extra">
                    <?php if (!empty($isAuthenticated)): ?>
                        <a href="<?= esc($wealthConversationUrl); ?>" class="gx-nav-link" data-wealth-track="wealth_continue_area"><?= lang('Wealth.l_nav_area'); ?></a>
                    <?php elseif (($generalSettings->registration_system ?? 0) == 1): ?>
                        <a href="#" class="gx-nav-link" data-bs-toggle="modal" data-bs-target="#modalLogin"><?= lang('Wealth.l_nav_entrar'); ?></a>
                    <?php else: ?>
                        <a href="<?= esc($blogUrl); ?>" class="gx-nav-link"><?= lang('Wealth.l_nav_blog'); ?></a>
                    <?php endif; ?>
                    <a href="#fale-com-especialista" class="gx-btn gx-btn-primary" data-wealth-track="start_signup"><?= esc($primaryCta); ?></a>
                </div>
            </div>

            <div class="gx-nav-right">
                <?php if (!empty($isAuthenticated)): ?>
                    <a href="<?= esc($wealthConversationUrl); ?>" class="gx-nav-link" data-wealth-track="wealth_continue_area"><?= lang('Wealth.l_nav_area'); ?></a>
                <?php elseif (($generalSettings->registration_system ?? 0) == 1): ?>
                    <a href="#" class="gx-nav-link" data-bs-toggle="modal" data-bs-target="#modalLogin"><?= lang('Wealth.l_nav_entrar'); ?></a>
                <?php else: ?>
                    <a href="<?= esc($blogUrl); ?>" class="gx-nav-link"><?= lang('Wealth.l_nav_blog'); ?></a>
                <?php endif; ?>
                <a href="#fale-com-especialista" class="gx-btn gx-btn-primary" id="gx-wealth-primary-cta" data-wealth-track="start_signup"><?= esc($primaryCta); ?></a>
                <button type="button" class="gx-nav-toggle" id="gx-nav-toggle" aria-expanded="false" aria-controls="gx-nav-links" aria-label="<?= lang('Wealth.l_nav_menu'); ?>">
                    <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
            </div>
        </div>
    </nav>

    <section class="gx-hero">
        <div class="gx-hero-inner">
            <div class="gx-hero-content" data-gx-reveal>
                <div class="gx-hero-badge">
                    <span class="gx-hero-badge-dot"></span>
                    <?= esc($heroBadge); ?>
                </div>

                <h1 class="gx-hero-title"><?= esc($heroTitle); ?></h1>
                <p class="gx-hero-sub"><?= esc($heroSubtitle); ?></p>

                <div class="gx-hero-cta">
                    <a href="#diagnostico" class="gx-btn gx-btn-primary gx-btn-lg" data-wealth-track="start_signup"><?= esc($primaryCta); ?></a>
                    <a href="#metodo" class="gx-btn gx-btn-ghost gx-btn-lg"><?= esc($secondaryCta); ?></a>
                </div>

                <div class="gx-hero-proof">
                    <div class="gx-hero-proof-item">
                        <strong><?= lang('Wealth.l_proof1_t'); ?></strong>
                        <span><?= lang('Wealth.l_proof1_s'); ?></span>
                    </div>
                    <div class="gx-hero-proof-item">
                        <strong><?= lang('Wealth.l_proof2_t'); ?></strong>
                        <span><?= lang('Wealth.l_proof2_s'); ?></span>
                    </div>
                    <div class="gx-hero-proof-item">
                        <strong><?= lang('Wealth.l_proof3_t'); ?></strong>
                        <span><?= lang('Wealth.l_proof3_s'); ?></span>
                    </div>
                </div>

                <?php if (!empty($isAuthenticated)): ?>
                    <div class="gx-wealth-auth-note">
                        <strong><?= lang('Wealth.l_auth_strong'); ?></strong>
                        <?= lang('Wealth.l_auth_text'); ?>

                        <a href="<?= esc($wealthConversationUrl); ?>" data-wealth-track="wealth_continue_area"><?= lang('Wealth.l_auth_link'); ?></a>.
                    </div>
                <?php endif; ?>
            </div>

            <div class="gx-hero-aside" data-gx-reveal data-gx-delay="140">
                <div class="gx-wealth-hero-panel">
                    <div class="gx-wealth-panel-top">
                        <div>
                            <p class="gx-wealth-panel-kicker"><?= lang('Wealth.l_panel_kicker'); ?></p>
                            <h2 class="gx-wealth-panel-title"><?= lang('Wealth.l_panel_title'); ?></h2>
                        </div>
                        <span class="gx-wealth-panel-badge"><?= lang('Wealth.l_panel_badge'); ?></span>
                    </div>

                    <div class="gx-wealth-mini-grid">
                        <div class="gx-wealth-mini-card">
                            <span><?= lang('Wealth.l_mini1_s'); ?></span>
                            <strong><?= lang('Wealth.l_mini1_t'); ?></strong>
                        </div>
                        <div class="gx-wealth-mini-card">
                            <span><?= lang('Wealth.l_mini2_s'); ?></span>
                            <strong><?= lang('Wealth.l_mini2_t'); ?></strong>
                        </div>
                        <div class="gx-wealth-mini-card">
                            <span><?= lang('Wealth.l_mini3_s'); ?></span>
                            <strong><?= lang('Wealth.l_mini3_t'); ?></strong>
                        </div>
                        <div class="gx-wealth-mini-card">
                            <span><?= lang('Wealth.l_mini4_s'); ?></span>
                            <strong><?= lang('Wealth.l_mini4_t'); ?></strong>
                        </div>
                    </div>

                    <?php if (!empty($memberProgress)): ?>
                        <div class="gx-wealth-member-progress">
                            <strong><?= esc((string)$memberProgress['pct']); ?>% do mapeamento concluído</strong>
                            <div class="gx-wealth-progress-bar">
                                <div class="gx-wealth-progress-fill" style="width: <?= esc((string)$memberProgress['pct']); ?>%;"></div>
                            </div>
                            <span class="gx-wealth-insight-caption"><?= esc((string)$memberProgress['score']); ?> de <?= esc((string)$memberProgress['total']); ?> etapas preenchidas na área completa.</span>
                        </div>
                    <?php else: ?>
                        <div class="gx-wealth-path">
                            <div class="gx-wealth-path-step is-active"><?= lang('Wealth.l_path1'); ?></div>
                            <div class="gx-wealth-path-step"><?= lang('Wealth.l_path2'); ?></div>
                            <div class="gx-wealth-path-step"><?= lang('Wealth.l_path3'); ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="gx-hero-stat-row">
                    <div class="gx-hero-metric">
                        <strong>60s</strong>
                        <span><?= lang('Wealth.l_stat1_s'); ?></span>
                    </div>
                    <div class="gx-hero-metric">
                        <strong>3</strong>
                        <span><?= lang('Wealth.l_stat2_s'); ?></span>
                    </div>
                    <div class="gx-hero-metric">
                        <strong>360°</strong>
                        <span><?= lang('Wealth.l_stat3_s'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="gx-strip">
        <div class="gx-strip-inner" data-gx-reveal>
            <span class="gx-strip-lead"><?= lang('Wealth.l_strip_lead'); ?></span>
            <span class="gx-strip-item"><?= lang('Wealth.l_strip1'); ?></span>
            <span class="gx-strip-item"><?= lang('Wealth.l_strip2'); ?></span>
            <span class="gx-strip-item"><?= lang('Wealth.l_strip3'); ?></span>
            <span class="gx-strip-item"><?= lang('Wealth.l_strip4'); ?></span>
            <span class="gx-strip-item"><?= lang('Wealth.l_strip5'); ?></span>
        </div>
    </div>

    <section class="gx-section" id="diagnostico">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label"><?= lang('Wealth.l_diag_label'); ?></p>
                    <h2 class="gx-section-title"><?= lang('Wealth.l_diag_title'); ?></h2>
                </div>
                <p class="gx-section-desc">
                    <?= lang('Wealth.l_diag_desc'); ?>

                </p>
            </div>

            <div class="gx-wealth-diagnostic-grid">
                <article class="gx-wealth-diagnostic-card" data-gx-reveal>
                    <div class="gx-wealth-objective-grid">
                        <button type="button" class="gx-wealth-objective is-active" data-wealth-objective="Blindar patrimônio e liquidez"><?= lang('Wealth.l_obj1'); ?></button>
                        <button type="button" class="gx-wealth-objective" data-wealth-objective="Gerar mais renda recorrente"><?= lang('Wealth.l_obj2'); ?></button>
                        <button type="button" class="gx-wealth-objective" data-wealth-objective="Organizar crescimento e alocação"><?= lang('Wealth.l_obj3'); ?></button>
                        <button type="button" class="gx-wealth-objective" data-wealth-objective="Planejar legado e sucessão"><?= lang('Wealth.l_obj4'); ?></button>
                    </div>

                    <div class="gx-wealth-input-grid">
                        <label class="gx-wealth-field">
                            <span><?= lang('Wealth.l_field_invested'); ?></span>
                            <input type="number" id="gx-wealth-invested" min="0" step="1000" value="250000">
                        </label>

                        <label class="gx-wealth-field">
                            <span><?= lang('Wealth.l_field_monthly'); ?></span>
                            <input type="number" id="gx-wealth-monthly-invest" min="0" step="100" value="5000">
                        </label>

                        <label class="gx-wealth-field gx-wealth-field-full">
                            <span><?= lang('Wealth.l_field_cost'); ?></span>
                            <input type="number" id="gx-wealth-monthly-cost" min="0" step="100" value="18000">
                        </label>
                    </div>

                    <div class="gx-wealth-chip-list">
                        <span class="gx-wealth-chip"><?= lang('Wealth.l_chip1'); ?></span>
                        <span class="gx-wealth-chip"><?= lang('Wealth.l_chip2'); ?></span>
                        <span class="gx-wealth-chip"><?= lang('Wealth.l_chip3'); ?></span>
                    </div>
                </article>

                <aside class="gx-wealth-insights-card" data-gx-reveal data-gx-delay="100">
                    <p class="gx-label"><?= lang('Wealth.l_ins_label'); ?></p>
                    <h3 class="gx-section-title" style="margin-bottom:0;"><?= lang('Wealth.l_ins_title'); ?></h3>

                    <div class="gx-wealth-kpi-stack">
                        <div class="gx-wealth-kpi-card">
                            <span><?= lang('Wealth.l_kpi1'); ?></span>
                            <strong id="gx-wealth-target-capital">R$ 0</strong>
                        </div>
                        <div class="gx-wealth-kpi-card">
                            <span><?= lang('Wealth.l_kpi2'); ?></span>
                            <strong id="gx-wealth-projection-10y">R$ 0</strong>
                        </div>
                        <div class="gx-wealth-kpi-card">
                            <span><?= lang('Wealth.l_kpi3'); ?></span>
                            <strong id="gx-wealth-coverage">0,0%</strong>
                        </div>
                        <div class="gx-wealth-kpi-card">
                            <span><?= lang('Wealth.l_kpi4'); ?></span>
                            <strong id="gx-wealth-gap">R$ 0</strong>
                        </div>
                    </div>

                    <p class="gx-wealth-insight-text" id="gx-wealth-insight-text">
                        <?= lang('Wealth.l_ins_text'); ?>

                    </p>

                    <div style="margin-top:22px;">
                        <a href="#fale-com-especialista" class="gx-btn gx-btn-primary gx-btn-lg" data-wealth-track="start_signup"><?= lang('Wealth.l_ins_cta'); ?></a>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <div class="gx-divider"></div>

    <section class="gx-section gx-section-alt">
        <div class="gx-wrap">
            <div class="gx-section-header is-centered" data-gx-reveal>
                <p class="gx-label"><?= lang('Wealth.l_sig_label'); ?></p>
                <h2 class="gx-section-title"><?= lang('Wealth.l_sig_title'); ?></h2>
                <p class="gx-section-desc" style="margin-left:auto;margin-right:auto;">
                    <?= lang('Wealth.l_sig_desc'); ?>

                </p>
            </div>

            <div class="gx-wealth-feature-grid" data-gx-reveal data-gx-delay="90">
                <?php foreach ($signalCards as $card): ?>
                    <article class="gx-wealth-feature-card">
                        <strong><?= esc($card['title']); ?></strong>
                        <p><?= esc($card['text']); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <div class="gx-divider"></div>

    <section class="gx-section" id="metodo">
        <div class="gx-wrap">
            <div class="gx-section-header is-centered" data-gx-reveal>
                <p class="gx-label"><?= lang('Wealth.l_met_label'); ?></p>
                <h2 class="gx-section-title"><?= brandLang('Wealth.l_met_title'); ?></h2>
                <p class="gx-section-desc" style="margin-left:auto;margin-right:auto;">
                    <?= lang('Wealth.l_met_desc'); ?>

                </p>
            </div>

            <div class="gx-process-grid">
                <article class="gx-process-card" data-gx-reveal>
                    <span class="gx-process-num">01</span>
                    <h3 class="gx-process-title"><?= lang('Wealth.l_proc1_t'); ?></h3>
                    <p class="gx-process-desc"><?= lang('Wealth.l_proc1_x'); ?></p>
                </article>

                <article class="gx-process-card" data-gx-reveal data-gx-delay="100">
                    <span class="gx-process-num">02</span>
                    <h3 class="gx-process-title"><?= lang('Wealth.l_proc2_t'); ?></h3>
                    <p class="gx-process-desc"><?= lang('Wealth.l_proc2_x'); ?></p>
                </article>

                <article class="gx-process-card" data-gx-reveal data-gx-delay="180">
                    <span class="gx-process-num">03</span>
                    <h3 class="gx-process-title"><?= lang('Wealth.l_proc3_t'); ?></h3>
                    <p class="gx-process-desc"><?= lang('Wealth.l_proc3_x'); ?></p>
                </article>
            </div>
        </div>
    </section>

    <div class="gx-divider"></div>

    <section class="gx-section gx-section-alt" id="entregaveis">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label"><?= lang('Wealth.l_ent_label'); ?></p>
                    <h2 class="gx-section-title"><?= lang('Wealth.l_ent_title'); ?></h2>
                </div>
                <p class="gx-section-desc">
                    <?= lang('Wealth.l_ent_desc'); ?>

                </p>
            </div>

            <div class="gx-wealth-deliverables-grid" data-gx-reveal data-gx-delay="80">
                <?php foreach ($deliverables as $item): ?>
                    <article class="gx-wealth-deliverable-card">
                        <h3><?= esc($item['title']); ?></h3>
                        <p><?= esc($item['text']); ?></p>
                        <div class="gx-wealth-chip-list">
                            <?php foreach ($item['chips'] as $chip): ?>
                                <span class="gx-wealth-chip"><?= esc($chip); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if (!empty($isAuthenticated)): ?>
        <div class="gx-divider"></div>

        <section class="gx-section">
            <div class="gx-wrap">
                <div class="gx-cta-block" data-gx-reveal>
                    <div class="gx-cta-content">
                        <p class="gx-label"><?= lang('Wealth.l_area_label'); ?></p>
                        <h2 class="gx-section-title"><?= lang('Wealth.l_area_title'); ?></h2>
                        <p class="gx-section-desc" style="margin-left:auto;margin-right:auto;">
                            <?= lang('Wealth.l_area_desc'); ?>

                        </p>
                        <div class="gx-cta-actions">
                            <a href="<?= esc($wealthConversationUrl); ?>" class="gx-btn gx-btn-primary gx-btn-lg" data-wealth-track="wealth_continue_area"><?= lang('Wealth.l_area_cta1'); ?></a>
                            <a href="<?= esc($wealthResultsUrl); ?>" class="gx-btn gx-btn-ghost gx-btn-lg"><?= lang('Wealth.l_area_cta2'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <div class="gx-divider"></div>

    <section class="gx-section" id="faq">
        <div class="gx-wrap">
            <div class="gx-section-header is-centered" data-gx-reveal>
                <p class="gx-label"><?= lang('Wealth.l_faq_label'); ?></p>
                <h2 class="gx-section-title"><?= lang('Wealth.l_faq_title'); ?></h2>
                <p class="gx-section-desc" style="margin-left:auto;margin-right:auto;">
                    <?= lang('Wealth.l_faq_desc'); ?>

                </p>
            </div>

            <div class="gx-wealth-faq-list" data-gx-reveal data-gx-delay="80">
                <?php foreach ($faqItems as $index => $item): ?>
                    <details class="gx-wealth-faq-item" <?= $index === 0 ? 'open' : ''; ?>>
                        <summary><?= esc($item['q'] ?? ''); ?></summary>
                        <p><?= esc($item['a'] ?? ''); ?></p>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="gx-lead-section" id="fale-com-especialista">
        <div class="gx-wrap">
            <div class="gx-lead-grid">
                <aside class="gx-lead-aside" data-gx-reveal>
                    <div class="gx-aside-icon" aria-hidden="true">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="rgba(199,160,83,0.6)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h10"/><rect x="3" y="4" width="18" height="16" rx="2"/></svg>
                    </div>
                    <p class="gx-label"><?= lang('Wealth.l_lead_label'); ?></p>
                    <h2 class="gx-section-title"><?= lang('Wealth.l_lead_title'); ?></h2>
                    <p class="gx-section-desc" style="color:rgba(255,255,255,0.62);">
                        <?= lang('Wealth.l_lead_desc'); ?>

                    </p>

                    <div class="gx-contact-list">
                        <span class="gx-contact-chip"><?= lang('Wealth.l_lead_chip1'); ?></span>
                        <span class="gx-contact-chip"><?= lang('Wealth.l_lead_chip2'); ?></span>
                        <span class="gx-contact-chip"><?= lang('Wealth.l_lead_chip3'); ?></span>
                        <?php if (!empty($baseSettings->contact_phone)): ?>
                            <a href="<?= !empty($contactPhone) ? 'tel:' . esc($contactPhone) : '#'; ?>" class="gx-contact-chip">
                                <?= esc($baseSettings->contact_phone); ?>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($baseSettings->contact_email)): ?>
                            <a href="mailto:<?= esc($baseSettings->contact_email); ?>" class="gx-contact-chip">
                                <?= esc($baseSettings->contact_email); ?>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($isAuthenticated)): ?>
                            <a href="<?= esc($wealthConversationUrl); ?>" class="gx-contact-chip" data-wealth-track="wealth_continue_area"><?= lang('Wealth.l_lead_area'); ?></a>
                        <?php endif; ?>
                    </div>
                </aside>

                <div class="gx-lead-card" data-gx-reveal data-gx-delay="100">
                    <?= view('wealth/_lead_form', [
                        'formId' => 'gx-wealth-lead-form',
                        'sourcePage' => 'landing',
                        'formTitle' => $leadFormTitle,
                        'formDescription' => $leadFormDescription,
                        'submitLabel' => $leadFormButton,
                        'activeLang' => $activeLang,
                        'blogUrl' => $blogUrl,
                    ]); ?>
                </div>
            </div>
        </div>
    </section>
</main>

<div class="gx-wealth-sticky-cta">
    <a href="#fale-com-especialista" class="gx-btn gx-btn-primary" data-wealth-track="start_signup"><?= esc($primaryCta); ?></a>
</div>

<?= view('wealth/_scripts'); ?>
