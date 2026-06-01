<!DOCTYPE html>
<html lang="<?= esc($activeLang->short_form); ?>" <?= $activeLang->text_direction == 'rtl' ? 'dir="rtl"' : ''; ?>>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script>
/* Google Consent Mode v2 — default denied until user grants consent */
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('consent', 'default', {
    'ad_storage': 'denied',
    'ad_user_data': 'denied',
    'ad_personalization': 'denied',
    'analytics_storage': 'granted',
    'functionality_storage': 'granted',
    'personalization_storage': 'granted',
    'security_storage': 'granted',
    'wait_for_update': 500
});
gtag('set', 'ads_data_redaction', true);
gtag('set', 'url_passthrough', true);
<?php
$cookiesSettings = json_decode($generalSettings->cookies_settings ?? '{}', true);
if (!empty($cookiesSettings['cookie_status']) && $cookiesSettings['cookie_status'] == 1): ?>
/* If the user already accepted cookies, update consent */
if (document.cookie.indexOf('gx_cookie_consent=accepted') !== -1) {
    gtag('consent', 'update', {
        'ad_storage': 'granted',
        'ad_user_data': 'granted',
        'ad_personalization': 'granted',
        'analytics_storage': 'granted'
    });
}
<?php endif; ?>
</script>
<title><?= escMeta($title); ?> - <?= escMeta($baseSettings->site_title); ?></title>
<meta name="description" content="<?= escMeta($description); ?>">
<meta name="keywords" content="<?= escMeta($keywords); ?>">
<meta name="author" content="<?= escMeta($baseSettings->application_name); ?>">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="theme-color" content="#0c3163">
<meta property="og:locale" content="<?= escMeta($activeLang->language_code); ?>">
<meta property="og:site_name" content="<?= escMeta($baseSettings->application_name); ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="<?= escMeta($title); ?> - <?= escMeta($baseSettings->site_title); ?>">
<meta property="og:description" content="<?= escMeta(!empty($ogDescription) ? $ogDescription : $description); ?>">
<meta property="og:url" content="<?= esc($canonicalUrl ?? langBaseUrl()); ?>">
<?php if (!empty($socialImage)): ?>
<meta property="og:image" content="<?= escMeta($socialImage); ?>">
<?php if (!empty($socialImageWidth)): ?>
<meta property="og:image:width" content="<?= esc($socialImageWidth); ?>">
<?php endif;
if (!empty($socialImageHeight)): ?>
<meta property="og:image:height" content="<?= esc($socialImageHeight); ?>">
<?php endif;
endif; ?>
<meta property="fb:app_id" content="<?= escMeta($generalSettings->facebook_app_id); ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= escMeta($title); ?> - <?= escMeta($baseSettings->site_title); ?>">
<meta name="twitter:description" content="<?= escMeta(!empty($ogDescription) ? $ogDescription : $description); ?>">
<?php if (!empty($socialImage)): ?>
<meta name="twitter:image" content="<?= escMeta($socialImage); ?>">
<?php endif; ?>
<?= csrf_meta(); ?>
<?php if ($generalSettings->pwa_status == 1): ?>
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="<?= escMeta($baseSettings->application_name); ?>">
<meta name="msapplication-TileImage" content="<?= base_url(getPwaLogo($generalSettings, 'sm')); ?>">
<meta name="msapplication-TileColor" content="#0c3163">
<link rel="manifest" href="<?= base_url('manifest.json'); ?>">
<link rel="apple-touch-icon" href="<?= base_url(getPwaLogo($generalSettings, 'sm')); ?>">
<?php endif; ?>
<link rel="alternate" type="application/rss+xml" title="<?= escMeta($baseSettings->application_name); ?> - RSS" href="<?= base_url('rss/latest-posts'); ?>">
<link rel="shortcut icon" type="image/png" href="<?= getFavicon(); ?>">
<link rel="canonical" href="<?= esc($canonicalUrl ?? langBaseUrl()); ?>">
<?php if (!empty($activeLanguages)):
    foreach ($activeLanguages as $language): ?>
<link rel="alternate" href="<?= esc(generateBaseURLByLang($language)); ?>" hreflang="<?= escMeta($language->language_code); ?>">
<?php endforeach;
endif; ?>
<link rel="alternate" href="<?= esc(generateBaseURLByLangId($generalSettings->site_lang)); ?>" hreflang="x-default">
<link rel="preload" as="font" type="font/woff2" crossorigin href="<?= base_url('assets/fonts/inter/inter-400.woff2'); ?>">
<link rel="preload" as="font" type="font/woff2" crossorigin href="<?= base_url('assets/fonts/inter/inter-600.woff2'); ?>">
<link rel="preload" as="font" type="font/woff2" crossorigin href="<?= base_url('assets/fonts/inter/inter-700.woff2'); ?>">
<link rel="preload" as="font" type="font/woff2" crossorigin href="<?= base_url('assets/fonts/open-sans/open-sans-400.woff2'); ?>">
<link rel="preload" as="font" type="font/woff2" crossorigin href="<?= base_url('assets/fonts/open-sans/open-sans-600.woff2'); ?>">
<link rel="preload" as="font" type="font/woff2" crossorigin href="<?= base_url('assets/fonts/open-sans/open-sans-700.woff2'); ?>">
<?php if (!empty($pageHeadView)): ?>
<?= view($pageHeadView); ?>
<?php endif; ?>
<?= $generalSettings->custom_header_codes; ?>
</head>
<body class="<?= !empty($bodyClass) ? esc($bodyClass) : ''; ?> <?= $activeLang->text_direction == 'rtl' ? 'rtl-mode' : ''; ?>">
