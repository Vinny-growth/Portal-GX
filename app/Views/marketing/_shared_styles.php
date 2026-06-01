<style>
@import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Fraunces:ital,opsz,wght@0,9..144,300..700;1,9..144,300..700&family=JetBrains+Mono:wght@400;500;600;700&display=swap');

@font-face {
    font-family: "Inter";
    font-style: normal;
    font-weight: 400;
    font-display: swap;
    src: url("<?= base_url('assets/fonts/inter/inter-400.woff2'); ?>") format("woff2"),
         url("<?= base_url('assets/fonts/inter/inter-400.woff'); ?>") format("woff");
}

@font-face {
    font-family: "Inter";
    font-style: normal;
    font-weight: 600;
    font-display: swap;
    src: url("<?= base_url('assets/fonts/inter/inter-600.woff2'); ?>") format("woff2"),
         url("<?= base_url('assets/fonts/inter/inter-600.woff'); ?>") format("woff");
}

@font-face {
    font-family: "Inter";
    font-style: normal;
    font-weight: 700;
    font-display: swap;
    src: url("<?= base_url('assets/fonts/inter/inter-700.woff2'); ?>") format("woff2"),
         url("<?= base_url('assets/fonts/inter/inter-700.woff'); ?>") format("woff");
}

@font-face {
    font-family: "Open Sans";
    font-style: normal;
    font-weight: 400;
    font-display: swap;
    src: url("<?= base_url('assets/fonts/open-sans/open-sans-400.woff2'); ?>") format("woff2"),
         url("<?= base_url('assets/fonts/open-sans/open-sans-400.woff'); ?>") format("woff");
}

@font-face {
    font-family: "Open Sans";
    font-style: normal;
    font-weight: 600;
    font-display: swap;
    src: url("<?= base_url('assets/fonts/open-sans/open-sans-600.woff2'); ?>") format("woff2"),
         url("<?= base_url('assets/fonts/open-sans/open-sans-600.woff'); ?>") format("woff");
}

@font-face {
    font-family: "Open Sans";
    font-style: normal;
    font-weight: 700;
    font-display: swap;
    src: url("<?= base_url('assets/fonts/open-sans/open-sans-700.woff2'); ?>") format("woff2"),
         url("<?= base_url('assets/fonts/open-sans/open-sans-700.woff'); ?>") format("woff");
}

:root {
    /* Nexus design system — brutalist financial.
       Single source of truth: /colors_and_type.css.
       Marketing CSS aliases its existing variable names to Nexus tokens
       so every component already in the page gets the new identity. */
    --gx-navy: #0c3163;
    --gx-navy-90: rgba(12,49,99,0.92);
    --gx-navy-70: rgba(12,49,99,0.7);
    --gx-navy-50: rgba(12,49,99,0.5);
    --gx-navy-15: rgba(12,49,99,0.15);
    --gx-navy-08: rgba(12,49,99,0.08);
    --gx-navy-04: rgba(12,49,99,0.04);
    --gx-navy-deep: #000d23;
    --gx-gold: #c9a96a;
    --gx-gold-hover: #87704a;
    --gx-gold-light: rgba(201,169,106,0.14);
    --gx-gold-soft: #dbc7a2;
    --gx-gold-etched: #87704a;
    --gx-ivory: #f5f3ee;
    --gx-vellum: #ebe6dc;
    --gx-bg: #ffffff;
    --gx-bg-warm: #f5f3ee;
    --gx-bg-cool: #ebe6dc;
    --gx-text: #000d23;
    --gx-text-secondary: #5a6a80;
    --gx-text-tertiary: #94a3b8;
    --gx-border: #d3d9e0;
    --gx-border-light: #e7ebf0;
    --gx-font-display: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    --gx-font-heading: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    --gx-font-sans-refined: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    --gx-font-body: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    --gx-font-mono: "JetBrains Mono", ui-monospace, "Menlo", monospace;
    /* Brutalist: sharp corners. Pills and 50% circles override locally. */
    --gx-radius: 0;
    --gx-radius-md: 0;
    --gx-radius-lg: 0;
    --gx-ease: cubic-bezier(0.16, 1, 0.3, 1);
    --gx-panel: #ffffff;
    /* Hard offset shadows — brutalist financial. */
    --gx-shadow-sm: 2px 2px 0 0 rgba(211,217,224,0.5);
    --gx-shadow-md: 4px 4px 0 0 rgba(12,49,99,0.18);
    --gx-shadow-lg: 8px 8px 0 0 rgba(12,49,99,0.22);
    --gx-shadow-card-hover: 6px 6px 0 0 rgba(12,49,99,0.3);
    --gx-max: 1140px;
}

.gx-marketing {
    position: relative;
    color: var(--gx-text);
    font-family: var(--gx-font-body);
    background: var(--gx-bg);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    font-size: 15px;
    line-height: 1.6;
}

.gx-marketing *,
.gx-marketing *::before,
.gx-marketing *::after {
    box-sizing: border-box;
}

.gx-marketing a {
    text-decoration: none;
    color: inherit;
}

.gx-sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.gx-wrap {
    width: 100%;
    max-width: var(--gx-max);
    margin: 0 auto;
    padding: 0 24px;
}

.gx-shell {
    position: relative;
}

body.gx-marketing-home {
    background: var(--gx-bg);
    overflow-x: hidden;
}

body.gx-modal-open {
    overflow: hidden;
}

body.gx-marketing-home #nav-top,
body.gx-marketing-home #header,
body.gx-marketing-home > .container-bn-header,
body.gx-marketing-home > .container-bn-mb.mb-3,
body.gx-marketing-home > #navMobile,
body.gx-marketing-home > .mobile-nav-search,
body.gx-marketing-home > .header-mobile-container,
body.gx-marketing-home > #overlay_bg {
    display: none !important;
}

.gx-nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 100;
    background: rgba(255,255,255,0.92);
    backdrop-filter: saturate(180%) blur(16px);
    -webkit-backdrop-filter: saturate(180%) blur(16px);
    border-bottom: 1px solid transparent;
    transition: border-color 0.3s ease;
}

.gx-nav.is-scrolled {
    border-bottom-color: var(--gx-border);
}

.gx-nav-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 64px;
    max-width: var(--gx-max);
    margin: 0 auto;
    padding: 0 24px;
}

.gx-nav-brand img {
    display: block;
    height: 28px;
    width: auto;
    object-fit: contain;
}

.gx-nav-links {
    display: flex;
    align-items: center;
    gap: 2px;
}

.gx-nav-menu-extra {
    display: none;
}

.gx-nav-link {
    padding: 6px 14px;
    font-family: var(--gx-font-heading);
    font-size: 14px;
    font-weight: 500;
    color: var(--gx-text-secondary);
    border-radius: var(--gx-radius);
    transition: color 0.2s ease;
}

.gx-nav-link:hover {
    color: var(--gx-navy);
}

.gx-nav-right {
    display: flex;
    align-items: center;
    gap: 8px;
}

.gx-nav-toggle {
    display: none;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    padding: 0;
    border: 0;
    border-radius: var(--gx-radius);
    background: transparent;
    cursor: pointer;
    color: var(--gx-navy);
}

.gx-nav-toggle svg {
    width: 22px;
    height: 22px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
}

.gx-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 40px;
    padding: 8px 20px;
    font-family: var(--gx-font-heading);
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    text-align: center;
    line-height: 1.2;
    border-radius: 0;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s var(--gx-ease);
    /* Long labels wrap instead of overflowing narrow grid cells. */
    white-space: normal;
    word-break: break-word;
    max-width: 100%;
}

.gx-btn:hover {
    box-shadow: var(--gx-shadow-card-hover);
    transform: translate(-2px, -2px);
}

.gx-btn:active {
    transform: scale(0.96);
    box-shadow: none;
}

.gx-btn svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

.gx-btn-primary {
    background: var(--gx-navy);
    color: var(--gx-gold-soft);
}

.gx-btn-primary:hover {
    background: var(--gx-navy-deep);
    color: var(--gx-gold-soft);
}

/* Contextual inversion: when a primary button sits inside a navy surface
   (lead aside, CTA block, callout), it would blend in. Flip to a gold
   button with navy text so it pops as the dominant call to action. */
.gx-section-navy .gx-btn-primary,
.gx-cta-block .gx-btn-primary,
.gx-lead-aside .gx-btn-primary,
.gx-consorcio-callout .gx-btn-primary {
    background: var(--gx-gold);
    color: var(--gx-navy-deep);
    border-color: var(--gx-gold);
}

.gx-section-navy .gx-btn-primary:hover,
.gx-cta-block .gx-btn-primary:hover,
.gx-lead-aside .gx-btn-primary:hover,
.gx-consorcio-callout .gx-btn-primary:hover {
    background: var(--gx-gold-soft);
    color: var(--gx-navy-deep);
    border-color: var(--gx-gold-soft);
}

.gx-btn-secondary {
    background: var(--gx-gold-soft);
    color: var(--gx-navy);
}

.gx-btn-secondary:hover {
    background: var(--gx-gold);
    color: var(--gx-navy-deep);
}

.gx-btn-ghost {
    background: transparent;
    color: var(--gx-navy);
    border: 1px solid var(--gx-border);
}

.gx-btn-ghost:hover {
    border-color: var(--gx-navy-15);
    background: var(--gx-navy-04);
    color: var(--gx-navy);
}

.gx-btn-whatsapp {
    background: #1f9d55;
    color: #FFFFFF;
}

.gx-btn-whatsapp:hover {
    background: #188348;
    color: #FFFFFF;
}

.gx-btn-lg {
    height: 48px;
    padding: 0 28px;
    font-size: 15px;
}

.gx-hero {
    position: relative;
    padding: 140px 0 80px;
    background: var(--gx-bg);
    overflow: hidden;
}

.gx-hero::before {
    content: "GXC";
    position: absolute;
    top: 64px;
    right: -40px;
    font-family: var(--gx-font-heading);
    font-size: clamp(180px, 22vw, 260px);
    font-weight: 900;
    letter-spacing: -0.06em;
    line-height: 0.8;
    color: var(--gx-navy);
    opacity: 0.04;
    pointer-events: none;
    user-select: none;
    white-space: nowrap;
    z-index: 0;
}

.gx-hero-inner {
    position: relative;
    z-index: 1;
}

.gx-hero-inner {
    display: grid;
    gap: 48px;
    max-width: var(--gx-max);
    margin: 0 auto;
    padding: 0 24px;
}

.gx-hero-content {
    max-width: 620px;
}

.gx-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    height: 28px;
    padding: 0 12px;
    margin-bottom: 24px;
    border-radius: var(--gx-radius);
    background: var(--gx-gold-light);
    font-family: var(--gx-font-heading);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--gx-gold-hover);
}

.gx-hero-badge-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--gx-gold);
}

.gx-hero-title {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: clamp(36px, 6vw, 64px);
    font-weight: 900;
    line-height: 0.94;
    letter-spacing: -0.04em;
    text-transform: uppercase;
    color: var(--gx-navy);
}

.gx-hero-sub {
    margin: 20px 0 0;
    max-width: 480px;
    font-size: 16px;
    line-height: 1.7;
    color: var(--gx-text-secondary);
}

.gx-hero-cta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
    margin-top: 32px;
}

.gx-hero-aside {
    display: grid;
    gap: 16px;
    align-content: start;
}

.gx-hero-visual-card {
    position: relative;
    padding: 24px;
    border-radius: 0;
    background: var(--gx-bg);
    border: 1px solid var(--gx-border);
    box-shadow: var(--gx-shadow-md);
}

.gx-hero-visual-card::before,
.gx-hero-visual-card::after {
    content: "";
    position: absolute;
    width: 12px;
    height: 12px;
    border-color: var(--gx-navy);
    pointer-events: none;
}

.gx-hero-visual-card::before {
    top: 0;
    left: 0;
    border-top: 2px solid;
    border-left: 2px solid;
}

.gx-hero-visual-card::after {
    bottom: 0;
    right: 0;
    border-bottom: 2px solid;
    border-right: 2px solid;
}

.gx-visual-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
}

.gx-visual-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: var(--gx-radius);
    background: var(--gx-gold-light);
}

.gx-visual-title {
    font-family: var(--gx-font-heading);
    font-size: 14px;
    font-weight: 600;
    color: var(--gx-navy);
    flex: 1;
}

.gx-visual-badge {
    font-family: var(--gx-font-heading);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--gx-gold-hover);
    padding: 3px 8px;
    border-radius: var(--gx-radius);
    background: var(--gx-gold-light);
}

.gx-visual-pillars {
    position: relative;
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    align-items: end;
    gap: 10px;
    height: 132px;
    margin: 8px 0 28px;
    padding-top: 18px;
}

.gx-pillars-link {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 30px;
    width: 100%;
    pointer-events: none;
}

.gx-pillar {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    height: 100%;
    padding-top: 6px;
}

.gx-pillar-dot {
    position: absolute;
    top: 0;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--gx-navy);
    box-shadow: 0 0 0 3px rgba(12,49,99,0.08);
}

.gx-pillar[data-kind="gold"] .gx-pillar-dot {
    background: var(--gx-gold);
    box-shadow: 0 0 0 3px rgba(201,169,106,0.15);
}

.gx-pillar.is-lead .gx-pillar-dot {
    width: 9px;
    height: 9px;
    background: var(--gx-gold);
    box-shadow: 0 0 0 4px rgba(201,169,106,0.18), 0 0 0 1px #FFFFFF inset;
}

.gx-pillar-fill {
    display: block;
    width: 100%;
    max-width: 28px;
    height: 58%;
    border-radius: 0;
    background: linear-gradient(180deg, rgba(12,49,99,0.28) 0%, rgba(12,49,99,0.08) 100%);
    border-top: 2px solid var(--gx-navy);
}

.gx-pillar[data-kind="gold"] .gx-pillar-fill {
    background: linear-gradient(180deg, rgba(201,169,106,0.55) 0%, rgba(201,169,106,0.18) 100%);
    border-top: 2px solid var(--gx-gold);
}

.gx-pillar.is-lead .gx-pillar-fill {
    height: 78%;
    max-width: 32px;
    background: linear-gradient(180deg, rgba(135,112,74,0.7) 0%, rgba(135,112,74,0.22) 100%);
    border-top: 2px solid var(--gx-gold-hover);
}

.gx-pillar-label {
    margin-top: 8px;
    font-family: var(--gx-font-heading);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--gx-text-secondary);
    white-space: nowrap;
}

.gx-pillar.is-lead .gx-pillar-label {
    color: var(--gx-navy);
}

.gx-visual-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1px;
    margin-top: 24px;
    background: var(--gx-border-light);
    border-radius: var(--gx-radius);
    overflow: hidden;
}

.gx-visual-stat {
    padding: 12px 8px;
    background: var(--gx-bg-cool);
    text-align: center;
}

.gx-visual-stat strong {
    display: block;
    font-family: var(--gx-font-mono);
    font-variant-numeric: tabular-nums;
    font-size: 20px;
    font-weight: 900;
    letter-spacing: -0.02em;
    color: var(--gx-navy);
    line-height: 1;
}

.gx-visual-stat span {
    display: block;
    margin-top: 2px;
    font-size: 11px;
    color: var(--gx-text-tertiary);
}

.gx-hero-stat-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1px;
    background: var(--gx-border);
    border-radius: var(--gx-radius);
    overflow: hidden;
}

.gx-hero-metric {
    padding: 20px 16px;
    background: var(--gx-bg-cool);
    text-align: center;
}

.gx-hero-metric strong {
    display: block;
    font-family: var(--gx-font-mono);
    font-variant-numeric: tabular-nums;
    font-size: 28px;
    font-weight: 900;
    letter-spacing: -0.03em;
    color: var(--gx-navy);
    line-height: 1;
}

.gx-hero-metric span {
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-weight: 700;
}

.gx-hero-metric span {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    color: var(--gx-text-tertiary);
}

.gx-hero-proof {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.gx-hero-proof-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: var(--gx-radius);
    background: var(--gx-bg-cool);
    border: 1px solid var(--gx-border-light);
    font-size: 13px;
    font-weight: 500;
    color: var(--gx-text-secondary);
}

.gx-hero-proof-item::before {
    content: "";
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: var(--gx-gold);
    flex-shrink: 0;
}

.gx-strip {
    border-top: 1px solid rgba(12,49,99,0.1);
    border-bottom: 1px solid rgba(12,49,99,0.1);
    padding: 32px 0;
    background: var(--gx-bg-warm);
}

.gx-strip-inner {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 20px 40px;
    max-width: var(--gx-max);
    margin: 0 auto;
    padding: 0 24px;
}

.gx-strip-item {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-family: var(--gx-font-heading);
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--gx-navy);
}

.gx-strip-item svg {
    flex-shrink: 0;
    width: 18px;
    height: 18px;
    color: var(--gx-gold);
    opacity: 1;
}

.gx-section {
    position: relative;
    padding: 96px 0;
}

.gx-section-alt {
    background: var(--gx-bg-warm);
}

.gx-section-navy {
    background: var(--gx-navy);
    color: #FFFFFF;
}

.gx-section-navy .gx-section-title {
    color: #FFFFFF;
}

.gx-section-navy .gx-section-desc {
    color: rgba(255,255,255,0.65);
}

.gx-section-navy .gx-label {
    color: var(--gx-gold);
}

.gx-section-header {
    max-width: 560px;
    margin-bottom: 56px;
}

.gx-section-header.is-centered {
    margin-left: auto;
    margin-right: auto;
    text-align: center;
}

.gx-section-header.is-split {
    max-width: none;
    display: grid;
    gap: 16px;
}

.gx-label {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
    font-family: var(--gx-font-heading);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--gx-gold-hover);
}

.gx-label::before {
    content: "";
    width: 32px;
    height: 2px;
    background: var(--gx-navy);
}

.gx-section-title {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: clamp(28px, 4vw, 44px);
    font-weight: 900;
    line-height: 1.05;
    letter-spacing: -0.03em;
    text-transform: uppercase;
    color: var(--gx-navy);
}

.gx-section-desc {
    margin: 14px 0 0;
    font-size: 15px;
    line-height: 1.7;
    color: var(--gx-text-secondary);
}

.gx-divider {
    position: relative;
    height: 11px;
    max-width: var(--gx-max);
    margin: 0 auto;
    background:
        linear-gradient(to right, rgba(12,49,99,0.14), rgba(12,49,99,0.14)) left center / calc(50% - 42px) 1px no-repeat,
        linear-gradient(to right, rgba(12,49,99,0.14), rgba(12,49,99,0.14)) right center / calc(50% - 42px) 1px no-repeat;
}

.gx-divider::before,
.gx-divider::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    pointer-events: none;
}

.gx-divider::before {
    width: 11px;
    height: 11px;
    border: 1px solid var(--gx-gold);
    transform: translate(-50%, -50%) rotate(45deg);
    background: transparent;
}

.gx-divider::after {
    width: 3px;
    height: 3px;
    background: var(--gx-gold);
    transform: translate(-50%, -50%) rotate(45deg);
}

.gx-grid-3 {
    display: grid;
    gap: 1px;
    background: var(--gx-border);
    border-radius: var(--gx-radius);
    overflow: hidden;
    border: 1px solid var(--gx-border);
}

.gx-grid-5 {
    display: grid;
    gap: 1px;
    background: var(--gx-border);
    border-radius: var(--gx-radius);
    overflow: hidden;
    border: 1px solid var(--gx-border);
}

.gx-card {
    position: relative;
    display: flex;
    flex-direction: column;
    padding: 32px 24px;
    background: var(--gx-bg);
    transition: background-color 0.25s ease, border-color 0.25s ease;
}

.gx-card::before,
.gx-card::after {
    content: "";
    position: absolute;
    width: 12px;
    height: 12px;
    border-color: transparent;
    transition: border-color 0.25s ease;
    pointer-events: none;
}

.gx-card::before {
    top: 0;
    left: 0;
    border-top: 2px solid;
    border-left: 2px solid;
}

.gx-card::after {
    bottom: 0;
    right: 0;
    border-bottom: 2px solid;
    border-right: 2px solid;
}

.gx-card:hover {
    background: var(--gx-bg-warm);
}

.gx-card:hover::before,
.gx-card:hover::after {
    border-color: var(--gx-navy);
}

.gx-card-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    margin-bottom: 16px;
    border-radius: var(--gx-radius);
    background: var(--gx-navy);
    color: #FFFFFF;
    font-family: var(--gx-font-heading);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
}

.gx-card-label {
    margin: 0 0 6px;
    font-family: var(--gx-font-heading);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--gx-gold-hover);
}

.gx-card-title {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: 16px;
    font-weight: 700;
    line-height: 1.3;
    color: var(--gx-navy);
}

.gx-card-desc {
    margin: 8px 0 0;
    font-size: 14px;
    line-height: 1.6;
    color: var(--gx-text-secondary);
}

.gx-card-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: auto;
    padding-top: 16px;
    font-family: var(--gx-font-heading);
    font-size: 13px;
    font-weight: 600;
    color: var(--gx-gold-hover);
    transition: gap 0.2s ease;
}

.gx-card-link:hover {
    gap: 8px;
    color: var(--gx-gold);
}

.gx-card-link::after {
    content: "\2192";
}

.gx-process-grid {
    display: grid;
    gap: 24px;
}

.gx-process-card {
    position: relative;
    padding: 36px 28px;
    border-radius: var(--gx-radius);
    background: var(--gx-bg);
    border: 1px solid var(--gx-border);
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.gx-process-card:hover {
    border-color: var(--gx-navy-15);
    box-shadow: 0 2px 12px rgba(12,49,99,0.06);
}

.gx-process-num {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    margin-bottom: 16px;
    border-radius: var(--gx-radius);
    background: var(--gx-gold-light);
    color: var(--gx-gold-hover);
    font-family: var(--gx-font-heading);
    font-size: 13px;
    font-weight: 700;
}

.gx-process-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    margin-bottom: 16px;
    border-radius: var(--gx-radius);
    background: var(--gx-navy);
    color: #FFFFFF;
}

.gx-process-title {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: 18px;
    font-weight: 700;
    color: var(--gx-navy);
}

.gx-process-desc {
    margin: 8px 0 0;
    font-size: 14px;
    line-height: 1.7;
    color: var(--gx-text-secondary);
}

.gx-simulator-grid,
.gx-sim-grid {
    display: grid;
    gap: 16px;
}

.gx-simulator-card {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 24px 20px;
    border-radius: var(--gx-radius);
    background: var(--gx-bg);
    border: 1px solid var(--gx-border);
    transition: border-color 0.25s ease, box-shadow 0.25s ease;
}

.gx-simulator-card:hover {
    border-color: var(--gx-navy-15);
    box-shadow: 0 2px 12px rgba(12,49,99,0.06);
}

.gx-simulator-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.gx-simulator-mark {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 32px;
    padding: 0 10px;
    border-radius: var(--gx-radius);
    background: var(--gx-navy);
    color: #FFFFFF;
    font-family: var(--gx-font-heading);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.06em;
}

.gx-legacy-pill {
    height: 24px;
    padding: 0 8px;
    border-radius: var(--gx-radius);
    display: inline-flex;
    align-items: center;
    font-size: 11px;
    font-weight: 500;
    color: var(--gx-text-tertiary);
    background: var(--gx-bg-cool);
    border: 1px solid var(--gx-border-light);
}

.gx-simulator-title {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: 16px;
    font-weight: 700;
    color: var(--gx-navy);
}

.gx-card-kicker {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--gx-gold-hover);
}

.gx-simulator-meta {
    font-size: 14px;
    line-height: 1.6;
    color: var(--gx-text-secondary);
}

.gx-simulator-footer {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: auto;
}

.gx-simulator-path {
    display: inline-flex;
    align-items: center;
    height: 24px;
    padding: 0 8px;
    border-radius: 0;
    font-family: var(--gx-font-mono);
    font-variant-numeric: tabular-nums;
    font-size: 12px;
    color: var(--gx-text-tertiary);
    background: var(--gx-bg-cool);
}

.gx-text-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 0;
    border: 0;
    background: none;
    cursor: pointer;
    font-family: var(--gx-font-heading);
    font-size: 13px;
    font-weight: 600;
    color: var(--gx-gold-hover);
    transition: gap 0.2s ease;
}

.gx-text-link:hover {
    gap: 8px;
    color: var(--gx-gold);
}

.gx-text-link::after {
    content: "\2192";
}

.gx-blog-grid {
    display: grid;
    gap: 24px;
}

.gx-blog-card {
    display: flex;
    flex-direction: column;
    border-radius: var(--gx-radius);
    background: var(--gx-bg);
    border: 1px solid var(--gx-border);
    overflow: hidden;
    transition: border-color 0.25s ease, box-shadow 0.25s ease;
}

.gx-blog-card:hover {
    border-color: var(--gx-navy-15);
    box-shadow: 0 2px 12px rgba(12,49,99,0.06);
}

.gx-blog-image {
    display: block;
    aspect-ratio: 16 / 9;
    background: var(--gx-bg-cool);
    overflow: hidden;
}

.gx-blog-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.5s var(--gx-ease);
}

.gx-blog-card:hover .gx-blog-image img {
    transform: scale(1.03);
}

.gx-blog-body {
    display: flex;
    flex: 1 1 auto;
    flex-direction: column;
    gap: 8px;
    padding: 20px;
}

.gx-blog-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.gx-blog-kicker {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: var(--gx-font-heading);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--gx-text-tertiary);
}

.gx-blog-kicker::before {
    content: "";
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--gx-badge, var(--gx-gold));
}

.gx-blog-label {
    display: inline-flex;
    align-items: center;
    height: 20px;
    padding: 0 6px;
    border-radius: var(--gx-radius);
    background: var(--gx-gold-light);
    color: var(--gx-gold-hover);
    font-family: var(--gx-font-heading);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.gx-blog-title {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: 16px;
    font-weight: 700;
    line-height: 1.35;
}

.gx-blog-title a {
    color: var(--gx-navy);
    transition: color 0.2s ease;
}

.gx-blog-title a:hover {
    color: var(--gx-gold-hover);
}

.gx-blog-meta {
    font-size: 13px;
    color: var(--gx-text-tertiary);
}

.gx-blog-meta a {
    color: inherit;
}

.gx-post-meta-list {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
}

.gx-post-meta-link {
    color: var(--gx-navy);
    font-weight: 600;
}

.gx-post-meta-link:hover {
    color: var(--gx-gold-hover);
}

.gx-post-meta-sep {
    color: rgba(12,49,99,0.28);
}

.gx-blog-summary {
    margin: 0;
    font-size: 14px;
    line-height: 1.6;
    color: var(--gx-text-secondary);
}

.gx-blog-card-featured .gx-blog-title {
    font-size: clamp(18px, 2.5vw, 24px);
    letter-spacing: -0.02em;
}

.gx-cta-block {
    position: relative;
    padding: 72px 40px;
    border-radius: 0;
    background: linear-gradient(180deg, var(--gx-navy) 0%, var(--gx-navy-deep) 100%);
    border-bottom: 4px solid var(--gx-gold);
    text-align: center;
    overflow: hidden;
}

.gx-cta-block::before {
    content: "GXC";
    position: absolute;
    top: -32px;
    right: -16px;
    font-family: var(--gx-font-heading);
    font-size: 220px;
    font-weight: 900;
    letter-spacing: -0.06em;
    line-height: 0.8;
    color: #ffffff;
    opacity: 0.05;
    pointer-events: none;
    user-select: none;
    white-space: nowrap;
    z-index: 1;
}

.gx-cta-content {
    position: relative;
    z-index: 2;
    max-width: 520px;
    margin: 0 auto;
}

.gx-cta-block .gx-label {
    color: var(--gx-gold);
}

.gx-cta-block .gx-section-title {
    color: #FFFFFF;
}

.gx-cta-block .gx-section-desc {
    color: rgba(255,255,255,0.6);
}

.gx-cta-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-top: 28px;
}

.gx-cta-block .gx-btn-ghost {
    color: rgba(255,255,255,0.85);
    border-color: rgba(255,255,255,0.2);
}

.gx-cta-block .gx-btn-ghost:hover {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.3);
    color: #FFFFFF;
}

.gx-cta-icon {
    margin-bottom: 20px;
    opacity: 0.6;
}

.gx-lead-section {
    padding: 96px 0 64px;
}

.gx-lead-grid {
    display: grid;
    gap: 24px;
}

.gx-lead-aside {
    position: relative;
    overflow: hidden;
    padding: 40px 32px;
    border-radius: 0;
    background: linear-gradient(180deg, var(--gx-navy) 0%, var(--gx-navy-deep) 100%);
    border-bottom: 4px solid var(--gx-gold);
    color: #ffffff;
}

.gx-lead-aside::before {
    content: "GXC";
    position: absolute;
    top: -32px;
    right: -16px;
    font-family: var(--gx-font-heading);
    font-size: 220px;
    font-weight: 900;
    letter-spacing: -0.06em;
    line-height: 0.8;
    color: #ffffff;
    opacity: 0.05;
    pointer-events: none;
    user-select: none;
    white-space: nowrap;
    z-index: 0;
}

.gx-lead-aside > * {
    position: relative;
    z-index: 1;
}

.gx-aside-icon {
    margin-bottom: 16px;
}

.gx-lead-aside .gx-label {
    color: var(--gx-gold);
}

.gx-lead-aside .gx-section-title {
    color: #FFFFFF;
}

.gx-lead-aside .gx-section-desc,
.gx-lead-aside .gx-section-text {
    color: rgba(255,255,255,0.6);
}

.gx-contact-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 24px;
}

/* Default (light-context): used inside white cards like .gx-lead-card. */
.gx-contact-highlight {
    display: grid;
    gap: 12px;
    margin-top: 26px;
    padding: 20px;
    border-radius: 0;
    background: var(--gx-gold-light);
    border: 1px solid rgba(201,169,106,0.28);
    border-left: 2px solid var(--gx-gold);
}

.gx-contact-highlight strong {
    color: var(--gx-navy-deep);
    font-family: var(--gx-font-heading);
    font-size: 22px;
    font-weight: 800;
    line-height: 1.2;
}

.gx-contact-highlight p {
    margin: 0;
    color: var(--gx-text-secondary);
    font-size: 14px;
    line-height: 1.65;
}

/* Dark-context override: when the highlight lives inside a navy surface
   (lead aside, navy section), flip text to white on a translucent panel. */
.gx-lead-aside .gx-contact-highlight,
.gx-section-navy .gx-contact-highlight {
    background: rgba(255,255,255,0.06);
    border-color: rgba(255,255,255,0.18);
    border-left-color: var(--gx-gold);
}

.gx-lead-aside .gx-contact-highlight strong,
.gx-section-navy .gx-contact-highlight strong {
    color: #ffffff;
}

.gx-lead-aside .gx-contact-highlight p,
.gx-section-navy .gx-contact-highlight p {
    color: rgba(255,255,255,0.72);
}

.gx-contact-cta-grid {
    display: grid;
    gap: 10px;
}

.gx-contact-note {
    margin: 0;
    color: var(--gx-text-tertiary);
    font-size: 13px;
    line-height: 1.6;
}

.gx-lead-aside .gx-contact-note,
.gx-section-navy .gx-contact-note {
    color: rgba(255,255,255,0.72);
}

.gx-contact-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    height: 36px;
    padding: 0 14px;
    border-radius: var(--gx-radius);
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.15);
    color: rgba(255,255,255,0.8);
    font-size: 14px;
    font-weight: 500;
    transition: background-color 0.2s ease;
}

.gx-contact-chip svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    opacity: 0.7;
}

.gx-contact-chip:hover {
    background: rgba(255,255,255,0.18);
    color: #FFFFFF;
}

.gx-lead-card {
    padding: 40px 32px;
    border-radius: var(--gx-radius);
    background: var(--gx-bg);
    border: 1px solid var(--gx-border);
}

.gx-form-shell {
    display: grid;
    gap: 24px;
}

.gx-form-intro {
    display: grid;
    gap: 6px;
}

.gx-form-title {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: 20px;
    font-weight: 700;
    color: var(--gx-navy);
}

.gx-form-copy {
    margin: 0;
    font-size: 14px;
    line-height: 1.6;
    color: var(--gx-text-secondary);
}

.gx-form-grid {
    display: grid;
    gap: 16px;
}

.gx-form-field {
    display: grid;
    gap: 4px;
}

.gx-form-field label {
    font-family: var(--gx-font-heading);
    font-size: 13px;
    font-weight: 500;
    color: var(--gx-text-secondary);
}

.gx-form-field input,
.gx-form-field select,
.gx-form-field textarea {
    width: 100%;
    height: 44px;
    padding: 10px 14px;
    border-radius: var(--gx-radius);
    border: 1px solid var(--gx-border);
    background: var(--gx-bg);
    color: var(--gx-text);
    font-family: var(--gx-font-body);
    font-size: 15px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.gx-form-field select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6' fill='none'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%23606E7C' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-position: right 14px center;
    background-repeat: no-repeat;
    background-size: 10px 6px;
    padding-right: 36px;
}

.gx-form-field textarea {
    height: auto;
    min-height: 100px;
    resize: vertical;
}

.gx-form-field input:focus,
.gx-form-field select:focus,
.gx-form-field textarea:focus {
    outline: 0;
    border-color: var(--gx-navy-50);
    box-shadow: 0 0 0 3px var(--gx-navy-08);
}

.gx-form-field input::placeholder,
.gx-form-field textarea::placeholder {
    color: var(--gx-text-tertiary);
}

.gx-check {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 13px;
    color: var(--gx-text-secondary);
}

.gx-check input {
    margin-top: 2px;
    width: 16px;
    height: 16px;
    flex: 0 0 auto;
    accent-color: var(--gx-gold);
}

.gx-check a {
    color: var(--gx-gold-hover);
    font-weight: 600;
}

.gx-captcha-row {
    min-height: 82px;
}

.gx-captcha-shell {
    display: flex;
    align-items: center;
    width: 100%;
    min-height: 78px;
}

.gx-captcha-placeholder {
    display: flex;
    align-items: center;
    width: 100%;
    min-height: 78px;
    padding: 12px 14px;
    border: 1px dashed rgba(12,49,99,0.18);
    background: var(--gx-bg-cool);
    color: var(--gx-text-secondary);
    font-size: 13px;
    line-height: 1.6;
}

.gx-captcha-shell.is-loading .gx-captcha-placeholder {
    background: linear-gradient(90deg, rgba(12,49,99,0.05) 0%, rgba(12,49,99,0.09) 50%, rgba(12,49,99,0.05) 100%);
    background-size: 200% 100%;
    animation: gxCaptchaPulse 1.2s linear infinite;
}

@keyframes gxCaptchaPulse {
    from {
        background-position: 200% 0;
    }
    to {
        background-position: -200% 0;
    }
}

.gx-form-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
}

.gx-form-submit {
    width: 100%;
}

.gx-form-note {
    margin: 0;
    font-size: 12px;
    color: var(--gx-text-tertiary);
}

.gx-hp {
    position: absolute;
    left: -9999px;
    width: 1px;
    height: 1px;
    opacity: 0;
    pointer-events: none;
}

.gx-empty-state {
    padding: 28px;
    border-radius: var(--gx-radius);
    background: var(--gx-bg-cool);
    border: 1px solid var(--gx-border);
    color: var(--gx-text-secondary);
    text-align: center;
    font-size: 15px;
}

.m-b-15 {
    margin-bottom: 15px;
}

.alert {
    padding: 12px 14px;
    border: 1px solid transparent;
    border-radius: var(--gx-radius);
    font-size: 14px;
    line-height: 1.6;
}

.alert ul {
    margin: 0;
    padding-left: 18px;
}

.alert-danger {
    border-color: #f2c5bf;
    background: #fff1f0;
    color: #8a2d22;
}

.alert-success {
    border-color: #bde0c3;
    background: #eef9f0;
    color: #166534;
}

.alert svg {
    vertical-align: middle;
}

.display-flex {
    display: flex;
    align-items: flex-start;
}

.gx-site-footer {
    position: relative;
    padding: 56px 0 28px;
    background: linear-gradient(180deg, #051a30 0%, #071f39 100%);
    color: rgba(255,255,255,0.82);
}

.gx-footer-grid {
    display: grid;
    gap: 28px;
}

.gx-footer-column {
    display: grid;
    gap: 16px;
}

.gx-footer-logo img {
    display: block;
    width: auto;
    height: 32px;
}

.gx-footer-copy {
    margin: 0;
    max-width: 44ch;
    color: rgba(255,255,255,0.58);
    font-size: 14px;
    line-height: 1.7;
}

.gx-footer-title {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--gx-gold);
}

.gx-footer-links,
.gx-social-links,
.gx-footer-contact {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.gx-footer-link,
.gx-social-link,
.gx-footer-chip {
    display: inline-flex;
    align-items: center;
    min-height: 36px;
    padding: 0 14px;
    border: 1px solid rgba(255,255,255,0.12);
    background: rgba(255,255,255,0.04);
    color: rgba(255,255,255,0.82);
    font-family: var(--gx-font-heading);
    font-size: 13px;
    font-weight: 600;
    transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
}

.gx-footer-link:hover,
.gx-social-link:hover,
.gx-footer-chip:hover {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.2);
    color: #FFFFFF;
    transform: translateY(-1px);
}

.gx-footer-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-top: 36px;
    padding-top: 22px;
    border-top: 1px solid rgba(255,255,255,0.12);
}

.gx-footer-bottom p {
    margin: 0;
    color: rgba(255,255,255,0.5);
    font-size: 13px;
}

.gx-scrollup {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: var(--gx-font-heading);
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.82);
}

.gx-scrollup::after {
    content: "\2191";
}

.gx-cookie-banner {
    position: fixed;
    right: 20px;
    bottom: 20px;
    z-index: 130;
    display: grid;
    gap: 14px;
    width: min(420px, calc(100vw - 32px));
    padding: 18px;
    background: rgba(6,26,48,0.96);
    color: rgba(255,255,255,0.82);
    border: 1px solid rgba(255,255,255,0.1);
    box-shadow: var(--gx-shadow-lg);
    backdrop-filter: blur(12px);
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.gx-cookie-banner.is-hidden {
    opacity: 0;
    transform: translateY(12px);
    pointer-events: none;
}

.gx-cookie-copy,
.gx-cookie-copy p {
    margin: 0;
    font-size: 13px;
    line-height: 1.7;
}

.gx-cookie-copy a {
    color: var(--gx-gold);
}

.gx-modal-backdrop[hidden] {
    display: none;
}

.gx-modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 140;
    display: grid;
    place-items: center;
    padding: 20px;
    background: rgba(0,18,38,0.56);
    backdrop-filter: blur(10px);
}

.gx-modal-dialog {
    position: relative;
    width: min(460px, 100%);
    max-height: calc(100vh - 40px);
    padding: 28px 24px 24px;
    background: #FFFFFF;
    border: 1px solid rgba(12,49,99,0.12);
    box-shadow: var(--gx-shadow-lg);
    overflow: auto;
}

.gx-modal-header {
    display: grid;
    gap: 8px;
    margin-bottom: 20px;
}

.gx-modal-header .gx-label {
    margin-bottom: 0;
}

.gx-auth-close {
    position: absolute;
    top: 12px;
    right: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    padding: 0;
    border: 0;
    background: transparent;
    color: var(--gx-navy);
    font-size: 26px;
    line-height: 1;
    cursor: pointer;
}

.gx-auth-form {
    display: grid;
    gap: 16px;
}

.gx-auth-links {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.gx-auth-links a {
    color: var(--gx-gold-hover);
    font-family: var(--gx-font-heading);
    font-size: 13px;
    font-weight: 600;
}

[data-gx-reveal] {
    opacity: 0;
    transform: translateY(24px);
    transition: opacity 0.7s var(--gx-ease), transform 0.7s var(--gx-ease);
}

[data-gx-reveal].is-visible {
    opacity: 1;
    transform: translateY(0);
}

body.gx-marketing-home #footer {
    margin-top: 0;
}

body.gx-marketing-home #footer,
body.gx-marketing-home #footer .footer-inner,
body.gx-marketing-home #footer > .container {
    background: var(--gx-navy);
    color: #FFFFFF;
}

body.gx-marketing-home #footer .widget-title,
body.gx-marketing-home #footer .title,
body.gx-marketing-home #footer h4,
body.gx-marketing-home #footer h5,
body.gx-marketing-home #footer a,
body.gx-marketing-home #footer p,
body.gx-marketing-home #footer li {
    color: rgba(255,255,255,0.8);
}

body.gx-marketing-home #footer .footer-about,
body.gx-marketing-home #footer .description,
body.gx-marketing-home #footer .copyright,
body.gx-marketing-home #footer .nav-footer a,
body.gx-marketing-home #footer .footer-widget p,
body.gx-marketing-home #footer .f-random-list .title a {
    color: rgba(255,255,255,0.5);
}

body.gx-marketing-home #footer .footer-copyright,
body.gx-marketing-home #footer .footer-bottom {
    background: transparent;
    border-top: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.4);
}

body.gx-marketing-home #footer .newsletter-input,
body.gx-marketing-home #footer .newsletter-inputs .form-input {
    border-radius: var(--gx-radius);
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.15);
    color: #fff;
}

body.gx-marketing-home #footer .newsletter-button,
body.gx-marketing-home #footer .btn-custom {
    background: var(--gx-gold);
    border-color: transparent;
    color: #FFF;
    border-radius: var(--gx-radius);
}

@media (min-width: 640px) {
    .gx-form-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .gx-form-field-full,
    .gx-check,
    .gx-captcha-row,
    .gx-form-actions {
        grid-column: 1 / -1;
    }
}

@media (min-width: 768px) {
    .gx-hero {
        padding: 140px 0 96px;
    }

    .gx-hero-inner {
        grid-template-columns: 1.1fr 0.9fr;
        align-items: center;
        gap: 56px;
    }

    .gx-grid-3 {
        grid-template-columns: repeat(3, 1fr);
    }

    .gx-grid-5 {
        grid-template-columns: repeat(2, 1fr);
    }

    .gx-process-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .gx-simulator-grid,
    .gx-sim-grid,
    .gx-blog-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .gx-contact-cta-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .gx-blog-card-featured {
        grid-column: 1 / -1;
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: stretch;
    }

    .gx-blog-card-featured .gx-blog-image {
        aspect-ratio: auto;
        min-height: 100%;
    }

    .gx-footer-grid {
        grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr) minmax(0, 1fr);
    }
}

@media (min-width: 992px) {
    .gx-lead-grid {
        grid-template-columns: minmax(280px, 0.85fr) minmax(0, 1.15fr);
        align-items: start;
    }

    .gx-simulator-grid,
    .gx-sim-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .gx-section-header.is-split {
        grid-template-columns: 1fr 1fr;
        align-items: end;
        gap: 48px;
    }
}

@media (min-width: 1200px) {
    .gx-grid-5 {
        grid-template-columns: repeat(5, 1fr);
    }
}

@media (max-width: 991px) {
    .gx-nav-toggle {
        display: inline-flex;
    }

    .gx-nav-right > .gx-nav-link,
    .gx-nav-right > .gx-btn {
        display: none;
    }

    .gx-nav-links {
        position: absolute;
        top: calc(100% + 4px);
        left: 12px;
        right: 12px;
        display: none;
        flex-direction: column;
        padding: 12px;
        border-radius: var(--gx-radius-lg);
        background: #FFFFFF;
        border: 1px solid var(--gx-border);
        box-shadow: 0 8px 32px rgba(12,49,99,0.08);
    }

    .gx-nav.is-open .gx-nav-links {
        display: flex;
    }

    .gx-nav-link {
        width: 100%;
        padding: 10px 8px;
    }

    .gx-nav-menu-extra {
        display: grid;
        gap: 8px;
        margin-top: 8px;
        padding-top: 12px;
        border-top: 1px solid var(--gx-border-light);
    }

    .gx-nav-menu-extra .gx-btn {
        width: 100%;
    }

    .gx-section {
        padding: 64px 0;
    }
}

@media (max-width: 767px) {
    .gx-hero {
        padding: 100px 0 48px;
    }

    .gx-hero-cta {
        flex-direction: column;
    }

    .gx-hero-cta .gx-btn {
        width: 100%;
    }

    .gx-hero-stat-row {
        grid-template-columns: 1fr;
    }

    .gx-process-grid {
        grid-template-columns: 1fr;
    }

    .gx-lead-aside,
    .gx-lead-card {
        padding: 28px 20px;
    }

    .gx-cta-block {
        padding: 40px 20px;
    }

    .gx-cta-actions {
        flex-direction: column;
    }

    .gx-cta-actions .gx-btn {
        width: 100%;
    }

    .gx-footer-bottom,
    .gx-auth-links {
        flex-direction: column;
        align-items: flex-start;
    }

    .gx-cookie-banner {
        right: 16px;
        left: 16px;
        width: auto;
        bottom: 16px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .gx-marketing *,
    .gx-marketing *::before,
    .gx-marketing *::after {
        animation: none !important;
        transition: none !important;
    }
}

.gx-home {
    position: relative;
    overflow: hidden;
    background: var(--gx-bg);
}

.gx-home::before,
.gx-home::after {
    content: "";
    position: absolute;
    pointer-events: none;
}

.gx-home::before {
    top: 0;
    left: 0;
    right: 0;
    height: 560px;
    background:
        linear-gradient(180deg, var(--gx-bg-warm) 0%, transparent 100%),
        linear-gradient(135deg, rgba(12,49,99,0.05) 0%, transparent 50%);
}

.gx-home::after {
    top: -24px;
    left: 58%;
    width: 40vw;
    min-width: 320px;
    height: 460px;
    background: repeating-linear-gradient(90deg, rgba(12,49,99,0.06) 0, rgba(12,49,99,0.06) 1px, transparent 1px, transparent 28px);
    mask-image: linear-gradient(180deg, rgba(0,0,0,0.4), transparent 100%);
    opacity: 0.4;
}

.gx-home .gx-nav.is-scrolled {
    background: rgba(255,255,255,0.96);
    box-shadow: 0 1px 0 0 var(--gx-border);
}

.gx-home .gx-nav-link:hover {
    background: var(--gx-navy-04);
}

.gx-home .gx-btn {
    transition: transform 0.2s var(--gx-ease), box-shadow 0.2s var(--gx-ease), background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
}

.gx-home .gx-hero,
.gx-home .gx-strip,
.gx-home .gx-section,
.gx-home .gx-lead-section {
    position: relative;
    z-index: 1;
}

.gx-home .gx-section,
.gx-home .gx-lead-section {
    content-visibility: auto;
    contain-intrinsic-size: 1px 860px;
}

.gx-home .gx-lead-section {
    contain-intrinsic-size: 1px 980px;
}

.gx-home .gx-hero {
    overflow: hidden;
    padding-bottom: 88px;
}

.gx-home .gx-hero-inner {
    gap: 56px;
    align-items: end;
}

.gx-home .gx-hero-content {
    position: relative;
    max-width: 680px;
}

.gx-home .gx-hero-title {
    max-width: 14ch;
    font-family: var(--gx-font-heading);
    font-size: clamp(40px, 6vw, 72px);
    font-weight: 900;
    line-height: 0.94;
    letter-spacing: -0.04em;
    text-transform: uppercase;
    color: var(--gx-navy-deep);
}

.gx-home .gx-hero-title em,
.gx-home .gx-hero-title i {
    font-style: normal;
    font-family: var(--gx-font-heading);
    font-weight: 900;
    color: var(--gx-gold-hover);
}

.gx-home .gx-hero-sub {
    max-width: 540px;
    margin-top: 22px;
    font-family: var(--gx-font-sans-refined);
    font-size: 18px;
    line-height: 1.55;
    letter-spacing: -0.005em;
    color: var(--gx-navy-70);
}

.gx-home .gx-hero-sub em,
.gx-home .gx-hero-sub i {
    font-family: var(--gx-font-heading);
    font-style: normal;
    font-weight: 700;
    color: var(--gx-navy-deep);
}

.gx-home .gx-hero-cta {
    margin-top: 36px;
}

.gx-home .gx-hero-proof {
    display: grid;
    gap: 12px;
    margin-top: 32px;
    padding-top: 22px;
    border-top: 1px solid rgba(12,49,99,0.12);
}

.gx-home .gx-hero-proof-item {
    display: grid;
    gap: 4px;
    min-height: 100%;
    padding: 14px 16px;
    background: rgba(255,255,255,0.78);
    border: 1px solid rgba(12,49,99,0.1);
    border-left: 2px solid var(--gx-gold);
    box-shadow: var(--gx-shadow-sm);
}

.gx-home .gx-hero-proof-item::before {
    content: none;
}

.gx-home .gx-hero-proof-item strong {
    font-family: var(--gx-font-heading);
    font-size: 13px;
    font-weight: 700;
    line-height: 1.3;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--gx-navy);
}

.gx-home .gx-hero-proof-item span {
    font-size: 13px;
    line-height: 1.6;
    color: var(--gx-text-secondary);
}

.gx-home .gx-hero-visual-card {
    position: relative;
    overflow: hidden;
    padding: 28px;
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-panel) 100%);
    border-color: rgba(12,49,99,0.12);
    box-shadow: var(--gx-shadow-lg);
}

.gx-home .gx-hero-visual-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        linear-gradient(135deg, rgba(12,49,99,0.05), transparent 52%),
        repeating-linear-gradient(90deg, rgba(12,49,99,0.04) 0, rgba(12,49,99,0.04) 1px, transparent 1px, transparent 30px);
    mask-image: linear-gradient(180deg, rgba(0,0,0,0.5), transparent 88%);
    opacity: 0.7;
    pointer-events: none;
}

.gx-home .gx-hero-visual-card::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--gx-navy), var(--gx-gold));
}

.gx-home .gx-visual-header,
.gx-home .gx-visual-pillars,
.gx-home .gx-visual-row {
    position: relative;
    z-index: 1;
}

.gx-home .gx-visual-header {
    margin-bottom: 24px;
}

.gx-home .gx-visual-title {
    font-size: 15px;
}

.gx-home .gx-visual-row {
    gap: 0;
    background: rgba(12,49,99,0.06);
    border: 1px solid rgba(12,49,99,0.08);
}

.gx-home .gx-visual-stat {
    padding: 14px 10px;
    background: rgba(255,255,255,0.84);
}

.gx-home .gx-hero-stat-row {
    gap: 12px;
    background: none;
    border-radius: 0;
    overflow: visible;
}

.gx-home .gx-hero-metric {
    position: relative;
    padding: 20px 16px 18px;
    background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(246,246,243,0.96));
    border: 1px solid rgba(12,49,99,0.1);
    box-shadow: var(--gx-shadow-sm);
}

.gx-home .gx-hero-metric strong {
    font-family: var(--gx-font-mono);
    font-variant-numeric: tabular-nums;
    font-weight: 900;
    font-size: 32px;
    letter-spacing: -0.03em;
    color: var(--gx-navy-deep);
}

.gx-home .gx-visual-stat strong {
    font-family: var(--gx-font-mono);
    font-variant-numeric: tabular-nums;
    font-weight: 900;
    font-size: 22px;
    letter-spacing: -0.02em;
    color: var(--gx-navy-deep);
}

.gx-home .gx-hero-metric::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--gx-gold), rgba(201,169,106,0));
}

.gx-home .gx-strip {
    position: relative;
    padding: 36px 0;
    background: var(--gx-bg-warm);
    border-top: 1px solid var(--gx-navy-15);
    border-bottom: 1px solid var(--gx-navy-15);
}

.gx-home .gx-strip::before {
    content: "";
    position: absolute;
    top: -1px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 2px;
    background: var(--gx-gold);
    opacity: 0.9;
}

.gx-home .gx-strip-inner {
    justify-content: space-between;
    gap: 18px 28px;
}

.gx-home .gx-strip-lead {
    display: inline-flex;
    align-items: center;
    margin-right: 4px;
    padding-right: 22px;
    border-right: 1px solid rgba(12,49,99,0.18);
    font-family: var(--gx-font-heading);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--gx-navy);
}

.gx-home .gx-strip-item {
    color: var(--gx-navy);
    font-weight: 700;
}

.gx-home .gx-strip-item svg {
    width: 20px;
    height: 20px;
    opacity: 1;
}

.gx-home .gx-section {
    padding: 104px 0;
}

.gx-home .gx-section-alt {
    background: var(--gx-bg-warm);
}

.gx-home .gx-section-header {
    margin-bottom: 48px;
}

.gx-home .gx-section-header.is-centered {
    max-width: 720px;
}

.gx-home .gx-section-title {
    max-width: 22ch;
    font-family: var(--gx-font-heading);
    font-weight: 900;
    font-size: clamp(28px, 4vw, 48px);
    line-height: 1.05;
    letter-spacing: -0.03em;
    text-transform: uppercase;
    color: var(--gx-navy-deep);
}

.gx-home .gx-section-title em,
.gx-home .gx-section-title i {
    font-style: normal;
    font-family: var(--gx-font-heading);
    font-weight: 900;
    color: var(--gx-gold-hover);
}

.gx-home .gx-section-desc {
    font-family: var(--gx-font-body);
    font-size: 15px;
    line-height: 1.6;
    color: var(--gx-navy-70);
}

.gx-home .gx-section-header.is-centered .gx-section-title {
    margin-left: auto;
    margin-right: auto;
}

.gx-home .gx-section-header.is-split .gx-section-title {
    max-width: 14ch;
}

.gx-home .gx-divider {
    max-width: calc(var(--gx-max) - 48px);
    opacity: 1;
}

.gx-home .gx-grid-5 {
    gap: 20px;
    background: none;
    border: 0;
    border-radius: 0;
    overflow: visible;
}

.gx-home .gx-card {
    min-height: 100%;
    padding: 34px 26px 28px;
    background: var(--gx-bg);
    border: 1px solid var(--gx-border);
    border-top: 4px solid var(--gx-card-accent, var(--gx-gold));
    overflow: hidden;
    transition: transform 0.25s var(--gx-ease), background-color 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}

.gx-home .gx-card:hover {
    background: var(--gx-bg-warm);
    border-color: var(--gx-navy-15);
    box-shadow: var(--gx-shadow-md);
}

.gx-home .gx-card:hover::before,
.gx-home .gx-card:hover::after {
    border-color: var(--gx-navy);
}

.gx-home .gx-card-index {
    position: absolute;
    top: 18px;
    right: 22px;
    font-family: var(--gx-font-mono);
    font-variant-numeric: tabular-nums;
    font-size: 32px;
    font-weight: 900;
    line-height: 1;
    letter-spacing: -0.04em;
    color: var(--gx-gold);
    opacity: 0.75;
}

.gx-home .gx-card-icon,
.gx-home .gx-card-label,
.gx-home .gx-card-title,
.gx-home .gx-card-desc,
.gx-home .gx-card-link {
    position: relative;
    z-index: 1;
}

.gx-home .gx-card-icon {
    margin-bottom: 18px;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.1);
}

.gx-home .gx-card-title {
    font-family: var(--gx-font-heading);
    font-weight: 800;
    font-size: 22px;
    line-height: 1.15;
    letter-spacing: -0.02em;
    color: var(--gx-navy-deep);
}

.gx-home .gx-card-desc {
    margin-top: 12px;
    max-width: 32ch;
    font-family: var(--gx-font-body);
    font-size: 14px;
    line-height: 1.6;
    color: var(--gx-navy-70);
}

.gx-home .gx-process-card {
    overflow: hidden;
    box-shadow: var(--gx-shadow-sm);
    transition: transform 0.25s var(--gx-ease), border-color 0.3s ease, box-shadow 0.3s ease;
}

.gx-home .gx-process-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--gx-gold), rgba(201,169,106,0));
}

.gx-home .gx-process-card:hover {
    transform: translateY(-4px);
    border-color: rgba(201,169,106,0.35);
    box-shadow: var(--gx-shadow-md);
}

.gx-home .gx-process-num {
    position: absolute;
    top: 22px;
    right: 24px;
    width: auto;
    height: auto;
    margin: 0;
    padding: 0;
    background: none;
    color: var(--gx-gold-etched);
    font-family: var(--gx-font-display);
    font-size: 38px;
    font-weight: 400;
    font-style: italic;
    line-height: 1;
    font-feature-settings: "onum" 1;
    font-variant-numeric: oldstyle-nums;
    opacity: 0.55;
}

.gx-home .gx-process-title,
.gx-home .gx-process-desc,
.gx-home .gx-process-icon {
    position: relative;
    z-index: 1;
}

.gx-home .gx-process-title {
    font-family: var(--gx-font-heading);
    font-weight: 800;
    font-size: 22px;
    line-height: 1.15;
    letter-spacing: -0.02em;
    color: var(--gx-navy-deep);
}

.gx-home .gx-process-desc {
    font-family: var(--gx-font-body);
    font-size: 14px;
    line-height: 1.6;
    color: var(--gx-navy-70);
}

.gx-home #simuladores .gx-simulator-grid {
    gap: 20px;
}

.gx-home #simuladores .gx-simulator-card {
    position: relative;
    overflow: hidden;
    padding: 26px 22px 22px;
    border-color: rgba(12,49,99,0.1);
    box-shadow: var(--gx-shadow-sm);
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-bg-warm) 100%);
    transition: transform 0.25s var(--gx-ease), border-color 0.25s ease, box-shadow 0.25s ease;
}

.gx-home #simuladores .gx-simulator-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--gx-navy), var(--gx-gold));
    opacity: 0.85;
}

.gx-home #simuladores .gx-simulator-card:hover {
    transform: translateY(-4px);
    border-color: rgba(201,169,106,0.35);
    box-shadow: var(--gx-shadow-md);
}

.gx-home #simuladores .gx-simulator-footer {
    padding-top: 10px;
    border-top: 1px solid rgba(12,49,99,0.08);
}

.gx-home .gx-blog-grid {
    gap: 28px;
}

.gx-home .gx-blog-card {
    border: 1px solid var(--gx-border);
    transition: transform 0.25s var(--gx-ease), border-color 0.25s ease, box-shadow 0.25s ease;
}

.gx-home .gx-blog-card:hover {
    border-color: var(--gx-navy-15);
    box-shadow: var(--gx-shadow-md);
}

.gx-home .gx-blog-card-featured {
    border-color: var(--gx-navy-15);
    box-shadow: var(--gx-shadow-md);
}

.gx-home .gx-blog-card-featured .gx-blog-body {
    padding: 28px 28px 30px;
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-bg-warm) 100%);
}

.gx-home .gx-blog-card-featured .gx-blog-label {
    background: var(--gx-navy);
    color: #FFFFFF;
}

.gx-home .gx-blog-card-featured .gx-blog-title {
    font-family: var(--gx-font-heading);
    font-weight: 800;
    font-size: clamp(24px, 3vw, 36px);
    line-height: 1.1;
    letter-spacing: -0.025em;
    max-width: 20ch;
    color: var(--gx-navy-deep);
}

.gx-home .gx-blog-card-featured .gx-blog-summary {
    font-size: 15px;
    line-height: 1.75;
    max-width: 60ch;
}

.gx-home .gx-blog-card:not(.gx-blog-card-featured) .gx-blog-body {
    padding: 22px;
}

.gx-home .gx-press-grid {
    display: grid;
    gap: 24px;
    align-items: stretch;
}

.gx-home .gx-press-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    border: 1px solid var(--gx-border);
    background: var(--gx-bg);
    transition: transform 0.25s var(--gx-ease), border-color 0.25s ease, box-shadow 0.25s ease;
}

.gx-home .gx-press-card:hover {
    border-color: var(--gx-navy-15);
    box-shadow: var(--gx-shadow-md);
}

.gx-home .gx-press-card-featured {
    border-color: var(--gx-navy-15);
    box-shadow: var(--gx-shadow-sm);
}

.gx-home .gx-press-image {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    overflow: hidden;
    aspect-ratio: 4 / 3;
    min-height: 320px;
    padding: 16px;
    background: linear-gradient(180deg, #ebe6dc 0%, var(--gx-vellum) 100%);
    border-bottom: 1px solid rgba(12,49,99,0.08);
}

.gx-home .gx-press-image img {
    display: block;
    width: 100%;
    height: 100%;
    max-height: 100%;
    object-fit: contain;
    object-position: top center;
    background: #FFFFFF;
    box-shadow: 0 12px 28px rgba(12,49,99,0.08);
    transition: transform 0.35s ease;
}

.gx-home .gx-press-card:hover .gx-press-image img {
    transform: scale(1.03);
}

.gx-home .gx-press-body {
    display: flex;
    flex: 1 1 auto;
    flex-direction: column;
    gap: 14px;
    padding: 22px;
}

.gx-home .gx-press-top {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 10px;
}

.gx-home .gx-press-kicker {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    background: rgba(12,49,99,0.06);
    color: var(--gx-navy);
    font-family: var(--gx-font-heading);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.gx-home .gx-press-date {
    align-self: center;
    color: var(--gx-text-secondary);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.gx-home .gx-press-title {
    margin: 0;
    color: var(--gx-navy-deep);
    font-family: var(--gx-font-display);
    font-size: clamp(21px, 2.3vw, 27px);
    font-weight: 400;
    line-height: 1.15;
    letter-spacing: -0.01em;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    overflow: hidden;
}

.gx-home .gx-press-summary {
    margin: 0;
    color: var(--gx-text-secondary);
    font-size: 15px;
    line-height: 1.75;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 4;
    overflow: hidden;
}

.gx-home .gx-press-card .gx-text-link {
    margin-top: auto;
}

.gx-home .gx-partners-grid {
    display: grid;
    gap: 18px;
}

.gx-home .gx-partner-card {
    display: grid;
    align-content: center;
    justify-items: center;
    gap: 16px;
    min-height: 176px;
    padding: 26px 22px;
    border: 1px solid rgba(12,49,99,0.08);
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-ivory) 100%);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.8),
        0 1px 0 0 rgba(201,169,106,0.12),
        var(--gx-shadow-sm);
    text-align: center;
    transition: transform 0.35s var(--gx-ease), border-color 0.35s ease, box-shadow 0.35s ease;
}

.gx-home .gx-partner-card:hover {
    transform: translateY(-4px);
    border-color: rgba(201,169,106,0.4);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.9),
        0 0 0 1px rgba(201,169,106,0.3),
        var(--gx-shadow-md);
}

.gx-home .gx-partner-logo-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 68px;
    width: 100%;
}

.gx-home .gx-partner-logo {
    max-width: 100%;
    max-height: 52px;
    object-fit: contain;
    filter: saturate(0.9) contrast(1.02);
}

.gx-home .gx-partner-name {
    color: var(--gx-navy);
    font-family: var(--gx-font-heading);
    font-size: 14px;
    font-weight: 700;
    line-height: 1.4;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

.gx-home .gx-partner-placeholder {
    color: var(--gx-text-secondary);
    font-family: var(--gx-font-heading);
    font-size: 16px;
    font-weight: 700;
    line-height: 1.3;
}

.gx-home .gx-cta-block {
    padding: 60px 44px;
    border: 1px solid rgba(12,49,99,0.14);
    box-shadow: var(--gx-shadow-lg);
    background: linear-gradient(145deg, #071D35 0%, #0D2947 60%, #173D60 100%);
}

.gx-home .gx-cta-block::after {
    content: "";
    position: absolute;
    inset: 0;
    background:
        linear-gradient(135deg, rgba(255,255,255,0.06), rgba(255,255,255,0) 42%),
        repeating-linear-gradient(90deg, rgba(255,255,255,0.05) 0, rgba(255,255,255,0.05) 1px, transparent 1px, transparent 30px);
    mask-image: linear-gradient(180deg, rgba(0,0,0,0.7), transparent 78%);
    opacity: 0.45;
    pointer-events: none;
}

.gx-home .gx-cta-content {
    position: relative;
    z-index: 2;
    max-width: 680px;
}

.gx-home .gx-lead-section {
    position: relative;
    padding-top: 88px;
    padding-bottom: 84px;
    background: linear-gradient(180deg, #F7F4EC 0%, #FFFFFF 52%);
}

.gx-home .gx-lead-aside {
    position: relative;
    overflow: hidden;
    box-shadow: var(--gx-shadow-md);
    background: linear-gradient(160deg, #08203A 0%, #113153 100%);
}

.gx-home .gx-lead-aside::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--gx-gold), rgba(201,169,106,0));
}

.gx-home .gx-lead-aside::after {
    content: "";
    position: absolute;
    inset: 0;
    background:
        linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0) 40%),
        repeating-linear-gradient(90deg, rgba(255,255,255,0.05) 0, rgba(255,255,255,0.05) 1px, transparent 1px, transparent 24px);
    mask-image: linear-gradient(180deg, rgba(0,0,0,0.6), transparent 90%);
    opacity: 0.35;
    pointer-events: none;
}

.gx-home .gx-aside-icon,
.gx-home .gx-contact-list,
.gx-home .gx-lead-aside .gx-label,
.gx-home .gx-lead-aside .gx-section-title,
.gx-home .gx-lead-aside .gx-section-desc {
    position: relative;
    z-index: 1;
}

.gx-home .gx-contact-chip {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.18);
}

.gx-home .gx-contact-chip:hover {
    background: rgba(255,255,255,0.16);
}

.gx-home .gx-lead-card {
    position: relative;
    overflow: hidden;
    padding: 48px 40px 44px;
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.85),
        0 0 0 1px rgba(201,169,106,0.2),
        var(--gx-shadow-md);
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-ivory) 100%);
}

.gx-home .gx-lead-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--gx-gold-etched), var(--gx-gold));
}

.gx-home .gx-form-shell {
    position: relative;
    z-index: 1;
    gap: 32px;
}

.gx-home .gx-form-title {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: clamp(24px, 2.6vw, 30px);
    line-height: 1.1;
    letter-spacing: -0.01em;
    color: var(--gx-navy-deep);
}

.gx-home .gx-form-title em,
.gx-home .gx-form-title i {
    font-style: italic;
    color: var(--gx-gold-etched);
}

.gx-home .gx-form-copy {
    font-family: var(--gx-font-sans-refined);
    font-size: 15px;
    line-height: 1.6;
    color: var(--gx-navy-70);
}

.gx-home .gx-form-grid {
    gap: 22px;
}

.gx-home .gx-form-field label {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--gx-navy-70);
}

.gx-home .gx-form-field input,
.gx-home .gx-form-field select,
.gx-home .gx-form-field textarea {
    height: auto;
    min-height: 42px;
    padding: 10px 0 8px;
    border: none;
    border-bottom: 1px solid rgba(12,49,99,0.18);
    border-radius: 0;
    background: transparent;
    font-family: var(--gx-font-sans-refined);
    font-size: 16px;
    color: var(--gx-navy-deep);
    transition: border-color 0.25s ease;
}

.gx-home .gx-form-field textarea {
    min-height: 96px;
    padding: 12px 0;
    resize: vertical;
}

.gx-home .gx-form-field input:focus,
.gx-home .gx-form-field select:focus,
.gx-home .gx-form-field textarea:focus {
    outline: none;
    border-bottom-color: var(--gx-gold);
    box-shadow: 0 1px 0 0 var(--gx-gold);
}

.gx-home .gx-form-field input:not(:placeholder-shown),
.gx-home .gx-form-field textarea:not(:placeholder-shown) {
    border-bottom-color: rgba(12,49,99,0.35);
}

.gx-home .gx-form-field select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6' fill='none'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%23C7A053' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 4px center;
    background-size: 10px 6px;
    padding-right: 28px;
}

.gx-home .gx-form-actions {
    gap: 14px;
    padding-top: 8px;
    border-top: 1px dashed rgba(12,49,99,0.12);
}

.gx-home .gx-form-submit {
    height: 54px;
    padding: 0 32px;
    font-family: var(--gx-font-sans-refined);
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    box-shadow: 0 14px 24px rgba(201,169,106,0.18);
}

.gx-home .gx-form-note {
    font-family: var(--gx-font-sans-refined);
    font-style: italic;
    font-size: 13px;
    color: var(--gx-navy-70);
}

@media (min-width: 640px) {
    .gx-home .gx-hero-proof {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (min-width: 768px) {
    .gx-home .gx-process-card:not(:last-child)::after {
        content: "";
        position: absolute;
        top: 48px;
        right: -24px;
        width: 24px;
        height: 1px;
        background: linear-gradient(90deg, rgba(12,49,99,0.22), rgba(12,49,99,0));
    }
}

@media (min-width: 768px) and (max-width: 1199px) {
    .gx-home .gx-card:first-child {
        grid-column: 1 / -1;
    }

    .gx-home .gx-press-card-featured {
        grid-column: auto;
    }

    .gx-home .gx-press-card {
        min-height: 560px;
    }
}

@media (min-width: 992px) {
    .gx-home .gx-press-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .gx-home .gx-press-card-featured {
        grid-row: auto;
    }

    .gx-home .gx-press-card {
        min-height: 590px;
    }

    .gx-home .gx-partners-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .gx-home .gx-cta-block {
        text-align: left;
    }

    .gx-home .gx-cta-content {
        margin: 0;
    }

    .gx-home .gx-cta-actions {
        justify-content: flex-start;
    }
}

@media (min-width: 1200px) {
    .gx-home .gx-grid-5 {
        grid-template-columns: repeat(4, 1fr);
    }

    .gx-home .gx-card:first-child {
        grid-column: span 2;
    }
}

@media (max-width: 991px) {
    .gx-home::after {
        left: 44%;
        width: 58vw;
        opacity: 0.22;
    }

    .gx-home .gx-section {
        padding: 72px 0;
    }

    .gx-home .gx-lead-section {
        padding-top: 72px;
        padding-bottom: 72px;
    }

    .gx-home .gx-strip-lead {
        width: 100%;
        margin-right: 0;
        padding-right: 0;
        padding-bottom: 8px;
        border-right: 0;
        border-bottom: 1px solid rgba(12,49,99,0.12);
    }

    .gx-home .gx-section-title,
    .gx-home .gx-section-header.is-split .gx-section-title,
    .gx-home .gx-blog-card-featured .gx-blog-title {
        max-width: none;
    }

    .gx-home .gx-press-grid,
    .gx-home .gx-partners-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .gx-home .gx-press-image {
        min-height: 280px;
    }
}

@media (max-width: 767px) {
    .gx-home .gx-hero {
        padding-top: 100px;
        padding-bottom: 48px;
    }

    .gx-home .gx-section {
        padding: 64px 0;
    }

    .gx-home .gx-lead-section {
        padding-top: 64px;
        padding-bottom: 64px;
    }

    .gx-home .gx-hero-title {
        max-width: none;
        font-size: clamp(34px, 10vw, 48px);
    }

    .gx-home .gx-hero-proof {
        grid-template-columns: 1fr;
    }

    .gx-home .gx-card-index {
        top: 16px;
        right: 16px;
        font-size: 26px;
    }

    .gx-home .gx-cta-block {
        padding: 44px 20px;
    }

    .gx-home .gx-hero-visual-card {
        padding: 24px 20px;
    }

    .gx-home .gx-press-grid,
    .gx-home .gx-partners-grid {
        grid-template-columns: 1fr;
    }

    .gx-home .gx-press-card {
        min-height: auto;
    }

    .gx-home .gx-press-image {
        min-height: 240px;
        padding: 12px;
    }

    .gx-home .gx-press-body,
    .gx-home .gx-partner-card {
        padding: 20px;
    }
}

.gx-hero-grid {
    display: grid;
    gap: 24px;
}

.gx-hero-copy {
    background: transparent;
    border: 0;
    box-shadow: none;
}

.gx-hero-panel {
    background: transparent;
    border: 0;
    box-shadow: none;
}

.gx-simulators-hub .gx-hero-copy {
    padding: 36px;
    border-radius: var(--gx-radius);
    background: var(--gx-navy);
    color: #fff;
}

.gx-simulators-hub .gx-shell {
    padding: 24px 0 72px;
}

.gx-simulators-hub .gx-hero {
    padding: 48px 0 72px;
}

@media (max-width: 767px) {
    .gx-simulators-hub .gx-hero {
        padding: 32px 0 48px;
    }
}

.gx-simulators-hub .gx-hero-title,
.gx-simulators-hub .gx-hero-copy .gx-label,
.gx-simulators-hub .gx-hero-copy .gx-eyebrow {
    color: #fff;
}

.gx-simulators-hub .gx-hero-copy .gx-label,
.gx-simulators-hub .gx-hero-copy .gx-eyebrow {
    color: var(--gx-gold);
}

.gx-simulators-hub .gx-hero-title {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: clamp(28px, 4vw, 44px);
    line-height: 1.05;
    letter-spacing: -0.015em;
    max-width: 18ch;
}

.gx-simulators-hub .gx-hero-title em,
.gx-simulators-hub .gx-hero-title i {
    font-style: italic;
    color: var(--gx-gold);
}

.gx-simulators-hub .gx-hero-text {
    color: rgba(255,255,255,0.72);
    font-family: var(--gx-font-sans-refined);
    font-size: 15px;
    line-height: 1.6;
    margin-top: 14px;
    letter-spacing: -0.003em;
}

.gx-simulators-hub .gx-hero-copy {
    background: linear-gradient(160deg, var(--gx-navy-deep) 0%, #0B2445 100%);
    box-shadow:
        inset 0 0 0 1px rgba(201,169,106,0.15),
        0 32px 72px rgba(5,25,52,0.25);
    position: relative;
    overflow: hidden;
}

.gx-simulators-hub .gx-hero-copy::before {
    content: "";
    position: absolute;
    top: 12px;
    left: 12px;
    right: 12px;
    bottom: 12px;
    border: 1px solid rgba(201,169,106,0.16);
    pointer-events: none;
}

.gx-simulators-hub .gx-hero-copy::after {
    content: "";
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(45deg, rgba(255,255,255,0.025) 0 2px, transparent 2px 24px);
    pointer-events: none;
}

.gx-simulators-hub .gx-hero-copy > * {
    position: relative;
    z-index: 1;
}

.gx-simulators-hub .gx-hero-panel {
    display: grid;
    gap: 16px;
}

.gx-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin: 0 0 12px;
    font-family: var(--gx-font-heading);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--gx-gold-hover);
}

.gx-eyebrow::before {
    content: "";
    width: 16px;
    height: 1px;
    background: var(--gx-gold);
}

.gx-hero-text {
    margin: 12px 0 0;
    color: var(--gx-text-secondary);
    font-size: 15px;
    line-height: 1.65;
}

.gx-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 20px;
}

.gx-chip-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 16px;
}

.gx-chip {
    display: inline-flex;
    align-items: center;
    height: 32px;
    padding: 0 12px;
    border-radius: var(--gx-radius);
    font-family: var(--gx-font-heading);
    font-size: 13px;
    font-weight: 500;
    color: rgba(255,255,255,0.7);
    border: 1px solid rgba(255,255,255,0.2);
    background: rgba(255,255,255,0.06);
    transition: color 0.2s ease, background-color 0.2s ease;
}

.gx-chip:hover {
    color: #FFFFFF;
    background: rgba(255,255,255,0.12);
}

.gx-note-card {
    padding: 24px;
    border-radius: var(--gx-radius);
    background: var(--gx-bg);
    border: 1px solid var(--gx-border);
}

.gx-note-card .gx-eyebrow {
    color: var(--gx-gold-hover);
}

.gx-note-card .gx-card-text {
    color: var(--gx-text-secondary);
    margin: 0;
    font-size: 14px;
    line-height: 1.6;
}

.gx-stat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1px;
    background: var(--gx-border);
    border-radius: var(--gx-radius);
    overflow: hidden;
    border: 1px solid var(--gx-border);
}

.gx-stat-card {
    padding: 16px 12px;
    background: var(--gx-bg);
    text-align: center;
}

.gx-stat-value {
    display: block;
    font-family: var(--gx-font-heading);
    font-size: 20px;
    font-weight: 700;
    line-height: 1;
    color: var(--gx-navy);
}

.gx-stat-label {
    display: block;
    margin-top: 4px;
    font-size: 11px;
    line-height: 1.4;
    color: var(--gx-text-tertiary);
}

.gx-cta-band {
    display: grid;
    gap: 20px;
    padding: 36px;
    border-radius: var(--gx-radius);
    background: var(--gx-navy);
    color: #ffffff;
}

.gx-cta-band .gx-eyebrow,
.gx-cta-band .gx-label {
    color: var(--gx-gold);
}

.gx-cta-band .gx-section-title {
    color: #FFFFFF;
}

.gx-cta-band .gx-section-desc,
.gx-cta-band .gx-section-text {
    color: rgba(255,255,255,0.6);
}

.gx-section-text {
    margin: 12px 0 0;
    font-size: 15px;
    line-height: 1.65;
    color: var(--gx-text-secondary);
}

.gx-inline-links {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 12px;
}

.gx-inline-links a {
    font-family: var(--gx-font-heading);
    font-weight: 600;
    font-size: 14px;
    color: var(--gx-gold);
    transition: color 0.2s ease;
}

.gx-inline-links a:hover {
    color: #FFFFFF;
}

.gx-section-head {
    margin-bottom: 40px;
}

.gx-section-head .gx-section-title {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: clamp(22px, 3vw, 30px);
    font-weight: 700;
    line-height: 1.15;
    letter-spacing: -0.02em;
    color: var(--gx-navy);
}

.gx-section-head-split {
    display: grid;
    gap: 16px;
}

@media (min-width: 992px) {
    .gx-section-head-split {
        grid-template-columns: 1fr 1fr;
        align-items: end;
        gap: 48px;
    }

    .gx-simulators-hub .gx-hero-grid {
        grid-template-columns: minmax(0, 1.1fr) minmax(300px, 0.9fr);
        align-items: stretch;
    }
}

.gx-fx-boutique-note {
    margin-top: 40px;
    padding: 24px 28px;
    background: var(--gx-surface);
    border: 1px solid var(--gx-border);
    border-radius: var(--gx-radius);
    border-left: 3px solid var(--gx-accent, #c9a96a);
}

.gx-fx-boutique-note p {
    margin: 0;
    font-size: 0.97rem;
    line-height: 1.7;
    color: var(--gx-muted);
}

.gx-fx-boutique-note strong {
    color: var(--gx-fg);
}

.gx-fx-simulators .gx-hero {
    padding-bottom: 88px;
}

.gx-fx-simulators .gx-hero::after {
    content: "";
    position: absolute;
    inset: auto 0 0 0;
    height: 140px;
    background: linear-gradient(180deg, rgba(245, 247, 250, 0), rgba(245, 247, 250, 0.95));
    pointer-events: none;
}

.gx-fx-simulators .gx-hero-inner {
    gap: clamp(28px, 5vw, 56px);
}

.gx-fx-simulators .gx-hero-content {
    max-width: 720px;
}

.gx-fx-simulators .gx-hero-proof-item strong {
    color: var(--gx-gold);
}

.gx-fx-hero-card {
    display: grid;
    gap: 20px;
}

.gx-home .gx-hero-visual-card.gx-fx-hero-card {
    background: linear-gradient(180deg, #0B2441 0%, #12395F 100%);
    border-color: rgba(15, 46, 77, 0.48);
    box-shadow: var(--gx-shadow-lg);
}

.gx-fx-hero-card .gx-visual-title {
    color: #FFFFFF;
}

.gx-fx-hero-card .gx-visual-badge {
    color: var(--gx-gold);
    background: rgba(199, 160, 83, 0.16);
}

.gx-fx-stat-stack {
    display: grid;
    gap: 12px;
}

.gx-fx-stat-card {
    display: grid;
    gap: 4px;
    padding: 16px 18px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.gx-fx-stat-card strong {
    font-family: var(--gx-font-heading);
    font-size: 28px;
    line-height: 1;
    color: #FFFFFF;
}

.gx-fx-stat-card span {
    color: rgba(255, 255, 255, 0.72);
    font-size: 13px;
    line-height: 1.5;
}

.gx-fx-signal-list {
    display: grid;
    gap: 10px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.gx-fx-signal-list li {
    position: relative;
    padding-left: 18px;
    color: rgba(255, 255, 255, 0.76);
    font-size: 14px;
    line-height: 1.6;
}

.gx-fx-signal-list li::before {
    content: "";
    position: absolute;
    top: 9px;
    left: 0;
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: var(--gx-gold);
    box-shadow: 0 0 0 4px rgba(199, 160, 83, 0.18);
}

.gx-fx-indicator-grid {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 16px;
}

.gx-fx-indicator-card {
    display: grid;
    gap: 6px;
    padding: 22px;
    border-radius: var(--gx-radius);
    border: 1px solid rgba(0, 42, 85, 0.08);
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-bg-warm) 100%);
    box-shadow: var(--gx-shadow-sm);
}

.gx-fx-indicator-label {
    font-family: var(--gx-font-heading);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--gx-gold-hover);
}

.gx-fx-indicator-card strong {
    font-family: var(--gx-font-heading);
    font-size: clamp(24px, 2.2vw, 30px);
    line-height: 1;
    color: var(--gx-navy);
}

.gx-fx-indicator-card span {
    color: var(--gx-text-secondary);
    font-size: 13px;
    line-height: 1.5;
}

.gx-fx-indicator-note {
    margin-top: 20px;
}

.gx-fx-tool-grid {
    gap: 24px;
}

.gx-fx-tool-card {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.gx-fx-tool-card.is-active {
    border-color: rgba(15, 46, 77, 0.24);
    box-shadow: var(--gx-shadow-md);
    transform: translateY(-4px);
}

.gx-fx-tool-card .gx-simulator-footer {
    margin-top: auto;
}

.gx-fx-tool-list {
    display: grid;
    gap: 10px;
    margin: 0;
    padding-left: 18px;
    color: var(--gx-text-secondary);
    font-size: 14px;
    line-height: 1.6;
}

.gx-fx-workbench {
    display: grid;
    gap: 24px;
}

.gx-fx-tablist {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 12px;
}

.gx-fx-tab {
    display: grid;
    gap: 6px;
    padding: 18px;
    border-radius: 18px;
    border: 1px solid rgba(0, 42, 85, 0.08);
    background: #FFFFFF;
    text-align: left;
    transition: transform 0.25s var(--gx-ease), border-color 0.25s ease, box-shadow 0.25s ease;
}

.gx-fx-tab strong {
    font-family: var(--gx-font-heading);
    font-size: 15px;
    line-height: 1.35;
    color: var(--gx-navy);
}

.gx-fx-tab span {
    color: var(--gx-text-secondary);
    font-size: 13px;
    line-height: 1.5;
}

.gx-fx-tab:hover,
.gx-fx-tab.is-active {
    transform: translateY(-2px);
    border-color: rgba(15, 46, 77, 0.18);
    box-shadow: var(--gx-shadow-sm);
}

.gx-fx-tab.is-active {
    background: linear-gradient(180deg, #FFFFFF 0%, #F7F3E8 100%);
}

.gx-fx-panel-shell {
    position: relative;
}

.gx-fx-tool-panel {
    display: none;
}

.gx-fx-tool-panel.is-active {
    display: block;
}

.gx-fx-panel-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
    gap: 20px;
    align-items: start;
}

.gx-fx-form,
.gx-fx-result-card {
    padding: 28px;
    border-radius: 24px;
    border: 1px solid rgba(0, 42, 85, 0.08);
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-bg-warm) 100%);
    box-shadow: var(--gx-shadow-sm);
}

.gx-fx-form-head {
    margin-bottom: 22px;
}

.gx-fx-form-head h3 {
    margin: 8px 0 0;
    font-family: var(--gx-font-heading);
    font-size: clamp(22px, 2.4vw, 30px);
    line-height: 1.12;
    color: var(--gx-navy);
}

.gx-fx-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.gx-fx-result-card {
    display: grid;
    gap: 20px;
    position: sticky;
    top: 110px;
    background: linear-gradient(180deg, #0B2441 0%, #12395F 100%);
    border-color: rgba(15, 46, 77, 0.48);
}

.gx-fx-result-head h3 {
    margin: 10px 0 8px;
    font-family: var(--gx-font-heading);
    font-size: clamp(22px, 2.2vw, 30px);
    line-height: 1.1;
    color: #FFFFFF;
}

.gx-fx-result-head p {
    margin: 0;
    color: rgba(255, 255, 255, 0.72);
    font-size: 14px;
    line-height: 1.65;
}

.gx-fx-result-chip {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(199, 160, 83, 0.16);
    color: var(--gx-gold);
    font-family: var(--gx-font-heading);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.gx-fx-metric-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

.gx-fx-metric {
    display: grid;
    gap: 6px;
    padding: 16px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.gx-fx-metric span {
    color: rgba(255, 255, 255, 0.62);
    font-size: 12px;
    line-height: 1.5;
}

.gx-fx-metric strong {
    color: #FFFFFF;
    font-family: var(--gx-font-heading);
    font-size: 22px;
    line-height: 1.1;
}

.gx-fx-result-list {
    display: grid;
    gap: 10px;
}

.gx-fx-result-list-title {
    margin: 0;
    color: var(--gx-gold);
    font-family: var(--gx-font-heading);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.gx-fx-result-list ul {
    display: grid;
    gap: 10px;
    margin: 0;
    padding-left: 18px;
    color: rgba(255, 255, 255, 0.78);
    font-size: 14px;
    line-height: 1.65;
}

.gx-fx-authority-grid {
    gap: 20px;
}

.gx-fx-authority-grid .gx-card {
    min-height: 100%;
}

.gx-fx-contact-band {
    display: grid;
    gap: 18px;
    padding: 34px;
    border-radius: var(--gx-radius);
    background: linear-gradient(135deg, #0A3D62 0%, #0c3163 100%);
    border: 1px solid rgba(0, 42, 85, 0.14);
    box-shadow: var(--gx-shadow-md);
}

.gx-fx-contact-band .gx-label {
    color: var(--gx-gold);
}

.gx-fx-contact-band .gx-section-title {
    color: #FFFFFF;
}

.gx-fx-contact-band .gx-section-desc {
    color: rgba(255,255,255,0.72);
}

.gx-fx-lead-aside {
    gap: 22px;
}

.gx-fx-live-panel {
    display: grid;
    gap: 12px;
    padding: 18px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.gx-fx-live-eyebrow {
    color: var(--gx-gold);
    font-family: var(--gx-font-heading);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.gx-fx-live-panel strong {
    color: #FFFFFF;
    font-family: var(--gx-font-heading);
    font-size: 22px;
    line-height: 1.2;
}

.gx-fx-live-metric {
    display: grid;
    gap: 4px;
}

.gx-fx-live-metric span {
    color: #FFFFFF;
    font-family: var(--gx-font-heading);
    font-size: 30px;
    line-height: 1;
}

.gx-fx-live-metric small,
.gx-fx-live-panel p {
    color: rgba(255, 255, 255, 0.72);
    font-size: 13px;
    line-height: 1.6;
}

.gx-fx-live-panel p {
    margin: 0;
}

.gx-fx-lead-card {
    overflow: hidden;
}

.gx-fx-form-status {
    margin-bottom: 14px;
    padding: 12px 14px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.6;
}

.gx-fx-form-status.is-loading {
    background: rgba(11, 92, 171, 0.08);
    color: #0B5CAB;
    border: 1px solid rgba(11, 92, 171, 0.14);
}

.gx-fx-form-status.is-success {
    background: rgba(15, 118, 110, 0.08);
    color: #0F766E;
    border: 1px solid rgba(15, 118, 110, 0.16);
}

.gx-fx-form-status.is-error {
    background: rgba(180, 83, 9, 0.08);
    color: #B45309;
    border: 1px solid rgba(180, 83, 9, 0.16);
}

@media (max-width: 1200px) {
    .gx-fx-indicator-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .gx-fx-tablist {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 991px) {
    .gx-fx-panel-grid {
        grid-template-columns: 1fr;
    }

    .gx-fx-result-card {
        position: static;
    }

    .gx-fx-indicator-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .gx-fx-tablist {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767px) {
    .gx-fx-simulators .gx-hero {
        padding-bottom: 56px;
    }

    .gx-fx-form,
    .gx-fx-result-card {
        padding: 22px;
        border-radius: 20px;
    }

    .gx-fx-form-grid,
    .gx-fx-metric-grid {
        grid-template-columns: 1fr;
    }

    .gx-fx-indicator-grid,
    .gx-fx-tablist {
        grid-template-columns: 1fr;
    }
}

/* ── CTA inline no resultado do simulador ── */
.gx-fx-result-cta {
    display: grid;
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.12);
    margin-top: 4px;
}

.gx-fx-result-cta > p {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: 13px;
    font-weight: 600;
    color: var(--gx-navy-70);
}

.gx-fx-result-cta-btns {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.gx-fx-result-cta-btns .gx-btn {
    flex: 1;
    min-width: 0;
    font-size: 13px;
    height: 40px;
}

/* ── Credibilidade no aside do formulário ── */
.gx-fx-credibility {
    padding: 20px 0;
    border-top: 1px solid rgba(255,255,255,0.12);
    border-bottom: 1px solid rgba(255,255,255,0.12);
    margin: 8px 0;
}

.gx-fx-credibility ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: 10px;
}

.gx-fx-credibility ul li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 13px;
    line-height: 1.5;
    color: rgba(255,255,255,0.8);
}

.gx-fx-credibility ul li::before {
    content: "";
    width: 16px;
    height: 16px;
    min-width: 16px;
    margin-top: 1px;
    border-radius: 50%;
    background: var(--gx-gold);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'%3E%3Cpath fill='none' stroke='%23fff' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round' d='M2 6l3 3 5-5'/%3E%3C/svg%3E");
    background-size: 10px 10px;
    background-repeat: no-repeat;
    background-position: center;
    flex-shrink: 0;
}

/* ── Label opcional no formulário ── */
.gx-form-optional {
    font-weight: 400;
    font-size: 12px;
    color: var(--gx-text-tertiary);
}

/* ── Sticky bar mobile ── */
.gx-fx-sticky-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 90;
    background: var(--gx-navy);
    border-top: 1px solid rgba(255,255,255,0.1);
    padding: 12px 16px;
    padding-bottom: max(12px, env(safe-area-inset-bottom, 12px));
    transform: translateY(100%);
    transition: transform 0.3s var(--gx-ease);
    box-shadow: 0 -8px 32px rgba(12,49,99,0.22);
}

.gx-fx-sticky-bar.is-visible {
    transform: translateY(0);
}

.gx-fx-sticky-inner {
    display: flex;
    align-items: center;
    gap: 12px;
    max-width: var(--gx-max);
    margin: 0 auto;
}

.gx-fx-sticky-text {
    flex: 1;
    min-width: 0;
    font-family: var(--gx-font-heading);
    font-size: 13px;
    font-weight: 600;
    color: rgba(255,255,255,0.85);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.gx-fx-sticky-actions {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}

.gx-fx-sticky-actions .gx-btn {
    height: 40px;
    font-size: 13px;
    padding: 0 16px;
}

.gx-fx-sticky-actions .gx-btn-whatsapp {
    width: 40px;
    padding: 0;
}

/* Sticky bar só aparece em mobile/tablet */
@media (min-width: 992px) {
    .gx-fx-sticky-bar {
        display: none;
    }
}

@media (max-width: 479px) {
    .gx-fx-sticky-text {
        font-size: 12px;
    }

    .gx-fx-sticky-actions .gx-btn {
        font-size: 12px;
        padding: 0 14px;
    }

    .gx-fx-result-cta-btns .gx-btn {
        font-size: 12px;
    }
}

/* ══════════════════════════════════════════════════════════ */
/* Vault Modernism — Home Institucional finishing layer        */
/* ══════════════════════════════════════════════════════════ */

.gx-home {
    background: var(--gx-bg);
}

.gx-home .gx-label {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.22em;
    color: var(--gx-gold-etched);
}

.gx-home .gx-label::before {
    width: 28px;
    height: 1px;
    background: var(--gx-gold);
    opacity: 0.9;
}

.gx-home .gx-section-header.is-centered .gx-label {
    justify-content: center;
}

.gx-home .gx-section-header.is-centered .gx-label::before,
.gx-home .gx-section-header.is-centered .gx-label::after {
    content: "";
    width: 24px;
    height: 1px;
    background: var(--gx-gold);
    opacity: 0.9;
}

/* Buttons — vault refinement */
.gx-home .gx-btn {
    font-family: var(--gx-font-sans-refined);
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    font-size: 12.5px;
    height: 46px;
    padding: 0 26px;
    transition: all 0.3s var(--gx-ease);
}

.gx-home .gx-btn-lg {
    height: 54px;
    padding: 0 34px;
    font-size: 13px;
    letter-spacing: 0.14em;
}

.gx-home .gx-btn-primary {
    background: var(--gx-gold-etched);
    color: #FFFFFF;
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,0.25),
        inset 0 0 0 1px rgba(255,255,255,0.08),
        0 14px 30px rgba(201,169,106,0.22);
}

.gx-home .gx-btn-primary:hover {
    background: var(--gx-navy-deep);
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,0.12),
        inset 0 0 0 1px rgba(201,169,106,0.4),
        0 18px 36px rgba(5,25,52,0.28);
    transform: translateY(-1px);
}

.gx-home .gx-btn-ghost {
    background: transparent;
    color: var(--gx-navy-deep);
    border: 1px solid rgba(12,49,99,0.22);
    box-shadow: inset 0 0 0 1px rgba(201,169,106,0.1);
}

.gx-home .gx-btn-ghost:hover {
    border-color: var(--gx-gold);
    background: rgba(251,247,238,0.8);
    box-shadow: inset 0 0 0 1px rgba(201,169,106,0.3);
    color: var(--gx-navy-deep);
}

.gx-home .gx-btn-whatsapp {
    box-shadow: 0 14px 26px rgba(31,157,85,0.22);
}

/* Hero — vault atmosphere */
.gx-home .gx-hero {
    position: relative;
    padding-top: 160px;
    padding-bottom: 120px;
    background:
        radial-gradient(ellipse 900px 420px at 85% 15%, rgba(201,169,106,0.08) 0%, transparent 65%),
        radial-gradient(ellipse 700px 500px at 10% 100%, rgba(12,49,99,0.04) 0%, transparent 70%),
        linear-gradient(180deg, #FFFFFF 0%, var(--gx-vellum) 100%);
    overflow: hidden;
}

.gx-home .gx-hero::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, rgba(201,169,106,0.35) 50%, transparent 100%);
}

.gx-home .gx-hero-badge {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--gx-navy-deep);
    padding: 8px 16px 8px 14px;
    border: 1px solid rgba(201,169,106,0.35);
    background: rgba(255,255,255,0.7);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-radius: 2px;
}

.gx-home .gx-hero-badge-dot {
    width: 6px;
    height: 6px;
    background: var(--gx-gold);
    box-shadow: 0 0 0 3px rgba(201,169,106,0.2);
    margin-right: 4px;
    border-radius: 50%;
}

/* Hero proof items — stats refinement */
.gx-home .gx-hero-proof-item {
    padding: 18px 20px;
    background: rgba(255,255,255,0.88);
    border: 1px solid rgba(12,49,99,0.08);
    border-left: 2px solid var(--gx-gold);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
}

.gx-home .gx-hero-proof-item strong {
    font-family: var(--gx-font-display);
    font-size: 22px;
    font-weight: 400;
    font-style: italic;
    text-transform: none;
    letter-spacing: -0.01em;
    color: var(--gx-gold-etched);
    font-feature-settings: "onum" 1;
    font-variant-numeric: oldstyle-nums;
}

.gx-home .gx-hero-proof-item span {
    font-family: var(--gx-font-sans-refined);
    font-size: 12.5px;
    line-height: 1.5;
    color: var(--gx-navy-70);
}

/* Hero visual card — vault treatment */
.gx-home .gx-hero-visual-card {
    background: linear-gradient(160deg, #FFFFFF 0%, var(--gx-vellum) 100%);
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.85),
        0 0 0 1px rgba(201,169,106,0.18),
        0 40px 80px rgba(5,25,52,0.1);
}

.gx-home .gx-visual-title {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 18px;
    font-style: italic;
    letter-spacing: -0.01em;
    color: var(--gx-navy-deep);
}

.gx-home .gx-visual-badge {
    font-family: var(--gx-font-sans-refined);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--gx-gold-etched);
    padding: 4px 8px;
    background: rgba(201,169,106,0.1);
    border: 1px solid rgba(201,169,106,0.25);
}

/* Section header centered: hide ::after for non-label elements only */
.gx-home .gx-section-header:not(.is-centered) .gx-label::after {
    display: none;
}

/* Strip lead typography */
.gx-home .gx-strip-lead {
    font-family: var(--gx-font-sans-refined);
}

.gx-home .gx-strip-item {
    font-family: var(--gx-font-sans-refined);
    font-size: 13px;
    letter-spacing: 0.06em;
}

/* Partner name typography */
.gx-home .gx-partner-name {
    font-family: var(--gx-font-sans-refined);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--gx-navy-70);
}

/* Press card kicker + title body */
.gx-home .gx-press-kicker {
    font-family: var(--gx-font-sans-refined);
    background: transparent;
    border: 1px solid rgba(12,49,99,0.14);
    color: var(--gx-navy-deep);
    font-size: 10.5px;
    letter-spacing: 0.16em;
    padding: 5px 10px;
}

.gx-home .gx-press-summary {
    font-family: var(--gx-font-sans-refined);
    font-size: 14px;
    line-height: 1.6;
    color: var(--gx-navy-70);
}

.gx-home .gx-press-date {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    letter-spacing: 0.12em;
    color: var(--gx-gold-etched);
    font-feature-settings: "onum" 1;
    font-variant-numeric: oldstyle-nums;
}

/* Blog kicker + summary */
.gx-home .gx-blog-kicker {
    font-family: var(--gx-font-sans-refined);
    font-size: 10.5px;
    letter-spacing: 0.18em;
    padding: 5px 10px;
}

.gx-home .gx-blog-label {
    font-family: var(--gx-font-sans-refined);
    font-size: 10px;
    font-style: italic;
    letter-spacing: 0.1em;
}

.gx-home .gx-blog-summary {
    font-family: var(--gx-font-sans-refined);
    font-size: 14.5px;
    line-height: 1.65;
    color: var(--gx-navy-70);
}

.gx-home .gx-blog-card-featured .gx-blog-summary {
    font-family: var(--gx-font-sans-refined);
    font-size: 16px;
    line-height: 1.7;
}

/* Text link refinement — arrow gold */
.gx-home .gx-text-link,
.gx-home .gx-card-link {
    font-family: var(--gx-font-sans-refined);
    font-size: 12.5px;
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--gx-gold-etched);
    transition: color 0.2s ease, gap 0.2s ease;
}

.gx-home .gx-text-link:hover,
.gx-home .gx-card-link:hover {
    color: var(--gx-navy-deep);
}

/* CTA block — vault prestige */
.gx-home .gx-cta-block {
    position: relative;
    padding: 80px 60px;
    background:
        radial-gradient(ellipse 600px 300px at 50% 0%, rgba(201,169,106,0.18) 0%, transparent 70%),
        linear-gradient(160deg, var(--gx-navy-deep) 0%, #0B2445 100%);
    border: 1px solid rgba(201,169,106,0.3);
    box-shadow:
        inset 0 0 0 1px rgba(201,169,106,0.12),
        0 40px 80px rgba(5,25,52,0.3);
    overflow: hidden;
}

.gx-home .gx-cta-block::before {
    content: "";
    position: absolute;
    top: 14px;
    left: 14px;
    right: 14px;
    bottom: 14px;
    border: 1px solid rgba(201,169,106,0.18);
    pointer-events: none;
}

.gx-home .gx-cta-block::after {
    content: "";
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(45deg, rgba(255,255,255,0.02) 0 2px, transparent 2px 24px);
    pointer-events: none;
}

.gx-home .gx-cta-block .gx-section-title {
    color: #FFFFFF;
    max-width: 18ch;
    margin-left: auto;
    margin-right: auto;
}

.gx-home .gx-cta-block .gx-label {
    color: var(--gx-gold);
    justify-content: center;
}

.gx-home .gx-cta-block .gx-section-desc {
    color: rgba(255,255,255,0.72);
}

.gx-home .gx-cta-block .gx-btn-ghost {
    color: #FFFFFF;
    border-color: rgba(201,169,106,0.4);
    background: rgba(255,255,255,0.03);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.05);
}

.gx-home .gx-cta-block .gx-btn-ghost:hover {
    background: rgba(201,169,106,0.12);
    border-color: var(--gx-gold);
    color: #FFFFFF;
}

.gx-home .gx-cta-icon svg {
    stroke: var(--gx-gold);
    opacity: 0.55;
}

/* Hero divider under title — delicate gold rule */
.gx-home .gx-hero-title {
    position: relative;
}

/* Lead aside — vault depth */
.gx-home .gx-lead-aside {
    padding: 56px 44px;
    background: linear-gradient(160deg, var(--gx-navy-deep) 0%, #0B2445 100%);
    box-shadow:
        inset 0 0 0 1px rgba(201,169,106,0.14),
        0 40px 80px rgba(5,25,52,0.3);
}

.gx-home .gx-lead-aside::after {
    background:
        linear-gradient(135deg, rgba(201,169,106,0.12), rgba(255,255,255,0) 40%),
        repeating-linear-gradient(90deg, rgba(255,255,255,0.04) 0, rgba(255,255,255,0.04) 1px, transparent 1px, transparent 28px);
    mask-image: linear-gradient(180deg, rgba(0,0,0,0.6), transparent 90%);
    opacity: 0.5;
}

.gx-home .gx-lead-aside .gx-section-title {
    color: #FFFFFF;
}

.gx-home .gx-lead-aside .gx-section-title em {
    color: var(--gx-gold);
}

/* Final touch: body refinement scoped to home */
.gx-home {
    font-family: var(--gx-font-sans-refined);
}

.gx-home p,
.gx-home li {
    font-family: var(--gx-font-sans-refined);
}

/* Fine numbering ornament near section titles */
.gx-home .gx-section-header.is-split .gx-label::before {
    width: 36px;
}

/* ══════════════════════════════════════════════════════════ */
/* Fit & enquadramento — Hero CTA row + Blog images            */
/* ══════════════════════════════════════════════════════════ */

/* Hero CTA: compactar botões para caber em 3 linha única em desktops médios */
.gx-home .gx-hero-cta {
    flex-wrap: wrap;
    gap: 10px;
    row-gap: 12px;
}

.gx-home .gx-hero-cta .gx-btn-lg {
    height: 52px;
    padding: 0 24px;
    font-size: 12px;
    letter-spacing: 0.1em;
}

.gx-home .gx-hero-cta .gx-btn-whatsapp.gx-btn-lg {
    padding: 0 22px;
    letter-spacing: 0.08em;
}

.gx-home .gx-hero-cta .gx-btn svg {
    width: 16px;
    height: 16px;
}

/* Garante que no viewport curto (entre 768 e 992) os botões compactam melhor */
@media (max-width: 1199px) and (min-width: 768px) {
    .gx-home .gx-hero-cta .gx-btn-lg {
        height: 48px;
        padding: 0 20px;
        font-size: 11.5px;
        letter-spacing: 0.08em;
    }
}

/* Blog images — evitar distorção/stretching */
.gx-home .gx-blog-image {
    aspect-ratio: 16 / 10;
    background: linear-gradient(180deg, var(--gx-vellum) 0%, var(--gx-vellum) 100%);
}

.gx-home .gx-blog-image img {
    object-fit: cover;
    object-position: center 30%;
}

.gx-home .gx-blog-card:hover .gx-blog-image img {
    transform: scale(1.015);
}

/* Featured blog: imagem deixa de esticar para acompanhar a altura do texto */
@media (min-width: 768px) {
    .gx-home .gx-blog-card-featured {
        grid-template-columns: 1.1fr 1fr;
        align-items: center;
    }

    .gx-home .gx-blog-card-featured .gx-blog-image {
        aspect-ratio: 4 / 3;
        min-height: 0;
        max-height: 420px;
        align-self: stretch;
    }

    .gx-home .gx-blog-card-featured .gx-blog-image img {
        object-position: center;
    }
}

@media (min-width: 1200px) {
    .gx-home .gx-blog-card-featured .gx-blog-image {
        aspect-ratio: 3 / 2;
        max-height: 460px;
    }
}

/* ══════════════════════════════════════════════════════════ */
/* Contact highlight — CTA grid dentro do aside navy           */
/* ══════════════════════════════════════════════════════════ */

/* No aside estreito, os 2 botões lado a lado não cabem com uppercase+spacing.
   Stack vertical + permite quebra de texto como fallback. */
.gx-home .gx-contact-highlight .gx-contact-cta-grid {
    grid-template-columns: 1fr;
    gap: 10px;
}

.gx-home .gx-contact-highlight .gx-btn {
    width: 100%;
    min-height: 48px;
    height: auto;
    padding: 12px 20px;
    letter-spacing: 0.08em;
    font-size: 11.5px;
    line-height: 1.25;
    white-space: normal;
    text-align: center;
}

.gx-home .gx-contact-highlight .gx-btn svg {
    flex-shrink: 0;
}

/* Labels/intro do highlight em serif para coerência Vault */
.gx-home .gx-contact-highlight strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 22px;
    line-height: 1.15;
    letter-spacing: -0.01em;
}

.gx-home .gx-contact-highlight p {
    font-family: var(--gx-font-sans-refined);
    font-size: 14px;
    line-height: 1.55;
}

/* Em viewports muito largos (≥1200) podemos voltar para 2 colunas, já que
   o aside fica mais espaçoso — mas mantém wrap como segurança */
@media (min-width: 1200px) {
    .gx-home .gx-contact-highlight .gx-contact-cta-grid {
        grid-template-columns: 1fr;
    }
}

/* ══════════════════════════════════════════════════════════ */
/* Vault Modernism — Simulador de Câmbio (gx-fx-*)             */
/* ══════════════════════════════════════════════════════════ */

.gx-fx-simulators {
    font-family: var(--gx-font-sans-refined);
}

/* Hero card navy profundo com frame duplo gold */
.gx-home .gx-hero-visual-card.gx-fx-hero-card {
    background: linear-gradient(160deg, var(--gx-navy-deep) 0%, #0B2441 55%, #12395F 100%);
    border: 1px solid rgba(201,169,106,0.22);
    box-shadow:
        inset 0 0 0 1px rgba(201,169,106,0.12),
        0 40px 80px rgba(5,25,52,0.3);
    position: relative;
    overflow: hidden;
}

.gx-home .gx-hero-visual-card.gx-fx-hero-card::after {
    content: "";
    position: absolute;
    top: 12px;
    left: 12px;
    right: 12px;
    bottom: 12px;
    border: 1px solid rgba(201,169,106,0.14);
    pointer-events: none;
    background: repeating-linear-gradient(45deg, rgba(255,255,255,0.02) 0 2px, transparent 2px 28px);
}

.gx-fx-hero-card .gx-visual-title {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-style: italic;
    font-size: 18px;
    letter-spacing: -0.01em;
    color: #FFFFFF;
}

.gx-fx-hero-card .gx-visual-badge {
    font-family: var(--gx-font-sans-refined);
    font-size: 10px;
    letter-spacing: 0.22em;
    color: var(--gx-gold);
    background: rgba(201,169,106,0.14);
    border: 1px solid rgba(201,169,106,0.3);
    padding: 4px 8px;
    text-transform: uppercase;
}

/* Stat cards dentro do hero navy */
.gx-fx-stat-card {
    padding: 18px 20px;
    border-radius: 2px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(201,169,106,0.18);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.03);
}

.gx-fx-stat-card strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 32px;
    line-height: 1;
    color: var(--gx-gold);
    font-feature-settings: "onum" 1;
    font-variant-numeric: oldstyle-nums;
    letter-spacing: -0.015em;
}

.gx-fx-stat-card span {
    font-family: var(--gx-font-sans-refined);
    font-size: 12px;
    letter-spacing: 0.04em;
    color: rgba(255,255,255,0.7);
}

/* Signal list — bullet gold refinada */
.gx-fx-signal-list li {
    font-family: var(--gx-font-sans-refined);
    font-size: 14px;
    line-height: 1.6;
    color: rgba(255,255,255,0.82);
    padding-left: 20px;
}

.gx-fx-signal-list li::before {
    width: 6px;
    height: 6px;
    top: 10px;
    background: var(--gx-gold);
    box-shadow: 0 0 0 3px rgba(201,169,106,0.22);
}

/* Indicator grid (cotações/taxas) */
.gx-fx-indicator-card {
    padding: 24px 22px;
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-ivory) 100%);
    border: 1px solid rgba(12,49,99,0.08);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.8),
        0 1px 0 0 rgba(201,169,106,0.12),
        var(--gx-shadow-sm);
    border-radius: 2px;
    transition: transform 0.35s var(--gx-ease), border-color 0.35s ease, box-shadow 0.35s ease;
}

.gx-fx-indicator-card:hover {
    transform: translateY(-3px);
    border-color: rgba(201,169,106,0.4);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.9),
        0 0 0 1px rgba(201,169,106,0.3),
        var(--gx-shadow-md);
}

.gx-fx-indicator-label {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    letter-spacing: 0.22em;
    color: var(--gx-gold-etched);
}

.gx-fx-indicator-card strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: clamp(26px, 2.4vw, 34px);
    line-height: 1;
    color: var(--gx-navy-deep);
    font-feature-settings: "onum" 1;
    font-variant-numeric: oldstyle-nums;
    letter-spacing: -0.015em;
}

.gx-fx-indicator-card span {
    font-family: var(--gx-font-sans-refined);
    color: var(--gx-navy-70);
    font-size: 13px;
}

/* Boutique note — vault inset frame */
.gx-fx-boutique-note {
    margin-top: 40px;
    padding: 28px 32px;
    border-radius: 2px;
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-vellum) 100%);
    border: 1px solid rgba(12,49,99,0.08);
    border-left: 2px solid var(--gx-gold);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.8),
        0 1px 0 0 rgba(201,169,106,0.12),
        var(--gx-shadow-sm);
}

.gx-fx-boutique-note p {
    font-family: var(--gx-font-sans-refined);
    font-size: 15px;
    line-height: 1.65;
    color: var(--gx-navy-70);
}

.gx-fx-boutique-note strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-style: italic;
    color: var(--gx-navy-deep);
    font-size: 1.08em;
}

/* Contact band e lead aside — vault deep navy */
.gx-fx-contact-band {
    padding: 60px 48px;
    background:
        radial-gradient(ellipse 600px 300px at 50% 0%, rgba(201,169,106,0.15) 0%, transparent 70%),
        linear-gradient(160deg, var(--gx-navy-deep) 0%, #0B2445 100%);
    border: 1px solid rgba(201,169,106,0.28);
    box-shadow:
        inset 0 0 0 1px rgba(201,169,106,0.1),
        0 40px 80px rgba(5,25,52,0.3);
    position: relative;
    overflow: hidden;
    border-radius: 2px;
}

.gx-fx-contact-band::before {
    content: "";
    position: absolute;
    top: 12px;
    left: 12px;
    right: 12px;
    bottom: 12px;
    border: 1px solid rgba(201,169,106,0.14);
    pointer-events: none;
}

.gx-fx-contact-band .gx-section-title {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: clamp(26px, 3.6vw, 38px);
    letter-spacing: -0.015em;
    line-height: 1.05;
}

.gx-fx-contact-band .gx-section-title em,
.gx-fx-contact-band .gx-section-title i {
    font-style: italic;
    color: var(--gx-gold);
}

/* Live panel (cotação em tempo real) */
.gx-fx-live-panel {
    padding: 20px 22px;
    border-radius: 2px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(201,169,106,0.2);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.03);
}

.gx-fx-live-eyebrow {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    letter-spacing: 0.22em;
    color: var(--gx-gold);
}

.gx-fx-live-panel strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-style: italic;
    font-size: 22px;
    line-height: 1.15;
    letter-spacing: -0.01em;
    color: #FFFFFF;
}

.gx-fx-live-metric span {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 36px;
    line-height: 1;
    color: var(--gx-gold);
    font-feature-settings: "onum" 1;
    font-variant-numeric: oldstyle-nums;
    letter-spacing: -0.015em;
}

.gx-fx-live-metric small,
.gx-fx-live-panel p {
    font-family: var(--gx-font-sans-refined);
}
</style>
