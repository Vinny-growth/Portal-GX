<?php
/**
 * Playbook Importação Blindada — Ebook interativo
 * Design: brutalist financial · navy + champagne gold (preservado do HTML original)
 * Tracking: Meta Pixel + GA + GX toolkit (UTMs, eventID, dedup)
 */
$contactChannels = $contactChannels ?? [];
$whatsAppDigits = $contactChannels['whatsapp_digits'] ?? '';
$whatsAppHref = !empty($whatsAppDigits) ? 'https://wa.me/' . $whatsAppDigits : 'https://wa.me/555120421991';
$contactEmail = $contactChannels['email'] ?? 'contato@gx.capital';
$contactPhone = $contactChannels['phone'] ?? '+55 (51) 2042·1991';
$specialistUrl = $specialistUrl ?? langBaseUrl('simuladores/cambio') . '#contato';
$simuladorUrl = $simuladorUrl ?? langBaseUrl('simuladores/cambio');
$homeUrl = $homeUrl ?? langBaseUrl();
?>
<!doctype html>
<html lang="<?= esc($activeLang->short_form ?? 'pt-BR'); ?>">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title><?= escMeta($title); ?> — <?= escMeta($baseSettings->site_title); ?></title>
<meta name="description" content="<?= escMeta($description); ?>">
<meta name="keywords" content="<?= escMeta($keywords); ?>">
<meta name="author" content="<?= escMeta($baseSettings->application_name); ?>">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
<meta name="theme-color" content="#0c3163">
<meta property="og:locale" content="<?= escMeta($activeLang->language_code ?? 'pt_BR'); ?>">
<meta property="og:site_name" content="<?= escMeta($baseSettings->application_name); ?>">
<meta property="og:type" content="article">
<meta property="og:title" content="<?= escMeta($title); ?>">
<meta property="og:description" content="<?= escMeta($description); ?>">
<meta property="og:url" content="<?= esc($canonicalUrl); ?>">
<?php if (!empty($socialImage)): ?>
<meta property="og:image" content="<?= escMeta($socialImage); ?>">
<?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= escMeta($title); ?>">
<meta name="twitter:description" content="<?= escMeta($description); ?>">
<?php if (!empty($socialImage)): ?>
<meta name="twitter:image" content="<?= escMeta($socialImage); ?>">
<?php endif; ?>
<link rel="canonical" href="<?= esc($canonicalUrl); ?>">
<link rel="shortcut icon" type="image/png" href="<?= getFavicon(); ?>">
<?php if (!empty($activeLanguages)):
    foreach ($activeLanguages as $language): ?>
<link rel="alternate" href="<?= esc(generateBaseURLByLang($language)); ?>playbook/importacao-blindada" hreflang="<?= escMeta($language->language_code); ?>">
<?php endforeach;
endif; ?>
<link rel="alternate" href="<?= esc($canonicalUrl); ?>" hreflang="x-default">

<?php if (!empty($playbookConfig['datePublished'])): ?>
<meta property="article:published_time" content="<?= escMeta($playbookConfig['datePublished']); ?>">
<meta property="article:modified_time"  content="<?= escMeta($playbookConfig['dateModified'] ?? $playbookConfig['datePublished']); ?>">
<meta property="article:section" content="<?= escMeta($playbookConfig['section'] ?? ''); ?>">
<meta property="article:tag" content="<?= escMeta($playbookConfig['keywords'] ?? ''); ?>">
<meta name="author" content="Vinicius Teixeira · GX Capital">
<meta property="article:author" content="<?= esc(base_url()); ?>">
<?php endif; ?>

<link rel="preload" as="image" href="<?= base_url('assets/logo-icon.png'); ?>" fetchpriority="high">
<link rel="preload" as="style" href="<?= base_url('colors_and_type.css'); ?>">

<?= csrf_meta(); ?>

<?php if (!empty($playbookConfig)): ?>
<?= view('marketing/_playbook_seo', ['playbookConfig' => $playbookConfig]); ?>
<?php endif; ?>

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
if (document.cookie.indexOf('gx_cookie_consent=accepted') !== -1) {
    gtag('consent', 'update', {
        'ad_storage': 'granted',
        'ad_user_data': 'granted',
        'ad_personalization': 'granted',
        'analytics_storage': 'granted'
    });
}
</script>

<link rel="stylesheet" href="<?= base_url('colors_and_type.css'); ?>">
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" defer></script>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  html, body {
    background: var(--gx-bg-muted);
    color: var(--gx-fg);
    font-family: var(--font-sans);
    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility;
  }
  body { padding-bottom: 0; }

  a { color: inherit; text-decoration: none; }

  /* Layout shell -------------------------------------------------------- */
  .shell {
    display: grid;
    grid-template-columns: 280px minmax(0, 1fr);
    max-width: 1400px;
    margin: 0 auto;
    background: var(--gx-bg);
    box-shadow: 0 0 0 1px var(--gx-border);
    min-height: 100vh;
  }

  /* Sidebar TOC --------------------------------------------------------- */
  .toc {
    position: sticky;
    top: 0;
    align-self: start;
    height: 100vh;
    background: linear-gradient(180deg, var(--gx-primary) 0%, var(--gx-primary-dark) 100%);
    color: #fff;
    padding: 28px 22px 28px 28px;
    border-right: 1px solid rgba(255,255,255,0.06);
    overflow-y: auto;
  }
  .toc::-webkit-scrollbar { width: 6px; }
  .toc::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); }

  .toc-brand {
    display: flex; align-items: center; gap: 10px;
    padding-bottom: 18px;
    border-bottom: 1px solid rgba(255,255,255,0.12);
    margin-bottom: 18px;
  }
  .toc-brand img { height: 36px; width: auto; }
  .toc-wordmark { font-family: var(--font-sans); font-weight: 900; font-size: 14px; letter-spacing: 0.04em; color: #fff; line-height: 1; text-transform: uppercase; }
  .toc-wordmark .gold { color: var(--gx-secondary-light); font-weight: 500; letter-spacing: 0.18em; font-size: 10px; display: block; margin-top: 4px; }
  .toc-brand-meta { font-size: 9px; letter-spacing: 0.18em; text-transform: uppercase; color: var(--gx-secondary-light); opacity: 0.7; line-height: 1.4; }

  .toc-eyebrow {
    font-size: 10px; letter-spacing: 0.18em; text-transform: uppercase;
    color: var(--gx-secondary-light); opacity: 0.55; margin: 18px 0 10px;
  }

  .toc-list { list-style: none; }
  .toc-list li { display: block; }
  .toc-list a {
    display: grid;
    grid-template-columns: 28px 1fr;
    gap: 10px;
    padding: 8px 8px 8px 8px;
    font-size: 12px;
    color: rgba(255,255,255,0.78);
    border-left: 2px solid transparent;
    transition: var(--transition-smooth);
    line-height: 1.35;
  }
  .toc-list a:hover { color: #fff; background: rgba(255,255,255,0.06); border-left-color: var(--gx-secondary-dark); transform: translateX(2px); }
  .toc-num {
    font-family: var(--font-mono);
    color: var(--gx-secondary-light);
    font-size: 10px;
    letter-spacing: 0.05em;
    padding-top: 1px;
  }
  .toc-foot {
    margin-top: 24px;
    padding-top: 18px;
    border-top: 1px solid rgba(255,255,255,0.12);
    font-size: 10px; letter-spacing: 0.16em; text-transform: uppercase;
    color: rgba(255,255,255,0.4);
    line-height: 1.7;
  }
  .toc-cta {
    display: inline-flex; align-items: center; gap: 6px;
    margin-top: 14px;
    padding: 9px 12px;
    background: var(--gx-secondary-dark);
    color: #fff;
    font-size: 10px; letter-spacing: 0.16em; text-transform: uppercase; font-weight: 700;
    box-shadow: 3px 3px 0 0 rgba(0,0,0,0.35);
    transition: var(--transition-smooth);
  }
  .toc-cta:hover { transform: translate(-1px,-1px); box-shadow: 5px 5px 0 0 rgba(0,0,0,0.4); }

  /* Page area ----------------------------------------------------------- */
  main { min-width: 0; }

  /* Cover --------------------------------------------------------------- */
  .cover {
    position: relative;
    min-height: 100vh;
    background: linear-gradient(180deg, var(--gx-primary) 0%, var(--gx-primary-dark) 100%);
    color: #fff;
    overflow: hidden;
    padding: 56px 64px 56px 64px;
    display: flex; flex-direction: column; justify-content: space-between;
  }
  .cover::before {
    content: ''; position: absolute; inset: 0;
    background: url('<?= base_url('assets/grid.svg'); ?>'); opacity: 0.06;
    pointer-events: none;
  }
  .cover-watermark {
    position: absolute;
    right: -30px;
    top: 60px;
    font-family: var(--font-sans);
    font-weight: 900;
    font-size: 320px;
    line-height: 1;
    letter-spacing: -0.06em;
    color: #fff;
    opacity: 0.04;
    pointer-events: none;
    user-select: none;
  }

  .cover-top { display: flex; justify-content: space-between; align-items: flex-start; position: relative; z-index: 2; }
  .cover-top img { height: 56px; width: auto; }
  .cover-brand { display: flex; align-items: center; gap: 14px; }
  .cover-wordmark { font-family: var(--font-sans); font-weight: 900; font-size: 22px; color: #fff; letter-spacing: 0.02em; text-transform: uppercase; line-height: 1; }
  .cover-wordmark .gold { display:block; font-weight: 500; font-size: 11px; letter-spacing: 0.22em; color: var(--gx-secondary-light); margin-top: 6px; }
  .cover-meta { text-align: right; font-size: 10px; letter-spacing: 0.18em; text-transform: uppercase; color: var(--gx-secondary-light); line-height: 1.7; }

  .cover-mid { position: relative; z-index: 2; }
  .cover-eyebrow {
    display: inline-flex; align-items: center; gap: 10px;
    font-size: 11px; letter-spacing: 0.22em; text-transform: uppercase; font-weight: 700;
    color: var(--gx-secondary-light);
    border-top: 2px solid var(--gx-secondary-dark);
    padding-top: 10px;
    margin-bottom: 28px;
  }
  .cover-eyebrow .dot { width: 8px; height: 8px; background: var(--gx-secondary-dark); display: inline-block; }

  .cover-title {
    font-family: var(--font-sans);
    font-weight: 900;
    font-size: clamp(56px, 7.2vw, 104px);
    line-height: 0.9;
    letter-spacing: -0.04em;
    text-transform: uppercase;
    margin-bottom: 24px;
    max-width: 14ch;
  }
  .cover-title .accent { color: var(--gx-secondary-light); }
  .cover-title .stripe {
    display: inline-block;
    background: var(--gx-secondary-dark);
    color: #fff;
    padding: 0 14px;
    margin-left: -2px;
  }
  .cover-sub {
    font-size: 18px;
    line-height: 1.45;
    color: rgba(219,199,162,0.85);
    max-width: 64ch;
    font-weight: 400;
    margin-bottom: 32px;
  }
  .cover-sub strong { color: #fff; font-weight: 700; }

  .cover-data {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    border-top: 1px solid rgba(255,255,255,0.18);
    border-bottom: 1px solid rgba(255,255,255,0.18);
    margin-top: 32px;
  }
  .cover-data > div { padding: 18px 20px; border-right: 1px solid rgba(255,255,255,0.12); }
  .cover-data > div:last-child { border-right: 0; }
  .cd-label { font-size: 10px; letter-spacing: 0.18em; text-transform: uppercase; color: var(--gx-secondary-light); margin-bottom: 6px; opacity: 0.7; }
  .cd-value { font-family: var(--font-mono); font-weight: 700; font-size: 22px; letter-spacing: -0.01em; }
  .cd-trend { font-size: 11px; color: #93d4a8; margin-top: 2px; font-family: var(--font-mono); }
  .cd-trend.warn { color: #ffb454; }

  .cover-bot { display: flex; justify-content: space-between; align-items: flex-end; position: relative; z-index: 2; gap: 32px; }
  .cover-volatility {
    display: flex; gap: 18px; align-items: flex-end;
  }
  .vol-svg { display: block; }
  .cover-stamp {
    border: 1px solid rgba(255,255,255,0.3);
    padding: 14px 18px;
    text-align: right;
  }
  .cover-stamp-label { font-size: 9px; letter-spacing: 0.22em; text-transform: uppercase; color: var(--gx-secondary-light); opacity: 0.7; }
  .cover-stamp-val { font-family: var(--font-mono); font-weight: 700; font-size: 14px; margin-top: 2px; }

  /* Breadcrumb (visible + JSON-LD support) ----------------------------- */
  .gx-breadcrumb {
    background: var(--gx-primary-dark);
    border-bottom: 1px solid rgba(255,255,255,0.08);
    padding: 12px 64px;
  }
  .gx-breadcrumb ol {
    list-style: none; margin: 0; padding: 0;
    display: flex; flex-wrap: wrap; align-items: center; gap: 8px;
    font-size: 11px; letter-spacing: 0.16em; text-transform: uppercase; font-weight: 600;
    color: rgba(255,255,255,0.55);
  }
  .gx-breadcrumb li { display: inline-flex; align-items: center; gap: 8px; }
  .gx-breadcrumb a { color: var(--gx-secondary-light); text-decoration: none; transition: color 0.15s ease; }
  .gx-breadcrumb a:hover { color: #fff; }
  .gx-breadcrumb [aria-current="page"] { color: #fff; }
  .gx-breadcrumb [aria-hidden="true"] { color: var(--gx-secondary-dark); }

  /* Reading-time pill on cover ----------------------------------------- */
  .cover-meta-pill {
    display: inline-flex; align-items: center; gap: 6px;
    margin-left: 14px; padding: 4px 10px;
    border: 1px solid rgba(219,199,162,0.4);
    background: rgba(219,199,162,0.08);
    color: var(--gx-secondary-light);
    font-size: 10px; letter-spacing: 0.16em; text-transform: uppercase; font-weight: 700;
  }

  @media (max-width: 1100px) {
    .gx-breadcrumb { padding: 10px 24px; font-size: 10px; }
    .cover-meta-pill { display: none; }
  }

  /* Section primitives ------------------------------------------------- */
  .chapter {
    padding: 88px 88px 96px;
    border-top: 1px solid var(--gx-border);
    position: relative;
  }
  .chapter:nth-child(even) { background: var(--gx-bg-muted); }

  .ch-head {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 24px;
    align-items: end;
    margin-bottom: 40px;
    padding-bottom: 24px;
    border-bottom: 2px solid var(--gx-primary);
  }
  .ch-num {
    font-family: var(--font-mono);
    font-weight: 700;
    font-size: 96px;
    line-height: 0.85;
    color: var(--gx-primary);
    letter-spacing: -0.04em;
  }
  .ch-num .pre { color: var(--gx-secondary-dark); font-size: 12px; letter-spacing: 0.2em; display: block; font-family: var(--font-sans); margin-bottom: 8px; }
  .ch-titlebox h2 {
    font-family: var(--font-sans);
    font-weight: 900;
    font-size: 44px;
    line-height: 0.95;
    letter-spacing: -0.025em;
    text-transform: uppercase;
    color: var(--gx-primary-dark);
  }
  .ch-titlebox .ch-deck {
    font-size: 16px;
    color: var(--gx-fg-muted);
    margin-top: 10px;
    max-width: 70ch;
    line-height: 1.5;
  }

  .body p { font-size: 15px; line-height: 1.7; color: var(--gx-fg); margin-bottom: 14px; max-width: 75ch; }
  .body p strong { font-weight: 700; color: var(--gx-primary-dark); }
  .body h3 {
    font-family: var(--font-sans);
    font-weight: 800;
    font-size: 22px;
    text-transform: uppercase;
    letter-spacing: -0.01em;
    color: var(--gx-primary-dark);
    margin: 32px 0 14px;
    padding-left: 14px;
    border-left: 4px solid var(--gx-secondary-dark);
  }
  .body h4 {
    font-family: var(--font-sans);
    font-weight: 700;
    font-size: 14px;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--gx-secondary-dark);
    margin: 22px 0 10px;
  }

  .lede {
    font-size: 22px;
    line-height: 1.4;
    font-weight: 500;
    color: var(--gx-primary-dark);
    max-width: 60ch;
    margin-bottom: 22px;
    letter-spacing: -0.01em;
  }
  .lede em { color: var(--gx-secondary-dark); font-style: normal; font-weight: 700; }

  /* Pull quote / callout */
  .pull {
    border-left: 4px solid var(--gx-secondary-dark);
    background: var(--gx-primary-dark);
    color: #fff;
    padding: 32px 36px;
    margin: 32px 0;
    position: relative;
    box-shadow: 6px 6px 0 0 rgba(12,49,99,0.18);
  }
  .pull::before {
    content: '"';
    font-family: var(--font-sans);
    font-weight: 900;
    font-size: 120px;
    line-height: 1;
    color: var(--gx-secondary-dark);
    position: absolute;
    top: 8px; right: 24px;
    opacity: 0.5;
  }
  .pull-eyebrow { font-size: 10px; letter-spacing: 0.22em; text-transform: uppercase; color: var(--gx-secondary-light); margin-bottom: 10px; }
  .pull-text { font-size: 22px; font-weight: 700; line-height: 1.35; max-width: 50ch; letter-spacing: -0.01em; }

  /* KPI grid */
  .kpis {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    border: 1px solid var(--gx-primary-dark);
    background: var(--gx-primary-dark);
    color: #fff;
    margin: 24px 0;
    box-shadow: 6px 6px 0 0 rgba(12,49,99,0.18);
  }
  .kpis.compact { grid-template-columns: repeat(4, 1fr); }
  .kpi {
    padding: 24px 22px 22px;
    border-right: 1px solid rgba(255,255,255,0.08);
    position: relative;
  }
  .kpi:last-child { border-right: 0; }
  .kpi::after {
    content: ''; position: absolute; left: 22px; bottom: 0; height: 4px; width: 36px;
    background: var(--gx-secondary-dark);
  }
  .kpi-label { font-size: 10px; letter-spacing: 0.18em; text-transform: uppercase; color: var(--gx-secondary-light); margin-bottom: 8px; opacity: 0.85; }
  .kpi-value { font-family: var(--font-mono); font-weight: 800; font-size: 30px; letter-spacing: -0.02em; line-height: 1; }
  .kpi-value .unit { font-size: 14px; opacity: 0.7; margin-left: 4px; font-weight: 500; }
  .kpi-delta { font-size: 12px; font-family: var(--font-mono); margin-top: 8px; }
  .kpi-delta.up { color: #5fd07f; }
  .kpi-delta.down { color: #f87171; }
  .kpi-delta.neutral { color: rgba(255,255,255,0.55); }

  /* Two col layout */
  .two-col { display: grid; grid-template-columns: 1.2fr 1fr; gap: 36px; align-items: start; }
  .two-col.flip { grid-template-columns: 1fr 1.2fr; }

  /* Tables */
  .gx-table {
    width: 100%;
    border-collapse: collapse;
    font-family: var(--font-sans);
    font-size: 13px;
    margin: 16px 0 8px;
    background: #fff;
    box-shadow: 4px 4px 0 0 rgba(12,49,99,0.18);
    border: 1px solid var(--gx-border);
  }
  .gx-table thead th {
    background: var(--gx-primary);
    color: #fff;
    text-align: left;
    padding: 12px 14px;
    font-size: 10px;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    font-weight: 700;
    border-right: 1px solid rgba(255,255,255,0.08);
  }
  .gx-table thead th:last-child { border-right: 0; }
  .gx-table tbody td {
    padding: 13px 14px;
    border-bottom: 1px solid var(--gx-border);
    border-right: 1px solid var(--gx-border);
    font-family: var(--font-mono);
    font-variant-numeric: tabular-nums;
    color: var(--gx-fg);
  }
  .gx-table tbody td:first-child { font-family: var(--font-sans); font-weight: 600; color: var(--gx-primary-dark); }
  .gx-table tbody td:last-child { border-right: 0; }
  .gx-table tbody tr:last-child td { border-bottom: 0; }
  .gx-table tbody tr:hover td { background: var(--gx-bg-muted); }
  .gx-table .pos { color: var(--gx-success); font-weight: 700; }
  .gx-table .neg { color: var(--gx-danger); font-weight: 700; }
  .gx-table .hot { background: rgba(217, 119, 6, 0.06); }
  .gx-table .hot td:first-child::before {
    content: '●'; color: var(--gx-warning); margin-right: 6px;
  }

  /* Cards */
  .card-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin: 18px 0; }
  .card-grid.three { grid-template-columns: repeat(3, 1fr); }
  .strat-card {
    background: #fff;
    border: 1px solid var(--gx-border);
    padding: 28px 26px;
    position: relative;
    transition: var(--transition-smooth);
    box-shadow: 4px 4px 0 0 rgba(12,49,99,0.12);
  }
  .strat-card:hover { transform: translate(-2px,-2px); box-shadow: 8px 8px 0 0 rgba(12,49,99,0.22); border-color: var(--gx-primary); }
  .strat-card::before, .strat-card::after {
    content: '';
    position: absolute;
    width: 14px; height: 14px;
    border: 2px solid var(--gx-secondary-dark);
    transition: opacity 0.2s ease;
  }
  .strat-card::before { top: -1px; left: -1px; border-right: 0; border-bottom: 0; }
  .strat-card::after { bottom: -1px; right: -1px; border-left: 0; border-top: 0; }

  .strat-tag {
    display: inline-block;
    font-size: 10px; letter-spacing: 0.2em; text-transform: uppercase; font-weight: 700;
    color: var(--gx-secondary-dark);
    margin-bottom: 10px;
  }
  .strat-card h4 {
    font-size: 20px; font-weight: 800; text-transform: uppercase;
    color: var(--gx-primary-dark); letter-spacing: -0.01em;
    margin-bottom: 6px; padding: 0; border: 0;
  }
  .strat-card .strat-sub { font-size: 12px; color: var(--gx-fg-muted); margin-bottom: 14px; line-height: 1.5; }
  .strat-card ul { list-style: none; }
  .strat-card ul li {
    font-size: 12.5px; line-height: 1.5; padding: 6px 0 6px 18px; position: relative;
    border-bottom: 1px dashed var(--gx-border);
  }
  .strat-card ul li:last-child { border-bottom: 0; }
  .strat-card ul li::before {
    content: ''; position: absolute; left: 0; top: 13px; width: 8px; height: 2px; background: var(--gx-secondary-dark);
  }
  .strat-card ul.cons li::before { background: var(--gx-danger); opacity: 0.6; }
  .strat-card .strat-block-label {
    display: block; font-size: 10px; letter-spacing: 0.18em; text-transform: uppercase;
    color: var(--gx-fg-muted); margin: 14px 0 4px;
  }
  .strat-card .usecase {
    margin-top: 16px;
    padding: 12px 14px;
    background: var(--gx-bg-muted);
    border-left: 3px solid var(--gx-primary);
    font-size: 12.5px; line-height: 1.5;
  }

  /* Steps */
  .steps {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    margin: 22px 0;
    border: 1px solid var(--gx-border);
    background: #fff;
  }
  .step {
    padding: 20px 18px;
    border-right: 1px solid var(--gx-border);
    position: relative;
  }
  .step:last-child { border-right: 0; }
  .step-num { font-family: var(--font-mono); font-size: 11px; color: var(--gx-secondary-dark); letter-spacing: 0.1em; margin-bottom: 6px; font-weight: 700; }
  .step h5 { font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; color: var(--gx-primary-dark); margin-bottom: 6px; }
  .step p { font-size: 12px; line-height: 1.5; color: var(--gx-fg-muted); margin: 0; }

  /* Sector cards */
  .sectors { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; margin: 18px 0; }
  .sector {
    background: #fff;
    border: 1px solid var(--gx-border);
    border-left: 4px solid var(--gx-primary);
    padding: 22px 24px;
    position: relative;
  }
  .sector.warn { border-left-color: var(--gx-warning); }
  .sector.danger { border-left-color: var(--gx-danger); }
  .sector.success { border-left-color: var(--gx-success); }
  .sector-head { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 4px; }
  .sector h4 { padding: 0; border: 0; margin: 0; font-size: 18px; font-weight: 800; text-transform: uppercase; color: var(--gx-primary-dark); letter-spacing: -0.01em; }
  .sector .delta { font-family: var(--font-mono); font-size: 18px; font-weight: 700; }
  .sector .delta.up { color: var(--gx-success); }
  .sector .delta.down { color: var(--gx-danger); }
  .sector .reason { font-size: 13px; color: var(--gx-fg-muted); margin: 8px 0 12px; line-height: 1.5; }
  .sector ul { list-style: none; display: flex; flex-wrap: wrap; gap: 6px; }
  .sector ul li {
    font-size: 11px; letter-spacing: 0.06em; text-transform: uppercase; font-weight: 600;
    background: var(--gx-bg-muted); color: var(--gx-primary-dark);
    padding: 5px 10px; border: 1px solid var(--gx-border);
  }
  .sector .impact { margin-top: 14px; padding-top: 12px; border-top: 1px dashed var(--gx-border); font-size: 12px; color: var(--gx-primary-dark); font-weight: 600; }
  .sector .impact strong { color: var(--gx-secondary-dark); text-transform: uppercase; letter-spacing: 0.1em; font-size: 10px; display: block; margin-bottom: 2px; }

  /* Volatility chart */
  .chart-wrap {
    background: var(--gx-primary-dark);
    color: #fff;
    padding: 32px 36px;
    margin: 24px 0;
    border: 1px solid var(--gx-primary-dark);
    box-shadow: 8px 8px 0 0 rgba(12,49,99,0.2);
    position: relative;
    overflow: hidden;
  }
  .chart-wrap::before {
    content: 'GXC'; position: absolute; right: -20px; bottom: -50px;
    font-family: var(--font-sans); font-weight: 900; font-size: 220px; opacity: 0.04;
    line-height: 1; letter-spacing: -0.06em;
  }
  .chart-head { display: flex; justify-content: space-between; align-items: end; margin-bottom: 18px; position: relative; z-index: 2; }
  .chart-head h4 {
    font-size: 11px; letter-spacing: 0.22em; text-transform: uppercase; color: var(--gx-secondary-light);
    border: 0; padding: 0; margin: 0; font-weight: 700;
  }
  .chart-head .now {
    font-family: var(--font-mono); font-size: 32px; font-weight: 800;
    letter-spacing: -0.02em;
  }
  .chart-svg { width: 100%; display: block; height: 280px; position: relative; z-index: 2; }
  .chart-legend { display: flex; gap: 18px; margin-top: 14px; font-size: 11px; color: rgba(255,255,255,0.7); position: relative; z-index: 2; }
  .chart-legend span { display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.06em; text-transform: uppercase; }
  .chart-legend .sw { width: 16px; height: 3px; display: inline-block; }

  /* Checklist */
  .timeline { margin: 12px 0; }
  .tl-month {
    display: grid;
    grid-template-columns: 220px 1fr;
    gap: 24px;
    padding: 22px 0;
    border-bottom: 1px solid var(--gx-border);
  }
  .tl-month:last-child { border-bottom: 0; }
  .tl-label {
    font-family: var(--font-mono);
    font-size: 13px; font-weight: 700;
    color: var(--gx-primary);
    letter-spacing: 0;
  }
  .tl-label .hint {
    display: block; margin-top: 4px;
    font-size: 10px; letter-spacing: 0.18em; text-transform: uppercase; font-weight: 700;
    color: var(--gx-secondary-dark); font-family: var(--font-sans);
  }
  .tl-list { list-style: none; display: grid; gap: 8px; }
  .tl-list li {
    font-size: 13.5px; line-height: 1.5;
    padding: 8px 12px 8px 36px;
    background: #fff;
    border: 1px solid var(--gx-border);
    position: relative;
  }
  .tl-list li::before {
    content: ''; position: absolute; left: 12px; top: 13px;
    width: 14px; height: 14px;
    border: 1.5px solid var(--gx-primary);
    background: #fff;
  }
  .tl-list li.urgent { border-left: 3px solid var(--gx-danger); }
  .tl-list li.urgent::before { border-color: var(--gx-danger); }

  /* Conclusion CTA */
  .cta-block {
    background: linear-gradient(135deg, var(--gx-primary) 0%, var(--gx-primary-dark) 100%);
    color: #fff;
    padding: 64px 64px;
    margin-top: 0;
    position: relative;
    overflow: hidden;
  }
  .cta-block::before {
    content: 'GX'; position: absolute; right: -30px; top: -60px;
    font-family: var(--font-sans); font-weight: 900; font-size: 360px; opacity: 0.05;
    line-height: 1; letter-spacing: -0.06em;
  }
  .cta-block h2 {
    font-family: var(--font-sans);
    font-weight: 900; font-size: 56px;
    text-transform: uppercase;
    letter-spacing: -0.03em;
    line-height: 0.95;
    margin-bottom: 18px;
    max-width: 18ch;
  }
  .cta-block h2 .accent { color: var(--gx-secondary-light); }
  .cta-block .lede2 { font-size: 18px; line-height: 1.5; max-width: 60ch; color: rgba(219,199,162,0.85); margin-bottom: 32px; }
  .cta-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; max-width: 760px; margin-bottom: 36px; position: relative; z-index: 2; }
  .cta-link {
    display: flex; justify-content: space-between; align-items: center;
    padding: 18px 22px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.18);
    transition: var(--transition-smooth);
  }
  .cta-link:hover { background: rgba(255,255,255,0.12); transform: translate(-2px,-2px); box-shadow: 4px 4px 0 0 rgba(0,0,0,0.4); border-color: var(--gx-secondary-dark); }
  .cta-link .l { font-size: 10px; letter-spacing: 0.2em; text-transform: uppercase; color: var(--gx-secondary-light); }
  .cta-link .v { font-family: var(--font-mono); font-size: 14px; font-weight: 600; margin-top: 2px; }
  .cta-link i[data-lucide] { width: 18px; height: 18px; color: var(--gx-secondary-light); }

  .cta-buttons { display: flex; gap: 12px; flex-wrap: wrap; }
  .btn-primary, .btn-ghost {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 16px 24px;
    font-size: 12px; letter-spacing: 0.18em; text-transform: uppercase; font-weight: 700;
    transition: var(--transition-smooth);
    cursor: pointer; border: 0;
  }
  .btn-primary {
    background: var(--gx-secondary-dark); color: #fff;
    box-shadow: 4px 4px 0 0 rgba(0,0,0,0.4);
  }
  .btn-primary:hover { transform: translate(-2px,-2px); box-shadow: 6px 6px 0 0 rgba(0,0,0,0.5); }
  .btn-ghost { background: transparent; color: #fff; border: 1px solid rgba(255,255,255,0.4); }
  .btn-ghost:hover { background: rgba(255,255,255,0.08); border-color: var(--gx-secondary-light); }

  /* Lead capture form (matched to brutalist style) */
  .lead-block {
    position: relative;
    background: var(--gx-primary-dark);
    color: #fff;
    padding: 80px 88px;
    border-top: 4px solid var(--gx-secondary-dark);
    overflow: hidden;
  }
  .lead-block::before {
    content: 'MESA DE CÂMBIO';
    position: absolute;
    bottom: -36px; right: -20px;
    font-family: var(--font-sans); font-weight: 900; font-size: 140px;
    line-height: 1; letter-spacing: -0.04em; color: #fff; opacity: 0.04;
    pointer-events: none; user-select: none;
  }
  .lead-grid {
    display: grid;
    grid-template-columns: 1fr 1.1fr;
    gap: 56px;
    align-items: start;
    position: relative;
    z-index: 2;
  }
  .lead-eyebrow {
    display: inline-flex; align-items: center; gap: 10px;
    font-size: 11px; letter-spacing: 0.22em; text-transform: uppercase; font-weight: 700;
    color: var(--gx-secondary-light);
    border-top: 2px solid var(--gx-secondary-dark);
    padding-top: 12px; margin-bottom: 24px;
  }
  .lead-eyebrow .dot { width: 8px; height: 8px; background: var(--gx-secondary-dark); display: inline-block; }
  .lead-block h3 {
    font-family: var(--font-sans);
    font-weight: 900; font-size: 40px;
    line-height: 0.95; letter-spacing: -0.025em;
    text-transform: uppercase; color: #fff;
    margin-bottom: 18px; padding: 0; border: 0;
  }
  .lead-block h3 .accent { color: var(--gx-secondary-light); }
  .lead-block p.lead-copy { font-size: 15px; line-height: 1.6; color: rgba(219,199,162,0.85); margin-bottom: 24px; max-width: 48ch; }
  .lead-block ul.lead-bullets { list-style: none; margin-top: 18px; }
  .lead-block ul.lead-bullets li {
    position: relative; padding: 10px 0 10px 28px;
    font-size: 13px; line-height: 1.55; color: rgba(255,255,255,0.85);
    border-bottom: 1px dashed rgba(255,255,255,0.12);
  }
  .lead-block ul.lead-bullets li:last-child { border-bottom: 0; }
  .lead-block ul.lead-bullets li::before {
    content: ''; position: absolute; left: 0; top: 17px;
    width: 14px; height: 2px; background: var(--gx-secondary-dark);
  }

  .lead-form-wrap {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.18);
    padding: 32px 32px 28px;
    box-shadow: 8px 8px 0 0 rgba(0,0,0,0.4);
    position: relative;
  }
  .lead-form-wrap::before, .lead-form-wrap::after {
    content: ''; position: absolute; width: 18px; height: 18px;
    border: 2px solid var(--gx-secondary-dark);
  }
  .lead-form-wrap::before { top: -1px; left: -1px; border-right: 0; border-bottom: 0; }
  .lead-form-wrap::after { bottom: -1px; right: -1px; border-left: 0; border-top: 0; }

  .lead-form-head {
    font-size: 10px; letter-spacing: 0.22em; text-transform: uppercase; font-weight: 700;
    color: var(--gx-secondary-light); margin-bottom: 12px;
  }
  .lead-form-title {
    font-size: 22px; font-weight: 900; text-transform: uppercase;
    letter-spacing: -0.01em; color: #fff; margin-bottom: 20px; line-height: 1.1;
  }

  .lf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
  .lf-row.full { grid-template-columns: 1fr; }
  .lf-field label {
    display: block;
    font-size: 10px; letter-spacing: 0.18em; text-transform: uppercase;
    font-weight: 700; color: var(--gx-secondary-light);
    margin-bottom: 6px;
  }
  .lf-field input,
  .lf-field select,
  .lf-field textarea {
    width: 100%;
    background: rgba(0,0,0,0.25);
    border: 1px solid rgba(255,255,255,0.18);
    color: #fff;
    padding: 13px 14px;
    font-family: var(--font-sans);
    font-size: 14px;
    line-height: 1.3;
    border-radius: 0;
    outline: none;
    transition: border-color 0.15s ease, background 0.15s ease;
  }
  .lf-field textarea { resize: vertical; min-height: 92px; font-family: var(--font-sans); }
  .lf-field input:focus,
  .lf-field select:focus,
  .lf-field textarea:focus {
    border-color: var(--gx-secondary-light);
    background: rgba(0,0,0,0.4);
  }
  .lf-field input::placeholder,
  .lf-field textarea::placeholder { color: rgba(255,255,255,0.35); }
  .lf-field select {
    appearance: none;
    background-image: linear-gradient(45deg, transparent 50%, rgba(219,199,162,0.7) 50%), linear-gradient(135deg, rgba(219,199,162,0.7) 50%, transparent 50%);
    background-position: calc(100% - 18px) 18px, calc(100% - 13px) 18px;
    background-size: 5px 5px, 5px 5px;
    background-repeat: no-repeat;
    padding-right: 32px;
  }
  .lf-field select option { background: var(--gx-primary-dark); color: #fff; }

  .lf-consent {
    display: flex; align-items: flex-start; gap: 10px;
    margin: 8px 0 18px;
    font-size: 11px; line-height: 1.5; color: rgba(255,255,255,0.7);
  }
  .lf-consent input { margin-top: 3px; accent-color: var(--gx-secondary-dark); }
  .lf-consent a { color: var(--gx-secondary-light); text-decoration: underline; }

  .lf-submit {
    display: inline-flex; align-items: center; justify-content: center; gap: 10px;
    width: 100%;
    padding: 18px 24px;
    border: 0; cursor: pointer;
    background: var(--gx-secondary-dark); color: #fff;
    font-size: 12px; font-weight: 800; letter-spacing: 0.2em; text-transform: uppercase;
    box-shadow: 4px 4px 0 0 rgba(0,0,0,0.5);
    transition: var(--transition-smooth);
  }
  .lf-submit:hover:not(:disabled) { transform: translate(-2px,-2px); box-shadow: 6px 6px 0 0 rgba(0,0,0,0.6); background: var(--gx-secondary-light); color: var(--gx-primary-dark); }
  .lf-submit:disabled { opacity: 0.55; cursor: progress; }

  .lf-feedback {
    margin-top: 16px;
    padding: 14px 16px;
    border-left: 3px solid var(--gx-secondary-dark);
    background: rgba(0,0,0,0.25);
    font-size: 13px; line-height: 1.5; color: #fff;
    display: none;
  }
  .lf-feedback.is-error { border-left-color: #f87171; color: #fca5a5; }
  .lf-feedback.is-success { border-left-color: #5fd07f; color: #c5f0d2; }
  .lf-feedback.is-visible { display: block; }
  .lf-honey { position: absolute; left: -10000px; top: -10000px; width: 1px; height: 1px; opacity: 0; pointer-events: none; }

  /* Appendix */
  .appendix {
    padding: 64px 88px 96px;
    background: var(--gx-primary-dark);
    color: rgba(255,255,255,0.85);
  }
  .appendix h3 { color: #fff; border-color: var(--gx-secondary-dark); }
  .gloss-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0 32px; margin-top: 20px; }
  .gloss-item { padding: 14px 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
  .gloss-item dt { font-size: 11px; letter-spacing: 0.2em; text-transform: uppercase; color: var(--gx-secondary-light); margin-bottom: 4px; font-weight: 700; }
  .gloss-item dd { font-size: 13px; line-height: 1.5; color: rgba(255,255,255,0.7); }

  .ref-list { list-style: none; display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px 24px; margin-top: 18px; }
  .ref-list li {
    font-size: 12px; line-height: 1.5; padding: 10px 14px;
    background: rgba(255,255,255,0.04);
    border-left: 2px solid var(--gx-secondary-dark);
    color: rgba(255,255,255,0.78);
  }
  .ref-list li strong { color: #fff; display: block; font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 2px; }

  /* Inline alert */
  .alert {
    background: var(--gx-secondary-light);
    border-left: 4px solid var(--gx-secondary-dark);
    padding: 18px 22px;
    margin: 22px 0;
    color: var(--gx-primary-dark);
    box-shadow: 4px 4px 0 0 rgba(135,112,74,0.25);
  }
  .alert .alert-eyebrow { font-size: 10px; letter-spacing: 0.22em; text-transform: uppercase; color: var(--gx-secondary-dark); font-weight: 800; margin-bottom: 4px; }
  .alert p { font-size: 14px; line-height: 1.5; margin: 0; max-width: none; }

  /* Footer */
  footer.foot {
    background: #000;
    color: rgba(255,255,255,0.5);
    padding: 26px 88px;
    font-size: 10px; letter-spacing: 0.18em; text-transform: uppercase;
    display: flex; justify-content: space-between; align-items: center;
    border-top: 1px solid rgba(255,255,255,0.1);
    flex-wrap: wrap;
    gap: 12px;
  }
  footer.foot img { height: 26px; opacity: 0.95; }
  .foot-brand { display:flex; align-items:center; gap: 10px; }
  .foot-wordmark { font-family: var(--font-sans); font-weight: 900; font-size: 11px; color: rgba(255,255,255,0.85); letter-spacing: 0.04em; text-transform: uppercase; }

  /* Tools section */
  .tools-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin: 18px 0; }
  .tool-card {
    background: #fff; border: 1px solid var(--gx-border); padding: 24px 22px;
    position: relative; box-shadow: 4px 4px 0 0 rgba(12,49,99,0.12);
    transition: var(--transition-smooth);
  }
  .tool-card:hover { transform: translate(-2px,-2px); box-shadow: 8px 8px 0 0 rgba(12,49,99,0.22); }
  .tool-card .ti {
    width: 44px; height: 44px; background: var(--gx-primary); color: #fff;
    display: flex; align-items: center; justify-content: center; margin-bottom: 14px;
  }
  .tool-card .ti i[data-lucide] { width: 22px; height: 22px; }
  .tool-card h5 { font-size: 16px; font-weight: 800; text-transform: uppercase; color: var(--gx-primary-dark); margin-bottom: 6px; letter-spacing: -0.01em; }
  .tool-card .tdesc { font-size: 12.5px; line-height: 1.5; color: var(--gx-fg-muted); margin-bottom: 14px; }
  .tool-card .turl {
    display: block; font-family: var(--font-mono); font-size: 11px;
    color: var(--gx-primary); border-top: 1px dashed var(--gx-border); padding-top: 10px;
    word-break: break-all;
  }

  .indicators {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0;
    background: var(--gx-primary-dark);
    color: #fff;
    margin: 22px 0;
    box-shadow: 6px 6px 0 0 rgba(12,49,99,0.18);
  }
  .indicator { padding: 18px 18px; border-right: 1px solid rgba(255,255,255,0.08); }
  .indicator:last-child { border-right: 0; }
  .indicator .il { font-size: 9px; letter-spacing: 0.2em; text-transform: uppercase; color: var(--gx-secondary-light); opacity: 0.7; margin-bottom: 6px; }
  .indicator .iv { font-family: var(--font-mono); font-weight: 800; font-size: 22px; letter-spacing: -0.02em; }

  /* Mobile top bar + drawer (hidden on desktop) ----------------------------- */
  .mobile-bar { display: none; }
  .toc-backdrop { display: none; }

  /* Responsive */
  @media (max-width: 1100px) {
    /* Mobile top bar */
    .mobile-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      position: sticky;
      top: 0;
      z-index: 60;
      padding: 12px 18px;
      background: linear-gradient(180deg, var(--gx-primary) 0%, var(--gx-primary-dark) 100%);
      color: #fff;
      border-bottom: 1px solid rgba(255,255,255,0.12);
      box-shadow: 0 2px 0 0 rgba(0,0,0,0.18);
    }
    .mobile-bar-brand { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .mobile-bar-brand img { height: 28px; width: auto; }
    .mobile-bar-wordmark { font-family: var(--font-sans); font-weight: 900; font-size: 12px; letter-spacing: 0.04em; color: #fff; line-height: 1; text-transform: uppercase; }
    .mobile-bar-wordmark .gold { display: block; font-weight: 500; font-size: 9px; letter-spacing: 0.18em; color: var(--gx-secondary-light); margin-top: 3px; }
    .mobile-bar-toggle {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 9px 12px;
      background: var(--gx-secondary-dark);
      color: #fff;
      border: 0; cursor: pointer;
      font-family: var(--font-sans);
      font-size: 10px; font-weight: 800; letter-spacing: 0.18em; text-transform: uppercase;
      box-shadow: 3px 3px 0 0 rgba(0,0,0,0.4);
      transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .mobile-bar-toggle:active { transform: translate(1px,1px); box-shadow: 2px 2px 0 0 rgba(0,0,0,0.4); }
    .mobile-bar-toggle .bars { width: 16px; height: 12px; position: relative; display: inline-block; }
    .mobile-bar-toggle .bars span {
      position: absolute; left: 0; right: 0; height: 2px; background: #fff; transition: transform 0.18s ease, opacity 0.18s ease, top 0.18s ease;
    }
    .mobile-bar-toggle .bars span:nth-child(1) { top: 0; }
    .mobile-bar-toggle .bars span:nth-child(2) { top: 5px; }
    .mobile-bar-toggle .bars span:nth-child(3) { top: 10px; }
    body.gx-toc-open .mobile-bar-toggle .bars span:nth-child(1) { top: 5px; transform: rotate(45deg); }
    body.gx-toc-open .mobile-bar-toggle .bars span:nth-child(2) { opacity: 0; }
    body.gx-toc-open .mobile-bar-toggle .bars span:nth-child(3) { top: 5px; transform: rotate(-45deg); }

    /* Layout */
    .shell { grid-template-columns: 1fr; }

    /* Off-canvas TOC drawer */
    .toc {
      position: fixed;
      top: 0; left: 0; bottom: 0;
      width: min(86vw, 320px);
      height: 100vh; height: 100dvh;
      padding: 20px 22px 28px;
      transform: translateX(-100%);
      transition: transform 0.28s ease;
      z-index: 70;
      box-shadow: 8px 0 24px rgba(0,0,0,0.35);
      overflow-y: auto;
    }
    body.gx-toc-open .toc { transform: translateX(0); }
    .toc-backdrop {
      display: block;
      position: fixed; inset: 0;
      background: rgba(0,0,0,0.55);
      z-index: 65;
      opacity: 0; pointer-events: none;
      transition: opacity 0.22s ease;
    }
    body.gx-toc-open .toc-backdrop { opacity: 1; pointer-events: auto; }
    body.gx-toc-open { overflow: hidden; }

    /* Spacing */
    .chapter, .appendix, .cta-block { padding: 48px 24px; }
    .cover { padding: 36px 24px; min-height: auto; }
    .cover-watermark { font-size: 200px; top: 30px; right: -20px; }
    .cover-data { grid-template-columns: repeat(2, 1fr); }
    .cover-bot { flex-direction: column; align-items: flex-start; gap: 18px; }
    .cover-stamp { align-self: stretch; }
    .ch-head { grid-template-columns: 1fr; gap: 8px; }
    .ch-num { font-size: 56px; }
    .ch-titlebox h2 { font-size: 28px; }
    .ch-titlebox .ch-deck { font-size: 14px; }
    .body p { font-size: 14.5px; }
    .body h3 { font-size: 19px; }
    .lede { font-size: 18px; }
    .pull { padding: 24px 22px; margin: 24px 0; }
    .pull-text { font-size: 18px; }
    .pull::before { font-size: 90px; top: 4px; right: 14px; }

    .two-col { grid-template-columns: 1fr; gap: 24px; }
    .card-grid, .card-grid.three { grid-template-columns: 1fr; }
    .sectors { grid-template-columns: 1fr; }
    .tools-grid { grid-template-columns: 1fr; }
    .kpis { grid-template-columns: repeat(2, 1fr); }
    .kpi { padding: 18px 16px 18px; }
    .kpi-value { font-size: 24px; }
    .indicators { grid-template-columns: repeat(2, 1fr); }
    .steps { grid-template-columns: 1fr; }
    .step { border-right: 0; border-bottom: 1px solid var(--gx-border); }
    .step:last-child { border-bottom: 0; }
    .gloss-grid, .ref-list { grid-template-columns: 1fr; }

    /* Tables: allow horizontal scroll on tight screens */
    .gx-table { display: block; overflow-x: auto; white-space: nowrap; }

    /* Timeline */
    .tl-month { grid-template-columns: 1fr; gap: 10px; padding: 18px 0; }

    /* CTA */
    .cta-block h2 { font-size: 36px; }
    .cta-block .lede2 { font-size: 15px; }
    .cta-grid { grid-template-columns: 1fr; gap: 12px; }
    .cta-buttons { flex-direction: column; align-items: stretch; }
    .cta-buttons .btn-primary, .cta-buttons .btn-ghost { justify-content: center; }

    /* Lead form */
    .lead-block { padding: 48px 24px; }
    .lead-grid { grid-template-columns: 1fr; gap: 28px; }
    .lead-block h3 { font-size: 28px; }
    .lf-row { grid-template-columns: 1fr; }
    .lead-form-wrap { padding: 24px 22px; }

    /* Appendix */
    .appendix { padding: 56px 24px 72px; }
    .appendix > div:first-child > div h2 { font-size: 28px !important; }

    /* Chart */
    .chart-wrap { padding: 22px 18px; }
    .chart-svg { height: 220px; }
    .chart-head { flex-direction: column; align-items: flex-start; gap: 8px; }
    .chart-head .now { font-size: 24px; }

    footer.foot {
      padding: 22px 24px;
      flex-direction: column;
      align-items: flex-start;
      text-align: left;
      font-size: 9px;
      gap: 8px;
    }
  }

  @media (max-width: 520px) {
    .cover { padding: 28px 20px; }
    .cover-title { font-size: clamp(40px, 12vw, 64px); }
    .cover-data { grid-template-columns: 1fr; }
    .cover-data > div { border-right: 0; border-bottom: 1px solid rgba(255,255,255,0.12); }
    .cover-data > div:last-child { border-bottom: 0; }
    .kpis { grid-template-columns: 1fr; }
    .indicators { grid-template-columns: 1fr; }
    .indicator { border-right: 0; border-bottom: 1px solid rgba(255,255,255,0.08); }
    .indicator:last-child { border-bottom: 0; }
    .chapter, .appendix, .cta-block { padding: 40px 20px; }
    .lead-block { padding: 40px 20px; }
    .ch-num { font-size: 44px; }
    .ch-titlebox h2 { font-size: 24px; }
    .lead-block h3 { font-size: 24px; }
    .cta-block h2 { font-size: 30px; }
  }

  /* Print — PDF-ready (A4) */
  @page { size: A4; margin: 0; }
  @media print {
    html, body { background: #fff; }
    .toc { display: none !important; }
    .shell { display: block !important; box-shadow: none !important; max-width: none !important; margin: 0; }
    main { display: block; }
    .cover { min-height: 297mm; height: 297mm; padding: 24mm 22mm; page-break-after: always; break-after: page; }
    .cover-watermark { font-size: 240px; }
    .chapter, .appendix, .cta-block, .lead-block { page-break-before: always; break-before: page; padding: 22mm 22mm 22mm; min-height: 297mm; box-sizing: border-box; }
    .chapter:nth-child(even) { background: #fff; }
    .ch-head, .pull, .kpis, .gx-table, .indicators, .chart-wrap, .strat-card, .sector, .tool-card, .tl-month, .alert, .steps { page-break-inside: avoid; break-inside: avoid; }
    .body h3, .body h4 { page-break-after: avoid; break-after: avoid; }
    .body p, .body li { orphans: 3; widows: 3; }
    .ch-num { font-size: 72px; }
    .ch-titlebox h2 { font-size: 34px; }
    .cover-title { font-size: 84px !important; }
    .cta-block, .appendix, .lead-block { color-adjust: exact; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    footer.foot { padding: 14mm 22mm; }
    a { text-decoration: none; color: inherit; }
    .lead-form-wrap, .lf-feedback { display: none !important; }
  }

  html { scroll-behavior: smooth; }
</style>

<?= $generalSettings->custom_header_codes; ?>
</head>
<body>

<!-- ============== MOBILE TOP BAR (visible only ≤1100px) ============== -->
<header class="mobile-bar" role="banner">
  <a class="mobile-bar-brand" href="<?= esc($homeUrl); ?>">
    <img src="<?= base_url('assets/logo-icon.png'); ?>" alt="GX Capital">
    <div class="mobile-bar-wordmark">GX Capital<span class="gold">Importação Blindada</span></div>
  </a>
  <button type="button" class="mobile-bar-toggle" aria-controls="gx-playbook-toc" aria-expanded="false" data-gx-toc-toggle>
    <span class="bars" aria-hidden="true"><span></span><span></span><span></span></span>
    <span class="label" data-gx-toc-label>Sumário</span>
  </button>
</header>
<div class="toc-backdrop" data-gx-toc-backdrop aria-hidden="true"></div>

<div class="shell">

  <!-- ============== TOC SIDEBAR ============== -->
  <aside class="toc" id="gx-playbook-toc" role="doc-toc" aria-label="Sumário do playbook Importação Blindada">
    <div class="toc-brand">
      <img src="<?= base_url('assets/logo-icon.png'); ?>" alt="GX Capital · Mesa de Câmbio" width="36" height="36" decoding="async" />
      <div class="toc-wordmark">GX Capital<span class="gold">Wealth &amp; Trade</span></div>
    </div>

    <div class="toc-eyebrow">Ebook 01 · 2026</div>
    <div style="font-size: 13px; font-weight: 700; line-height: 1.3; margin-bottom: 6px; letter-spacing: -0.01em;">Importação Blindada</div>
    <div style="font-size: 11px; color: rgba(255,255,255,0.5); line-height: 1.5;">Guia estratégico para importadores brasileiros enfrentarem a volatilidade do dólar eleitoral.</div>

    <div class="toc-eyebrow">Sumário</div>
    <ul class="toc-list">
      <li><a href="#ch-1" data-gx-toc="01"><span class="toc-num">01</span><span>Cenário de Risco 2026</span></a></li>
      <li><a href="#ch-2" data-gx-toc="02"><span class="toc-num">02</span><span>Balança Comercial em Números</span></a></li>
      <li><a href="#ch-3" data-gx-toc="03"><span class="toc-num">03</span><span>O Dólar Eleitoral</span></a></li>
      <li><a href="#ch-4" data-gx-toc="04"><span class="toc-num">04</span><span>Análise Setorial</span></a></li>
      <li><a href="#ch-5" data-gx-toc="05"><span class="toc-num">05</span><span>Simulações de Impacto</span></a></li>
      <li><a href="#ch-6" data-gx-toc="06"><span class="toc-num">06</span><span>Hedge Cambial Defensivo</span></a></li>
      <li><a href="#ch-7" data-gx-toc="07"><span class="toc-num">07</span><span>Antecipação de Estoque</span></a></li>
      <li><a href="#ch-8" data-gx-toc="08"><span class="toc-num">08</span><span>Ex-Tarifário &amp; Estaduais</span></a></li>
      <li><a href="#ch-9" data-gx-toc="09"><span class="toc-num">09</span><span>Ferramentas GX Capital</span></a></li>
      <li><a href="#ch-10" data-gx-toc="10"><span class="toc-num">10</span><span>Checklist de Ação</span></a></li>
      <li><a href="#ch-11" data-gx-toc="11"><span class="toc-num">11</span><span>Próximos Passos</span></a></li>
      <li><a href="#lead" data-gx-toc="lead"><span class="toc-num">★</span><span>Falar com a Mesa</span></a></li>
      <li><a href="#appendix" data-gx-toc="A"><span class="toc-num">A</span><span>Apêndices</span></a></li>
    </ul>

    <div class="toc-foot">
      Mesa de Câmbio<br/><?= esc($contactPhone); ?><br/>
      <a href="#lead" class="toc-cta" data-gx-cta="toc-falar">
        Falar com especialista
        <svg width="10" height="10" viewBox="0 0 10 10"><path d="M0 5h8M5 1l4 4-4 4" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>
      </a>
    </div>
  </aside>

  <main>

    <article itemscope itemtype="https://schema.org/Article" aria-labelledby="gx-playbook-h1">
      <meta itemprop="datePublished" content="<?= escMeta($playbookConfig['datePublished'] ?? ''); ?>">
      <meta itemprop="dateModified"  content="<?= escMeta($playbookConfig['dateModified'] ?? ''); ?>">
      <meta itemprop="inLanguage" content="pt-BR">
      <meta itemprop="articleSection" content="<?= escMeta($playbookConfig['section'] ?? ''); ?>">
      <meta itemprop="wordCount" content="<?= (int)($playbookConfig['wordCount'] ?? 0); ?>">

      <!-- Visible breadcrumb (also surfaces in SERPs via JSON-LD) -->
      <nav class="gx-breadcrumb" aria-label="Você está em">
        <ol>
          <li><a href="<?= esc($homeUrl); ?>">Início</a></li>
          <li><span aria-hidden="true">›</span><span>Playbooks</span></li>
          <li><span aria-hidden="true">›</span><span aria-current="page">Importação Blindada</span></li>
        </ol>
      </nav>

    <!-- ============== COVER ============== -->
    <section class="cover" data-screen-label="00 Capa">
      <div class="cover-watermark">USD</div>

      <div class="cover-top">
        <div class="cover-brand">
          <a href="<?= esc($homeUrl); ?>"><img src="<?= base_url('assets/logo-icon.png'); ?>" alt="GX Capital · Mesa de Câmbio" width="56" height="56" fetchpriority="high" /></a>
          <div class="cover-wordmark">GX Capital<span class="gold">Wealth &amp; Trade</span></div>
        </div>
        <div class="cover-meta">
          Ebook · 01 / 2026<br/>
          Confidencial · Mesa de Câmbio<br/>
          Edição revisada · <time datetime="<?= escMeta($playbookConfig['datePublished'] ?? '2026-05-08'); ?>" itemprop="datePublished">Maio 2026</time>
        </div>
      </div>

      <div class="cover-mid">
        <div class="cover-eyebrow">
          <span class="dot"></span>Guia estratégico para importadores · 2026
          <span class="cover-meta-pill" aria-label="Tempo de leitura">
            <svg width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="6" cy="6" r="5"/><path d="M6 3v3l2 1.5"/></svg>
            <span itemprop="timeRequired" content="PT<?= (int)($playbookConfig['readingTimeMin'] ?? 18); ?>M"><?= (int)($playbookConfig['readingTimeMin'] ?? 18); ?> min de leitura</span>
          </span>
        </div>
        <h1 class="cover-title" id="gx-playbook-h1" itemprop="headline">
          Importação<br/>
          <span class="accent">Blindada.</span>
        </h1>
        <p class="cover-sub">
          Como proteger o fluxo de caixa da sua empresa da volatilidade do <strong>"dólar eleitoral"</strong>
          em 2026 — três estratégias práticas, simuladores reais e um checklist mês a mês para
          fechar o ano com a margem intacta.
        </p>

        <div class="cover-data">
          <div>
            <div class="cd-label">USD/BRL · Maio 26</div>
            <div class="cd-value">R$ 4,91</div>
            <div class="cd-trend">−2,4% · 2 anos de mínima</div>
          </div>
          <div>
            <div class="cd-label">Pico Projetado · Q3</div>
            <div class="cd-value">R$ 5,55</div>
            <div class="cd-trend warn">+13,0% · estresse eleitoral</div>
          </div>
          <div>
            <div class="cd-label">Importações 2025</div>
            <div class="cd-value">US$ 280,4 bi</div>
            <div class="cd-trend">+6,7% · recorde</div>
          </div>
          <div>
            <div class="cd-label">Janela de Hedge</div>
            <div class="cd-value">90 dias</div>
            <div class="cd-trend warn">Mai · Jul</div>
          </div>
        </div>
      </div>

      <div class="cover-bot">
        <div class="cover-volatility">
          <svg class="vol-svg" width="320" height="80" viewBox="0 0 320 80">
            <defs>
              <linearGradient id="grad" x1="0" x2="0" y1="0" y2="1">
                <stop offset="0%" stop-color="#dbc7a2" stop-opacity="0.35"/>
                <stop offset="100%" stop-color="#dbc7a2" stop-opacity="0"/>
              </linearGradient>
            </defs>
            <path d="M0 60 L20 55 L40 50 L60 58 L80 45 L100 40 L120 35 L140 25 L160 15 L180 22 L200 30 L220 28 L240 38 L260 50 L280 55 L300 60 L320 58 L320 80 L0 80 Z" fill="url(#grad)"/>
            <path d="M0 60 L20 55 L40 50 L60 58 L80 45 L100 40 L120 35 L140 25 L160 15 L180 22 L200 30 L220 28 L240 38 L260 50 L280 55 L300 60 L320 58" stroke="#dbc7a2" stroke-width="1.5" fill="none"/>
            <line x1="140" y1="0" x2="140" y2="80" stroke="#87704a" stroke-width="1" stroke-dasharray="2 3"/>
            <line x1="180" y1="0" x2="180" y2="80" stroke="#87704a" stroke-width="1" stroke-dasharray="2 3"/>
            <text x="160" y="10" fill="#87704a" font-size="8" text-anchor="middle" font-family="JetBrains Mono" letter-spacing="0.1em">Q3 ESTRESSE</text>
          </svg>
        </div>
        <div class="cover-stamp">
          <div class="cover-stamp-label">Edição</div>
          <div class="cover-stamp-val">GX-EB01-2026/05</div>
        </div>
      </div>
    </section>

    <!-- ============== CH 1 — INTRO ============== -->
    <section class="chapter" id="ch-1" data-screen-label="01 Introdução">
      <div class="ch-head">
        <div class="ch-num"><span class="pre">Capítulo</span>01</div>
        <div class="ch-titlebox">
          <h2>Introdução: o cenário de risco 2026</h2>
          <p class="ch-deck">Por que este é o ano em que importadores brasileiros precisam parar de torcer e começar a estruturar proteção.</p>
        </div>
      </div>

      <div class="body">
        <p class="lede">
          Um dólar a <em>R$ 5,50</em> pode engolir toda a margem de lucro do seu negócio
          de uma vez só. E 2026 é exatamente o ano em que isso pode acontecer.
        </p>

        <div class="two-col">
          <div>
            <h3>O que muda em 2026</h3>
            <p>2026 é ano eleitoral — Presidencial e Estadual. Historicamente, períodos eleitorais geram <strong>altíssima volatilidade cambial</strong>: o mercado precifica risco fiscal, especula sobre como o próximo governo lidará com a dívida pública, e os juros nos EUA continuam atraindo capital de mercados emergentes.</p>
            <p>O resultado é um padrão repetido eleição após eleição: <strong>uma janela curta de calmaria entre maio e julho, seguida por um pico de estresse entre agosto e outubro</strong>. Para o importador, isso significa que o custo do dólar contratado em agosto pode ser 13% mais alto do que o contratado hoje.</p>
          </div>
          <div>
            <h3>O que este ebook entrega</h3>
            <ul style="list-style: none; display: grid; gap: 10px;">
              <li style="padding: 10px 14px; background: var(--gx-bg-muted); border-left: 3px solid var(--gx-primary); font-size: 13px; line-height: 1.5;"><strong>Análise profunda</strong> dos dados de 2025 e Q1 2026 com fontes oficiais.</li>
              <li style="padding: 10px 14px; background: var(--gx-bg-muted); border-left: 3px solid var(--gx-primary); font-size: 13px; line-height: 1.5;"><strong>Projeção de cenários</strong> para o USD/BRL trimestre a trimestre.</li>
              <li style="padding: 10px 14px; background: var(--gx-bg-muted); border-left: 3px solid var(--gx-primary); font-size: 13px; line-height: 1.5;"><strong>Três estratégias</strong> de proteção testáveis hoje.</li>
              <li style="padding: 10px 14px; background: var(--gx-bg-muted); border-left: 3px solid var(--gx-primary); font-size: 13px; line-height: 1.5;"><strong>Ferramentas e simuladores</strong> da GX Capital.</li>
              <li style="padding: 10px 14px; background: var(--gx-bg-muted); border-left: 3px solid var(--gx-primary); font-size: 13px; line-height: 1.5;"><strong>Checklist mês a mês</strong> para implementar até dezembro.</li>
            </ul>
          </div>
        </div>

        <div class="pull">
          <div class="pull-eyebrow">Tese central</div>
          <div class="pull-text">
            Importador brasileiro em 2026 não tem o luxo de "esperar para ver". Quem não travar câmbio até julho vai pagar a diferença em margem.
          </div>
        </div>
      </div>
    </section>

    <!-- ============== CH 2 — BALANÇA ============== -->
    <section class="chapter" id="ch-2" data-screen-label="02 Balança Comercial">
      <div class="ch-head">
        <div class="ch-num"><span class="pre">Capítulo</span>02</div>
        <div class="ch-titlebox">
          <h2>A balança comercial brasileira em números</h2>
          <p class="ch-deck">2025 fechou em recorde histórico. Q1 2026 manteve o ritmo. Mas o crescimento esconde uma fragilidade: a competição por dólares.</p>
        </div>
      </div>

      <div class="body">
        <h3>2025 — Ano recorde</h3>
        <div class="kpis">
          <div class="kpi">
            <div class="kpi-label">Exportações</div>
            <div class="kpi-value">348,7<span class="unit">US$ bi</span></div>
            <div class="kpi-delta up">▲ +3,5% vs 2024 · recorde histórico</div>
          </div>
          <div class="kpi">
            <div class="kpi-label">Importações</div>
            <div class="kpi-value">280,4<span class="unit">US$ bi</span></div>
            <div class="kpi-delta up">▲ +6,7% vs 2024 · recorde</div>
          </div>
          <div class="kpi">
            <div class="kpi-label">Superávit</div>
            <div class="kpi-value">68,3<span class="unit">US$ bi</span></div>
            <div class="kpi-delta neutral">3º maior da série histórica</div>
          </div>
          <div class="kpi">
            <div class="kpi-label">Corrente de Comércio</div>
            <div class="kpi-value">629,1<span class="unit">US$ bi</span></div>
            <div class="kpi-delta up">▲ maior patamar registrado</div>
          </div>
        </div>

        <h3>Q1 2026 — Continuação do crescimento</h3>
        <div class="kpis">
          <div class="kpi">
            <div class="kpi-label">Exportações Q1</div>
            <div class="kpi-value">82,3<span class="unit">US$ bi</span></div>
            <div class="kpi-delta up">▲ +7,1% vs Q1 2025</div>
          </div>
          <div class="kpi">
            <div class="kpi-label">Importações Q1</div>
            <div class="kpi-value">68,2<span class="unit">US$ bi</span></div>
            <div class="kpi-delta up">▲ +1,3% vs Q1 2025</div>
          </div>
          <div class="kpi">
            <div class="kpi-label">Superávit Q1</div>
            <div class="kpi-value">14,2<span class="unit">US$ bi</span></div>
            <div class="kpi-delta up">▲ acima de Q1 2025</div>
          </div>
          <div class="kpi">
            <div class="kpi-label">Corrente Q1</div>
            <div class="kpi-value">150,5<span class="unit">US$ bi</span></div>
            <div class="kpi-delta up">▲ +4,4% vs Q1 2025</div>
          </div>
        </div>

        <h3>O que isso significa para você</h3>
        <div class="card-grid">
          <div class="strat-card">
            <span class="strat-tag">Sinal 01</span>
            <h4>Mercado em expansão</h4>
            <p class="strat-sub">Comércio global em crescimento abre janelas de importação — mas concorrência por containers e crédito sobe junto.</p>
          </div>
          <div class="strat-card">
            <span class="strat-tag">Sinal 02</span>
            <h4>Bens de capital em alta</h4>
            <p class="strat-sub">+23,7% em 2025. Quem importa máquinas, automação e tecnologia industrial é o mais exposto ao câmbio do segundo semestre.</p>
          </div>
          <div class="strat-card">
            <span class="strat-tag">Sinal 03</span>
            <h4>Competição por crédito</h4>
            <p class="strat-sub">Mais empresas importando significa mais demanda por linhas de FINIMP. Spread sobe em julho.</p>
          </div>
          <div class="strat-card">
            <span class="strat-tag">Sinal 04</span>
            <h4>Proteção é crítica</h4>
            <p class="strat-sub">Volume crescente + dólar volátil = exposição financeira inédita. Hedge deixou de ser opcional.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ============== CH 3 — DÓLAR ELEITORAL ============== -->
    <section class="chapter" id="ch-3" data-screen-label="03 Dólar Eleitoral">
      <div class="ch-head">
        <div class="ch-num"><span class="pre">Capítulo</span>03</div>
        <div class="ch-titlebox">
          <h2>O dólar eleitoral: entendendo a volatilidade</h2>
          <p class="ch-deck">Histórico, comportamento e projeção trimestral do USD/BRL — onde fica a janela de oportunidade e onde fica o pico de risco.</p>
        </div>
      </div>

      <div class="body">
        <p>Eleições presidenciais brasileiras geram um padrão recorrente de volatilidade. O período crítico vai de <strong>julho a outubro</strong> — o mercado precifica incerteza sobre responsabilidade fiscal, juros americanos elevados puxam capital para fora, e o real é o ativo de ajuste. Em 2026 o cenário ainda traz uma camada extra: queda recente do dólar criou complacência, e a entrada do estresse eleitoral pode pegar importadores desavisados.</p>

        <div class="chart-wrap">
          <div class="chart-head">
            <h4>Projeção USD/BRL · 2026</h4>
            <div class="now"><span style="font-size: 11px; color: var(--gx-secondary-light); letter-spacing: 0.18em; text-transform: uppercase; display: block; margin-bottom: 4px;">Spot · Mai 2026</span>R$ 4,91</div>
          </div>
          <svg class="chart-svg" viewBox="0 0 800 280" preserveAspectRatio="none">
            <defs>
              <linearGradient id="bandg" x1="0" x2="0" y1="0" y2="1">
                <stop offset="0%" stop-color="#dbc7a2" stop-opacity="0.18"/>
                <stop offset="100%" stop-color="#dbc7a2" stop-opacity="0.02"/>
              </linearGradient>
              <linearGradient id="stressg" x1="0" x2="0" y1="0" y2="1">
                <stop offset="0%" stop-color="#dc2626" stop-opacity="0.32"/>
                <stop offset="100%" stop-color="#dc2626" stop-opacity="0"/>
              </linearGradient>
            </defs>

            <g font-family="JetBrains Mono" font-size="10" fill="rgba(255,255,255,0.5)">
              <text x="0" y="40">5,60</text>
              <text x="0" y="100">5,40</text>
              <text x="0" y="160">5,20</text>
              <text x="0" y="220">5,00</text>
              <text x="0" y="270">4,80</text>
            </g>

            <g stroke="rgba(255,255,255,0.06)" stroke-width="1">
              <line x1="40" y1="40" x2="800" y2="40"/>
              <line x1="40" y1="100" x2="800" y2="100"/>
              <line x1="40" y1="160" x2="800" y2="160"/>
              <line x1="40" y1="220" x2="800" y2="220"/>
              <line x1="40" y1="270" x2="800" y2="270"/>
            </g>

            <rect x="430" y="0" width="200" height="280" fill="url(#stressg)"/>
            <text x="530" y="20" fill="#f87171" font-size="10" text-anchor="middle" font-family="Inter" font-weight="700" letter-spacing="2">PICO ELEITORAL</text>

            <path d="M40 230 L160 235 L280 215 L380 175 L500 50 L600 70 L720 100 L800 110 L800 270 L40 270 Z" fill="url(#bandg)"/>
            <path d="M40 230 L160 235 L280 215 L380 175 L500 50 L600 70 L720 100 L800 110" stroke="#dbc7a2" stroke-width="1" stroke-dasharray="4 4" fill="none"/>
            <path d="M40 250 L160 250 L280 240 L380 215 L500 110 L600 130 L720 160 L800 165" stroke="#dbc7a2" stroke-width="1" stroke-dasharray="4 4" fill="none"/>
            <path d="M40 240 L160 245 L280 230 L380 200 L500 80 L600 100 L720 130 L800 140" stroke="#c9a96a" stroke-width="2.5" fill="none"/>

            <circle cx="40" cy="240" r="6" fill="#c9a96a"/>
            <circle cx="40" cy="240" r="11" fill="none" stroke="#c9a96a" stroke-opacity="0.5"/>
            <text x="48" y="234" fill="#c9a96a" font-size="11" font-family="Inter" font-weight="700">SPOT · 4,91</text>

            <circle cx="500" cy="80" r="5" fill="#f87171"/>
            <text x="510" y="74" fill="#f87171" font-size="11" font-family="Inter" font-weight="700">PICO · 5,55</text>

            <g font-family="Inter" font-size="10" fill="rgba(255,255,255,0.6)" letter-spacing="2" font-weight="700">
              <text x="40" y="295">MAI</text>
              <text x="160" y="295">JUN</text>
              <text x="280" y="295">JUL</text>
              <text x="400" y="295">AGO</text>
              <text x="520" y="295">SET</text>
              <text x="640" y="295">OUT</text>
              <text x="760" y="295">DEZ</text>
            </g>
          </svg>
          <div class="chart-legend">
            <span><span class="sw" style="background:#c9a96a;"></span>Trajetória central</span>
            <span><span class="sw" style="background:#dbc7a2;border-top:1px dashed #dbc7a2;"></span>Banda de projeção</span>
            <span><span class="sw" style="background:rgba(220,38,38,0.4);"></span>Janela de estresse Q3</span>
          </div>
        </div>

        <h3>Projeção trimestre a trimestre</h3>
        <table class="gx-table">
          <thead>
            <tr>
              <th>Período</th>
              <th>Faixa USD/BRL</th>
              <th>Cenário</th>
              <th>Recomendação</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Q2 · Mai · Jun</td>
              <td>R$ 4,95 — R$ 5,15</td>
              <td>Volatilidade controlada</td>
              <td><span class="pos">JANELA DE TRAVA</span></td>
            </tr>
            <tr class="hot">
              <td>Q3 · Ago · Out</td>
              <td>R$ 5,45 — R$ 5,55</td>
              <td>Pico de estresse eleitoral</td>
              <td><span class="neg">EVITAR EXPOSIÇÃO NOVA</span></td>
            </tr>
            <tr>
              <td>Q4 · Pós-eleição</td>
              <td>R$ 5,25 — R$ 5,30</td>
              <td>Acomodação (se equipe fiscal responsável)</td>
              <td>Reequilibrar</td>
            </tr>
            <tr>
              <td>Fechamento 2026</td>
              <td colspan="2">R$ 5,37 · projeção final</td>
              <td>Renegociar 2027</td>
            </tr>
          </tbody>
        </table>

        <div class="alert">
          <div class="alert-eyebrow">Implicação para importadores</div>
          <p><strong>Janela de oportunidade:</strong> Maio–Junho para travar câmbio em níveis defensivos. <strong>Risco máximo:</strong> Agosto–Outubro. <strong>Necessidade de proteção:</strong> urgente — a janela atual fecha em 60 a 90 dias.</p>
        </div>
      </div>
    </section>

    <!-- ============== CH 4 — SETORIAL ============== -->
    <section class="chapter" id="ch-4" data-screen-label="04 Análise Setorial">
      <div class="ch-head">
        <div class="ch-num"><span class="pre">Capítulo</span>04</div>
        <div class="ch-titlebox">
          <h2>Análise setorial: quem importa mais em 2026</h2>
          <p class="ch-deck">Quatro setores em alta, dois em retração. Cada um exige uma postura diferente diante do câmbio.</p>
        </div>
      </div>

      <div class="body">
        <h3>Setores com maior crescimento</h3>
        <div class="sectors">
          <div class="sector success">
            <div class="sector-head">
              <h4>Bens de Capital</h4>
              <span class="delta up">+23,7%</span>
            </div>
            <p class="reason">Indústria pesada e agronegócio importando antes do "furacão" cambial. Lead times longos amplificam exposição.</p>
            <ul>
              <li>Máquinas e equipamentos</li>
              <li>Tecnologia industrial</li>
              <li>Automação</li>
            </ul>
            <div class="impact"><strong>Impacto</strong>Urgência máxima de proteger câmbio.</div>
          </div>
          <div class="sector success">
            <div class="sector-head">
              <h4>Insumos Verdes &amp; Semicondutores</h4>
              <span class="delta up">+ alto</span>
            </div>
            <p class="reason">Transição energética global. Dependência asiática forte; qualquer choque cambial é repassado direto ao custo unitário.</p>
            <ul>
              <li>Painéis solares</li>
              <li>Baterias · mobilidade elétrica</li>
              <li>Energia renovável</li>
            </ul>
            <div class="impact"><strong>Impacto</strong>Câmbio crítico, sem similar nacional.</div>
          </div>
          <div class="sector">
            <div class="sector-head">
              <h4>Bens Intermediários</h4>
              <span class="delta up">+5,9%</span>
            </div>
            <p class="reason">Suporte à indústria de transformação. Pressão de custo se propaga ao consumidor final.</p>
            <ul>
              <li>Matérias-primas processadas</li>
              <li>Componentes de produção</li>
            </ul>
            <div class="impact"><strong>Impacto</strong>Repasse parcial possível.</div>
          </div>
          <div class="sector">
            <div class="sector-head">
              <h4>Bens de Consumo</h4>
              <span class="delta up">+5,7%</span>
            </div>
            <p class="reason">Demanda do consumidor brasileiro segue resiliente. Margem se sustenta apenas se o câmbio cooperar.</p>
            <ul>
              <li>Eletrodomésticos</li>
              <li>Eletrônicos</li>
              <li>Produtos finais</li>
            </ul>
            <div class="impact"><strong>Impacto</strong>Preço sensível a repasses.</div>
          </div>
        </div>

        <h3>Setores em retração ou pressão</h3>
        <div class="sectors">
          <div class="sector danger">
            <div class="sector-head">
              <h4>Consumo Não-Durável</h4>
              <span class="delta down">queda forte</span>
            </div>
            <p class="reason">Conformidade tributária (Remessa Conforme) e aumento do imposto de importação reduziram volume estruturalmente.</p>
            <ul>
              <li>Vestuário</li>
              <li>Alimentos processados</li>
              <li>Higiene e cuidados</li>
            </ul>
            <div class="impact"><strong>Impacto</strong>Margem mais apertada; sem espaço para erro cambial.</div>
          </div>
          <div class="sector warn">
            <div class="sector-head">
              <h4>Combustíveis</h4>
              <span class="delta down">−8,6%</span>
            </div>
            <p class="reason">Produção doméstica em alta reduziu necessidade de importação. Volatilidade impacta menos volume mas ainda preço.</p>
            <ul>
              <li>Derivados</li>
              <li>GLP</li>
            </ul>
            <div class="impact"><strong>Impacto</strong>Exposição decrescente.</div>
          </div>
        </div>

        <div class="pull">
          <div class="pull-eyebrow">Implicação estratégica</div>
          <div class="pull-text">
            Bens de capital protegem agora. Consumo negocia repasse. Insumos verdes aproveitam a demanda. Não existe estratégia única — existe a sua estratégia.
          </div>
        </div>
      </div>
    </section>

    <!-- ============== CH 5 — SIMULAÇÕES ============== -->
    <section class="chapter" id="ch-5" data-screen-label="05 Simulações">
      <div class="ch-head">
        <div class="ch-num"><span class="pre">Capítulo</span>05</div>
        <div class="ch-titlebox">
          <h2>Impacto financeiro: simulações de cenários</h2>
          <p class="ch-deck">Dois importadores reais, mesmas variáveis. A diferença entre travar e não travar câmbio em maio.</p>
        </div>
      </div>

      <div class="body">
        <h3>Cenário 1 · Importador de bens de capital</h3>
        <div class="indicators">
          <div class="indicator"><div class="il">Volume mensal</div><div class="iv">US$ 500 mil</div></div>
          <div class="indicator"><div class="il">Margem bruta</div><div class="iv">15%</div></div>
          <div class="indicator"><div class="il">Câmbio atual</div><div class="iv">R$ 5,00</div></div>
          <div class="indicator"><div class="il">Setor</div><div class="iv" style="font-size:14px; padding-top:6px;">Bens de Capital</div></div>
          <div class="indicator"><div class="il">Risco</div><div class="iv" style="color:#f87171; font-size:14px; padding-top:6px;">ALTO</div></div>
        </div>

        <table class="gx-table">
          <thead>
            <tr>
              <th>Cenário</th>
              <th>Taxa</th>
              <th>Custo em R$</th>
              <th>Margem Bruta</th>
              <th>Impacto</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Sem proteção · Q2</td>
              <td>R$ 5,00</td>
              <td>R$ 2.500.000</td>
              <td>R$ 375.000</td>
              <td>Base</td>
            </tr>
            <tr class="hot">
              <td>Sem proteção · Q3 pico</td>
              <td>R$ 5,50</td>
              <td>R$ 2.750.000</td>
              <td>R$ 125.000</td>
              <td><span class="neg">−66% margem</span></td>
            </tr>
            <tr>
              <td>Hedge a R$ 5,15</td>
              <td>R$ 5,15</td>
              <td>R$ 2.575.000</td>
              <td>R$ 300.000</td>
              <td><span class="neg">−20% vs base</span></td>
            </tr>
            <tr>
              <td>Hedge a R$ 5,10</td>
              <td>R$ 5,10</td>
              <td>R$ 2.550.000</td>
              <td>R$ 325.000</td>
              <td><span class="pos">−13% vs base</span></td>
            </tr>
          </tbody>
        </table>
        <p style="font-size:13px; color:var(--gx-fg-muted); margin-top:10px;"><strong>Conclusão:</strong> Hedge a R$ 5,10–5,15 contratado em maio protege ~80% da margem mesmo no cenário de pico de Q3.</p>

        <h3>Cenário 2 · Importador de consumo não-durável</h3>
        <div class="indicators">
          <div class="indicator"><div class="il">Volume mensal</div><div class="iv">US$ 300 mil</div></div>
          <div class="indicator"><div class="il">Margem bruta</div><div class="iv">8%</div></div>
          <div class="indicator"><div class="il">Câmbio atual</div><div class="iv">R$ 5,00</div></div>
          <div class="indicator"><div class="il">Setor</div><div class="iv" style="font-size:14px; padding-top:6px;">Consumo</div></div>
          <div class="indicator"><div class="il">Risco</div><div class="iv" style="color:#f87171; font-size:14px; padding-top:6px;">CRÍTICO</div></div>
        </div>

        <table class="gx-table">
          <thead>
            <tr>
              <th>Cenário</th>
              <th>Taxa</th>
              <th>Custo em R$</th>
              <th>Margem Bruta</th>
              <th>Impacto</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Sem proteção · Q2</td>
              <td>R$ 5,00</td>
              <td>R$ 1.500.000</td>
              <td>R$ 120.000</td>
              <td>Base</td>
            </tr>
            <tr class="hot">
              <td>Sem proteção · Q3 pico</td>
              <td>R$ 5,50</td>
              <td>R$ 1.650.000</td>
              <td><span class="neg">NEGATIVO</span></td>
              <td><span class="neg">PREJUÍZO</span></td>
            </tr>
            <tr>
              <td>Hedge a R$ 5,15</td>
              <td>R$ 5,15</td>
              <td>R$ 1.545.000</td>
              <td>R$ 75.000</td>
              <td><span class="neg">−38% vs base</span></td>
            </tr>
            <tr>
              <td>Hedge a R$ 5,10</td>
              <td>R$ 5,10</td>
              <td>R$ 1.530.000</td>
              <td>R$ 90.000</td>
              <td><span class="pos">−25% vs base</span></td>
            </tr>
          </tbody>
        </table>
        <p style="font-size:13px; color:var(--gx-fg-muted); margin-top:10px;"><strong>Conclusão:</strong> Sem hedge, a operação vira prejuízo em Q3. Para este perfil, hedge não é estratégia — é sobrevivência.</p>
      </div>
    </section>

    <!-- ============== CH 6 — HEDGE ============== -->
    <section class="chapter" id="ch-6" data-screen-label="06 Hedge Cambial">
      <div class="ch-head">
        <div class="ch-num"><span class="pre">Capítulo</span>06</div>
        <div class="ch-titlebox">
          <h2>Estratégia 1 · Hedge cambial defensivo</h2>
          <p class="ch-deck">Quatro instrumentos disponíveis hoje. Quando usar cada um e o que cada um custa.</p>
        </div>
      </div>

      <div class="body">
        <p class="lede">
          O problema é simples: <em>volatilidade cambial elimina margem</em>. A solução também é —
          existem instrumentos consolidados que travam o câmbio antes do estresse chegar.
        </p>

        <div class="card-grid">
          <div class="strat-card">
            <span class="strat-tag">Opção A</span>
            <h4>NDF · Non-Deliverable Forward</h4>
            <p class="strat-sub">Contrato que fixa a taxa para uma data futura, sem entrega física.</p>

            <span class="strat-block-label">Como funciona</span>
            <ul>
              <li>Você contrata: "Vou comprar US$ 500 mil a R$ 5,15 em 90 dias"</li>
              <li>Banco fixa a taxa</li>
              <li>Em 90 dias, mesmo se o spot estiver em R$ 5,50, você paga R$ 5,15</li>
              <li>Recebe os dólares ou o equivalente em reais</li>
            </ul>

            <span class="strat-block-label">Vantagens</span>
            <ul>
              <li>Simples de executar</li>
              <li>Sem entrega física</li>
              <li>Ideal para 30 a 180 dias</li>
              <li>Protege para os dois lados</li>
            </ul>

            <span class="strat-block-label">Desvantagens</span>
            <ul class="cons">
              <li>Perde upside se dólar cair</li>
              <li>Exige limite de crédito com banco</li>
              <li>Custo de spread</li>
            </ul>

            <div class="usecase"><strong>Melhor caso:</strong> importações com lead time definido e operações recorrentes.</div>
          </div>

          <div class="strat-card">
            <span class="strat-tag">Opção B</span>
            <h4>Trava de Câmbio · Termo</h4>
            <p class="strat-sub">Contrato a termo que fixa a taxa com entrega de moeda.</p>

            <span class="strat-block-label">Como funciona</span>
            <ul>
              <li>Você tem fatura ou contrato de importação</li>
              <li>Contrata termo: "US$ 500 mil a R$ 5,15 em 120 dias"</li>
              <li>Banco fixa a taxa</li>
              <li>Em 120 dias, recebe os dólares e paga o real fixado</li>
            </ul>

            <span class="strat-block-label">Vantagens</span>
            <ul>
              <li>Muito seguro com documentação</li>
              <li>Ideal para Incoterms claros</li>
              <li>Custo menor que NDF</li>
              <li>Margem previsível</li>
            </ul>

            <span class="strat-block-label">Desvantagens</span>
            <ul class="cons">
              <li>Exige documento ou fatura</li>
              <li>Risco de cancelamento gera custo</li>
              <li>Menos flexível</li>
            </ul>

            <div class="usecase"><strong>Melhor caso:</strong> operações recorrentes com disciplina documental.</div>
          </div>

          <div class="strat-card">
            <span class="strat-tag">Opção C</span>
            <h4>Swap Cambial</h4>
            <p class="strat-sub">Troca de indexador (CDI ↔ USD) sem renegociar contrato.</p>

            <span class="strat-block-label">Como funciona</span>
            <ul>
              <li>Você tem dívida em CDI (taxa BR)</li>
              <li>Quer trocar a base para USD</li>
              <li>Faz swap: paga SOFR + spread, recebe CDI</li>
              <li>Sua dívida vira efetivamente USD</li>
            </ul>

            <span class="strat-block-label">Vantagens</span>
            <ul>
              <li>Troca base sem renegociar</li>
              <li>Útil para FX loans</li>
              <li>Protege ALM</li>
            </ul>

            <span class="strat-block-label">Desvantagens</span>
            <ul class="cons">
              <li>Mais complexo</li>
              <li>Exige gestão de margin calls</li>
              <li>Custo maior</li>
            </ul>

            <div class="usecase"><strong>Melhor caso:</strong> importadores com dívida em CDI que querem migrar para USD.</div>
          </div>

          <div class="strat-card">
            <span class="strat-tag">Opção D</span>
            <h4>Collar · Proteção com upside</h4>
            <p class="strat-sub">Combinação piso + teto que permite aproveitar parte da queda.</p>

            <span class="strat-block-label">Como funciona</span>
            <ul>
              <li>Você define: "Não pago mais que R$ 5,30" (piso)</li>
              <li>Aceita: "Se cair para R$ 4,80, vendo a R$ 5,00" (teto)</li>
              <li>Resultado: protegido entre R$ 5,00 e R$ 5,30</li>
            </ul>

            <span class="strat-block-label">Vantagens</span>
            <ul>
              <li>Proteção com flexibilidade</li>
              <li>Aproveita parte da queda</li>
              <li>Custo menor (venda do upside paga a proteção)</li>
            </ul>

            <span class="strat-block-label">Desvantagens</span>
            <ul class="cons">
              <li>Perde ganho completo</li>
              <li>Mais complexo de estruturar</li>
              <li>Exige orçamento de prêmio</li>
            </ul>

            <div class="usecase"><strong>Melhor caso:</strong> importadores que querem proteção sem abrir mão de oportunidade.</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============== CH 7 — ANTECIPAÇÃO ============== -->
    <section class="chapter" id="ch-7" data-screen-label="07 Antecipação Estoque">
      <div class="ch-head">
        <div class="ch-num"><span class="pre">Capítulo</span>07</div>
        <div class="ch-titlebox">
          <h2>Estratégia 2 · Antecipação tática de estoque</h2>
          <p class="ch-deck">Trazer estoque agora antes do furacão. Por que funciona, como implementar, e o que isso economiza.</p>
        </div>
      </div>

      <div class="body">
        <p>O Q3 de 2026 vai combinar dólar alto (R$ 5,45–5,55) com gargalos logísticos — exatamente quando o resto do mercado tenta importar ao mesmo tempo. A solução tática é trazer estoque <strong>agora</strong>, em maio e junho, e atravessar o estresse com armazém cheio.</p>

        <h3>Por que funciona</h3>
        <div class="kpis">
          <div class="kpi">
            <div class="kpi-label">Câmbio favorável</div>
            <div class="kpi-value">R$ 4,95</div>
            <div class="kpi-delta up">Mai · Jun · janela atual</div>
          </div>
          <div class="kpi">
            <div class="kpi-label">Concorrência</div>
            <div class="kpi-value">↓<span class="unit">menor</span></div>
            <div class="kpi-delta neutral">Containers disponíveis</div>
          </div>
          <div class="kpi">
            <div class="kpi-label">Frete</div>
            <div class="kpi-value">↓<span class="unit">barato</span></div>
            <div class="kpi-delta neutral">Sobe forte em Q3</div>
          </div>
          <div class="kpi">
            <div class="kpi-label">Pico evitado</div>
            <div class="kpi-value">Q3</div>
            <div class="kpi-delta up">Sem importação no estresse</div>
          </div>
        </div>

        <h3>Como implementar</h3>
        <div class="steps">
          <div class="step">
            <div class="step-num">PASSO 01 · ABR–MAI</div>
            <h5>Análise de demanda</h5>
            <p>Projete demanda Jul–Dez. Calcule estoque de segurança. Mapeie itens com lead time longo.</p>
          </div>
          <div class="step">
            <div class="step-num">PASSO 02 · MAI</div>
            <h5>Negociação com fornecedores</h5>
            <p>Antecipe embarques. Ofereça pagamento antecipado com desconto. Estruture FINIMP para alongar prazo.</p>
          </div>
          <div class="step">
            <div class="step-num">PASSO 03 · MAI–JUN</div>
            <h5>Importação acelerada</h5>
            <p>Embarque produtos com lead time longo. Priorize alta margem. Trave frete antecipado.</p>
          </div>
          <div class="step">
            <div class="step-num">PASSO 04 · JUL–DEZ</div>
            <h5>Gestão de estoque</h5>
            <p>Venda estoque antecipado. Evite importações em Q3. Mantenha caixa para emergências.</p>
          </div>
        </div>

        <h3>Simulação · Importador de bens de capital</h3>
        <div style="margin: 12px 0 18px; font-size: 13px; color: var(--gx-fg-muted);">
          Estoque mensal de US$ 500 mil · Antecipação de 3 meses (US$ 1,5 mi) · Câmbio Mai R$ 5,10 · Câmbio Ago R$ 5,50.
        </div>
        <table class="gx-table">
          <thead>
            <tr>
              <th>Cenário</th>
              <th>Câmbio</th>
              <th>Custo em R$</th>
              <th>Economia</th>
            </tr>
          </thead>
          <tbody>
            <tr class="hot">
              <td>Sem antecipação · Agosto</td>
              <td>R$ 5,50</td>
              <td>R$ 8.250.000</td>
              <td>—</td>
            </tr>
            <tr>
              <td>Com antecipação · Maio</td>
              <td>R$ 5,10</td>
              <td>R$ 7.650.000</td>
              <td><span class="pos">R$ 600.000</span></td>
            </tr>
          </tbody>
        </table>
        <div class="alert">
          <div class="alert-eyebrow">Resultado</div>
          <p><strong>R$ 600 mil</strong> de economia apenas em câmbio — sem contar economia adicional de frete e prêmio de hedge evitado.</p>
        </div>
      </div>
    </section>

    <!-- ============== CH 8 — EX-TARIFÁRIO ============== -->
    <section class="chapter" id="ch-8" data-screen-label="08 Ex-Tarifário">
      <div class="ch-head">
        <div class="ch-num"><span class="pre">Capítulo</span>08</div>
        <div class="ch-titlebox">
          <h2>Estratégia 3 · Ex-tarifário &amp; benefícios estaduais</h2>
          <p class="ch-deck">Reduzir ou eliminar imposto de importação por design fiscal. Combinada com hedge, pode chegar a 44% de economia.</p>
        </div>
      </div>

      <div class="body">
        <h3>Ex-Tarifário</h3>
        <p>Redução ou eliminação de imposto de importação para produtos específicos. Aplicável a importadores de máquinas, tecnologia, insumos sem similar nacional.</p>

        <div class="steps">
          <div class="step">
            <div class="step-num">PASSO 01</div>
            <h5>Identificar produto</h5>
            <p>Mapeie itens potencialmente elegíveis no seu mix.</p>
          </div>
          <div class="step">
            <div class="step-num">PASSO 02</div>
            <h5>Pegar o NCM</h5>
            <p>Solicite ao fornecedor o código NCM correto.</p>
          </div>
          <div class="step">
            <div class="step-num">PASSO 03</div>
            <h5>Consultar SECEX/MDIC</h5>
            <p>Verifique se existe Ex-Tarifário para esse NCM.</p>
          </div>
          <div class="step">
            <div class="step-num">PASSO 04</div>
            <h5>Importar com II reduzido</h5>
            <p>Aprovado: II reduzido ou zerado. Validade 2 anos, renovável.</p>
          </div>
        </div>

        <div class="alert">
          <div class="alert-eyebrow">Exemplo prático</div>
          <p>Máquina de embalagem · II normal <strong>14%</strong> · Com Ex-Tarifário aprovado <strong>0%</strong> · Em US$ 100 mil a R$ 5,10 → economia de <strong>R$ 71.400</strong> por operação.</p>
        </div>

        <h3>Benefícios Estaduais</h3>
        <p>Incentivos fiscais para importadores instalados em estados com regime SUDENE / SUDAM. Podem chegar a <strong>75% de redução</strong> de impostos federais e estaduais.</p>

        <table class="gx-table">
          <thead>
            <tr>
              <th>Estado</th>
              <th>Programa</th>
              <th>Setores Elegíveis</th>
              <th>Redução Típica</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Santa Catarina · SC</td><td>SUDENE / SUDAM</td><td>Bens de capital · Tecnologia</td><td>até 75%</td></tr>
            <tr><td>Espírito Santo · ES</td><td>SUDENE / SUDAM</td><td>Minerais · Bens de capital</td><td>até 75%</td></tr>
            <tr><td>Rondônia · RO</td><td>SUDENE / SUDAM</td><td>Bens de capital · Tecnologia</td><td>até 75%</td></tr>
            <tr><td>Amazonas · AM</td><td>SUDAM</td><td>Bens de capital · Insumos</td><td>até 75%</td></tr>
          </tbody>
        </table>

        <h3>Combinação de estratégias</h3>
        <p>A melhor prática para 2026 é empilhar: <strong>Ex-Tarifário + Benefício Estadual + Hedge Cambial</strong>. Veja como isso funciona em uma operação real.</p>

        <div class="chart-wrap" style="background:#fff; color:var(--gx-fg); border:1px solid var(--gx-border); box-shadow: 6px 6px 0 0 rgba(12,49,99,0.15);">
          <div class="chart-head">
            <h4 style="color: var(--gx-secondary-dark);">Empilhamento de Benefícios · Máquina de Embalagem</h4>
            <div class="now" style="color: var(--gx-primary-dark);"><span style="font-size: 11px; color: var(--gx-secondary-dark); letter-spacing: 0.18em; text-transform: uppercase; display: block; margin-bottom: 4px;">US$ 100 mil · R$ 5,10</span>R$ 510.000</div>
          </div>

          <table class="gx-table" style="margin: 12px 0 0; box-shadow: none;">
            <thead>
              <tr>
                <th>Camada</th>
                <th>Efeito</th>
                <th>Custo Acumulado</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Importação base</td>
                <td>R$ 510.000 (FOB convertido)</td>
                <td>R$ 510.000</td>
              </tr>
              <tr>
                <td>Impostos normais (~25%)</td>
                <td>+ R$ 127.500</td>
                <td>R$ 637.500</td>
              </tr>
              <tr>
                <td>Ex-Tarifário (−14% II)</td>
                <td><span class="pos">− R$ 71.400</span></td>
                <td>R$ 566.100</td>
              </tr>
              <tr class="hot">
                <td>Benefício SUDENE (−75%)</td>
                <td><span class="pos">− R$ 213.575</span></td>
                <td><strong>R$ 352.525</strong></td>
              </tr>
            </tbody>
          </table>
          <div style="margin-top: 18px; display: flex; justify-content: space-between; align-items: center; padding: 14px 0 0; border-top: 1px dashed var(--gx-border);">
            <div style="font-size: 11px; letter-spacing: 0.2em; text-transform: uppercase; color: var(--gx-secondary-dark); font-weight: 700;">Economia total</div>
            <div style="font-family: var(--font-mono); font-size: 28px; font-weight: 800; color: var(--gx-success); letter-spacing: -0.02em;">R$ 284.975 · −44,7%</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============== CH 9 — FERRAMENTAS ============== -->
    <section class="chapter" id="ch-9" data-screen-label="09 Ferramentas GX">
      <div class="ch-head">
        <div class="ch-num"><span class="pre">Capítulo</span>09</div>
        <div class="ch-titlebox">
          <h2>Ferramentas da GX Capital para importadores</h2>
          <p class="ch-deck">Simuladores, leituras comparativas de mercado e mesa de câmbio dedicada — disponíveis hoje.</p>
        </div>
      </div>

      <div class="body">

        <div class="tools-grid">
          <a class="tool-card" href="<?= esc($simuladorUrl); ?>" data-gx-cta="tool-simulador-cambio">
            <div class="ti"><i data-lucide="line-chart"></i></div>
            <h5>Simulador de Câmbio</h5>
            <p class="tdesc">Teste cenários de importação. Compare estruturas (NDF · Termo · Swap · Collar). Receba leitura comparativa de 10+ instituições.</p>
            <span class="turl"><?= esc(parse_url($simuladorUrl, PHP_URL_HOST) . parse_url($simuladorUrl, PHP_URL_PATH)); ?></span>
          </a>
          <a class="tool-card" href="<?= esc(langBaseUrl('simuladores')); ?>" data-gx-cta="tool-simulador-finimp">
            <div class="ti"><i data-lucide="banknote"></i></div>
            <h5>Simulador de FINIMP</h5>
            <p class="tdesc">Teste linhas de Financiamento à Importação. Compare taxas e prazos. Calcule impacto no caixa operacional. Decisão objetiva: vale a pena financiar?</p>
            <span class="turl">gx.capital/simuladores</span>
          </a>
          <a class="tool-card" href="#lead" data-gx-cta="tool-mesa">
            <div class="ti"><i data-lucide="users"></i></div>
            <h5>Mesa de Câmbio</h5>
            <p class="tdesc">Agenda direta com especialista da mesa. Cenário próprio analisado, recomendação personalizada, implementação assistida.</p>
            <span class="turl">Falar com a mesa →</span>
          </a>
        </div>

        <h3>Indicadores de referência · Maio 2026</h3>
        <div class="indicators">
          <div class="indicator"><div class="il">USD/BRL</div><div class="iv">5,20</div></div>
          <div class="indicator"><div class="il">SELIC</div><div class="iv">14,25%</div></div>
          <div class="indicator"><div class="il">CDI</div><div class="iv">14,15%</div></div>
          <div class="indicator"><div class="il">SOFR</div><div class="iv">4,70%</div></div>
          <div class="indicator"><div class="il">Spread Comercial</div><div class="iv">1,10%</div></div>
        </div>

        <h3>Fluxo de uso · Simulador de Câmbio</h3>
        <div class="steps">
          <div class="step">
            <div class="step-num">01</div>
            <h5>Seleciona "Importação"</h5>
            <p>Acesse o simulador na plataforma GX.</p>
          </div>
          <div class="step">
            <div class="step-num">02</div>
            <h5>Insere os dados</h5>
            <p>Valor em dólar, prazo, tipo de proteção desejada.</p>
          </div>
          <div class="step">
            <div class="step-num">03</div>
            <h5>Testa cenários</h5>
            <p>Veja impacto de NDF, Termo, Swap e Collar lado a lado.</p>
          </div>
          <div class="step">
            <div class="step-num">04</div>
            <h5>Envia para a mesa</h5>
            <p>Receba cotações de múltiplos bancos em horas.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ============== CH 10 — CHECKLIST ============== -->
    <section class="chapter" id="ch-10" data-screen-label="10 Checklist">
      <div class="ch-head">
        <div class="ch-num"><span class="pre">Capítulo</span>10</div>
        <div class="ch-titlebox">
          <h2>Checklist de ação imediata</h2>
          <p class="ch-deck">Da janela atual até dezembro. O que fazer mês a mês para chegar em 2027 com a margem inteira.</p>
        </div>
      </div>

      <div class="body">
        <div class="timeline">
          <div class="tl-month">
            <div class="tl-label">Maio · 2026<span class="hint">Janela aberta · Agir agora</span></div>
            <ul class="tl-list">
              <li class="urgent">Mapear importações mensais (valor em USD, prazo, setor)</li>
              <li class="urgent">Identificar produtos com Ex-Tarifário elegível</li>
              <li>Consultar qualificação para benefício estadual</li>
              <li>Simular cenários no Simulador de Câmbio da GX Capital</li>
              <li class="urgent">Contratar hedge para importações de Mai · Jun (NDF ou Termo)</li>
              <li>Iniciar processo de Ex-Tarifário (se aplicável)</li>
              <li>Agendar consulta com especialista GX Capital</li>
              <li>Comunicar ao time financeiro: "Vamos proteger câmbio"</li>
            </ul>
          </div>

          <div class="tl-month">
            <div class="tl-label">Junho · 2026<span class="hint">Antecipar e fechar hedges</span></div>
            <ul class="tl-list">
              <li class="urgent">Antecipar importações de Q3 (bens de capital prioritários)</li>
              <li>Negociar FINIMP com bancos parceiros</li>
              <li class="urgent">Finalizar hedges para Jun · Jul</li>
              <li>Monitorar aprovação de Ex-Tarifário</li>
              <li>Preparar documentação para benefício estadual</li>
            </ul>
          </div>

          <div class="tl-month">
            <div class="tl-label">Jul – Ago · 2026<span class="hint">Estresse a caminho · defender</span></div>
            <ul class="tl-list">
              <li class="urgent">Monitorar dólar diariamente · pico esperado em ago–out</li>
              <li>Manter hedges em dia · sem cancelamentos oportunistas</li>
              <li>Evitar importações não-essenciais</li>
              <li>Vender estoque antecipado</li>
              <li>Manter caixa para emergências</li>
            </ul>
          </div>

          <div class="tl-month">
            <div class="tl-label">Set – Out · 2026<span class="hint">Pico eleitoral · não tirar o pé</span></div>
            <ul class="tl-list">
              <li class="urgent">Acompanhar eleições e leitura de mesa diária</li>
              <li>Manter proteção cambial ativa</li>
              <li>Preparar para acomodação pós-eleição</li>
            </ul>
          </div>

          <div class="tl-month">
            <div class="tl-label">Nov – Dez · 2026<span class="hint">Acomodação · planejar 2027</span></div>
            <ul class="tl-list">
              <li>Dólar deve acomodar (R$ 5,25–5,35)</li>
              <li>Renegociar hedges para 2027</li>
              <li>Planejar estratégia 2027</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- ============== CH 11 — CONCLUSÃO ============== -->
    <section class="chapter" id="ch-11" data-screen-label="11 Conclusão">
      <div class="ch-head">
        <div class="ch-num"><span class="pre">Capítulo</span>11</div>
        <div class="ch-titlebox">
          <h2>Conclusão e próximos passos</h2>
          <p class="ch-deck">O cenário é claro. As ferramentas existem. O que falta é decisão e execução.</p>
        </div>
      </div>

      <div class="body">
        <p class="lede">
          2026 é ano eleitoral — volatilidade está garantida. O dólar pode chegar a <em>R$ 5,50</em>
          em Q3. Sem proteção, margens desaparecem. Com as três estratégias deste guia, elas continuam intactas.
        </p>

        <div class="card-grid three">
          <div class="strat-card">
            <span class="strat-tag">Estratégia 01</span>
            <h4>Hedge Cambial</h4>
            <p class="strat-sub" style="margin-bottom:0;">Protege a margem de forma previsível. NDF, Termo, Swap ou Collar — escolha conforme o perfil.</p>
          </div>
          <div class="strat-card">
            <span class="strat-tag">Estratégia 02</span>
            <h4>Antecipação</h4>
            <p class="strat-sub" style="margin-bottom:0;">Aproveita o câmbio favorável de Mai–Jun e atravessa o Q3 com armazém cheio.</p>
          </div>
          <div class="strat-card">
            <span class="strat-tag">Estratégia 03</span>
            <h4>Ex-Tarifário &amp; Estaduais</h4>
            <p class="strat-sub" style="margin-bottom:0;">Reduz custos estruturalmente. Empilhada com hedge, chega a -44% no custo final.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ============== LEAD CAPTURE BLOCK ============== -->
    <section class="lead-block" id="lead" data-screen-label="12 Falar com a Mesa">
      <div class="lead-grid">
        <div>
          <div class="lead-eyebrow"><span class="dot"></span>Mesa de câmbio · resposta em até 2h úteis</div>
          <h3>Solicite seu <span class="accent">estudo cambial</span> personalizado.</h3>
          <p class="lead-copy">
            A janela está aberta — Maio e Junho. Em julho ela começa a fechar.
            Conte o contexto da sua operação e a mesa GX Capital retorna com a estrutura
            mais aderente para proteger sua margem em 2026.
          </p>
          <ul class="lead-bullets">
            <li>Comparativo entre 10+ instituições financeiras</li>
            <li>Estudo de hedge sob medida (NDF · Termo · Swap · Collar)</li>
            <li>Análise de Ex-Tarifário e benefícios estaduais</li>
            <li>Sem custo · sem compromisso de contratação</li>
          </ul>
        </div>

        <div class="lead-form-wrap">
          <div class="lead-form-head">Formulário · Mesa de Câmbio</div>
          <div class="lead-form-title">Falar com um especialista</div>
          <form id="gx-playbook-form" novalidate>
            <input type="text" name="message_content" class="lf-honey" tabindex="-1" autocomplete="off" aria-hidden="true">
            <input type="hidden" name="origem" value="Playbook Importação Blindada">
            <input type="hidden" name="landing_page" value="<?= esc(current_url()); ?>">
            <input type="hidden" name="meta_content_name" value="Playbook Importação Blindada">
            <input type="hidden" name="meta_content_category" value="Câmbio · Importação">
            <input type="hidden" name="meta_value" value="1">
            <input type="hidden" name="meta_currency" value="BRL">
            <input type="hidden" name="event_id" value="">
            <input type="hidden" name="utm_source" value="">
            <input type="hidden" name="utm_medium" value="">
            <input type="hidden" name="utm_campaign" value="">
            <input type="hidden" name="utm_term" value="">
            <input type="hidden" name="utm_content" value="">
            <input type="hidden" name="phone_country" value="">

            <div class="lf-row">
              <div class="lf-field">
                <label for="lf-name">Nome</label>
                <input id="lf-name" name="name" type="text" maxlength="120" autocomplete="name" required>
              </div>
              <div class="lf-field">
                <label for="lf-company">Empresa</label>
                <input id="lf-company" name="company" type="text" maxlength="120" autocomplete="organization">
              </div>
            </div>

            <div class="lf-row">
              <div class="lf-field">
                <label for="lf-email">E-mail corporativo</label>
                <input id="lf-email" name="email" type="email" maxlength="160" autocomplete="email" required>
              </div>
              <div class="lf-field">
                <label for="lf-phone">WhatsApp</label>
                <input id="lf-phone" name="phone" type="tel" inputmode="tel" maxlength="40" autocomplete="tel" placeholder="(11) 90000-0000" required>
              </div>
            </div>

            <div class="lf-row">
              <div class="lf-field">
                <label for="lf-flow">Fluxo principal</label>
                <select id="lf-flow" name="sim_flow" required>
                  <option value="">Selecione…</option>
                  <option value="Importação">Importação</option>
                  <option value="Exportação">Exportação</option>
                  <option value="Importação e exportação">Importação e exportação</option>
                  <option value="Outro / Avaliando">Outro / Avaliando</option>
                </select>
              </div>
              <div class="lf-field">
                <label for="lf-volume">Volume mensal (USD)</label>
                <select id="lf-volume" name="sim_volume" required>
                  <option value="">Selecione…</option>
                  <option value="Até US$ 100 mil">Até US$ 100 mil</option>
                  <option value="US$ 100k – 500k">US$ 100 mil – 500 mil</option>
                  <option value="US$ 500k – 1 mi">US$ 500 mil – 1 milhão</option>
                  <option value="US$ 1 mi – 5 mi">US$ 1 mi – 5 mi</option>
                  <option value="US$ 5 mi – 20 mi">US$ 5 mi – 20 mi</option>
                  <option value="Acima de US$ 20 mi">Acima de US$ 20 mi</option>
                </select>
              </div>
            </div>

            <div class="lf-row full">
              <div class="lf-field">
                <label for="lf-message">Contexto da operação (opcional)</label>
                <textarea id="lf-message" name="message" maxlength="800" rows="3" placeholder="Setor, prazo médio das importações, exposição atual, dúvidas específicas…"></textarea>
              </div>
            </div>

            <label class="lf-consent">
              <input type="checkbox" required>
              <span>
                Li e concordo com a <a href="<?= esc($homeUrl); ?>" target="_blank" rel="noopener">Política de Privacidade</a> da GX Capital e autorizo o contato comercial sobre câmbio estruturado.
              </span>
            </label>

            <button type="submit" class="lf-submit">
              Quero proteger minha margem
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 7h11M7 1l5 6-5 6"/></svg>
            </button>

            <div class="lf-feedback" id="lf-feedback" role="status" aria-live="polite"></div>
          </form>
        </div>
      </div>
    </section>

    <!-- ============== CTA ============== -->
    <section class="cta-block" id="cta" data-screen-label="13 Próximos Passos">
      <h2>Sua margem está<br/><span class="accent">protegida.</span></h2>
      <p class="lede2">
        A janela está aberta — Maio e Junho. Em julho ela começa a fechar.
        A GX Capital é a sua mesa de câmbio para atravessar 2026 com a margem intacta.
      </p>

      <div class="cta-grid">
        <a class="cta-link" href="<?= esc($simuladorUrl); ?>" data-gx-cta="cta-simulador">
          <div>
            <div class="l">Simulador de Câmbio</div>
            <div class="v">Abrir agora</div>
          </div>
          <i data-lucide="arrow-up-right"></i>
        </a>
        <a class="cta-link" href="#lead" data-gx-cta="cta-especialista">
          <div>
            <div class="l">Agendar Especialista</div>
            <div class="v">Formulário acima</div>
          </div>
          <i data-lucide="arrow-up-right"></i>
        </a>
        <a class="cta-link" href="<?= esc($homeUrl); ?>" data-gx-cta="cta-website">
          <div>
            <div class="l">Website</div>
            <div class="v">gx.capital</div>
          </div>
          <i data-lucide="arrow-up-right"></i>
        </a>
        <a class="cta-link" href="<?= esc($whatsAppHref); ?>" target="_blank" rel="noopener" data-gx-whatsapp-link data-gx-cta="cta-whatsapp">
          <div>
            <div class="l">WhatsApp · Mesa</div>
            <div class="v"><?= esc($contactPhone); ?></div>
          </div>
          <i data-lucide="message-square"></i>
        </a>
        <?php if (!empty($contactEmail)): ?>
        <a class="cta-link" href="mailto:<?= esc($contactEmail); ?>" data-gx-cta="cta-email">
          <div>
            <div class="l">E-mail · Mesa</div>
            <div class="v"><?= esc($contactEmail); ?></div>
          </div>
          <i data-lucide="mail"></i>
        </a>
        <?php endif; ?>
      </div>

      <div class="cta-buttons">
        <a class="btn-primary" href="#lead" data-gx-cta="cta-btn-mesa">
          Falar com a Mesa
          <i data-lucide="arrow-right" style="width:14px;height:14px;"></i>
        </a>
        <a class="btn-ghost" href="<?= esc($simuladorUrl); ?>" data-gx-cta="cta-btn-simulador">
          Abrir Simulador
        </a>
      </div>
    </section>

    <!-- ============== APÊNDICE ============== -->
    <section class="appendix" id="appendix" data-screen-label="14 Apêndices">
      <div style="display:flex; align-items:end; justify-content:space-between; padding-bottom: 24px; border-bottom: 2px solid var(--gx-secondary-dark); margin-bottom: 32px;">
        <div>
          <div style="font-size: 11px; letter-spacing: 0.22em; text-transform: uppercase; color: var(--gx-secondary-light); margin-bottom: 8px; font-weight: 700;">Apêndices · A · B · C</div>
          <h2 style="font-size: 40px; font-weight: 900; text-transform: uppercase; letter-spacing: -0.025em; line-height: 0.95; color: #fff;">Glossário · Fontes · Contatos</h2>
        </div>
        <div style="font-family: var(--font-mono); font-size: 12px; color: var(--gx-secondary-light); opacity: 0.7;">EB01 · 2026/05</div>
      </div>

      <h3>A · Glossário de termos</h3>
      <dl class="gloss-grid">
        <div class="gloss-item"><dt>Hedge</dt><dd>Proteção contra variação de câmbio ou indexador.</dd></div>
        <div class="gloss-item"><dt>NDF</dt><dd>Non-Deliverable Forward — contrato de câmbio sem entrega física.</dd></div>
        <div class="gloss-item"><dt>Termo</dt><dd>Contrato de câmbio com entrega de moeda.</dd></div>
        <div class="gloss-item"><dt>Swap</dt><dd>Troca de indexadores (ex.: CDI por USD).</dd></div>
        <div class="gloss-item"><dt>Collar</dt><dd>Proteção combinada de piso e teto.</dd></div>
        <div class="gloss-item"><dt>Ex-Tarifário</dt><dd>Redução de Imposto de Importação para produtos sem similar nacional.</dd></div>
        <div class="gloss-item"><dt>FINIMP</dt><dd>Financiamento à Importação — linha de crédito em moeda estrangeira para importadores, com prazo alongado e taxa internacional.</dd></div>
        <div class="gloss-item"><dt>SUDENE</dt><dd>Superintendência do Desenvolvimento do Nordeste.</dd></div>
        <div class="gloss-item"><dt>SUDAM</dt><dd>Superintendência do Desenvolvimento da Amazônia.</dd></div>
      </dl>

      <h3 style="margin-top:42px;">B · Referências e fontes</h3>
      <ul class="ref-list">
        <li><strong>MDIC</strong>Ministério do Desenvolvimento, Indústria, Comércio e Serviços</li>
        <li><strong>BCB</strong>Banco Central do Brasil</li>
        <li><strong>SECEX</strong>Secretaria de Comércio Exterior</li>
        <li><strong>SUDENE</strong>sudene.gov.br</li>
        <li><strong>SUDAM</strong>sudam.gov.br</li>
        <li><strong>GX Capital</strong>Simuladores e análises da mesa de câmbio</li>
        <li><strong>Balança Comercial</strong>Dados 2025 e Q1 2026 oficiais</li>
        <li><strong>Ex-Tarifário</strong>gov.br/mdic/comercio-exterior/ex-tarifario</li>
      </ul>

      <h3 style="margin-top:42px;">C · Contatos úteis</h3>
      <ul class="ref-list">
        <li><strong>SECEX / MDIC</strong>gov.br/mdic</li>
        <li><strong>SUDENE</strong>sudene.gov.br</li>
        <li><strong>SUDAM</strong>sudam.gov.br</li>
        <li><strong>GX Capital</strong>gx.capital</li>
      </ul>
    </section>

    <footer class="foot">
      <div class="foot-brand">
        <img src="<?= base_url('assets/logo-icon.png'); ?>" alt="GX Capital · Mesa de Câmbio" width="26" height="26" loading="lazy" decoding="async" />
        <div class="foot-wordmark">GX Capital</div>
      </div>
      <div>© <?= date('Y'); ?> GX Capital · Documento confidencial · Uso restrito</div>
      <div>EB01 / 2026 / 05</div>
    </footer>

    </article>
  </main>
</div>

<!-- ============== MOBILE TOC DRAWER ============== -->
<script>
(function() {
    var body = document.body;
    var toggle = document.querySelector('[data-gx-toc-toggle]');
    var backdrop = document.querySelector('[data-gx-toc-backdrop]');
    var toc = document.getElementById('gx-playbook-toc');
    var label = document.querySelector('[data-gx-toc-label]');
    if (!toggle || !toc) return;

    function open() {
        body.classList.add('gx-toc-open');
        toggle.setAttribute('aria-expanded', 'true');
        if (label) label.textContent = 'Fechar';
    }
    function close() {
        body.classList.remove('gx-toc-open');
        toggle.setAttribute('aria-expanded', 'false');
        if (label) label.textContent = 'Sumário';
    }

    toggle.addEventListener('click', function() {
        if (body.classList.contains('gx-toc-open')) close(); else open();
    });
    if (backdrop) backdrop.addEventListener('click', close);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') close();
    });
    /* Close when navigating to a chapter */
    toc.addEventListener('click', function(e) {
        var a = e.target.closest ? e.target.closest('a[href^="#"]') : null;
        if (a) close();
    });
    /* Reset state when crossing the desktop breakpoint */
    var mq = window.matchMedia('(min-width: 1101px)');
    var onChange = function(ev) { if (ev.matches) close(); };
    if (mq.addEventListener) mq.addEventListener('change', onChange);
    else if (mq.addListener) mq.addListener(onChange);
})();
</script>

<!-- ============== TRACKING + INTERACTION ============== -->
<script>
(function() {
    /* ── GX Tracking Toolkit (mirrors marketing/_home_footer.php) ── */
    window.dataLayer = window.dataLayer || [];

    window.gxFbq = function(action, event, params) {
        if (typeof fbq !== 'function') return;
        if (action === 'trackCustom') {
            fbq('trackCustom', event, params || {});
        } else {
            fbq('track', event, params || {});
        }
    };
    window.gxGtag = function() {
        if (typeof gtag === 'function') gtag.apply(null, arguments);
    };

    var utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
    var params = new URLSearchParams(window.location.search);
    var stored = {};
    try { stored = JSON.parse(sessionStorage.getItem('gx_utm') || '{}'); } catch (e) {}
    var hasNew = false;
    utmKeys.forEach(function(key) {
        if (params.has(key)) { stored[key] = params.get(key); hasNew = true; }
    });
    if (hasNew) {
        try { sessionStorage.setItem('gx_utm', JSON.stringify(stored)); } catch (e) {}
    }

    window.gxHydrateUtmFields = function(form) {
        if (!form) return;
        var utm = {};
        try { utm = JSON.parse(sessionStorage.getItem('gx_utm') || '{}'); } catch (e) {}
        var p = new URLSearchParams(window.location.search);
        utmKeys.forEach(function(key) {
            var val = p.get(key) || utm[key] || '';
            var field = form.querySelector('input[name="' + key + '"]');
            if (field && val) field.value = val;
        });
    };

    window.gxEventId = function() {
        var ts = Date.now().toString(36);
        var rand = Math.random().toString(36).substring(2, 10);
        return 'gx_' + ts + '_' + rand;
    };

    window.gxFbqDedup = function(action, event, params) {
        if (typeof fbq !== 'function') return '';
        var eventId = gxEventId();
        var p = Object.assign({}, params || {});
        p.eventID = eventId;
        if (action === 'trackCustom') {
            fbq('trackCustom', event, p, { eventID: eventId });
        } else {
            fbq('track', event, p, { eventID: eventId });
        }
        return eventId;
    };

    /* Standardize ViewContent (Meta) + page_view (GA) for the playbook */
    gxFbq('track', 'ViewContent', {
        content_name: 'Playbook Importação Blindada',
        content_category: 'Câmbio · Importação',
        currency: 'BRL'
    });
    gxGtag('event', 'page_view_playbook', {
        playbook: 'importacao_blindada',
        edition: 'EB01-2026-05'
    });

    /* WhatsApp / CTA click tracking */
    document.addEventListener('click', function(e) {
        var wa = e.target.closest ? e.target.closest('[data-gx-whatsapp-link]') : null;
        if (wa) {
            gxFbq('track', 'Contact', { content_name: 'WhatsApp · Playbook Importação Blindada' });
            gxGtag('event', 'contact', { method: 'whatsapp', source: 'playbook_importacao_blindada' });
        }
        var cta = e.target.closest ? e.target.closest('[data-gx-cta]') : null;
        if (cta) {
            var label = cta.getAttribute('data-gx-cta') || 'cta';
            gxFbq('trackCustom', 'PlaybookCtaClick', { cta: label, playbook: 'importacao_blindada' });
            gxGtag('event', 'cta_click', { cta: label, playbook: 'importacao_blindada' });
        }
        var toc = e.target.closest ? e.target.closest('[data-gx-toc]') : null;
        if (toc) {
            gxGtag('event', 'toc_click', { chapter: toc.getAttribute('data-gx-toc'), playbook: 'importacao_blindada' });
        }
    });

    /* Reading depth (25 / 50 / 75 / 100 %) */
    var depthHits = {25:false,50:false,75:false,100:false};
    function checkDepth() {
        var doc = document.documentElement;
        var scrolled = (window.scrollY || window.pageYOffset);
        var max = Math.max(doc.scrollHeight - window.innerHeight, 1);
        var pct = Math.round(scrolled / max * 100);
        [25,50,75,100].forEach(function(t) {
            if (pct >= t && !depthHits[t]) {
                depthHits[t] = true;
                gxGtag('event', 'scroll_depth', { depth: t, playbook: 'importacao_blindada' });
                if (t === 75) {
                    gxFbq('trackCustom', 'PlaybookReadDeep', { depth: t, playbook: 'importacao_blindada' });
                }
            }
        });
    }
    var depthScheduled = false;
    window.addEventListener('scroll', function() {
        if (depthScheduled) return;
        depthScheduled = true;
        window.requestAnimationFrame(function() { checkDepth(); depthScheduled = false; });
    }, { passive: true });
})();

/* ============== LEAD CAPTURE FORM ============== */
(function() {
    var form = document.getElementById('gx-playbook-form');
    if (!form) return;

    var feedback = document.getElementById('lf-feedback');
    var submit = form.querySelector('.lf-submit');
    var endpoint = <?= json_encode(base_url('api/save-simulator-lead')); ?>;

    if (window.gxHydrateUtmFields) window.gxHydrateUtmFields(form);

    function showFeedback(msg, type) {
        if (!feedback) return;
        feedback.className = 'lf-feedback is-visible' + (type ? ' is-' + type : '');
        feedback.textContent = msg;
    }

    form.addEventListener('submit', function(ev) {
        ev.preventDefault();

        if (form.querySelector('input[name="message_content"]').value.trim() !== '') {
            return; /* honeypot tripped */
        }
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        var eventId = (window.gxEventId ? window.gxEventId() : '');
        var eventIdField = form.querySelector('input[name="event_id"]');
        if (eventIdField) eventIdField.value = eventId;

        var payload = new FormData(form);
        var simData = {
            flow: payload.get('sim_flow') || '',
            volume: payload.get('sim_volume') || '',
            playbook: 'Importação Blindada · EB01-2026-05'
        };
        payload.append('sim_data', JSON.stringify(simData));
        payload.append('lead_origin', 'Playbook Importação Blindada');

        if (submit) { submit.disabled = true; submit.textContent = 'Enviando…'; }
        showFeedback('Enviando seus dados para a mesa…', null);

        fetch(endpoint, {
            method: 'POST',
            body: payload,
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function(r) {
            return r.json().catch(function() { return null; });
        }).then(function(data) {
            if (data && (data.result === 1 || data.status === 'success')) {
                showFeedback('Recebemos seu pedido. Em até 2h úteis um especialista da mesa GX Capital entra em contato pelo WhatsApp informado.', 'success');
                form.reset();

                /* Meta + GA Lead with deduplication */
                var leadParams = {
                    content_name: 'Playbook Importação Blindada',
                    content_category: 'Câmbio · Importação',
                    currency: 'BRL',
                    value: 1
                };
                if (typeof fbq === 'function' && eventId) {
                    var p = Object.assign({}, leadParams);
                    p.eventID = eventId;
                    fbq('track', 'Lead', p, { eventID: eventId });
                } else if (window.gxFbq) {
                    window.gxFbq('track', 'Lead', leadParams);
                }
                if (window.gxGtag) {
                    window.gxGtag('event', 'generate_lead', {
                        playbook: 'importacao_blindada',
                        currency: 'BRL',
                        value: 1
                    });
                }

                if (window.dataLayer) {
                    window.dataLayer.push({
                        event: 'playbook_lead',
                        playbook: 'importacao_blindada',
                        sim_flow: simData.flow,
                        sim_volume: simData.volume
                    });
                }
            } else {
                var msg = (data && data.message) ? data.message : 'Não foi possível enviar agora. Tente novamente em instantes ou fale conosco pelo WhatsApp.';
                showFeedback(msg, 'error');
            }
        }).catch(function() {
            showFeedback('Conexão instável. Tente novamente ou fale conosco pelo WhatsApp.', 'error');
        }).finally(function() {
            if (submit) { submit.disabled = false; submit.innerHTML = 'Quero proteger minha margem <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 7h11M7 1l5 6-5 6"/></svg>'; }
        });
    });
})();

/* Lucide icons */
window.addEventListener('load', function() {
    if (window.lucide && lucide.createIcons) lucide.createIcons();
});
</script>

<?= $generalSettings->google_analytics; ?>
<?= $generalSettings->custom_footer_codes; ?>

</body>
</html>
