<!DOCTYPE html>
<html lang="<?= $activeLang->short_form; ?>" <?= $rtl ? 'dir="rtl"' : ''; ?>>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script>
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
if (document.cookie.indexOf('gx_cookie_consent=accepted') !== -1) {
    gtag('consent', 'update', { 'ad_storage': 'granted', 'ad_user_data': 'granted', 'ad_personalization': 'granted', 'analytics_storage': 'granted' });
}
</script>
<title><?= escMeta(seoTitle($title, $baseSettings->site_title)); ?></title>
<meta name="description" content="<?= escMeta($description); ?>"/>
<meta name="author" content="<?= escMeta($baseSettings->application_name); ?>"/>
<meta name="robots" content="<?= escMeta($metaRobots ?? 'max-image-preview:large, max-snippet:-1, max-video-preview:-1'); ?>">
<meta property="og:locale" content="<?= escMeta($activeLang->language_code); ?>"/>
<meta property="og:site_name" content="<?= escMeta($baseSettings->application_name); ?>"/>
<?= csrf_meta(); ?>

<?php if (isset($postType)): ?>
<?php if (!empty($post->image_id) || !empty($post->image_url)): ?>
<link rel="preload" as="image" href="<?= esc(getPostImage($post, 'default')); ?>" fetchpriority="high"/>
<?php endif; ?>
<meta property="og:type" content="<?= escMeta($ogType); ?>"/>
<meta property="og:title" content="<?= escMeta($ogTitle); ?>"/>
<meta property="og:description" content="<?= escMeta($description); ?>"/>
<meta property="og:url" content="<?= esc(currentFullURL()); ?>"/>
<meta property="og:image" content="<?= escMeta($ogImage); ?>"/>
<meta property="og:image:width" content="<?= $ogWidth; ?>"/>
<meta property="og:image:height" content="<?= $ogHeight; ?>"/>
<meta property="article:author" content="<?= escMeta($ogAuthor); ?>"/>
<meta property="fb:app_id" content="<?= escMeta($generalSettings->facebook_app_id); ?>"/>
<?php foreach ($ogTags as $tag): ?>
<meta property="article:tag" content="<?= escMeta($tag->tag); ?>"/>
<?php endforeach; ?>
<meta property="article:published_time" content="<?= $ogPublishedTime; ?>"/>
<meta property="article:modified_time" content="<?= $ogModifiedTime; ?>"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:site" content="@<?= escMeta($baseSettings->application_name); ?>"/>
<meta name="twitter:creator" content="@<?= escMeta($ogCreator); ?>"/>
<meta name="twitter:title" content="<?= escMeta($post->title); ?>"/>
<meta name="twitter:description" content="<?= escMeta($description); ?>"/>
<meta name="twitter:image" content="<?= escMeta($ogImage); ?>"/>
<?php else:
    $nonPostOgImage = getLogo();
    $nonPostOgW = (int) getLogoSize('width');
    $nonPostOgH = (int) getLogoSize('height');
    if (!empty($categoryOgImage)) {
        $nonPostOgImage = $categoryOgImage;
        $nonPostOgW = 1536;
        $nonPostOgH = 1024;
    } ?>
<meta property="og:image" content="<?= escMeta($nonPostOgImage); ?>"/>
<meta property="og:image:width" content="<?= $nonPostOgW; ?>"/>
<meta property="og:image:height" content="<?= $nonPostOgH; ?>"/>
<meta property="og:type" content="website"/>
<meta property="og:title" content="<?= escMeta($title); ?> - <?= escMeta($baseSettings->site_title); ?>"/>
<meta property="og:description" content="<?= escMeta($description); ?>"/>
<meta property="og:url" content="<?= esc(currentFullURL()); ?>"/>
<meta property="fb:app_id" content="<?= escMeta($generalSettings->facebook_app_id); ?>"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:site" content="@<?= escMeta($baseSettings->application_name); ?>"/>
<meta name="twitter:title" content="<?= escMeta($title); ?> - <?= escMeta($baseSettings->site_title); ?>"/>
<meta name="twitter:description" content="<?= escMeta($description); ?>"/>
<meta name="twitter:image" content="<?= escMeta($nonPostOgImage); ?>"/>
<?php endif;
if ($generalSettings->pwa_status == 1): ?>
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="<?= escMeta($baseSettings->application_name); ?>">
<meta name="msapplication-TileImage" content="<?= base_url(getPwaLogo($generalSettings, 'sm')); ?>">
<meta name="msapplication-TileColor" content="#2F3BA2">
<link rel="manifest" href="<?= base_url('manifest.json'); ?>">
<link rel="apple-touch-icon" href="<?= base_url(getPwaLogo($generalSettings, 'sm')); ?>">
<?php endif; ?>
<link rel="alternate" type="application/rss+xml" title="<?= escMeta($baseSettings->application_name); ?> - RSS" href="<?= base_url('rss/latest-posts'); ?>">
<link rel="shortcut icon" type="image/png" href="<?= getFavicon(); ?>"/>
<link rel="canonical" href="<?= esc(base_url(uri_string()));?>"/>
<link rel="alternate" href="<?= esc(currentFullURL()); ?>" hreflang="<?= escMeta($activeLang->language_code); ?>"/>
<?php if (!empty($amphtmlUrl)): ?>
<link rel="amphtml" href="<?= esc($amphtmlUrl); ?>"/>
<?php endif; ?>
<?= view('common/_fonts'); ?>
<?php if ($activeLang->text_direction == 'rtl'): ?>
<link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.rtl.min.css'); ?>" rel="stylesheet">
<?php else: ?>
<link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
<?php endif; ?>
<link href="<?= base_url($assetsPath . '/css/style-2.4.1.min.css'); ?>" rel="stylesheet">
<?= loadView('partials/_css_js_header'); ?>
<?php if (!empty($pageHeadView)): ?>
<?= view($pageHeadView); ?>
<?php endif; ?>
<?= $generalSettings->custom_header_codes; ?>
</head>
<body class="<?= $activeTheme->theme; ?> <?= !empty($bodyClass) ? esc($bodyClass) : ''; ?> <?= $darkMode == true ? 'dark-mode' : ''; ?> <?= $activeLang->text_direction == 'rtl' ? 'rtl-mode' : ''; ?>">
<?= loadView('nav/_nav_top'); ?>
<header id="header" <?= isset($headerNoMargin) ? 'class="mb-0"' : ''; ?>>
<?= loadView('nav/_nav_main'); ?>
<?= loadView('nav/_nav_mobile'); ?>
</header>
<?= loadView('partials/_ad_spaces', ['adSpace' => 'header', 'class' => 'mb-3']); ?>
<?= loadView('partials/_modals'); ?>
