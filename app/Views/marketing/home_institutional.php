<?php
$isEnabled = static function ($section) {
    return !isset($section['enabled']) || !empty($section['enabled']);
};

$homeConfig = $homeConfig ?? [];
$navConfig = $homeConfig['nav'] ?? [];
$heroConfig = $homeConfig['hero'] ?? [];
$trustConfig = $homeConfig['trust_strip'] ?? [];
$verticalsConfig = $homeConfig['verticals'] ?? [];
$processConfig = $homeConfig['process'] ?? [];
$simulatorsConfig = $homeConfig['simulators'] ?? [];
$clippingsConfig = $homeConfig['clippings'] ?? [];
$partnersConfig = $homeConfig['partners'] ?? [];
$blogConfig = $homeConfig['blog'] ?? [];
$ctaConfig = $homeConfig['cta'] ?? [];
$leadConfig = $homeConfig['lead'] ?? [];

$contactChannels = $contactChannels ?? [];
$contactPhone = trim((string)($contactChannels['phone'] ?? (!empty($baseSettings->contact_phone) ? $baseSettings->contact_phone : '')));
$contactPhoneHref = !empty($contactChannels['phone_href']) ? $contactChannels['phone_href'] : (!empty($contactPhone) ? 'tel:' . preg_replace('/[^0-9+]/', '', (string)$contactPhone) : '');
$contactEmail = trim((string)($contactChannels['email'] ?? (!empty($baseSettings->contact_email) ? $baseSettings->contact_email : '')));
$whatsAppUrl = $whatsAppUrl ?? '';
$whatsAppMessage = $whatsAppMessage ?? '';
$whatsAppIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20.52 3.48A11.8 11.8 0 0 0 12.08 0C5.55 0 .24 5.31.24 11.84c0 2.08.54 4.11 1.58 5.89L0 24l6.46-1.69a11.8 11.8 0 0 0 5.62 1.43h.01c6.53 0 11.84-5.31 11.84-11.84 0-3.16-1.23-6.13-3.41-8.42Zm-8.44 18.26h-.01a9.84 9.84 0 0 1-5.01-1.37l-.36-.22-3.84 1 1.03-3.74-.24-.38a9.8 9.8 0 0 1-1.51-5.2C2.14 6.42 6.66 1.9 12.08 1.9c2.63 0 5.1 1.02 6.96 2.88a9.78 9.78 0 0 1 2.89 6.97c0 5.42-4.42 9.99-9.85 9.99Zm5.39-7.41c-.29-.14-1.71-.84-1.98-.94-.26-.1-.45-.14-.64.14-.19.29-.74.94-.91 1.13-.17.19-.34.22-.63.07-.29-.14-1.21-.45-2.31-1.45-.85-.76-1.42-1.69-1.59-1.98-.17-.29-.02-.44.13-.58.13-.13.29-.34.43-.5.14-.17.19-.29.29-.48.1-.19.05-.36-.02-.5-.07-.14-.64-1.55-.87-2.12-.23-.55-.47-.48-.64-.49h-.55c-.19 0-.5.07-.76.36-.26.29-.99.97-.99 2.37s1.01 2.75 1.15 2.94c.14.19 1.98 3.03 4.79 4.25.67.29 1.2.47 1.61.6.68.22 1.3.19 1.79.12.55-.08 1.71-.7 1.95-1.37.24-.67.24-1.24.17-1.37-.07-.12-.26-.19-.55-.34Z"/></svg>';
$navPrimaryCtaLabel = trim((string)($navConfig['primary_cta_label'] ?? 'Começar agora'));
$navPrimaryCtaUrl = trim((string)($navConfig['primary_cta_url'] ?? '#fale-especialista'));
$navLinks = array_values(array_filter($quickLinks ?? [], static function ($item) {
    return !empty($item['label']) && !empty($item['href']);
}));
$heroProof = array_values(array_filter($heroConfig['proof_items'] ?? [], static function ($item) {
    return !empty($item['enabled']) && (!empty($item['title']) || !empty($item['text']));
}));
$trustItems = array_values(array_filter($trustConfig['items'] ?? [], static function ($item) {
    return !empty($item['enabled']) && !empty($item['label']);
}));
$businessVerticals = array_values(array_filter($businessVerticals ?? [], static function ($item) {
    return !empty($item['enabled']);
}));
$processSteps = array_values(array_filter($processConfig['items'] ?? [], static function ($item) {
    return !empty($item['enabled']) && (!empty($item['title']) || !empty($item['desc']));
}));
$clippingItems = array_values(array_filter($clippingsConfig['items'] ?? [], static function ($item) {
    return !empty($item['enabled']) && (!empty($item['title']) || !empty($item['article_url']) || !empty($item['image_url']));
}));
$partnerItems = array_values(array_filter($partnersConfig['items'] ?? [], static function ($item) {
    return !empty($item['enabled']) && (!empty($item['name']) || !empty($item['logo_url']));
}));
$showClippings = $isEnabled($clippingsConfig) && !empty($clippingItems);
$showPartners = $isEnabled($partnersConfig) && !empty($partnerItems);
$showAuthorityDivider = $showClippings || $showPartners;
$showHero = $isEnabled($heroConfig);
$showTrust = $isEnabled($trustConfig) && !empty($trustItems);
$showVerticals = $isEnabled($verticalsConfig) && !empty($businessVerticals);
$showProcess = $isEnabled($processConfig) && !empty($processSteps);
$showSimulators = $isEnabled($simulatorsConfig);
$showBlog = $isEnabled($blogConfig) && !empty($latestPosts);
$showCta = $isEnabled($ctaConfig);
$showLead = $isEnabled($leadConfig);

$sectionAnchors = [
    '#verticais' => $showVerticals,
    '#simuladores' => $showSimulators,
    '#clipping-gx' => $showClippings,
    '#blog-tecnico' => $showBlog,
    '#fale-especialista' => $showLead,
];

$navLinks = array_values(array_filter($navLinks, static function ($item) use ($sectionAnchors) {
    $href = trim((string)($item['href'] ?? ''));
    if ($href !== '' && array_key_exists($href, $sectionAnchors) && !$sectionAnchors[$href]) {
        return false;
    }
    return !empty($item['label']) && $href !== '';
}));
$navLinks[] = ['label' => 'Blog', 'href' => $blogUrl ?? langBaseUrl('blog')];
if (service('moduleRegistry')->enabled('wealth')) {
    $navLinks[] = ['label' => 'Wealth', 'href' => $wealthUrl];
}
$navLinks[] = ['label' => 'Newsletter', 'href' => langBaseUrl('newsletter')];
$navLinks[] = ['label' => 'Histórias', 'href' => langBaseUrl('web-stories')];

$verticalIcons = [
    '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><line x1="12" y1="14" x2="18" y2="14"/></svg>',
    '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>',
    '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>',
    '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/><circle cx="12" cy="12" r="1" fill="currentColor" stroke="none"/></svg>',
];

$processIcons = [
    '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/><line x1="11" y1="8" x2="11" y2="14"/></svg>',
    '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
    '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
];

$trustIcons = [
    '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>',
    '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
    '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
    '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>',
];
?>
<main class="gx-marketing gx-home">
    <?php if (!$showHero): ?>
        <h1 class="gx-sr-only">GX Capital</h1>
    <?php endif; ?>

    <nav class="gx-nav" id="gx-nav">
        <div class="gx-nav-inner">
            <a href="<?= langBaseUrl(); ?>" class="gx-nav-brand" aria-label="GX Capital">
                <img src="<?= getLogo(); ?>" alt="GX Capital" width="<?= getLogoSize('width'); ?>" height="<?= getLogoSize('height'); ?>">
            </a>

            <div class="gx-nav-links" id="gx-nav-links">
                <?php foreach ($navLinks as $item): ?>
                    <a href="<?= esc($item['href']); ?>" class="gx-nav-link"><?= esc($item['label']); ?></a>
                <?php endforeach; ?>
                <div class="gx-nav-menu-extra">
                    <?php if (!authCheck() && $generalSettings->registration_system == 1): ?>
                        <a href="#" class="gx-nav-link" data-gx-modal-open="modalLogin" data-bs-target="#modalLogin">Entrar</a>
                    <?php else: ?>
                        <a href="<?= esc($blogUrl); ?>" class="gx-nav-link">Blog</a>
                    <?php endif; ?>
                    <a href="<?= esc($navPrimaryCtaUrl); ?>" class="gx-btn gx-btn-primary"><?= esc($navPrimaryCtaLabel); ?></a>
                </div>
            </div>

            <div class="gx-nav-right">
                <?php if (!authCheck() && $generalSettings->registration_system == 1): ?>
                    <a href="#" class="gx-nav-link" data-gx-modal-open="modalLogin" data-bs-target="#modalLogin">Entrar</a>
                <?php else: ?>
                    <a href="<?= esc($blogUrl); ?>" class="gx-nav-link">Blog</a>
                <?php endif; ?>
                <a href="<?= esc($navPrimaryCtaUrl); ?>" class="gx-btn gx-btn-primary"><?= esc($navPrimaryCtaLabel); ?></a>
                <button type="button" class="gx-nav-toggle" id="gx-nav-toggle" aria-expanded="false" aria-controls="gx-nav-links" aria-label="Menu">
                    <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
            </div>
        </div>
    </nav>

    <?php if ($showHero): ?>
        <section class="gx-hero">
            <div class="gx-hero-inner">
                <div class="gx-hero-content" data-gx-reveal>
                    <div class="gx-hero-badge">
                        <span class="gx-hero-badge-dot"></span>
                        <?= esc($heroConfig['badge'] ?? ''); ?>
                    </div>

                    <h1 class="gx-hero-title">
                        <?= esc($heroConfig['title'] ?? ''); ?>
                    </h1>

                    <p class="gx-hero-sub">
                        <?= esc($heroConfig['subtitle'] ?? ''); ?>
                    </p>

                    <div class="gx-hero-cta">
                        <a href="<?= esc($heroConfig['primary_cta_url'] ?? '#fale-especialista'); ?>" class="gx-btn gx-btn-primary gx-btn-lg"><?= esc($heroConfig['primary_cta_label'] ?? 'Falar com especialista'); ?></a>
                        <a href="<?= esc($heroConfig['secondary_cta_url'] ?? $simulatorsHubUrl); ?>" class="gx-btn gx-btn-ghost gx-btn-lg"><?= esc($heroConfig['secondary_cta_label'] ?? 'Explorar simuladores'); ?></a>
                        <?php if (!empty($whatsAppUrl)): ?>
                            <a href="<?= esc($whatsAppUrl); ?>" target="_blank" rel="noopener" class="gx-btn gx-btn-whatsapp gx-btn-lg"><?= $whatsAppIcon; ?>Chamar no WhatsApp</a>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($heroProof)): ?>
                        <div class="gx-hero-proof">
                            <?php foreach ($heroProof as $item): ?>
                                <div class="gx-hero-proof-item">
                                    <strong><?= esc($item['title']); ?></strong>
                                    <span><?= esc($item['text']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="gx-hero-aside" data-gx-reveal data-gx-delay="150">
                    <div class="gx-hero-visual-card">
                        <div class="gx-visual-header">
                            <span class="gx-visual-logo">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c9a96a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 L2 7 L12 12 L22 7 Z"/><path d="M2 17 L12 22 L22 17"/><path d="M2 12 L12 17 L22 12"/></svg>
                            </span>
                            <span class="gx-visual-title">Portfólio integrado</span>
                            <span class="gx-visual-badge">GX Capital</span>
                        </div>
                        <div class="gx-visual-pillars" aria-hidden="true">
                            <svg class="gx-pillars-link" viewBox="0 0 100 40" preserveAspectRatio="none" aria-hidden="true">
                                <path d="M10 30 Q 30 8 50 14 T 90 10" fill="none" stroke="url(#gxPillarGrad)" stroke-width="1.2" stroke-linecap="round" opacity="0.55"/>
                                <defs>
                                    <linearGradient id="gxPillarGrad" x1="0" y1="0" x2="1" y2="0">
                                        <stop offset="0%" stop-color="#c9a96a" stop-opacity="0.2"/>
                                        <stop offset="50%" stop-color="#c9a96a"/>
                                        <stop offset="100%" stop-color="#0c3163"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="gx-pillar" data-kind="navy">
                                <span class="gx-pillar-dot"></span>
                                <span class="gx-pillar-fill"></span>
                                <span class="gx-pillar-label">Crédito</span>
                            </div>
                            <div class="gx-pillar" data-kind="gold">
                                <span class="gx-pillar-dot"></span>
                                <span class="gx-pillar-fill"></span>
                                <span class="gx-pillar-label">FX</span>
                            </div>
                            <div class="gx-pillar is-lead" data-kind="navy">
                                <span class="gx-pillar-dot"></span>
                                <span class="gx-pillar-fill"></span>
                                <span class="gx-pillar-label">Consórcio</span>
                            </div>
                            <div class="gx-pillar" data-kind="gold">
                                <span class="gx-pillar-dot"></span>
                                <span class="gx-pillar-fill"></span>
                                <span class="gx-pillar-label">Seguros</span>
                            </div>
                            <div class="gx-pillar" data-kind="navy">
                                <span class="gx-pillar-dot"></span>
                                <span class="gx-pillar-fill"></span>
                                <span class="gx-pillar-label">Wealth</span>
                            </div>
                        </div>
                        <div class="gx-visual-row">
                            <div class="gx-visual-stat">
                                <strong><?= esc((string)count($businessVerticals)); ?></strong>
                                <span>verticais</span>
                            </div>
                            <div class="gx-visual-stat">
                                <strong><?= esc((string)count($simulators ?? [])); ?></strong>
                                <span>simuladores</span>
                            </div>
                            <div class="gx-visual-stat">
                                <strong>360&deg;</strong>
                                <span>cobertura</span>
                            </div>
                        </div>
                    </div>

                    <div class="gx-hero-stat-row">
                        <?php foreach ($heroStats as $stat): ?>
                            <div class="gx-hero-metric">
                                <strong><?= esc((string)$stat['value']); ?></strong>
                                <span><?= esc($stat['label']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($showTrust): ?>
        <div class="gx-strip">
            <div class="gx-strip-inner" data-gx-reveal>
                <span class="gx-strip-lead"><?= esc($trustConfig['lead'] ?? ''); ?></span>
                <?php foreach ($trustItems as $i => $item): ?>
                    <span class="gx-strip-item">
                        <?= $trustIcons[$i] ?? ''; ?>
                        <?= esc($item['label']); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($showClippings): ?>
        <section class="gx-section gx-section-alt" id="clipping-gx">
            <div class="gx-wrap">
                <div class="gx-section-header is-split" data-gx-reveal>
                    <div>
                        <p class="gx-label"><?= esc($clippingsConfig['label'] ?? ''); ?></p>
                        <h2 class="gx-section-title"><?= esc($clippingsConfig['title'] ?? ''); ?></h2>
                    </div>
                    <p class="gx-section-desc">
                        <?= esc($clippingsConfig['description'] ?? ''); ?>
                    </p>
                </div>

                <div class="gx-press-grid" data-gx-reveal data-gx-delay="80">
                    <?php foreach ($clippingItems as $index => $item): ?>
                        <article class="gx-press-card<?= $index === 0 ? ' gx-press-card-featured' : ''; ?>">
                            <?php if (!empty($item['image_url']) && !empty($item['article_url'])): ?>
                                <a href="<?= esc($item['article_url'] ?? '#'); ?>" class="gx-press-image" target="_blank" rel="noopener">
                                    <img src="<?= esc($item['image_url']); ?>" alt="<?= esc($item['title'] ?? $item['portal'] ?? 'Clipping GX Capital'); ?>" width="1600" height="1200" loading="lazy" decoding="async">
                                </a>
                            <?php elseif (!empty($item['image_url'])): ?>
                                <div class="gx-press-image">
                                    <img src="<?= esc($item['image_url']); ?>" alt="<?= esc($item['title'] ?? $item['portal'] ?? 'Clipping GX Capital'); ?>" width="1600" height="1200" loading="lazy" decoding="async">
                                </div>
                            <?php endif; ?>
                            <div class="gx-press-body">
                                <div class="gx-press-top">
                                    <?php if (!empty($item['portal'])): ?>
                                        <span class="gx-press-kicker"><?= esc($item['portal']); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($item['published_at'])): ?>
                                        <span class="gx-press-date"><?= esc($item['published_at']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="gx-press-title"><?= esc($item['title'] ?? ''); ?></h3>
                                <?php if (!empty($item['excerpt'])): ?>
                                    <p class="gx-press-summary"><?= esc($item['excerpt']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($item['article_url'])): ?>
                                    <a href="<?= esc($item['article_url']); ?>" class="gx-text-link" target="_blank" rel="noopener"><?= esc($clippingsConfig['item_cta_label'] ?? 'Ler matéria'); ?></a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($showPartners): ?>
        <section class="gx-section" id="parceiros-qualidade">
            <div class="gx-wrap">
                <div class="gx-section-header is-centered" data-gx-reveal>
                    <p class="gx-label"><?= esc($partnersConfig['label'] ?? ''); ?></p>
                    <h2 class="gx-section-title"><?= esc($partnersConfig['title'] ?? ''); ?></h2>
                    <p class="gx-section-desc" style="margin-left:auto;margin-right:auto;">
                        <?= esc($partnersConfig['description'] ?? ''); ?>
                    </p>
                </div>

                <div class="gx-partners-grid" data-gx-reveal data-gx-delay="80">
                    <?php foreach ($partnerItems as $item): ?>
                        <?php $partnerTag = !empty($item['website_url']) ? 'a' : 'div'; ?>
                        <<?= $partnerTag; ?>
                            class="gx-partner-card"
                            <?php if ($partnerTag === 'a'): ?>
                                href="<?= esc($item['website_url']); ?>"
                                target="_blank"
                                rel="noopener"
                            <?php endif; ?>>
                            <div class="gx-partner-logo-wrap">
                                <?php if (!empty($item['logo_url'])): ?>
                                    <img src="<?= esc($item['logo_url']); ?>" alt="<?= esc($item['name'] ?? 'Parceiro GX Capital'); ?>" class="gx-partner-logo" width="320" height="120" loading="lazy" decoding="async">
                                <?php else: ?>
                                    <span class="gx-partner-placeholder"><?= esc($item['name'] ?? 'Parceiro'); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($item['name'])): ?>
                                <span class="gx-partner-name"><?= esc($item['name']); ?></span>
                            <?php endif; ?>
                        </<?= $partnerTag; ?>>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($showAuthorityDivider): ?>
        <div class="gx-divider"></div>
    <?php endif; ?>

    <?php if ($showVerticals): ?>
        <section class="gx-section" id="verticais">
            <div class="gx-wrap">
                <div class="gx-section-header is-centered" data-gx-reveal>
                    <p class="gx-label"><?= esc($verticalsConfig['label'] ?? ''); ?></p>
                    <h2 class="gx-section-title"><?= esc($verticalsConfig['title'] ?? ''); ?></h2>
                    <p class="gx-section-desc" style="margin-left:auto;margin-right:auto;">
                        <?= esc($verticalsConfig['description'] ?? ''); ?>
                    </p>
                </div>

                <div class="gx-grid-5" data-gx-reveal data-gx-delay="100">
                    <?php foreach ($businessVerticals as $index => $vertical): ?>
                        <article class="gx-card" style="--gx-card-accent: <?= esc($vertical['accent'] ?? '#c9a96a'); ?>;">
                            <span class="gx-card-index"><?= esc(str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                            <span class="gx-card-icon"><?= $verticalIcons[$index] ?? esc($index + 1); ?></span>
                            <p class="gx-card-label"><?= esc($vertical['eyebrow'] ?? ''); ?></p>
                            <h3 class="gx-card-title"><?= esc($vertical['title'] ?? ''); ?></h3>
                            <p class="gx-card-desc"><?= esc($vertical['description'] ?? ''); ?></p>
                            <?php if (!empty($vertical['link_label']) && !empty($vertical['link_url'])): ?>
                                <a href="<?= esc($vertical['link_url']); ?>" class="gx-card-link"><?= esc($vertical['link_label']); ?></a>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($showVerticals && ($showProcess || $showSimulators || $showBlog || $showCta || $showLead)): ?>
        <div class="gx-divider"></div>
    <?php endif; ?>

    <?php if ($showProcess): ?>
        <section class="gx-section gx-section-alt">
            <div class="gx-wrap">
                <div class="gx-section-header is-centered" data-gx-reveal>
                    <p class="gx-label"><?= esc($processConfig['label'] ?? ''); ?></p>
                    <h2 class="gx-section-title"><?= esc($processConfig['title'] ?? ''); ?></h2>
                    <p class="gx-section-desc" style="margin-left:auto;margin-right:auto;">
                        <?= esc($processConfig['description'] ?? ''); ?>
                    </p>
                </div>

                <div class="gx-process-grid">
                    <?php foreach ($processSteps as $index => $step): ?>
                        <article class="gx-process-card" data-gx-reveal data-gx-delay="<?= esc((string)($index * 100)); ?>">
                            <span class="gx-process-num"><?= esc(str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                            <span class="gx-process-icon"><?= $processIcons[$index] ?? ''; ?></span>
                            <h3 class="gx-process-title"><?= esc($step['title'] ?? ''); ?></h3>
                            <p class="gx-process-desc"><?= esc($step['desc'] ?? ''); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($showProcess && ($showSimulators || $showBlog || $showCta || $showLead)): ?>
        <div class="gx-divider"></div>
    <?php endif; ?>

    <?php if ($showSimulators): ?>
        <section class="gx-section" id="simuladores">
            <div class="gx-wrap">
                <div class="gx-section-header is-split" data-gx-reveal>
                    <div>
                        <p class="gx-label"><?= esc($simulatorsConfig['label'] ?? ''); ?></p>
                        <h2 class="gx-section-title"><?= esc($simulatorsConfig['title'] ?? ''); ?></h2>
                    </div>
                    <p class="gx-section-desc">
                        <?= esc($simulatorsConfig['description'] ?? ''); ?>
                    </p>
                </div>

                <div data-gx-reveal data-gx-delay="80">
                    <?= view('marketing/_simulator_grid', [
                        'simulators' => $simulators,
                        'showPath' => false,
                        'showLegacyBadge' => false
                    ]); ?>
                </div>

                <div style="margin-top: 28px;" data-gx-reveal>
                    <a href="<?= esc($simulatorsConfig['cta_url'] ?? $simulatorsHubUrl); ?>" class="gx-btn gx-btn-ghost"><?= esc($simulatorsConfig['cta_label'] ?? 'Ver catálogo completo'); ?></a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($showSimulators && ($showBlog || $showCta || $showLead)): ?>
        <div class="gx-divider"></div>
    <?php endif; ?>

    <?php if ($showBlog): ?>
        <section class="gx-section gx-section-alt" id="blog-tecnico">
            <div class="gx-wrap">
                <div class="gx-section-header is-split" data-gx-reveal>
                    <div>
                        <p class="gx-label"><?= esc($blogConfig['label'] ?? ''); ?></p>
                        <h2 class="gx-section-title"><?= esc($blogConfig['title'] ?? ''); ?></h2>
                    </div>
                    <p class="gx-section-desc">
                        <?= esc($blogConfig['description'] ?? ''); ?>
                    </p>
                </div>

                <div class="gx-blog-grid" data-gx-reveal data-gx-delay="80">
                    <?php foreach ($latestPosts as $index => $post): ?>
                        <article class="gx-blog-card<?= $index === 0 ? ' gx-blog-card-featured' : ''; ?>">
                            <?php if (checkPostImg($post)): ?>
                                <a href="<?= generatePostURL($post); ?>" class="gx-blog-image"<?php postURLNewTab($post); ?>>
                                    <img src="<?= getPostImage($post, 'mid'); ?>" alt="<?= esc($post->title); ?>" width="600" height="338" loading="lazy" decoding="async">
                                </a>
                            <?php endif; ?>
                            <div class="gx-blog-body">
                                <div class="gx-blog-top">
                                    <a href="<?= generateCategoryURLById($post->category_id, $baseCategories); ?>" class="gx-blog-kicker" style="--gx-badge: <?= esc($post->category_color); ?>;">
                                        <?= esc($post->category_name); ?>
                                    </a>
                                    <?php if ($index === 0): ?>
                                        <span class="gx-blog-label">Destaque</span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="gx-blog-title">
                                    <a href="<?= generatePostURL($post); ?>"<?php postURLNewTab($post); ?>><?= esc(characterLimiter($post->title, 110, '...')); ?></a>
                                </h3>
                                <p class="gx-blog-meta"><?= view('marketing/_post_meta', ['postItem' => $post]); ?></p>
                                <p class="gx-blog-summary"><?= esc(characterLimiter($post->summary, 160, '...')); ?></p>
                                <a href="<?= generatePostURL($post); ?>" class="gx-text-link"<?php postURLNewTab($post); ?>><?= esc($blogConfig['featured_cta_label'] ?? 'Ler análise'); ?></a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top: 28px;" data-gx-reveal>
                    <a href="<?= esc($blogConfig['cta_url'] ?? $blogUrl); ?>" class="gx-btn gx-btn-ghost"><?= esc($blogConfig['cta_label'] ?? 'Ver todos os artigos'); ?></a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($showCta): ?>
        <section class="gx-section">
            <div class="gx-wrap">
                <div class="gx-cta-block" data-gx-reveal>
                    <div class="gx-cta-icon" aria-hidden="true">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(199,160,83,0.4)" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    </div>
                    <div class="gx-cta-content">
                        <p class="gx-label"><?= esc($ctaConfig['label'] ?? ''); ?></p>
                        <h2 class="gx-section-title"><?= esc($ctaConfig['title'] ?? ''); ?></h2>
                        <p class="gx-section-desc" style="margin-left:auto;margin-right:auto;">
                            <?= esc($ctaConfig['description'] ?? ''); ?>
                        </p>
                        <div class="gx-cta-actions">
                            <a href="<?= esc($ctaConfig['primary_cta_url'] ?? '#fale-especialista'); ?>" class="gx-btn gx-btn-primary gx-btn-lg"><?= esc($ctaConfig['primary_cta_label'] ?? 'Falar com o time'); ?></a>
                            <a href="<?= esc($ctaConfig['secondary_cta_url'] ?? $simulatorsHubUrl); ?>" class="gx-btn gx-btn-ghost gx-btn-lg"><?= esc($ctaConfig['secondary_cta_label'] ?? 'Abrir simuladores'); ?></a>
                            <?php if (!empty($whatsAppUrl)): ?>
                                <a href="<?= esc($whatsAppUrl); ?>" target="_blank" rel="noopener" class="gx-btn gx-btn-whatsapp gx-btn-lg"><?= $whatsAppIcon; ?>Chamar no WhatsApp</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($showLead): ?>
        <section class="gx-lead-section" id="fale-especialista">
            <div class="gx-wrap">
                <div class="gx-lead-grid">
                    <aside class="gx-lead-aside" data-gx-reveal>
                        <div class="gx-aside-icon" aria-hidden="true">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="rgba(199,160,83,0.6)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <p class="gx-label"><?= esc($leadConfig['label'] ?? ''); ?></p>
                        <h2 class="gx-section-title"><?= esc($leadConfig['title'] ?? ''); ?></h2>
                        <p class="gx-section-desc" style="color:rgba(255,255,255,0.6);">
                            <?= esc($leadConfig['description'] ?? ''); ?>
                        </p>
                        <?php if (!empty($whatsAppUrl)): ?>
                            <div class="gx-contact-highlight">
                                <span class="gx-fx-live-eyebrow">Contato imediato</span>
                                <strong>Fale agora com o time comercial pelo WhatsApp.</strong>
                                <p>Atendimento direto com especialistas prontos para entender sua demanda.</p>
                                <div class="gx-contact-cta-grid">
                                    <a href="#gx-home-specialist-form" class="gx-btn gx-btn-primary">Pedir contato do especialista</a>
                                    <a href="<?= esc($whatsAppUrl); ?>" target="_blank" rel="noopener" class="gx-btn gx-btn-whatsapp"><?= $whatsAppIcon; ?>Chamar no WhatsApp</a>
                                </div>
                                <p class="gx-contact-note">Resposta rápida em horário comercial.</p>
                            </div>
                        <?php endif; ?>
                        <div class="gx-contact-list">
                            <?php if (($leadConfig['show_phone'] ?? 1) && !empty($contactPhone)): ?>
                                <a href="<?= !empty($contactPhoneHref) ? esc($contactPhoneHref) : '#'; ?>" class="gx-contact-chip">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    <?= esc($contactPhone); ?>
                                </a>
                            <?php endif; ?>
                            <?php if (($leadConfig['show_email'] ?? 1) && !empty($contactEmail)): ?>
                                <a href="mailto:<?= esc($contactEmail); ?>" class="gx-contact-chip">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                    <?= esc($contactEmail); ?>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($whatsAppUrl)): ?>
                                <a href="<?= esc($whatsAppUrl); ?>" target="_blank" rel="noopener" class="gx-contact-chip">
                                    <?= $whatsAppIcon; ?>
                                    WhatsApp
                                </a>
                            <?php endif; ?>
                            <?php if ($leadConfig['show_simulators_chip'] ?? 1): ?>
                                <a href="<?= esc($simulatorsHubUrl); ?>" class="gx-contact-chip">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/></svg>
                                    <?= esc($leadConfig['simulators_chip_label'] ?? 'Ver simuladores'); ?>
                                </a>
                            <?php endif; ?>
                            <?php if ($leadConfig['show_blog_chip'] ?? 1): ?>
                                <a href="<?= esc($blogUrl); ?>" class="gx-contact-chip">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                    <?= esc($leadConfig['blog_chip_label'] ?? 'Explorar blog'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </aside>

                    <div class="gx-lead-card" data-gx-reveal data-gx-delay="100">
                        <?= view('marketing/_specialist_form', [
                            'formId' => 'gx-home-specialist-form',
                            'heading' => $leadConfig['form_heading'] ?? 'Envie sua demanda para o time GX Capital',
                            'description' => $leadConfig['form_description'] ?? 'Informe a estrutura, operação ou objetivo patrimonial. O retorno parte da vertical mais aderente.',
                            'buttonLabel' => $leadConfig['form_button_label'] ?? 'Solicitar contato',
                            'messagePlaceholder' => $leadConfig['message_placeholder'] ?? 'Ex.: preciso revisar hedge cambial, custo de capital, recebíveis, consórcio, seguros ou carteira de investimentos.'
                        ]); ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>

<script>
(function() {
    var nav = document.getElementById('gx-nav');
    var toggle = document.getElementById('gx-nav-toggle');
    var links = document.getElementById('gx-nav-links');
    var raf = window.requestAnimationFrame ? window.requestAnimationFrame.bind(window) : function(cb) {
        return setTimeout(cb, 16);
    };

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
            if (navTicking) return;
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
        links.querySelectorAll('a').forEach(function(a) {
            a.addEventListener('click', function() {
                setMenuState(false);
            });
        });
    }

    var nodes = document.querySelectorAll('[data-gx-reveal]');
    if ('IntersectionObserver' in window && nodes.length) {
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
        var obs = new IntersectionObserver(function(entries) {
            entries.forEach(function(e) {
                if (!e.isIntersecting) return;
                scheduleReveal(e.target, e.target.getAttribute('data-gx-delay'));
                obs.unobserve(e.target);
            });
        }, {threshold: 0.1, rootMargin: '0px 0px -40px 0px'});
        nodes.forEach(function(n) {
            /* acima da dobra fica visível direto — LCP não pode esperar animação */
            if (n.getBoundingClientRect().top > window.innerHeight * 0.85) {
                n.classList.add('gx-reveal-armed');
                obs.observe(n);
            } else {
                n.classList.add('is-visible');
            }
        });
    } else {
        nodes.forEach(function(n) { n.classList.add('is-visible'); });
    }
})();
</script>
<script>
(function() {
    /* Track simulator card clicks as select_content */
    document.querySelectorAll('.gx-sim-card a').forEach(function(link) {
        link.addEventListener('click', function() {
            var card = link.closest('.gx-sim-card');
            var title = card ? (card.querySelector('.gx-sim-title') || {}).textContent : '';
            title = (title || '').trim();
            if (typeof gxFbq === 'function') gxFbq('track', 'ViewContent', { content_name: title, content_category: 'Home Institucional' });
            if (typeof gxGtag === 'function') gxGtag('event', 'select_content', { content_type: 'simulator', item_id: title });
        });
    });

    /* Track CTA button clicks */
    document.querySelectorAll('.gx-hero-actions a, .gx-cta-actions a').forEach(function(link) {
        link.addEventListener('click', function() {
            var label = (link.textContent || '').trim();
            if (typeof gxGtag === 'function') gxGtag('event', 'select_promotion', { promotion_name: label, creative_slot: 'home_cta' });
        });
    });
})();
</script>
