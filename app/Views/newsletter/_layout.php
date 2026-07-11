<?php
$pageTitle = $title ?? brandLang('Newsletter.nl_brand_title');
$pageDescription = $description ?? lang('Newsletter.lay_desc');
$canonical = $canonical ?? (function_exists('current_url') ? current_url() : '');
$ogImage = $ogImage ?? null;
$ogType = $ogType ?? 'website';
$preloadHero = $preloadHero ?? null;
$jsonLd = $jsonLd ?? null;
$robots = $robots ?? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
?><!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle); ?> — <?= esc(brand('display_name')); ?></title>
    <meta name="description" content="<?= esc($pageDescription); ?>">
    <meta name="robots" content="<?= esc($robots); ?>">
    <meta name="author" content="<?= esc(brand('display_name')); ?>">
    <?php if (!empty($canonical)): ?>
    <link rel="canonical" href="<?= esc($canonical); ?>">
    <?php endif; ?>

    <!-- Open Graph -->
    <meta property="og:type" content="<?= esc($ogType); ?>">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="<?= esc(brand('display_name')); ?>">
    <meta property="og:title" content="<?= esc($pageTitle); ?>">
    <meta property="og:description" content="<?= esc($pageDescription); ?>">
    <?php if (!empty($canonical)): ?>
    <meta property="og:url" content="<?= esc($canonical); ?>">
    <?php endif; ?>
    <?php if (!empty($ogImage)): ?>
    <meta property="og:image" content="<?= esc($ogImage); ?>">
    <meta property="og:image:alt" content="<?= esc($pageTitle); ?>">
    <meta property="og:image:width" content="1536">
    <meta property="og:image:height" content="1024">
    <?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($pageTitle); ?>">
    <meta name="twitter:description" content="<?= esc($pageDescription); ?>">
    <?php if (!empty($ogImage)): ?>
    <meta name="twitter:image" content="<?= esc($ogImage); ?>">
    <?php endif; ?>

    <link rel="icon" href="/assets/logo-icon.png">
    <?php if (!empty($preloadHero)): ?>
    <link rel="preload" as="image" href="<?= esc($preloadHero); ?>" fetchpriority="high">
    <?php endif; ?>
    <link rel="stylesheet" href="/colors_and_type.css">

    <?php if (!empty($jsonLd)): ?>
    <script type="application/ld+json"><?= $jsonLd; ?></script>
    <?php endif; ?>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: var(--font-sans);
            color: var(--fg1);
            background: var(--bg1);
            line-height: var(--lh-normal);
            -webkit-font-smoothing: antialiased;
        }
        a { color: var(--gx-primary); text-decoration: none; transition: var(--transition-smooth); }
        a:hover { color: var(--gx-secondary-dark); }
        img { max-width: 100%; display: block; }
        .gx-container { max-width: 1200px; margin: 0 auto; padding: 0 var(--space-6); }
        .gx-eyebrow-bar { display: inline-flex; align-items: center; gap: var(--space-3); }
        .gx-eyebrow-bar::before { content: ''; display: block; width: 32px; height: 2px; background: var(--gx-secondary-light); }
        .gx-eyebrow-bar.on-light::before { background: var(--gx-primary); }

        /* Top nav (minimal) */
        .nl-topbar {
            background: var(--bg-dark);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding: var(--space-4) 0;
        }
        .nl-topbar-inner { display: flex; align-items: center; justify-content: space-between; }
        .nl-topbar a.logo { display: flex; align-items: center; gap: var(--space-3); }
        .nl-topbar a.logo img { height: 32px; }
        .nl-topbar .nav-links { display: flex; gap: var(--space-6); }
        .nl-topbar .nav-links a {
            color: var(--gx-secondary-light);
            font-size: var(--fs-xs);
            font-weight: var(--fw-bold);
            letter-spacing: var(--ls-widest);
            text-transform: uppercase;
        }
        .nl-topbar .nav-links a:hover { color: var(--fg-on-dark); }

        /* Footer */
        .nl-footer {
            background: var(--bg-dark);
            color: var(--gx-secondary-light);
            padding: var(--space-12) 0 var(--space-8);
            margin-top: var(--space-20);
            border-top: 4px solid var(--gx-secondary-dark);
        }
        .nl-footer-inner { display: flex; flex-wrap: wrap; gap: var(--space-8); justify-content: space-between; align-items: flex-start; }
        .nl-footer .footer-brand { display: flex; align-items: center; gap: var(--space-3); }
        .nl-footer .footer-brand img { height: 36px; }
        .nl-footer .copyright {
            font-size: var(--fs-xs);
            letter-spacing: var(--ls-widest);
            text-transform: uppercase;
            opacity: 0.7;
        }

        @media (max-width: 640px) {
            .nl-topbar .nav-links { display: none; }
        }
    </style>
    <?= $headExtra ?? ''; ?>
</head>
<body>
    <header class="nl-topbar">
        <div class="gx-container nl-topbar-inner">
            <a href="/" class="logo">
                <img src="/assets/logo-app-blue.png" alt="<?= esc(brand('display_name')); ?>">
            </a>
            <nav class="nav-links">
                <a href="/"><?= lang('Newsletter.lay_nav_inicio'); ?></a>
                <a href="/wealth"><?= lang('Newsletter.lay_nav_wealth'); ?></a>
                <a href="/simuladores"><?= lang('Newsletter.lay_nav_sim'); ?></a>
                <a href="/newsletter"><?= lang('Newsletter.lay_nav_nl'); ?></a>
            </nav>
        </div>
    </header>

    <main>
        <?= $bodyContent ?? ''; ?>
    </main>

    <footer class="nl-footer">
        <div class="gx-container nl-footer-inner">
            <div class="footer-brand">
                <img src="/assets/logo-white.png" alt="<?= esc(brand('display_name')); ?>">
            </div>
            <div class="copyright">
                &copy; <?= date('Y'); ?> <?= brandLang('Newsletter.lay_copyright'); ?>

            </div>
        </div>
    </footer>
</body>
</html>
