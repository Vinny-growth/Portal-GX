<?php
$pageConfig = $pageConfig ?? [];
$hero = $pageConfig['hero'] ?? [];
$heroProof = array_values(array_filter($pageConfig['hero_proof'] ?? [], static function ($item) {
    return !empty($item['text']);
}));
$technology = $pageConfig['technology'] ?? [];
$technologySignals = array_values(array_filter($technology['signals'] ?? [], static function ($item) {
    return !empty($item['text']);
}));
$indicators = $pageConfig['indicators'] ?? [];
$leadConfig = $pageConfig['lead'] ?? [];
$contactChannels = $contactChannels ?? [];
$contactPhone = trim((string)($contactChannels['phone'] ?? (!empty($baseSettings->contact_phone) ? $baseSettings->contact_phone : '')));
$contactPhoneHref = !empty($contactChannels['phone_href']) ? $contactChannels['phone_href'] : (!empty($contactPhone) ? 'tel:' . preg_replace('/[^0-9+]/', '', $contactPhone) : '');
$contactEmail = trim((string)($contactChannels['email'] ?? (!empty($baseSettings->contact_email) ? $baseSettings->contact_email : '')));
$whatsAppUrl = $whatsAppUrl ?? '';
$whatsAppBaseUrl = $whatsAppBaseUrl ?? '';
$whatsAppDefaultMessage = $whatsAppDefaultMessage ?? '';
$whatsAppMessagesByTool = $whatsAppMessagesByTool ?? [];
$whatsAppIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20.52 3.48A11.8 11.8 0 0 0 12.08 0C5.55 0 .24 5.31.24 11.84c0 2.08.54 4.11 1.58 5.89L0 24l6.46-1.69a11.8 11.8 0 0 0 5.62 1.43h.01c6.53 0 11.84-5.31 11.84-11.84 0-3.16-1.23-6.13-3.41-8.42Zm-8.44 18.26h-.01a9.84 9.84 0 0 1-5.01-1.37l-.36-.22-3.84 1 1.03-3.74-.24-.38a9.8 9.8 0 0 1-1.51-5.2C2.14 6.42 6.66 1.9 12.08 1.9c2.63 0 5.1 1.02 6.96 2.88a9.78 9.78 0 0 1 2.89 6.97c0 5.42-4.42 9.99-9.85 9.99Zm5.39-7.41c-.29-.14-1.71-.84-1.98-.94-.26-.1-.45-.14-.64.14-.19.29-.74.94-.91 1.13-.17.19-.34.22-.63.07-.29-.14-1.21-.45-2.31-1.45-.85-.76-1.42-1.69-1.59-1.98-.17-.29-.02-.44.13-.58.13-.13.29-.34.43-.5.14-.17.19-.29.29-.48.1-.19.05-.36-.02-.5-.07-.14-.64-1.55-.87-2.12-.23-.55-.47-.48-.64-.49h-.55c-.19 0-.5.07-.76.36-.26.29-.99.97-.99 2.37s1.01 2.75 1.15 2.94c.14.19 1.98 3.03 4.79 4.25.67.29 1.2.47 1.61.6.68.22 1.3.19 1.79.12.55-.08 1.71-.7 1.95-1.37.24-.67.24-1.24.17-1.37-.07-.12-.26-.19-.55-.34Z"/></svg>';
$numberValue = static function ($value) {
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }
    if (strpos($value, ',') !== false && strpos($value, '.') !== false) {
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
    } elseif (strpos($value, ',') !== false) {
        $value = str_replace(',', '.', $value);
    }
    return preg_replace('/[^0-9.\-]/', '', $value);
};

$navLinks = [
    ['label' => lang('Fx.nav_mesa'), 'href' => '#quem-somos'],
    ['label' => lang('Fx.nav_indicadores'), 'href' => '#indicadores-cambio'],
    ['label' => lang('Fx.nav_ferramentas'), 'href' => '#ferramentas-cambio'],
    ['label' => lang('Fx.nav_laboratorio'), 'href' => '#laboratorio-cambio'],
    ['label' => lang('Fx.nav_especialista'), 'href' => '#lead-cambio'],
];

$indicatorCards = [
    ['label' => 'USD/BRL', 'value' => $indicators['usd_brl'] ?? '', 'suffix' => '', 'hint' => lang('Fx.ind_hint_usdbrl')],
    ['label' => 'Spread comercial', 'value' => $indicators['commercial_spread'] ?? '', 'suffix' => '%', 'hint' => lang('Fx.ind_hint_spread')],
    ['label' => 'SELIC', 'value' => $indicators['selic'] ?? '', 'suffix' => '%', 'hint' => lang('Fx.ind_hint_selic')],
    ['label' => 'CDI', 'value' => $indicators['cdi'] ?? '', 'suffix' => '%', 'hint' => lang('Fx.ind_hint_cdi')],
    ['label' => 'IPCA 12m', 'value' => $indicators['ipca_12m'] ?? '', 'suffix' => '%', 'hint' => lang('Fx.ind_hint_ipca')],
    ['label' => 'SOFR', 'value' => $indicators['sofr'] ?? '', 'suffix' => '%', 'hint' => lang('Fx.ind_hint_sofr')],
];

$toolCards = [
    'import' => [
        'mark' => 'IMP',
        'eyebrow' => lang('Fx.tc_import_eyebrow'),
        'title' => lang('Fx.tc_import_title'),
        'description' => lang('Fx.tc_import_desc'),
        'bullets' => [
            lang('Fx.tc_import_b1'),
            lang('Fx.tc_import_b2'),
            lang('Fx.tc_import_b3'),
        ],
        'button' => lang('Fx.tc_import_btn'),
    ],
    'export' => [
        'mark' => 'EXP',
        'eyebrow' => lang('Fx.tc_export_eyebrow'),
        'title' => lang('Fx.tc_export_title'),
        'description' => lang('Fx.tc_export_desc'),
        'bullets' => [
            lang('Fx.tc_export_b1'),
            lang('Fx.tc_export_b2'),
            lang('Fx.tc_export_b3'),
        ],
        'button' => lang('Fx.tc_export_btn'),
    ],
    'hedge' => [
        'mark' => 'HDG',
        'eyebrow' => lang('Fx.tc_hedge_eyebrow'),
        'title' => lang('Fx.tc_hedge_title'),
        'description' => lang('Fx.tc_hedge_desc'),
        'bullets' => [
            lang('Fx.tc_hedge_b1'),
            lang('Fx.tc_hedge_b2'),
            lang('Fx.tc_hedge_b3'),
        ],
        'button' => lang('Fx.tc_hedge_btn'),
    ],
    'funding4131' => [
        'mark' => '4131',
        'eyebrow' => lang('Fx.tc_funding_eyebrow'),
        'title' => lang('Fx.tc_funding_title'),
        'description' => lang('Fx.tc_funding_desc'),
        'bullets' => [
            lang('Fx.tc_funding_b1'),
            lang('Fx.tc_funding_b2'),
            lang('Fx.tc_funding_b3'),
        ],
        'button' => lang('Fx.tc_funding_btn'),
    ],
    'trade' => [
        'mark' => 'TF',
        'eyebrow' => lang('Fx.tc_trade_eyebrow'),
        'title' => lang('Fx.tc_trade_title'),
        'description' => lang('Fx.tc_trade_desc'),
        'bullets' => [
            lang('Fx.tc_trade_b1'),
            lang('Fx.tc_trade_b2'),
            lang('Fx.tc_trade_b3'),
        ],
        'button' => lang('Fx.tc_trade_btn'),
    ],
];

$toolOrder = ['import', 'export', 'hedge', 'funding4131', 'trade'];
?>
<main class="gx-marketing gx-home gx-fx-simulators" data-gx-fx-page>
    <nav class="gx-nav" id="gx-nav">
        <div class="gx-nav-inner">
            <a href="<?= esc($homeUrl); ?>" class="gx-nav-brand" aria-label="<?= esc(brand('display_name')); ?>">
                <img src="<?= getLogo(); ?>" alt="<?= esc(brand('display_name')); ?>" width="<?= getLogoSize('width'); ?>" height="<?= getLogoSize('height'); ?>">
            </a>

            <div class="gx-nav-links" id="gx-nav-links">
                <?php foreach ($navLinks as $item): ?>
                    <a href="<?= esc($item['href']); ?>" class="gx-nav-link"><?= esc($item['label']); ?></a>
                <?php endforeach; ?>
                <div class="gx-nav-menu-extra">
                    <a href="<?= esc($blogUrl); ?>" class="gx-nav-link"><?= lang('Fx.nav_blog'); ?></a>
                    <a href="<?= esc($hero['primary_cta_url'] ?? '#lead-cambio'); ?>" class="gx-btn gx-btn-primary"><?= esc($hero['primary_cta_label'] ?? lang('Fx.hero_cta')); ?></a>
                </div>
            </div>

            <div class="gx-nav-right">
                <a href="<?= esc($blogUrl); ?>" class="gx-nav-link"><?= lang('Fx.nav_blog'); ?></a>
                <a href="<?= esc($hero['primary_cta_url'] ?? '#lead-cambio'); ?>" class="gx-btn gx-btn-primary"><?= esc($hero['primary_cta_label'] ?? lang('Fx.hero_cta')); ?></a>
                <button type="button" class="gx-nav-toggle" id="gx-nav-toggle" aria-expanded="false" aria-controls="gx-nav-links" aria-label="<?= lang('Fx.nav_menu'); ?>">
                    <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
            </div>
        </div>
    </nav>

    <section class="gx-hero" id="visao-geral">
        <div class="gx-hero-inner gx-fx-hero-inner">
            <div class="gx-hero-content" data-gx-reveal>
                <div class="gx-hero-badge">
                    <span class="gx-hero-badge-dot"></span>
                    <?= esc($hero['badge'] ?? lang('Fx.hero_badge')); ?>
                </div>

                <h1 class="gx-hero-title">
                    <?= esc($hero['title'] ?? lang('Fx.hero_title')); ?>
                </h1>

                <p class="gx-hero-sub">
                    <?= esc($hero['subtitle'] ?? brandLang('Fx.hero_subtitle')); ?>
                </p>

                <div class="gx-hero-cta">
                    <a href="<?= esc($hero['primary_cta_url'] ?? '#lead-cambio'); ?>" class="gx-btn gx-btn-primary gx-btn-lg"><?= esc($hero['primary_cta_label'] ?? lang('Fx.hero_cta')); ?></a>
                    <?php if (!empty($whatsAppUrl)): ?>
                        <a href="<?= esc($whatsAppUrl); ?>" target="_blank" rel="noopener" class="gx-btn gx-btn-whatsapp gx-btn-lg" data-gx-whatsapp-link><?= $whatsAppIcon; ?><?= lang('Fx.hero_wa'); ?></a>
                    <?php else: ?>
                        <a href="<?= esc($hero['secondary_cta_url'] ?? '#laboratorio-cambio'); ?>" class="gx-btn gx-btn-ghost gx-btn-lg"><?= esc($hero['secondary_cta_label'] ?? lang('Fx.hero_secondary_cta')); ?></a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($heroProof)): ?>
                    <div class="gx-hero-proof">
                        <?php foreach ($heroProof as $item): ?>
                            <div class="gx-hero-proof-item">
                                <strong><?= esc(brand('display_name')); ?></strong>
                                <span><?= esc($item['text']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="gx-hero-aside" data-gx-reveal data-gx-delay="150">
                <div class="gx-hero-visual-card gx-fx-hero-card">
                    <div class="gx-visual-header">
                        <span class="gx-visual-title"><?= esc($technology['label'] ?? brandLang('Fx.tech_label')); ?></span>
                        <span class="gx-visual-badge">FX Desk</span>
                    </div>
                    <div class="gx-fx-stat-stack">
                        <div class="gx-fx-stat-card">
                            <strong><?= esc($technology['stat_primary_value'] ?? '10+'); ?></strong>
                            <span><?= esc($technology['stat_primary_label'] ?? lang('Fx.tech_stat1_label')); ?></span>
                        </div>
                        <div class="gx-fx-stat-card">
                            <strong><?= esc($technology['stat_secondary_value'] ?? '16+'); ?></strong>
                            <span><?= esc($technology['stat_secondary_label'] ?? lang('Fx.tech_stat2_label')); ?></span>
                        </div>
                        <div class="gx-fx-stat-card">
                            <strong><?= esc($technology['stat_tertiary_value'] ?? '360°'); ?></strong>
                            <span><?= esc($technology['stat_tertiary_label'] ?? lang('Fx.tech_stat3_label')); ?></span>
                        </div>
                    </div>

                    <?php if (!empty($technologySignals)): ?>
                        <ul class="gx-fx-signal-list">
                            <?php foreach ($technologySignals as $signal): ?>
                                <li><?= esc($signal['text']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </section>

    <div class="gx-strip">
        <div class="gx-strip-inner" data-gx-reveal>
            <span class="gx-strip-lead"><?= lang('Fx.strip_lead'); ?></span>
            <span class="gx-strip-item"><?= lang('Fx.strip_1'); ?></span>
            <span class="gx-strip-item"><?= lang('Fx.strip_2'); ?></span>
            <span class="gx-strip-item"><?= lang('Fx.strip_3'); ?></span>
            <span class="gx-strip-item"><?= lang('Fx.strip_4'); ?></span>
        </div>
    </div>

    <section class="gx-section" id="quem-somos">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label"><?= lang('Fx.auth_label'); ?></p>
                    <h2 class="gx-section-title"><?= lang('Fx.auth_title'); ?></h2>
                </div>
                <p class="gx-section-desc">
                    <?= brandLang('Fx.auth_desc'); ?>

                </p>
            </div>

            <div class="gx-grid-3 gx-fx-authority-grid" data-gx-reveal data-gx-delay="80">
                <article class="gx-card">
                    <div class="gx-card-icon">FX</div>
                    <p class="gx-card-label"><?= lang('Fx.auth_card1_label'); ?></p>
                    <h3 class="gx-card-title"><?= lang('Fx.auth_card1_title'); ?></h3>
                    <p class="gx-card-desc"><?= lang('Fx.auth_card1_desc'); ?></p>
                </article>
                <article class="gx-card">
                    <div class="gx-card-icon">HD</div>
                    <p class="gx-card-label"><?= lang('Fx.auth_card2_label'); ?></p>
                    <h3 class="gx-card-title"><?= lang('Fx.auth_card2_title'); ?></h3>
                    <p class="gx-card-desc"><?= lang('Fx.auth_card2_desc'); ?></p>
                </article>
                <article class="gx-card">
                    <div class="gx-card-icon">TF</div>
                    <p class="gx-card-label"><?= lang('Fx.auth_card3_label'); ?></p>
                    <h3 class="gx-card-title"><?= lang('Fx.auth_card3_title'); ?></h3>
                    <p class="gx-card-desc"><?= lang('Fx.auth_card3_desc'); ?></p>
                </article>
            </div>

            <div class="gx-fx-boutique-note" data-gx-reveal data-gx-delay="140">
                <p>
                    <?= brandLang('Fx.boutique_note'); ?>

                </p>
            </div>
        </div>
    </section>

    <div class="gx-divider"></div>

    <section class="gx-section gx-section-alt" id="indicadores-cambio">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label"><?= esc($indicators['reference_label'] ?? lang('Fx.ind_ref_label')); ?></p>
                    <h2 class="gx-section-title"><?= lang('Fx.ind_title'); ?></h2>
                </div>
                <p class="gx-section-desc">
                    <?= lang('Fx.ind_desc_label'); ?> <strong><?= esc($indicators['reference_date'] ?? date('m/Y')); ?></strong>.
                    <?= lang('Fx.ind_desc_2'); ?>

                </p>
            </div>

            <div class="gx-fx-indicator-grid" data-gx-reveal data-gx-delay="80">
                <?php foreach ($indicatorCards as $card): ?>
                    <article class="gx-fx-indicator-card">
                        <span class="gx-fx-indicator-label"><?= esc($card['label']); ?></span>
                        <strong><?= esc($card['value'] . $card['suffix']); ?></strong>
                        <span><?= esc($card['hint']); ?></span>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($indicators['note'])): ?>
                <div class="gx-note-card gx-fx-indicator-note" data-gx-reveal data-gx-delay="120">
                    <p class="gx-card-text"><?= esc($indicators['note']); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="gx-section" id="ferramentas-cambio">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label"><?= lang('Fx.tools_label'); ?></p>
                    <h2 class="gx-section-title"><?= lang('Fx.tools_title'); ?></h2>
                </div>
                <p class="gx-section-desc">
                    <?= lang('Fx.tools_desc'); ?>

                </p>
            </div>

            <div class="gx-simulator-grid gx-fx-tool-grid" data-gx-reveal data-gx-delay="80">
                <?php foreach ($toolOrder as $toolKey): ?>
                    <?php $tool = $toolCards[$toolKey]; ?>
                    <article class="gx-simulator-card gx-fx-tool-card<?= $toolKey === 'import' ? ' is-active' : ''; ?>" data-gx-tool-card="<?= esc($toolKey); ?>">
                        <div class="gx-simulator-top">
                            <span class="gx-simulator-mark"><?= esc($tool['mark']); ?></span>
                            <span class="gx-legacy-pill"><?= lang('Fx.tool_pill'); ?></span>
                        </div>
                        <p class="gx-card-kicker"><?= esc($tool['eyebrow']); ?></p>
                        <h3 class="gx-simulator-title"><?= esc($tool['title']); ?></h3>
                        <p class="gx-simulator-meta"><?= esc($tool['description']); ?></p>
                        <ul class="gx-fx-tool-list">
                            <?php foreach ($tool['bullets'] as $bullet): ?>
                                <li><?= esc($bullet); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="gx-simulator-footer">
                            <button type="button" class="gx-text-link" data-gx-tool-jump="<?= esc($toolKey); ?>"><?= esc($tool['button']); ?></button>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="gx-section gx-section-alt" id="laboratorio-cambio">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label">Laboratório de cenário</p>
                    <h2 class="gx-section-title">Modele a operação antes de pedir cotação ou estruturação.</h2>
                </div>
                <p class="gx-section-desc">
                    Cada resultado é indicativo. O fechamento final depende da leitura de documentação, fluxo, instituição, prazo, garantia e momento de mercado.
                </p>
            </div>

            <div class="gx-fx-workbench" data-gx-reveal data-gx-delay="80">
                <div class="gx-fx-tablist" role="tablist" aria-label="Escolha a ferramenta">
                    <?php foreach ($toolOrder as $toolKey): ?>
                        <?php $tool = $toolCards[$toolKey]; ?>
                        <button
                            type="button"
                            class="gx-fx-tab<?= $toolKey === 'import' ? ' is-active' : ''; ?>"
                            data-gx-tool-trigger="<?= esc($toolKey); ?>"
                            role="tab"
                            aria-selected="<?= $toolKey === 'import' ? 'true' : 'false'; ?>">
                            <strong><?= esc($tool['title']); ?></strong>
                            <span><?= esc($tool['eyebrow']); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="gx-fx-panel-shell">
                    <section class="gx-fx-tool-panel is-active" data-gx-tool-panel="import">
                        <div class="gx-fx-panel-grid">
                            <form class="gx-fx-form" data-gx-tool-form="import">
                                <div class="gx-fx-form-head">
                                    <span class="gx-label">Importadores</span>
                                    <h3>Quanto do seu custo em BRL fica exposto se o câmbio andar contra?</h3>
                                </div>
                                <div class="gx-fx-form-grid">
                                    <div class="gx-form-field">
                                        <label for="gx-import-amount">Valor da fatura (USD)</label>
                                        <input id="gx-import-amount" type="number" min="0" step="0.01" name="invoice_amount" value="250000">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-import-spot">USD/BRL atual</label>
                                        <input id="gx-import-spot" type="number" min="0" step="0.0001" name="spot_rate" value="<?= esc($numberValue($indicators['usd_brl'] ?? '5.20')); ?>">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-import-stress">USD/BRL estressado</label>
                                        <input id="gx-import-stress" type="number" min="0" step="0.0001" name="stress_rate" value="">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-import-spread">Spread comercial (%)</label>
                                        <input id="gx-import-spread" type="number" min="0" step="0.01" name="spread_pct" value="<?= esc($numberValue($indicators['commercial_spread'] ?? '1.10')); ?>">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-import-iof">IOF (%)</label>
                                        <input id="gx-import-iof" type="number" min="0" step="0.01" name="iof_pct" value="<?= esc($numberValue($indicators['iof'] ?? '0.38')); ?>">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-import-days">Dias até fechamento</label>
                                        <input id="gx-import-days" type="number" min="1" step="1" name="settlement_days" value="30">
                                    </div>
                                    <div class="gx-form-field gx-form-field-full">
                                        <label for="gx-import-sale">Receita projetada em BRL (opcional)</label>
                                        <input id="gx-import-sale" type="number" min="0" step="0.01" name="projected_sale_brl" value="">
                                    </div>
                                </div>
                            </form>
                            <div class="gx-fx-result-card" data-gx-result-body="import"></div>
                        </div>
                    </section>

                    <section class="gx-fx-tool-panel" data-gx-tool-panel="export">
                        <div class="gx-fx-panel-grid">
                            <form class="gx-fx-form" data-gx-tool-form="export">
                                <div class="gx-fx-form-head">
                                    <span class="gx-label">Exportadores</span>
                                    <h3>Qual receita em BRL você perde se o câmbio cair antes da liquidação?</h3>
                                </div>
                                <div class="gx-fx-form-grid">
                                    <div class="gx-form-field">
                                        <label for="gx-export-amount">Recebível em moeda (USD)</label>
                                        <input id="gx-export-amount" type="number" min="0" step="0.01" name="receivable_amount" value="200000">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-export-spot">USD/BRL atual</label>
                                        <input id="gx-export-spot" type="number" min="0" step="0.0001" name="spot_rate" value="<?= esc($numberValue($indicators['usd_brl'] ?? '5.20')); ?>">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-export-downside">USD/BRL em queda</label>
                                        <input id="gx-export-downside" type="number" min="0" step="0.0001" name="downside_rate" value="">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-export-floor">Câmbio piso desejado</label>
                                        <input id="gx-export-floor" type="number" min="0" step="0.0001" name="floor_rate" value="<?= esc($numberValue($indicators['exporter_floor_rate'] ?? '5.00')); ?>">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-export-spread">Spread comercial (%)</label>
                                        <input id="gx-export-spread" type="number" min="0" step="0.01" name="spread_pct" value="<?= esc($numberValue($indicators['commercial_spread'] ?? '1.10')); ?>">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-export-days">Dias até liquidação</label>
                                        <input id="gx-export-days" type="number" min="1" step="1" name="settlement_days" value="45">
                                    </div>
                                    <div class="gx-form-field gx-form-field-full">
                                        <label for="gx-export-cost">Custo ou caixa mínimo em BRL (opcional)</label>
                                        <input id="gx-export-cost" type="number" min="0" step="0.01" name="minimum_brl_need" value="">
                                    </div>
                                </div>
                            </form>
                            <div class="gx-fx-result-card" data-gx-result-body="export"></div>
                        </div>
                    </section>

                    <section class="gx-fx-tool-panel" data-gx-tool-panel="hedge">
                        <div class="gx-fx-panel-grid">
                            <form class="gx-fx-form" data-gx-tool-form="hedge">
                                <div class="gx-fx-form-head">
                                    <span class="gx-label">Decisão de hedge</span>
                                    <h3>Quanto custa proteger versus o tamanho da perda potencial operando aberto?</h3>
                                </div>
                                <div class="gx-fx-form-grid">
                                    <div class="gx-form-field">
                                        <label for="gx-hedge-exposure">Exposição em USD</label>
                                        <input id="gx-hedge-exposure" type="number" min="0" step="0.01" name="exposure_amount" value="300000">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-hedge-spot">USD/BRL atual</label>
                                        <input id="gx-hedge-spot" type="number" min="0" step="0.0001" name="spot_rate" value="<?= esc($numberValue($indicators['usd_brl'] ?? '5.20')); ?>">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-hedge-move">Movimento adverso (%)</label>
                                        <input id="gx-hedge-move" type="number" min="0" step="0.01" name="adverse_move_pct" value="<?= esc($numberValue($indicators['stress_scenario'] ?? '7.50')); ?>">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-hedge-margin">Margem da operação (%)</label>
                                        <input id="gx-hedge-margin" type="number" min="0" step="0.01" name="margin_pct" value="<?= esc($numberValue($indicators['importer_target_margin'] ?? '18.00')); ?>">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-hedge-cost">Custo mensal do hedge (%)</label>
                                        <input id="gx-hedge-cost" type="number" min="0" step="0.01" name="hedge_cost_monthly_pct" value="<?= esc($numberValue($indicators['hedge_cost_monthly'] ?? '0.65')); ?>">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-hedge-months">Meses até liquidação</label>
                                        <input id="gx-hedge-months" type="number" min="1" step="1" name="months_to_settlement" value="3">
                                    </div>
                                </div>
                            </form>
                            <div class="gx-fx-result-card" data-gx-result-body="hedge"></div>
                        </div>
                    </section>

                    <section class="gx-fx-tool-panel" data-gx-tool-panel="funding4131">
                        <div class="gx-fx-panel-grid">
                            <form class="gx-fx-form" data-gx-tool-form="funding4131">
                                <div class="gx-fx-form-head">
                                    <span class="gx-label">Operação 4131</span>
                                    <h3>Faz sentido aprofundar um funding offshore em vez de ficar 100% onshore?</h3>
                                </div>
                                <div class="gx-fx-form-grid">
                                    <div class="gx-form-field">
                                        <label for="gx-4131-principal">Principal em USD</label>
                                        <input id="gx-4131-principal" type="number" min="0" step="0.01" name="principal_usd" value="1000000">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-4131-tenor">Prazo (meses)</label>
                                        <input id="gx-4131-tenor" type="number" min="1" step="1" name="tenor_months" value="18">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-4131-sofr">SOFR base (%)</label>
                                        <input id="gx-4131-sofr" type="number" min="0" step="0.01" name="offshore_base_rate" value="<?= esc($numberValue($indicators['sofr'] ?? '4.70')); ?>">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-4131-offshore-spread">Spread offshore (%)</label>
                                        <input id="gx-4131-offshore-spread" type="number" min="0" step="0.01" name="offshore_spread_pct" value="<?= esc($numberValue($indicators['offshore_spread'] ?? '3.20')); ?>">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-4131-local-rate">Base local CDI/SELIC (%)</label>
                                        <input id="gx-4131-local-rate" type="number" min="0" step="0.01" name="local_base_rate" value="<?= esc($numberValue($indicators['cdi'] ?? '14.15')); ?>">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-4131-local-spread">Spread onshore (%)</label>
                                        <input id="gx-4131-local-spread" type="number" min="0" step="0.01" name="local_spread_pct" value="<?= esc($numberValue($indicators['onshore_spread'] ?? '4.50')); ?>">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-4131-hedge">Hedge mensal (%)</label>
                                        <input id="gx-4131-hedge" type="number" min="0" step="0.01" name="hedge_monthly_pct" value="<?= esc($numberValue($indicators['hedge_cost_monthly'] ?? '0.65')); ?>">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-4131-fee">Fees de estrutura (%)</label>
                                        <input id="gx-4131-fee" type="number" min="0" step="0.01" name="fees_pct" value="<?= esc($numberValue($indicators['trade_finance_fee'] ?? '1.20')); ?>">
                                    </div>
                                    <div class="gx-form-field gx-form-field-full">
                                        <label for="gx-4131-natural-hedge">Receita natural em moeda forte (%)</label>
                                        <input id="gx-4131-natural-hedge" type="number" min="0" max="100" step="0.01" name="natural_hedge_pct" value="0">
                                    </div>
                                </div>
                            </form>
                            <div class="gx-fx-result-card" data-gx-result-body="funding4131"></div>
                        </div>
                    </section>

                    <section class="gx-fx-tool-panel" data-gx-tool-panel="trade">
                        <div class="gx-fx-panel-grid">
                            <form class="gx-fx-form" data-gx-tool-form="trade">
                                <div class="gx-fx-form-head">
                                    <span class="gx-label">Trade finance</span>
                                    <h3>Qual estrutura faz mais sentido para o ponto do fluxo internacional em que sua empresa está?</h3>
                                </div>
                                <div class="gx-fx-form-grid">
                                    <div class="gx-form-field">
                                        <label for="gx-trade-profile">Perfil</label>
                                        <select id="gx-trade-profile" name="profile">
                                            <option value="importer">Importador</option>
                                            <option value="exporter">Exportador</option>
                                            <option value="both">Importador e exportador</option>
                                        </select>
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-trade-stage">Objetivo principal</label>
                                        <select id="gx-trade-stage" name="stage">
                                            <option value="pre_shipment">Financiar antes do embarque</option>
                                            <option value="post_shipment">Antecipar depois do embarque</option>
                                            <option value="pay_supplier">Pagar fornecedor e alongar caixa</option>
                                            <option value="guarantee">Dar mais segurança para a contraparte</option>
                                            <option value="term_extension">Alongar prazo da operação</option>
                                        </select>
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-trade-ticket">Ticket em USD</label>
                                        <input id="gx-trade-ticket" type="number" min="0" step="0.01" name="ticket_usd" value="350000">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-trade-tenor">Prazo (dias)</label>
                                        <input id="gx-trade-tenor" type="number" min="1" step="1" name="tenor_days" value="180">
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-trade-collateral">Tem colateral/garantia?</label>
                                        <select id="gx-trade-collateral" name="has_collateral">
                                            <option value="yes">Sim</option>
                                            <option value="no">Não</option>
                                        </select>
                                    </div>
                                    <div class="gx-form-field">
                                        <label for="gx-trade-natural">Tem hedge natural?</label>
                                        <select id="gx-trade-natural" name="has_natural_hedge">
                                            <option value="yes">Sim</option>
                                            <option value="no">Não</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                            <div class="gx-fx-result-card" data-gx-result-body="trade"></div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>

    <section class="gx-section" id="mesa-gx">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label"><?= esc($technology['label'] ?? 'Mesa GX Capital'); ?></p>
                    <h2 class="gx-section-title"><?= esc($technology['title'] ?? 'Tecnologia para comparar. Experiência para fechar a operação certa.'); ?></h2>
                </div>
                <p class="gx-section-desc">
                    <?= esc($technology['description'] ?? 'A mesa usa agente de IA para cruzar cotação, spread, prazo, documentação e aderência operacional entre múltiplas instituições. O foco não é a menor taxa isolada. É a estrutura mais eficiente para a necessidade real do cliente.'); ?>
                </p>
            </div>

            <div class="gx-grid-5 gx-fx-authority-grid" data-gx-reveal data-gx-delay="80">
                <article class="gx-card">
                    <span class="gx-card-index">01</span>
                    <div class="gx-card-icon">FX</div>
                    <p class="gx-card-label">Cotação inteligente</p>
                    <h3 class="gx-card-title">Mais de 10 instituições na mesma mesa de comparação.</h3>
                    <p class="gx-card-desc">Bancos de câmbio e corretoras entram na leitura para buscar a operação mais eficiente para o contexto do cliente.</p>
                </article>
                <article class="gx-card">
                    <span class="gx-card-index">02</span>
                    <div class="gx-card-icon">HD</div>
                    <p class="gx-card-label">Proteção</p>
                    <h3 class="gx-card-title">Hedge como decisão de margem, não como produto de prateleira.</h3>
                    <p class="gx-card-desc">A proteção é desenhada conforme exposição, prazo, repasse e capacidade de absorver volatilidade.</p>
                </article>
                <article class="gx-card">
                    <span class="gx-card-index">03</span>
                    <div class="gx-card-icon">TF</div>
                    <p class="gx-card-label">Trade finance</p>
                    <h3 class="gx-card-title">ACC, ACE, FINIMP e outras estruturas lidas no mesmo raciocínio.</h3>
                    <p class="gx-card-desc">A escolha depende de etapa do fluxo, pressão de caixa, risco de contraparte e timing operacional.</p>
                </article>
                <article class="gx-card">
                    <span class="gx-card-index">04</span>
                    <div class="gx-card-icon">4131</div>
                    <p class="gx-card-label">Funding</p>
                    <h3 class="gx-card-title">Operações 4131 entram quando a equação de custo, prazo e proteção fecha.</h3>
                    <p class="gx-card-desc">O simulador filtra viabilidade. A mesa valida premissas, hedge, documentação e governança da operação.</p>
                </article>
                <article class="gx-card">
                    <span class="gx-card-index">05</span>
                    <div class="gx-card-icon">16+</div>
                    <p class="gx-card-label">Experiência</p>
                    <h3 class="gx-card-title">Time com mais de 16 anos de experiência atendendo operações de diferentes portes.</h3>
                    <p class="gx-card-desc">Da demanda recorrente de comércio exterior a estruturas sofisticadas de funding, a leitura parte da operação real.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="gx-lead-section" id="lead-cambio">
        <div class="gx-wrap">
            <div class="gx-lead-grid">
                <aside class="gx-lead-aside gx-fx-lead-aside" data-gx-reveal>
                    <p class="gx-label"><?= esc($leadConfig['label'] ?? 'Leve o cenário para a mesa'); ?></p>
                    <h2 class="gx-section-title"><?= esc($leadConfig['title'] ?? 'Receba uma leitura consultiva da sua operação de câmbio.'); ?></h2>
                    <p class="gx-section-desc">
                        <?= esc($leadConfig['description'] ?? 'Envie o seu cenário e nossa mesa avalia cotação, estrutura de hedge, trade finance e melhor janela de execução para o seu fluxo.'); ?>
                    </p>

                    <div class="gx-fx-live-panel" data-gx-live-summary>
                        <span class="gx-fx-live-eyebrow">Ferramenta ativa</span>
                        <strong data-gx-live-title>Simulador de importação</strong>
                        <div class="gx-fx-live-metric">
                            <span data-gx-live-value>R$ 0</span>
                            <small data-gx-live-label>impacto indicativo em cenário de estresse</small>
                        </div>
                        <p data-gx-live-copy>Preencha os campos para gerar uma leitura executiva da exposição.</p>
                    </div>

                    <div class="gx-fx-credibility">
                        <ul>
                            <li>Mais de 10 instituições financeiras na cotação comparativa</li>
                            <li>16+ anos de experiência no mercado financeiro e operações internacionais</li>
                            <li>Retorno em até 1 dia útil após o envio do cenário</li>
                        </ul>
                    </div>

                    <div class="gx-contact-list">
                        <?php if (!empty($contactPhone)): ?>
                            <a href="<?= !empty($contactPhoneHref) ? esc($contactPhoneHref) : '#'; ?>" class="gx-contact-chip"><?= esc($contactPhone); ?></a>
                        <?php endif; ?>
                        <?php if (!empty($contactEmail)): ?>
                            <a href="mailto:<?= esc($contactEmail); ?>" class="gx-contact-chip"><?= esc($contactEmail); ?></a>
                        <?php endif; ?>
                        <?php if (!empty($whatsAppUrl)): ?>
                            <a href="<?= esc($whatsAppUrl); ?>" target="_blank" rel="noopener" class="gx-contact-chip" data-gx-whatsapp-link><?= $whatsAppIcon; ?>WhatsApp</a>
                        <?php endif; ?>
                        <a href="<?= esc($blogUrl); ?>" class="gx-contact-chip">Ver análises técnicas</a>
                    </div>
                </aside>

                <div class="gx-lead-card gx-fx-lead-card" data-gx-reveal data-gx-delay="120">
                    <div class="gx-form-shell">
                        <div class="gx-form-intro">
                            <h3 class="gx-form-title"><?= esc($leadConfig['form_title'] ?? 'Fale com a mesa de câmbio GX Capital'); ?></h3>
                            <p class="gx-form-copy"><?= esc($leadConfig['form_description'] ?? 'Compartilhe os dados da operação. O retorno considera preço, prazo, risco, funding e documentação.'); ?></p>
                        </div>

                        <div class="gx-fx-form-status" data-gx-lead-status hidden></div>

                        <form id="gx-fx-lead-form" class="gx-form-grid" action="<?= base_url('api/save-simulator-lead'); ?>" method="post">
                            <input type="hidden" name="landing_page" value="<?= esc(current_url()); ?>">
                            <input type="hidden" name="lead_origin" value="Simuladores de Câmbio - Importadores e Exportadores">
                            <input type="hidden" name="sim_data" value="">
                            <input type="hidden" name="meta_content_name" value="Simuladores de Câmbio GX Capital">
                            <input type="hidden" name="meta_content_category" value="Câmbio">
                            <input type="hidden" name="meta_value" value="1">
                            <input type="hidden" name="meta_currency" value="BRL">

                            <div class="gx-form-field">
                                <label for="gx-fx-lead-name">Nome</label>
                                <input id="gx-fx-lead-name" type="text" name="name" maxlength="199" minlength="1" pattern=".*\S+.*" autocomplete="name" required>
                            </div>
                            <div class="gx-form-field">
                                <label for="gx-fx-lead-email">E-mail</label>
                                <input id="gx-fx-lead-email" type="email" name="email" maxlength="199" autocomplete="email" inputmode="email" required>
                            </div>
                            <?= view('partials/_lead_phone_field', [
                                'fieldIdPrefix' => 'gx-fx-lead-phone',
                                'wrapperClass' => 'gx-form-field',
                                'countryValue' => 'BR',
                                'phoneValue' => '',
                            ]); ?>
                            <div class="gx-form-field">
                                <label for="gx-fx-lead-company">Empresa</label>
                                <input id="gx-fx-lead-company" type="text" name="company" maxlength="199" autocomplete="organization" placeholder="Opcional">
                            </div>
                            <div class="gx-form-field gx-form-field-full">
                                <label for="gx-fx-lead-message">Complemento <span class="gx-form-optional">(opcional)</span></label>
                                <textarea id="gx-fx-lead-message" name="message" maxlength="4970" rows="3" placeholder="Ex.: vencimento da fatura, exposição mensal, se já tentou hedge antes."></textarea>
                            </div>
                            <input type="text" name="message_content" class="gx-hp" tabindex="-1" autocomplete="off">
                            <label class="gx-check">
                                <input type="checkbox" required>
                                <span>
                                    Li e concordo com os
                                    <a href="<?= getPageLinkByDefaultName('terms_conditions', $activeLang->id); ?>" target="_blank" rel="noopener">
                                        termos e condições
                                    </a>.
                                </span>
                            </label>
                            <div class="gx-form-actions">
                                <button type="submit" class="gx-btn gx-btn-primary gx-form-submit"><?= esc($leadConfig['button_label'] ?? 'Solicitar leitura da mesa'); ?></button>
                                <p class="gx-form-note">Retorno em até 1 dia útil. Priorizamos operações com fechamento nos próximos 30 dias.</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="gx-fx-sticky-bar" id="gx-fx-sticky-bar" hidden aria-live="polite">
        <div class="gx-fx-sticky-inner">
            <p class="gx-fx-sticky-text">Cenário pronto. Fale com um especialista.</p>
            <div class="gx-fx-sticky-actions">
                <a href="#lead-cambio" class="gx-btn gx-btn-primary">Agendar reunião</a>
                <?php if (!empty($whatsAppUrl)): ?>
                    <a href="<?= esc($whatsAppUrl); ?>" target="_blank" rel="noopener" class="gx-btn gx-btn-whatsapp" data-gx-whatsapp-link><?= $whatsAppIcon; ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
(function() {
    var nav = document.getElementById('gx-nav');
    var toggle = document.getElementById('gx-nav-toggle');
    var links = document.getElementById('gx-nav-links');
    var raf = window.requestAnimationFrame ? window.requestAnimationFrame.bind(window) : function(cb) {
        return setTimeout(cb, 16);
    };
    var pageNode = document.querySelector('[data-gx-fx-page]');
    if (!pageNode) {
        return;
    }

    if (nav) {
        var navTicking = false;
        var navScrolled = false;
        var syncNavState = function(force) {
            var nextScrolled = window.scrollY > 20;
            if (force || nextScrolled !== navScrolled) {
                navScrolled = nextScrolled;
                if (navScrolled) {
                    nav.classList.add('is-scrolled');
                } else {
                    nav.classList.remove('is-scrolled');
                }
            }
            navTicking = false;
        };
        syncNavState(true);
        window.addEventListener('scroll', function() {
            if (navTicking) {
                return;
            }
            navTicking = true;
            raf(syncNavState);
        }, {passive: true});
    }

    if (toggle && links && nav) {
        var setMenuState = function(open) {
            if (open) {
                nav.classList.add('is-open');
            } else {
                nav.classList.remove('is-open');
            }
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        };

        toggle.addEventListener('click', function() {
            setMenuState(!nav.classList.contains('is-open'));
        });

        links.querySelectorAll('a').forEach(function(anchor) {
            anchor.addEventListener('click', function() {
                setMenuState(false);
            });
        });
    }

    var revealNodes = document.querySelectorAll('[data-gx-reveal]');
    if ('IntersectionObserver' in window && revealNodes.length) {
        var revealQueue = [];
        var revealScheduled = false;
        var flushRevealQueue = function() {
            revealScheduled = false;
            while (revealQueue.length) {
                var item = revealQueue.shift();
                if (item.delay) {
                    item.node.style.transitionDelay = item.delay + 'ms';
                }
                item.node.classList.add('is-visible');
            }
        };
        var scheduleReveal = function(node, delay) {
            revealQueue.push({node: node, delay: delay});
            if (!revealScheduled) {
                revealScheduled = true;
                raf(flushRevealQueue);
            }
        };
        var revealObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (!entry.isIntersecting) {
                    return;
                }
                scheduleReveal(entry.target, entry.target.getAttribute('data-gx-delay'));
                revealObserver.unobserve(entry.target);
            });
        }, {threshold: 0.1, rootMargin: '0px 0px -40px 0px'});
        revealNodes.forEach(function(node) {
            revealObserver.observe(node);
        });
    } else {
        revealNodes.forEach(function(node) {
            node.classList.add('is-visible');
        });
    }

    var toolCards = <?= json_encode($toolCards, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var indicators = <?= json_encode($indicators, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var whatsAppBaseUrl = <?= json_encode($whatsAppBaseUrl, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var whatsAppDefaultMessage = <?= json_encode($whatsAppDefaultMessage, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var whatsAppMessagesByTool = <?= json_encode($whatsAppMessagesByTool, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var whatsAppIconHtml = <?= json_encode($whatsAppIcon, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var activeTool = 'import';
    var hasToolInteraction = false;
    var results = {};
    var currencyFormatter = new Intl.NumberFormat('pt-BR', {style: 'currency', currency: 'BRL'});
    var numberFormatter = new Intl.NumberFormat('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    var ratioFormatter = new Intl.NumberFormat('pt-BR', {minimumFractionDigits: 1, maximumFractionDigits: 1});

    function normalizeNumeric(value) {
        var input = String(value == null ? '' : value).trim();
        if (input === '') {
            return 0;
        }
        input = input.replace(/\s+/g, '');
        if (input.indexOf(',') !== -1 && input.indexOf('.') !== -1) {
            input = input.replace(/\./g, '').replace(',', '.');
        } else if (input.indexOf(',') !== -1) {
            input = input.replace(',', '.');
        }
        input = input.replace(/[^0-9.-]/g, '');
        var parsed = parseFloat(input);
        return Number.isFinite(parsed) ? parsed : 0;
    }

    function valueOf(form, name) {
        var field = form.querySelector('[name="' + name + '"]');
        return normalizeNumeric(field ? field.value : 0);
    }

    function rawValue(form, name) {
        var field = form.querySelector('[name="' + name + '"]');
        return field ? String(field.value || '').trim() : '';
    }

    function brl(value) {
        return currencyFormatter.format(Number.isFinite(value) ? value : 0);
    }

    function num(value, digits) {
        if (digits === 4) {
            return new Intl.NumberFormat('pt-BR', {minimumFractionDigits: 4, maximumFractionDigits: 4}).format(Number.isFinite(value) ? value : 0);
        }
        return numberFormatter.format(Number.isFinite(value) ? value : 0);
    }

    function pct(value) {
        return numberFormatter.format(Number.isFinite(value) ? value : 0) + '%';
    }

    function ratio(value) {
        return ratioFormatter.format(Number.isFinite(value) ? value : 0) + 'x';
    }

    function renderResult(toolKey, result) {
        var target = document.querySelector('[data-gx-result-body="' + toolKey + '"]');
        if (!target) {
            return;
        }

        var metricsHtml = result.metrics.map(function(metric) {
            return '<div class="gx-fx-metric"><span>' + metric.label + '</span><strong>' + metric.value + '</strong></div>';
        }).join('');

        var insightsHtml = result.insights.map(function(item) {
            return '<li>' + item + '</li>';
        }).join('');

        var resultCtaWa = whatsAppBaseUrl
            ? '<a href="' + whatsAppBaseUrl + '" target="_blank" rel="noopener" class="gx-btn gx-btn-whatsapp" data-gx-whatsapp-link>' + whatsAppIconHtml + 'WhatsApp</a>'
            : '';

        target.innerHTML =
            '<div class="gx-fx-result-head">' +
                '<span class="gx-fx-result-chip">' + result.badge + '</span>' +
                '<h3>' + result.title + '</h3>' +
                '<p>' + result.description + '</p>' +
            '</div>' +
            '<div class="gx-fx-metric-grid">' + metricsHtml + '</div>' +
            '<div class="gx-fx-result-list">' +
                '<p class="gx-fx-result-list-title">Leitura inicial da mesa</p>' +
                '<ul>' + insightsHtml + '</ul>' +
            '</div>' +
            '<div class="gx-fx-result-cta">' +
                '<p>Esse cenário merece uma leitura da mesa.</p>' +
                '<div class="gx-fx-result-cta-btns">' +
                    '<a href="#lead-cambio" class="gx-btn gx-btn-primary">Agendar conversa com especialista</a>' +
                    resultCtaWa +
                '</div>' +
            '</div>';
    }

    function updateLiveSummary() {
        var summary = results[activeTool];
        if (!summary) {
            updateWhatsAppLink();
            return;
        }
        var titleNode = document.querySelector('[data-gx-live-title]');
        var valueNode = document.querySelector('[data-gx-live-value]');
        var labelNode = document.querySelector('[data-gx-live-label]');
        var copyNode = document.querySelector('[data-gx-live-copy]');

        if (titleNode) {
            titleNode.textContent = summary.toolLabel;
        }
        if (valueNode) {
            valueNode.textContent = summary.liveValue;
        }
        if (labelNode) {
            labelNode.textContent = summary.liveLabel;
        }
        if (copyNode) {
            copyNode.textContent = summary.liveCopy;
        }
        updateWhatsAppLink();
    }

    function updateWhatsAppLink() {
        if (!whatsAppBaseUrl) {
            return;
        }

        var message = hasToolInteraction ? (whatsAppMessagesByTool[activeTool] || whatsAppDefaultMessage) : whatsAppDefaultMessage;
        var href = whatsAppBaseUrl + '?text=' + encodeURIComponent(message);
        var toolLabel = toolCards[activeTool] ? toolCards[activeTool].title : 'sua conversa com a mesa';

        document.querySelectorAll('[data-gx-whatsapp-link]').forEach(function(node) {
            node.setAttribute('href', href);
        });

        document.querySelectorAll('[data-gx-whatsapp-copy]').forEach(function(node) {
            node.textContent = hasToolInteraction
                ? 'Mensagem pronta para ' + toolLabel.toLowerCase() + '.'
                : 'Mensagem pronta para iniciar a conversa com a mesa de câmbio.';
        });
    }

    function computeImport(form) {
        var invoiceAmount = valueOf(form, 'invoice_amount');
        var spotRate = valueOf(form, 'spot_rate');
        var spreadPct = valueOf(form, 'spread_pct');
        var iofPct = valueOf(form, 'iof_pct');
        var settlementDays = valueOf(form, 'settlement_days');
        var projectedSale = valueOf(form, 'projected_sale_brl');
        var stressInput = rawValue(form, 'stress_rate');
        var stressRate = stressInput !== '' ? valueOf(form, 'stress_rate') : spotRate * (1 + normalizeNumeric(indicators.stress_scenario || 0) / 100);
        var baseCost = invoiceAmount * spotRate * (1 + spreadPct / 100) * (1 + iofPct / 100);
        var stressedCost = invoiceAmount * stressRate * (1 + spreadPct / 100) * (1 + iofPct / 100);
        var impact = stressedCost - baseCost;
        var baseMargin = projectedSale > 0 ? ((projectedSale - baseCost) / projectedSale) * 100 : null;
        var stressedMargin = projectedSale > 0 ? ((projectedSale - stressedCost) / projectedSale) * 100 : null;
        var targetMargin = normalizeNumeric(indicators.importer_target_margin || 0);
        var hedgeScore = settlementDays > 20 || impact > (baseCost * 0.03) || (stressedMargin !== null && stressedMargin < targetMargin);
        var insights = [
            'Sem proteção, o custo sairia de ' + brl(baseCost) + ' para ' + brl(stressedCost) + ' se o USD/BRL fosse a ' + num(stressRate, 4) + '.',
            'O impacto indicativo da exposição aberta é de ' + brl(impact) + ' antes de negociar eventual repasse com fornecedor ou cliente.',
        ];

        if (projectedSale > 0 && baseMargin !== null && stressedMargin !== null) {
            insights.push('Com receita projetada de ' + brl(projectedSale) + ', a margem iria de ' + pct(baseMargin) + ' para ' + pct(stressedMargin) + '.');
        } else {
            insights.push('Se você informar a receita projetada em BRL, a mesa também consegue ler compressão de margem no lote.');
        }

        insights.push(hedgeScore
            ? 'Leitura inicial: vale comparar NDF, termo ou trava parcial para reduzir a pressão de caixa do fechamento.'
            : 'Leitura inicial: a exposição parece administrável, mas ainda vale testar cotação e proteção parcial com a mesa.');

        return {
            toolKey: 'import',
            toolLabel: toolCards.import.title,
            badge: hedgeScore ? 'Hedge recomendado' : 'Monitorar exposição',
            title: hedgeScore ? 'A margem tende a sofrer se o câmbio andar contra.' : 'A exposição parece controlável, mas merece leitura de mesa.',
            description: 'O cálculo compara o custo em BRL no cenário atual com um cenário cambial mais pressionado.',
            metrics: [
                {label: 'Custo base em BRL', value: brl(baseCost)},
                {label: 'Custo em estresse', value: brl(stressedCost)},
                {label: 'Impacto da exposição', value: brl(impact)},
                {label: 'Prazo até fechamento', value: Math.round(settlementDays) + ' dias'},
            ],
            insights: insights,
            liveValue: brl(impact),
            liveLabel: 'impacto indicativo em cenário de estresse',
            liveCopy: hedgeScore
                ? 'O cenário sugere discutir hedge ou trava parcial antes do fechamento.'
                : 'A exposição parece menos agressiva, mas a mesa pode buscar execução mais eficiente.',
            leadSummary: 'Importação | Fatura USD ' + num(invoiceAmount) + ' | custo base ' + brl(baseCost) + ' | custo em estresse ' + brl(stressedCost),
            payload: {
                inputs: {
                    invoice_amount_usd: invoiceAmount,
                    spot_rate: spotRate,
                    stress_rate: stressRate,
                    spread_pct: spreadPct,
                    iof_pct: iofPct,
                    settlement_days: settlementDays,
                    projected_sale_brl: projectedSale,
                },
                results: {
                    base_cost_brl: baseCost,
                    stressed_cost_brl: stressedCost,
                    fx_impact_brl: impact,
                    base_margin_pct: baseMargin,
                    stressed_margin_pct: stressedMargin,
                    target_margin_pct: targetMargin,
                },
            },
        };
    }

    function computeExport(form) {
        var receivableAmount = valueOf(form, 'receivable_amount');
        var spotRate = valueOf(form, 'spot_rate');
        var spreadPct = valueOf(form, 'spread_pct');
        var settlementDays = valueOf(form, 'settlement_days');
        var minimumNeed = valueOf(form, 'minimum_brl_need');
        var downsideInput = rawValue(form, 'downside_rate');
        var downsideRate = downsideInput !== '' ? valueOf(form, 'downside_rate') : spotRate * (1 - normalizeNumeric(indicators.stress_scenario || 0) / 100);
        var floorRate = valueOf(form, 'floor_rate');
        var proceedsToday = receivableAmount * spotRate * (1 - spreadPct / 100);
        var proceedsDownside = receivableAmount * downsideRate * (1 - spreadPct / 100);
        var proceedsProtected = receivableAmount * floorRate * (1 - spreadPct / 100);
        var gap = proceedsProtected - proceedsDownside;
        var hedgeScore = proceedsProtected > proceedsDownside && (minimumNeed > 0 ? proceedsDownside < minimumNeed : settlementDays > 20);
        var insights = [
            'No cenário atual, a liquidação indicativa seria de ' + brl(proceedsToday) + ' líquidos de spread.',
            'Se o câmbio cair para ' + num(downsideRate, 4) + ', a receita cairia para ' + brl(proceedsDownside) + '.',
            'Com piso em ' + num(floorRate, 4) + ', a operação protegeria aproximadamente ' + brl(gap) + ' versus o cenário de queda.',
        ];

        if (minimumNeed > 0) {
            insights.push(minimumNeed > proceedsDownside
                ? 'O cenário de queda não cobre o caixa mínimo informado de ' + brl(minimumNeed) + '.'
                : 'Mesmo no cenário de queda, a receita ainda cobre o caixa mínimo informado de ' + brl(minimumNeed) + '.');
        } else {
            insights.push('Se você informar o caixa mínimo em BRL, a mesa consegue avaliar melhor o piso operacional necessário.');
        }

        insights.push(hedgeScore
            ? 'Leitura inicial: faz sentido discutir trava de piso, ACC/ACE ou execução escalonada para proteger a conversão.'
            : 'Leitura inicial: o câmbio ainda parece acomodar a operação, mas vale comparar alternativas de proteção com a mesa.');

        return {
            toolKey: 'export',
            toolLabel: toolCards.export.title,
            badge: hedgeScore ? 'Piso recomendado' : 'Receita monitorada',
            title: hedgeScore ? 'A queda do câmbio pode comprimir a receita em BRL.' : 'A receita parece mais equilibrada, mas ainda pede disciplina de execução.',
            description: 'O cálculo compara receita líquida atual, cenário de queda e cenário protegido.',
            metrics: [
                {label: 'Receita hoje', value: brl(proceedsToday)},
                {label: 'Receita em queda', value: brl(proceedsDownside)},
                {label: 'Receita protegida', value: brl(proceedsProtected)},
                {label: 'Diferença protegida', value: brl(gap)},
            ],
            insights: insights,
            liveValue: brl(gap),
            liveLabel: 'proteção indicativa de receita versus cenário de queda',
            liveCopy: hedgeScore
                ? 'A operação tende a ganhar previsibilidade se você travar um piso de conversão.'
                : 'A exposição parece menos crítica, mas a mesa pode calibrar a melhor janela de venda.',
            leadSummary: 'Exportação | Recebível USD ' + num(receivableAmount) + ' | receita em queda ' + brl(proceedsDownside) + ' | receita protegida ' + brl(proceedsProtected),
            payload: {
                inputs: {
                    receivable_amount_usd: receivableAmount,
                    spot_rate: spotRate,
                    downside_rate: downsideRate,
                    floor_rate: floorRate,
                    spread_pct: spreadPct,
                    settlement_days: settlementDays,
                    minimum_brl_need: minimumNeed,
                },
                results: {
                    proceeds_today_brl: proceedsToday,
                    proceeds_downside_brl: proceedsDownside,
                    proceeds_protected_brl: proceedsProtected,
                    protection_gap_brl: gap,
                },
            },
        };
    }

    function computeHedge(form) {
        var exposureAmount = valueOf(form, 'exposure_amount');
        var spotRate = valueOf(form, 'spot_rate');
        var adverseMovePct = valueOf(form, 'adverse_move_pct');
        var marginPct = valueOf(form, 'margin_pct');
        var hedgeCostMonthlyPct = valueOf(form, 'hedge_cost_monthly_pct');
        var monthsToSettlement = valueOf(form, 'months_to_settlement');
        var exposureBrl = exposureAmount * spotRate;
        var potentialLoss = exposureBrl * (adverseMovePct / 100);
        var hedgeCost = exposureBrl * (hedgeCostMonthlyPct / 100) * monthsToSettlement;
        var marginBuffer = exposureBrl * (marginPct / 100);
        var pressureOnMargin = marginBuffer > 0 ? (potentialLoss / marginBuffer) * 100 : 0;
        var breakEvenMove = hedgeCostMonthlyPct * monthsToSettlement;
        var hedgeScore = potentialLoss > hedgeCost * 1.5 || pressureOnMargin > 30;
        var insights = [
            'Operando aberto, uma variação adversa de ' + pct(adverseMovePct) + ' consumiria aproximadamente ' + brl(potentialLoss) + '.',
            'O custo indicativo do hedge para ' + Math.round(monthsToSettlement) + ' meses seria de ' + brl(hedgeCost) + '.',
            'O break-even da proteção fica próximo de ' + pct(breakEvenMove) + ' de movimento cambial no período.',
            'A perda potencial consome cerca de ' + pct(pressureOnMargin) + ' da margem estimada da operação.',
            hedgeScore
                ? 'Leitura inicial: a assimetria favorece discutir hedge total ou parcial com a mesa.'
                : 'Leitura inicial: a proteção pode ser seletiva, mas ainda vale comparar custos e estruturas antes de decidir operar aberto.',
        ];

        return {
            toolKey: 'hedge',
            toolLabel: toolCards.hedge.title,
            badge: hedgeScore ? 'Assimetria pró-hedge' : 'Proteção seletiva',
            title: hedgeScore ? 'O custo da proteção parece menor que o risco de ficar aberto.' : 'A decisão pode admitir proteção parcial, mas não deve ignorar o risco.',
            description: 'O simulador compara perda potencial, custo indicativo do hedge e espaço de margem da operação.',
            metrics: [
                {label: 'Exposição em BRL', value: brl(exposureBrl)},
                {label: 'Perda potencial', value: brl(potentialLoss)},
                {label: 'Custo do hedge', value: brl(hedgeCost)},
                {label: 'Risco / custo', value: hedgeCost > 0 ? ratio(potentialLoss / hedgeCost) : 'n/a'},
            ],
            insights: insights,
            liveValue: hedgeCost > 0 ? ratio(potentialLoss / hedgeCost) : 'n/a',
            liveLabel: 'risco potencial em relação ao custo da proteção',
            liveCopy: hedgeScore
                ? 'O quadro sugere que vale aprofundar a proteção antes da execução.'
                : 'A decisão pode admitir hedge parcial ou tático, mas com leitura de margem.',
            leadSummary: 'Hedge | Exposição BRL ' + brl(exposureBrl) + ' | perda potencial ' + brl(potentialLoss) + ' | custo do hedge ' + brl(hedgeCost),
            payload: {
                inputs: {
                    exposure_amount_usd: exposureAmount,
                    spot_rate: spotRate,
                    adverse_move_pct: adverseMovePct,
                    margin_pct: marginPct,
                    hedge_cost_monthly_pct: hedgeCostMonthlyPct,
                    months_to_settlement: monthsToSettlement,
                },
                results: {
                    exposure_brl: exposureBrl,
                    potential_loss_brl: potentialLoss,
                    hedge_cost_brl: hedgeCost,
                    break_even_move_pct: breakEvenMove,
                    pressure_on_margin_pct: pressureOnMargin,
                },
            },
        };
    }

    function computeFunding4131(form) {
        var principalUsd = valueOf(form, 'principal_usd');
        var tenorMonths = valueOf(form, 'tenor_months');
        var offshoreBaseRate = valueOf(form, 'offshore_base_rate');
        var offshoreSpreadPct = valueOf(form, 'offshore_spread_pct');
        var localBaseRate = valueOf(form, 'local_base_rate');
        var localSpreadPct = valueOf(form, 'local_spread_pct');
        var hedgeMonthlyPct = valueOf(form, 'hedge_monthly_pct');
        var feesPct = valueOf(form, 'fees_pct');
        var naturalHedgePct = valueOf(form, 'natural_hedge_pct');
        var spotRate = normalizeNumeric(indicators.usd_brl || 0);
        var principalBrl = principalUsd * spotRate;
        var effectiveHedgeAnnual = hedgeMonthlyPct * 12 * Math.max(0, 1 - (naturalHedgePct / 100));
        var offshoreAnnual = offshoreBaseRate + offshoreSpreadPct + effectiveHedgeAnnual + (feesPct * (12 / Math.max(tenorMonths, 1)));
        var localAnnual = localBaseRate + localSpreadPct;
        var annualDiff = localAnnual - offshoreAnnual;
        var savingsBrl = principalBrl * (annualDiff / 100) * (tenorMonths / 12);
        var score = annualDiff > 1 && tenorMonths >= 6 && principalUsd >= 500000;
        var insights = [
            'O custo anual indicativo onshore fica em ' + pct(localAnnual) + ' considerando base local e spread adicional.',
            'O custo anual indicativo offshore fica em ' + pct(offshoreAnnual) + ' somando SOFR, spread, hedge ajustado e fees.',
            annualDiff >= 0
                ? 'A diferença favorece o offshore em cerca de ' + pct(annualDiff) + ' ao ano, ou ' + brl(savingsBrl) + ' no prazo analisado.'
                : 'A diferença favorece o onshore em cerca de ' + pct(Math.abs(annualDiff)) + ' ao ano no prazo analisado.',
            naturalHedgePct > 0
                ? 'A presença de hedge natural reduz parte do custo de proteção considerado no comparativo.'
                : 'Sem hedge natural, o custo de proteção pesa integralmente na conta offshore.',
            score
                ? 'Leitura inicial: a tese de 4131 parece merecer aprofundamento com a mesa e validação jurídica/operacional.'
                : 'Leitura inicial: a operação pode até ser viável, mas ainda não mostra folga suficiente para priorizar 4131 sem análise mais fina.',
        ];

        return {
            toolKey: 'funding4131',
            toolLabel: toolCards.funding4131.title,
            badge: score ? '4131 faz sentido analisar' : 'Comparar com funding local',
            title: score ? 'O offshore parece competitivo para esta operação.' : 'A tese ainda precisa provar eficiência frente ao funding local.',
            description: 'O simulador compara custo anual indicativo local e internacional já considerando proteção e fees.',
            metrics: [
                {label: 'Custo onshore', value: pct(localAnnual)},
                {label: 'Custo offshore', value: pct(offshoreAnnual)},
                {label: 'Diferença anual', value: pct(annualDiff)},
                {label: 'Economia indicativa', value: brl(savingsBrl)},
            ],
            insights: insights,
            liveValue: pct(annualDiff),
            liveLabel: 'diferença anual indicativa entre funding local e offshore',
            liveCopy: score
                ? 'Os números sugerem que vale aprofundar a estrutura 4131 com hedge e documentação.'
                : 'A conta ainda pede mais cuidado. Talvez o funding local ou outra estrutura continue mais eficiente.',
            leadSummary: '4131 | Principal USD ' + num(principalUsd) + ' | custo local ' + pct(localAnnual) + ' | custo offshore ' + pct(offshoreAnnual),
            payload: {
                inputs: {
                    principal_usd: principalUsd,
                    tenor_months: tenorMonths,
                    offshore_base_rate_pct: offshoreBaseRate,
                    offshore_spread_pct: offshoreSpreadPct,
                    local_base_rate_pct: localBaseRate,
                    local_spread_pct: localSpreadPct,
                    hedge_monthly_pct: hedgeMonthlyPct,
                    fees_pct: feesPct,
                    natural_hedge_pct: naturalHedgePct,
                },
                results: {
                    principal_brl: principalBrl,
                    offshore_annual_cost_pct: offshoreAnnual,
                    local_annual_cost_pct: localAnnual,
                    annual_difference_pct: annualDiff,
                    indicative_savings_brl: savingsBrl,
                },
            },
        };
    }

    function computeTrade(form) {
        var profile = rawValue(form, 'profile') || 'importer';
        var stage = rawValue(form, 'stage') || 'pre_shipment';
        var ticketUsd = valueOf(form, 'ticket_usd');
        var tenorDays = valueOf(form, 'tenor_days');
        var hasCollateral = rawValue(form, 'has_collateral') === 'yes';
        var hasNaturalHedge = rawValue(form, 'has_natural_hedge') === 'yes';
        var structures = [];

        if (profile === 'exporter' && stage === 'pre_shipment') {
            structures.push('ACC para financiar antes do embarque e aliviar caixa da produção.');
        }
        if (profile === 'exporter' && stage === 'post_shipment') {
            structures.push('ACE ou desconto internacional para antecipar recursos depois do embarque.');
        }
        if ((profile === 'importer' || profile === 'both') && stage === 'pay_supplier') {
            structures.push('FINIMP ou supplier credit para alongar o pagamento ao fornecedor.');
        }
        if (stage === 'guarantee') {
            structures.push('Carta de crédito ou garantia bancária para reforçar a segurança da contraparte.');
        }
        if ((profile === 'importer' || profile === 'both') && stage === 'term_extension' && tenorDays >= 180) {
            structures.push('Supplier credit, financiamento estruturado ou 4131 para ganhar prazo com critério.');
        }
        if (structures.length === 0) {
            structures.push('A operação pede desenho sob medida entre câmbio, funding e garantia.');
        }

        if (ticketUsd >= 500000 && tenorDays >= 180) {
            structures.push('Pelo ticket e prazo, vale comparar uma estrutura mais elaborada com funding internacional.');
        }
        if (!hasCollateral) {
            structures.push('Sem colateral claro, a mesa precisa priorizar instituições e produtos mais aderentes ao risco da operação.');
        }
        if (!hasNaturalHedge) {
            structures.push('Sem hedge natural, a proteção cambial precisa entrar cedo na conversa para evitar que o funding vire nova exposição.');
        }

        var title = structures[0];
        var insights = [
            'Perfil da operação: ' + (profile === 'importer' ? 'importador' : (profile === 'exporter' ? 'exportador' : 'importador e exportador')) + '.',
            'Objetivo principal: ' + ({
                pre_shipment: 'financiar antes do embarque',
                post_shipment: 'antecipar depois do embarque',
                pay_supplier: 'pagar fornecedor e alongar caixa',
                guarantee: 'dar segurança para a contraparte',
                term_extension: 'ganhar prazo para a operação'
            }[stage] || 'avaliar estrutura') + '.',
            'Prazo analisado: ' + Math.round(tenorDays) + ' dias para um ticket de aproximadamente USD ' + num(ticketUsd) + '.',
        ].concat(structures);

        return {
            toolKey: 'trade',
            toolLabel: toolCards.trade.title,
            badge: 'Estrutura prioritária',
            title: title,
            description: 'O roteador abaixo não substitui análise de crédito e documentação, mas ajuda a separar a conversa certa desde o início.',
            metrics: [
                {label: 'Ticket', value: 'USD ' + num(ticketUsd)},
                {label: 'Prazo', value: Math.round(tenorDays) + ' dias'},
                {label: 'Colateral', value: hasCollateral ? 'Sim' : 'Não'},
                {label: 'Hedge natural', value: hasNaturalHedge ? 'Sim' : 'Não'},
            ],
            insights: insights,
            liveValue: structures.length + ' opções',
            liveLabel: 'estruturas sugeridas para aprofundar com a mesa',
            liveCopy: 'O objetivo aqui é chegar na instituição certa com uma tese mais madura de execução.',
            leadSummary: 'Trade finance | Perfil ' + profile + ' | objetivo ' + stage + ' | ticket USD ' + num(ticketUsd) + ' | prazo ' + Math.round(tenorDays) + ' dias',
            payload: {
                inputs: {
                    profile: profile,
                    stage: stage,
                    ticket_usd: ticketUsd,
                    tenor_days: tenorDays,
                    has_collateral: hasCollateral,
                    has_natural_hedge: hasNaturalHedge,
                },
                results: {
                    recommended_structures: structures,
                },
            },
        };
    }

    function runCalculator(toolKey, form) {
        var result;
        if (toolKey === 'import') {
            result = computeImport(form);
        } else if (toolKey === 'export') {
            result = computeExport(form);
        } else if (toolKey === 'hedge') {
            result = computeHedge(form);
        } else if (toolKey === 'funding4131') {
            result = computeFunding4131(form);
        } else if (toolKey === 'trade') {
            result = computeTrade(form);
        }

        if (!result) {
            return;
        }

        results[toolKey] = result;
        renderResult(toolKey, result);

        if (toolKey === activeTool) {
            updateLiveSummary();
            if (hasToolInteraction) {
                showStickyBar();
            }
        }
    }

    function activateTool(toolKey) {
        activeTool = toolKey;
        document.querySelectorAll('[data-gx-tool-panel]').forEach(function(panel) {
            panel.classList.toggle('is-active', panel.getAttribute('data-gx-tool-panel') === toolKey);
        });
        document.querySelectorAll('[data-gx-tool-trigger]').forEach(function(button) {
            var isActive = button.getAttribute('data-gx-tool-trigger') === toolKey;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
        document.querySelectorAll('[data-gx-tool-card]').forEach(function(card) {
            card.classList.toggle('is-active', card.getAttribute('data-gx-tool-card') === toolKey);
        });
        updateLiveSummary();
    }

    var fxViewContentFired = false;
    document.querySelectorAll('[data-gx-tool-form]').forEach(function(form) {
        var toolKey = form.getAttribute('data-gx-tool-form');
        form.addEventListener('input', function() {
            hasToolInteraction = true;
            if (!fxViewContentFired) {
                fxViewContentFired = true;
                if (typeof gxFbq === 'function') gxFbq('track', 'ViewContent', { content_name: 'Simulador Câmbio Interação', content_category: toolKey });
                if (typeof gxGtag === 'function') gxGtag('event', 'view_item', { content_type: 'simulator', item_id: 'simulador_cambio_' + toolKey });
            }
            runCalculator(toolKey, form);
        });
        form.addEventListener('change', function() {
            hasToolInteraction = true;
            runCalculator(toolKey, form);
        });
        runCalculator(toolKey, form);
    });

    document.querySelectorAll('[data-gx-tool-trigger], [data-gx-tool-jump]').forEach(function(button) {
        var attribute = button.hasAttribute('data-gx-tool-trigger') ? 'data-gx-tool-trigger' : 'data-gx-tool-jump';
        button.addEventListener('click', function() {
            var toolKey = button.getAttribute(attribute);
            hasToolInteraction = true;
            activateTool(toolKey);
            if (attribute === 'data-gx-tool-jump') {
                var target = document.getElementById('laboratorio-cambio');
                if (target) {
                    target.scrollIntoView({behavior: 'smooth', block: 'start'});
                }
            }
        });
    });

    activateTool(activeTool);

    var stickyBar = document.getElementById('gx-fx-sticky-bar');
    var stickyVisible = false;

    function showStickyBar() {
        if (!stickyBar || stickyVisible) {
            return;
        }
        stickyVisible = true;
        stickyBar.removeAttribute('hidden');
        raf(function() { stickyBar.classList.add('is-visible'); });
    }

    function hideStickyBar() {
        if (!stickyBar || !stickyVisible) {
            return;
        }
        stickyVisible = false;
        stickyBar.classList.remove('is-visible');
        var timer = setTimeout(function() {
            if (!stickyVisible) {
                stickyBar.setAttribute('hidden', '');
            }
        }, 320);
        void timer;
    }

    var leadSectionNode = document.getElementById('lead-cambio');
    if (leadSectionNode && 'IntersectionObserver' in window) {
        var leadVisibilityObserver = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting) {
                hideStickyBar();
            } else if (hasToolInteraction) {
                showStickyBar();
            }
        }, {threshold: 0.15});
        leadVisibilityObserver.observe(leadSectionNode);
    }

    var leadForm = document.getElementById('gx-fx-lead-form');
    var statusNode = document.querySelector('[data-gx-lead-status]');

    function setLeadStatus(type, message) {
        if (!statusNode) {
            return;
        }
        statusNode.hidden = false;
        statusNode.className = 'gx-fx-form-status is-' + type;
        statusNode.textContent = message;
    }

    function buildLeadPayload() {
        var summary = results[activeTool];
        var activeForm = document.querySelector('[data-gx-tool-form="' + activeTool + '"]');
        if (!summary || !activeForm) {
            return '{}';
        }

        var toolInputs = {};
        activeForm.querySelectorAll('input, select, textarea').forEach(function(field) {
            if (!field.name) {
                return;
            }
            toolInputs[field.name] = field.type === 'number' ? normalizeNumeric(field.value) : String(field.value || '').trim();
        });

        return JSON.stringify({
            page: 'simuladores/cambio',
            tool: activeTool,
            tool_label: summary.toolLabel,
            generated_at: new Date().toISOString(),
            market_context: indicators,
            tool_inputs: toolInputs,
            summary: summary.payload,
            live_summary: summary.leadSummary
        });
    }

    if (leadForm) {
        leadForm.addEventListener('submit', function(event) {
            event.preventDefault();

            var submitButton = leadForm.querySelector('button[type="submit"]');
            var summary = results[activeTool];
            var payload = new FormData(leadForm);
            var originalButtonLabel = submitButton ? submitButton.textContent : '';
            var origin = 'Simuladores de Câmbio - ' + (summary ? summary.toolLabel : 'Mesa GX Capital');
            var userMessage = String(payload.get('message') || '').trim();
            var companyName = String(payload.get('company') || '').trim();
            var simDataObject;

            try {
                simDataObject = JSON.parse(buildLeadPayload());
            } catch (error) {
                simDataObject = {};
            }

            simDataObject.lead_context = {
                company: companyName,
                message: userMessage
            };

            payload.set('lead_origin', origin);
            payload.set('origin', origin);
            payload.set('origem', origin);
            payload.set('landing_page', window.location.href);
            payload.set('sim_data', JSON.stringify(simDataObject));
            payload.set('meta_content_name', 'Hub de Câmbio GX Capital');
            payload.set('meta_content_category', summary ? summary.toolLabel : 'Câmbio');
            payload.set('observations', (summary ? summary.leadSummary : 'Lead do hub de câmbio') + (userMessage ? ' | ' + userMessage : ''));

            /* Append UTMs from URL/session */
            if (typeof gxAppendUtm === 'function') gxAppendUtm(payload);

            /* Generate shared event ID for Meta dedup */
            var eventId = typeof gxEventId === 'function' ? gxEventId() : '';
            if (eventId) payload.set('event_id', eventId);

            /* Capture email/phone before form reset for Enhanced Conversions */
            var leadEmail = String(payload.get('email') || '').trim().toLowerCase();
            var leadPhone = String(payload.get('phone') || '').trim();

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Enviando...';
            }
            setLeadStatus('loading', 'Enviando cenário para a mesa...');

            fetch(leadForm.getAttribute('action'), {
                method: 'POST',
                body: payload,
                credentials: 'same-origin'
            }).then(function(response) {
                return response.json().catch(function() {
                    return {};
                });
            }).then(function(data) {
                if (!data || data.status !== 'success') {
                    throw new Error(data && data.message ? data.message : 'Falha ao enviar a simulação.');
                }

                leadForm.reset();
                var phoneCountry = leadForm.querySelector('[data-gx-phone-country]');
                if (phoneCountry) {
                    phoneCountry.dispatchEvent(new Event('change'));
                }

                /* Fire conversion events with shared event_id for Meta dedup */
                var fbqParams = { content_name: 'Hub de Câmbio GX Capital', content_category: summary ? summary.toolLabel : 'Câmbio', currency: 'BRL', value: 1 };
                if (eventId && typeof fbq === 'function') {
                    fbq('track', 'Lead', fbqParams, { eventID: eventId });
                } else if (typeof gxFbq === 'function') {
                    gxFbq('track', 'Lead', fbqParams);
                }
                /* Enhanced Conversions: send user_data to Google */
                if (typeof gxSetUserData === 'function') gxSetUserData(leadEmail, leadPhone);
                if (typeof gxGtag === 'function') gxGtag('event', 'generate_lead', {
                    event_category: 'conversion',
                    event_label: summary ? summary.toolLabel : 'Câmbio',
                    currency: 'BRL',
                    value: 1
                });

                setLeadStatus('success', <?= json_encode($leadConfig['success_message'] ?? 'Recebemos o seu cenário. A mesa de câmbio da GX Capital vai avaliar o melhor caminho para a sua operação.'); ?>);
            }).catch(function(error) {
                setLeadStatus('error', error && error.message ? error.message : 'Não foi possível enviar o cenário agora.');
            }).finally(function() {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = originalButtonLabel;
                }
            });
        });
    }
})();
</script>
