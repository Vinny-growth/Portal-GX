<style>
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

.gx-marketing {
    --gx-font-display: "Inter", "Segoe UI", sans-serif;
    --gx-font-body: "Inter", "Segoe UI", sans-serif;
    --gx-font-ui: "Inter", "Segoe UI", sans-serif;
    --gx-brand: #6b46ff;
    --gx-brand-strong: #00d4ff;
    --gx-cyan: #00d4ff;
    --gx-navy: #1c1363;
    --gx-midnight: #0b1320;
    --gx-ink: #0b1320;
    --gx-ink-soft: #5d6778;
    --gx-line: rgba(11, 19, 32, 0.1);
    --gx-bg: #f3f7fb;
    --gx-card: rgba(255, 255, 255, 0.92);
    --gx-card-alt: #eef3f8;
    --gx-shadow: 0 18px 48px rgba(11, 19, 32, 0.08);
    --gx-radius-xl: 32px;
    --gx-radius-lg: 24px;
    --gx-radius-md: 18px;
    position: relative;
    overflow: hidden;
    color: var(--gx-ink);
    font-family: var(--gx-font-body), sans-serif;
    background:
        radial-gradient(circle at top left, rgba(107, 70, 255, 0.12), transparent 28%),
        radial-gradient(circle at 82% 10%, rgba(0, 212, 255, 0.12), transparent 22%),
        linear-gradient(180deg, #fbfcff 0%, #f4f7fc 44%, #f8f9fd 100%);
}

.gx-marketing::before,
.gx-marketing::after {
    content: "";
    position: absolute;
    pointer-events: none;
}

.gx-marketing::before {
    inset: auto auto -180px -140px;
    width: 520px;
    height: 520px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(107, 70, 255, 0.16) 0%, rgba(107, 70, 255, 0) 70%);
    filter: blur(10px);
}

.gx-marketing::after {
    top: -30px;
    right: -10vw;
    width: 50vw;
    height: 460px;
    background:
        linear-gradient(135deg, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0)),
        repeating-linear-gradient(90deg, rgba(11, 19, 32, 0.03) 0, rgba(11, 19, 32, 0.03) 1px, transparent 1px, transparent 26px);
    mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.7), transparent 100%);
    opacity: 0.55;
}

.gx-marketing section {
    position: relative;
    z-index: 1;
}

.gx-marketing a {
    text-decoration: none;
}

.gx-shell {
    position: relative;
    padding: 18px 0 76px;
}

.gx-hero {
    padding: 14px 0 26px;
}

.gx-section {
    padding-top: 1.5rem;
}

.gx-hero-grid,
.gx-lead-grid {
    display: grid;
    gap: 1.1rem;
}

.gx-hero-copy,
.gx-hero-panel,
.gx-vertical-card,
.gx-simulator-card,
.gx-blog-card,
.gx-lead-card,
.gx-lead-aside,
.gx-note-card,
.gx-cta-band,
.gx-signature-card {
    background: var(--gx-card);
    border: 1px solid rgba(255, 255, 255, 0.72);
    border-radius: var(--gx-radius-xl);
    box-shadow: var(--gx-shadow);
    overflow: hidden;
    transition: transform 0.26s ease, box-shadow 0.26s ease, border-color 0.26s ease;
}

.gx-hero-copy {
    position: relative;
    isolation: isolate;
    padding: 1.45rem 1.3rem;
    background:
        linear-gradient(150deg, rgba(9, 31, 53, 0.98) 0%, rgba(10, 43, 74, 0.96) 46%, rgba(22, 71, 113, 0.94) 76%, rgba(199, 160, 83, 0.86) 100%);
    color: #ffffff;
}

.gx-hero-copy::before,
.gx-hero-copy::after {
    content: "";
    position: absolute;
    pointer-events: none;
}

.gx-hero-copy::before {
    inset: 0;
    background:
        radial-gradient(circle at 82% 18%, rgba(255, 255, 255, 0.16), rgba(255, 255, 255, 0) 24%),
        linear-gradient(135deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0));
    z-index: -2;
}

.gx-hero-copy::after {
    top: -12%;
    right: -7%;
    width: 250px;
    height: 250px;
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: radial-gradient(circle, rgba(103, 216, 255, 0.22) 0%, rgba(255, 255, 255, 0) 72%);
    z-index: -1;
    animation: gxFloat 8s ease-in-out infinite;
}

.gx-hero-panel {
    padding: 1.25rem;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(244, 247, 251, 0.96) 100%);
}

.gx-home .gx-hero-frame {
    position: relative;
    padding: 0.6rem;
    border-radius: 38px;
    background:
        linear-gradient(145deg, rgba(3, 15, 25, 0.96) 0%, rgba(9, 31, 53, 0.98) 38%, rgba(11, 52, 93, 0.94) 72%, rgba(199, 160, 83, 0.52) 100%);
    box-shadow: 0 36px 90px rgba(4, 16, 29, 0.2);
    overflow: hidden;
}

.gx-home .gx-hero-frame::before,
.gx-home .gx-hero-frame::after {
    content: "";
    position: absolute;
    pointer-events: none;
}

.gx-home .gx-hero-frame::before {
    inset: 0;
    background:
        radial-gradient(circle at 16% 18%, rgba(255, 255, 255, 0.16), rgba(255, 255, 255, 0) 26%),
        linear-gradient(135deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0));
}

.gx-home .gx-hero-frame::after {
    right: -10%;
    bottom: -24%;
    width: 360px;
    height: 360px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(103, 216, 255, 0.18) 0%, rgba(103, 216, 255, 0) 68%);
    filter: blur(10px);
}

.gx-home .gx-hero-copy,
.gx-home .gx-hero-panel {
    background: transparent;
    border: 0;
    box-shadow: none;
}

.gx-home .gx-hero-panel {
    padding: 0;
}

.gx-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0 0 0.9rem;
    font-family: var(--gx-font-ui), sans-serif;
    font-size: 0.76rem;
    font-weight: 800;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    opacity: 0.92;
}

.gx-eyebrow::before {
    content: "";
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: var(--gx-brand);
    box-shadow: 0 0 0 6px rgba(199, 160, 83, 0.16);
}

.gx-hero-badge-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.85rem;
}

.gx-hero-badge-row .gx-eyebrow {
    margin-bottom: 0;
}

.gx-hero-status {
    display: inline-flex;
    align-items: center;
    min-height: 36px;
    padding: 0.52rem 0.8rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.8rem;
    font-weight: 700;
    line-height: 1.35;
}

.gx-hero-title,
.gx-section-title,
.gx-vertical-title,
.gx-simulator-title,
.gx-blog-title,
.gx-form-title {
    margin: 0;
    font-family: var(--gx-font-display), sans-serif;
    line-height: 0.98;
    letter-spacing: -0.05em;
    font-weight: 800;
    text-wrap: balance;
}

.gx-hero-title {
    font-size: clamp(2.35rem, 8vw, 5.25rem);
    max-width: 10ch;
}

.gx-section-title {
    font-size: clamp(1.8rem, 5vw, 3.25rem);
    max-width: 13ch;
}

.gx-simulator-title,
.gx-blog-title {
    font-size: 1.36rem;
}

.gx-vertical-title {
    font-size: 1.5rem;
}

.gx-hero-text,
.gx-section-text,
.gx-card-text,
.gx-form-copy,
.gx-blog-summary {
    margin: 0;
    color: inherit;
    font-size: 1rem;
    line-height: 1.72;
}

.gx-hero-text {
    margin-top: 1rem;
    max-width: 58ch;
    color: rgba(255, 255, 255, 0.84);
}

.gx-section-text,
.gx-card-text,
.gx-form-copy,
.gx-blog-summary,
.gx-simulator-meta,
.gx-blog-meta {
    color: var(--gx-ink-soft);
}

.gx-actions,
.gx-chip-list,
.gx-contact-list,
.gx-inline-links,
.gx-form-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.85rem;
}

.gx-actions {
    margin-top: 1.45rem;
}

.gx-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.55rem;
    font-family: var(--gx-font-ui), sans-serif;
    min-height: 56px;
    padding: 1rem 1.24rem;
    border-radius: 18px;
    border: 1px solid transparent;
    font-weight: 800;
    font-size: 0.98rem;
    line-height: 1;
    transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease, border-color 0.18s ease;
    cursor: pointer;
}

.gx-btn:hover,
.gx-btn:focus {
    transform: translateY(-1px);
}

.gx-btn-primary {
    background: linear-gradient(90deg, var(--gx-brand) 0%, var(--gx-brand-strong) 100%);
    color: #ffffff;
    box-shadow: 0 14px 30px rgba(107, 70, 255, 0.22);
}

.gx-btn-secondary {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    border-color: rgba(255, 255, 255, 0.22);
    backdrop-filter: blur(10px);
}

.gx-btn-ghost {
    background: rgba(255, 255, 255, 0.88);
    color: var(--gx-ink);
    border-color: rgba(8, 23, 39, 0.08);
}

.gx-chip-list {
    margin-top: 1.2rem;
}

.gx-chip {
    display: inline-flex;
    align-items: center;
    min-height: 42px;
    padding: 0.72rem 0.96rem;
    border-radius: 999px;
    font-size: 0.88rem;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(255, 255, 255, 0.14);
    background: rgba(255, 255, 255, 0.07);
}

.gx-chip:hover,
.gx-chip:focus {
    background: rgba(255, 255, 255, 0.16);
    color: #ffffff;
}

.gx-hero-proof {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    margin-top: 1.2rem;
}

.gx-hero-proof-item {
    display: inline-flex;
    align-items: center;
    min-height: 40px;
    padding: 0.62rem 0.84rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.14);
    color: rgba(255, 255, 255, 0.88);
    font-family: var(--gx-font-ui), sans-serif;
    font-size: 0.82rem;
    font-weight: 700;
    line-height: 1.3;
}

.gx-hero-rail {
    display: grid;
    gap: 0.75rem;
    margin-top: 1.25rem;
}

.gx-hero-rail-item {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 0.85rem;
    align-items: start;
    padding: 0.95rem;
    border-radius: 22px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
}

.gx-hero-rail-no {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    min-height: 38px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    font-family: var(--gx-font-ui), sans-serif;
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.12em;
}

.gx-hero-rail-item strong {
    display: block;
    font-family: var(--gx-font-ui), sans-serif;
    font-size: 0.95rem;
    letter-spacing: 0.01em;
}

.gx-hero-rail-item p {
    margin: 0.28rem 0 0;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.88rem;
    line-height: 1.6;
}

.gx-stage-window {
    display: grid;
    gap: 1rem;
    padding: 1rem;
    border-radius: 30px;
    background:
        linear-gradient(180deg, rgba(6, 18, 31, 0.76) 0%, rgba(10, 28, 47, 0.88) 100%);
    border: 1px solid rgba(255, 255, 255, 0.12);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.05),
        0 24px 60px rgba(0, 0, 0, 0.16);
    backdrop-filter: blur(18px);
}

.gx-stage-top,
.gx-stage-footer {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}

.gx-stage-title,
.gx-stage-pulse,
.gx-stage-kicker,
.gx-stage-card a,
.gx-stage-card strong {
    font-family: var(--gx-font-ui), sans-serif;
}

.gx-stage-title {
    color: rgba(255, 255, 255, 0.86);
    font-size: 0.82rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.gx-stage-pulse {
    display: inline-flex;
    align-items: center;
    min-height: 34px;
    padding: 0.48rem 0.72rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.08);
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.76rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.gx-stage-primary {
    padding: 1rem;
    border-radius: 24px;
    background:
        linear-gradient(140deg, rgba(12, 42, 72, 0.98) 0%, rgba(15, 63, 103, 0.92) 64%, rgba(199, 160, 83, 0.42) 100%);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.gx-stage-main {
    display: grid;
    gap: 1rem;
}

.gx-stage-kicker {
    margin: 0 0 0.65rem;
    color: rgba(255, 255, 255, 0.66);
    font-size: 0.74rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.gx-stage-copy-block {
    display: grid;
    gap: 0.8rem;
}

.gx-stage-value {
    display: block;
    color: #ffffff;
    font-family: var(--gx-font-display), sans-serif;
    font-size: clamp(1.7rem, 4vw, 2.8rem);
    font-weight: 800;
    line-height: 1.02;
    letter-spacing: -0.04em;
    text-wrap: balance;
}

.gx-stage-copy {
    margin: 0;
    color: rgba(255, 255, 255, 0.76);
    line-height: 1.7;
}

.gx-stage-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.55rem;
}

.gx-stage-tag {
    display: inline-flex;
    align-items: center;
    min-height: 34px;
    padding: 0.45rem 0.68rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.8rem;
    font-weight: 700;
}

.gx-stage-grid {
    display: grid;
    gap: 0.8rem;
}

.gx-stage-card {
    --gx-accent: var(--gx-brand);
    display: grid;
    gap: 0.55rem;
    padding: 0.95rem;
    border-radius: 22px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.gx-stage-card .gx-stage-kicker {
    margin-bottom: 0;
    color: var(--gx-accent);
}

.gx-stage-card strong {
    display: block;
    color: #ffffff;
    font-size: 1rem;
    font-weight: 800;
    line-height: 1.25;
}

.gx-stage-card span {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.88rem;
    line-height: 1.6;
}

.gx-stage-card a,
.gx-stage-footer a {
    color: #ffffff;
    font-size: 0.9rem;
    font-weight: 700;
}

.gx-stage-card a::after,
.gx-stage-footer a::after {
    content: "->";
    margin-left: 0.35rem;
}

.gx-note-card {
    padding: 1rem 1rem 1.1rem;
    background: linear-gradient(180deg, rgba(10, 35, 58, 0.98) 0%, rgba(13, 52, 88, 0.94) 100%);
    color: #ffffff;
    box-shadow: none;
}

.gx-note-card .gx-eyebrow {
    color: rgba(255, 255, 255, 0.9);
}

.gx-note-card .gx-card-text {
    color: rgba(255, 255, 255, 0.82);
}

.gx-stat-grid,
.gx-hero-metrics {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.75rem;
}

.gx-hero-metrics {
    margin-top: 0.95rem;
}

.gx-stat-card {
    padding: 1rem 0.9rem;
    border-radius: 22px;
    background: var(--gx-card-alt);
    border: 1px solid rgba(8, 23, 39, 0.06);
}

.gx-home .gx-stat-card {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
}

.gx-stat-value {
    display: block;
    font-family: var(--gx-font-display), sans-serif;
    font-size: 1.55rem;
    font-weight: 800;
    line-height: 1;
    color: var(--gx-navy);
    letter-spacing: -0.04em;
}

.gx-home .gx-stat-value {
    color: #ffffff;
}

.gx-stat-label {
    display: block;
    margin-top: 0.42rem;
    font-size: 0.82rem;
    line-height: 1.45;
    color: var(--gx-ink-soft);
}

.gx-home .gx-stat-label {
    color: rgba(255, 255, 255, 0.7);
}

.gx-section-head {
    display: grid;
    gap: 0.9rem;
    margin-bottom: 1rem;
}

.gx-section-text {
    max-width: 70ch;
}

.gx-command-section {
    padding-top: 1rem;
}

.gx-command-shell {
    display: grid;
    gap: 1.1rem;
    padding: 1.2rem;
    border-radius: 34px;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(244, 247, 251, 0.96) 100%);
    color: var(--gx-ink);
    border: 1px solid rgba(8, 23, 39, 0.08);
    box-shadow: var(--gx-shadow);
}

.gx-command-copy .gx-section-title,
.gx-command-copy .gx-section-text,
.gx-command-copy .gx-eyebrow {
    color: var(--gx-ink);
}

.gx-command-copy .gx-section-text {
    color: var(--gx-ink-soft);
    opacity: 1;
}

.gx-command-grid {
    display: grid;
    gap: 0.9rem;
}

.gx-command-item {
    padding: 1rem;
    border-radius: 24px;
    background: #ffffff;
    border: 1px solid rgba(8, 23, 39, 0.08);
}

.gx-command-no {
    display: inline-flex;
    align-items: center;
    min-height: 28px;
    padding: 0.24rem 0.58rem;
    border-radius: 999px;
    background: rgba(8, 23, 39, 0.06);
    color: var(--gx-navy);
    font-family: var(--gx-font-ui), sans-serif;
    font-size: 0.74rem;
    font-weight: 800;
    letter-spacing: 0.12em;
}

.gx-command-item h3 {
    margin: 0.8rem 0 0;
    font-family: var(--gx-font-ui), sans-serif;
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: 0.01em;
}

.gx-command-item p {
    margin: 0.55rem 0 0;
    color: var(--gx-ink-soft);
    line-height: 1.68;
}

.gx-scroll-grid {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(84%, 1fr);
    gap: 1rem;
    overflow-x: auto;
    scroll-snap-type: x proximity;
    padding-bottom: 0.2rem;
    -webkit-overflow-scrolling: touch;
}

.gx-scroll-grid > * {
    scroll-snap-align: start;
}

.gx-vertical-card {
    --gx-accent: var(--gx-brand);
    position: relative;
    display: flex;
    flex-direction: column;
    min-height: 100%;
    padding: 1.15rem;
    border: 1px solid rgba(8, 23, 39, 0.08);
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.97) 100%);
}

.gx-vertical-card::before {
    content: "";
    position: absolute;
    top: -40px;
    right: -26px;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: radial-gradient(circle, var(--gx-accent) 0%, rgba(255, 255, 255, 0) 70%);
    opacity: 0.14;
}

.gx-vertical-card::after {
    content: "";
    display: block;
    width: 100%;
    height: 4px;
    margin-top: 1.25rem;
    border-radius: 999px;
    background: linear-gradient(90deg, var(--gx-accent), rgba(255, 255, 255, 0));
}

.gx-vertical-card:hover,
.gx-simulator-card:hover,
.gx-blog-card:hover,
.gx-signature-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 34px 84px rgba(5, 25, 45, 0.16);
    border-color: rgba(199, 160, 83, 0.25);
}

.gx-vertical-index {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    min-height: 44px;
    margin-bottom: 1rem;
    border-radius: 16px;
    background: rgba(8, 23, 39, 0.06);
    color: var(--gx-ink);
    font-family: var(--gx-font-ui), sans-serif;
    font-size: 0.8rem;
    font-weight: 800;
    letter-spacing: 0.12em;
}

.gx-card-kicker {
    margin: 0 0 0.7rem;
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--gx-accent, var(--gx-brand));
}

.gx-card-text {
    margin-top: 0.92rem;
}

.gx-text-link {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    margin-top: auto;
    padding-top: 1.1rem;
    font-weight: 800;
    color: var(--gx-ink);
}

.gx-text-link::after {
    content: "->";
    font-size: 0.9rem;
}

.gx-cta-band {
    display: grid;
    gap: 1rem;
    padding: 1.15rem 1.2rem;
    margin-top: 1.2rem;
    background:
        linear-gradient(135deg, rgba(5, 20, 34, 0.98) 0%, rgba(11, 49, 83, 0.96) 58%, rgba(199, 160, 83, 0.74) 100%);
    border: 1px solid rgba(0, 42, 85, 0.2);
    color: #ffffff;
}

.gx-cta-band .gx-eyebrow,
.gx-cta-band .gx-section-text,
.gx-cta-band .gx-section-title {
    color: #ffffff;
}

.gx-cta-band .gx-section-text {
    max-width: 62ch;
    opacity: 0.84;
}

.gx-cta-band .gx-btn-ghost {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.18);
    color: #ffffff;
}

.gx-simulator-grid,
.gx-blog-grid,
.gx-signature-grid {
    display: grid;
    gap: 1rem;
}

.gx-simulator-card,
.gx-blog-card,
.gx-signature-card,
.gx-lead-card {
    border: 1px solid rgba(8, 23, 39, 0.08);
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(247, 249, 252, 0.98) 100%);
}

.gx-simulator-card {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
    padding: 1.05rem;
}

.gx-simulator-top,
.gx-blog-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.85rem;
}

.gx-simulator-mark {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 56px;
    min-height: 42px;
    padding: 0.55rem 0.75rem;
    border-radius: 14px;
    background: rgba(11, 42, 74, 0.08);
    color: var(--gx-navy);
    font-weight: 800;
    letter-spacing: 0.08em;
}

.gx-legacy-pill,
.gx-simulator-path {
    display: inline-flex;
    align-items: center;
    min-height: 34px;
    padding: 0.4rem 0.72rem;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 700;
}

.gx-legacy-pill {
    color: #0b5cab;
    background: rgba(11, 92, 171, 0.1);
}

.gx-simulator-meta {
    line-height: 1.68;
}

.gx-simulator-footer {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.85rem;
    margin-top: auto;
    padding-top: 0.2rem;
}

.gx-simulator-path {
    font-family: monospace;
    color: var(--gx-ink);
    background: rgba(8, 23, 39, 0.06);
}

.gx-blog-card {
    display: flex;
    flex-direction: column;
}

.gx-blog-image {
    display: block;
    aspect-ratio: 16 / 10;
    background: linear-gradient(135deg, rgba(11, 42, 74, 0.12), rgba(199, 160, 83, 0.18));
}

.gx-blog-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.gx-blog-body {
    display: flex;
    flex: 1 1 auto;
    flex-direction: column;
    gap: 0.8rem;
    padding: 1.05rem;
}

.gx-blog-title a {
    color: inherit;
}

.gx-blog-label {
    display: inline-flex;
    align-items: center;
    min-height: 30px;
    padding: 0.35rem 0.62rem;
    border-radius: 999px;
    background: rgba(199, 160, 83, 0.12);
    color: #8f6b25;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.gx-blog-card-featured {
    border-color: rgba(8, 23, 39, 0.12);
    background:
        linear-gradient(145deg, rgba(6, 22, 37, 0.98) 0%, rgba(11, 46, 78, 0.96) 100%);
    color: #ffffff;
}

.gx-blog-card-featured .gx-blog-title {
    font-size: clamp(1.7rem, 4.2vw, 2.8rem);
}

.gx-blog-card-featured .gx-blog-summary,
.gx-blog-card-featured .gx-blog-meta,
.gx-blog-card-featured .gx-blog-kicker {
    color: rgba(255, 255, 255, 0.74);
}

.gx-blog-card-featured .gx-text-link {
    color: #ffffff;
}

.gx-blog-card-featured .gx-blog-image {
    background: linear-gradient(135deg, rgba(17, 65, 109, 0.3), rgba(199, 160, 83, 0.22));
}

.gx-blog-kicker {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--gx-ink-soft);
}

.gx-blog-kicker::before {
    content: "";
    width: 9px;
    height: 9px;
    border-radius: 999px;
    background: var(--gx-badge, var(--gx-brand));
}

.gx-blog-meta {
    font-size: 0.85rem;
}

.gx-blog-meta a {
    color: inherit;
}

.gx-inline-links {
    margin-top: 1rem;
}

.gx-inline-links a {
    font-weight: 800;
    color: var(--gx-ink);
}

.gx-home-signature {
    padding-top: 0.9rem;
}

.gx-signature-card {
    position: relative;
    padding: 1.15rem;
}

.gx-signature-card::before {
    content: "";
    position: absolute;
    inset: 0 auto 0 0;
    width: 4px;
    border-radius: 24px 0 0 24px;
    background: linear-gradient(180deg, var(--gx-brand), rgba(11, 42, 74, 0.8));
}

.gx-signature-no {
    display: inline-flex;
    align-items: center;
    min-height: 26px;
    padding: 0.2rem 0.55rem;
    border-radius: 999px;
    background: rgba(8, 23, 39, 0.06);
    color: var(--gx-navy);
    font-family: var(--gx-font-ui), sans-serif;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.1em;
}

.gx-signature-title {
    margin: 0.82rem 0 0;
    font-family: var(--gx-font-ui), sans-serif;
    font-size: 1rem;
    font-weight: 800;
    color: var(--gx-ink);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.gx-signature-text {
    margin: 0.72rem 0 0;
    color: var(--gx-ink-soft);
    line-height: 1.72;
}

.gx-lead-section {
    padding-bottom: 0.4rem;
}

.gx-lead-aside,
.gx-lead-card {
    padding: 1.15rem;
    border: 1px solid rgba(8, 23, 39, 0.08);
}

.gx-home .gx-lead-aside {
    background:
        linear-gradient(155deg, rgba(5, 20, 34, 0.98) 0%, rgba(10, 46, 79, 0.96) 62%, rgba(199, 160, 83, 0.72) 100%);
    color: #ffffff;
}

.gx-home .gx-lead-aside .gx-eyebrow,
.gx-home .gx-lead-aside .gx-section-title,
.gx-home .gx-lead-aside .gx-section-text {
    color: #ffffff;
}

.gx-home .gx-lead-aside .gx-section-text {
    opacity: 0.84;
}

.gx-home .gx-contact-chip {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.14);
}

.gx-home .gx-lead-card {
    background: rgba(255, 255, 255, 0.99);
}

.gx-contact-list {
    margin-top: 1.25rem;
}

.gx-contact-chip {
    display: inline-flex;
    align-items: center;
    min-height: 46px;
    padding: 0.8rem 0.95rem;
    border-radius: 14px;
    background: rgba(8, 23, 39, 0.05);
    color: var(--gx-ink);
    font-weight: 700;
}

.gx-form-shell {
    display: grid;
    gap: 1rem;
}

.gx-form-intro {
    display: grid;
    gap: 0.65rem;
}

.gx-form-grid {
    display: grid;
    gap: 0.85rem;
}

.gx-form-field {
    display: grid;
    gap: 0.42rem;
}

.gx-form-field label,
.gx-check {
    font-size: 0.9rem;
    color: var(--gx-ink-soft);
}

.gx-form-field input,
.gx-form-field textarea {
    width: 100%;
    min-height: 56px;
    padding: 0.95rem 1rem;
    border-radius: 16px;
    border: 1px solid rgba(8, 23, 39, 0.12);
    background: #ffffff;
    color: var(--gx-ink);
    font-size: 1rem;
    line-height: 1.5;
}

.gx-form-field textarea {
    min-height: 140px;
    resize: vertical;
}

.gx-form-field input:focus,
.gx-form-field textarea:focus {
    outline: 0;
    border-color: rgba(199, 160, 83, 0.7);
    box-shadow: 0 0 0 4px rgba(199, 160, 83, 0.12);
}

.gx-check {
    display: flex;
    align-items: flex-start;
    gap: 0.65rem;
}

.gx-check input {
    margin-top: 0.2rem;
    width: 18px;
    height: 18px;
    flex: 0 0 auto;
}

.gx-check a {
    color: var(--gx-ink);
    font-weight: 800;
}

.gx-captcha-row {
    min-height: 1px;
}

.gx-form-actions {
    align-items: center;
    justify-content: space-between;
}

.gx-form-submit {
    width: 100%;
}

.gx-form-note {
    margin: 0;
    font-size: 0.88rem;
    color: var(--gx-ink-soft);
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
    padding: 1rem 1.05rem;
    border-radius: 18px;
    background: rgba(8, 23, 39, 0.05);
    color: var(--gx-ink-soft);
}

@keyframes gxRise {
    from {
        opacity: 0;
        transform: translateY(22px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes gxFloat {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(10px);
    }
}

.gx-home .gx-hero-frame,
.gx-home .gx-signature-card,
.gx-home .gx-command-item,
.gx-home .gx-vertical-card,
.gx-home .gx-blog-card,
.gx-home .gx-lead-aside,
.gx-home .gx-lead-card,
.gx-simulators-hub .gx-hero-copy,
.gx-simulators-hub .gx-hero-panel,
.gx-simulators-hub .gx-simulator-card {
    animation: gxRise 0.72s cubic-bezier(0.22, 1, 0.36, 1) both;
}

.gx-home .gx-signature-card:nth-child(2),
.gx-home .gx-blog-card:nth-child(2),
.gx-home .gx-command-item:nth-child(2),
.gx-simulators-hub .gx-simulator-card:nth-child(2) {
    animation-delay: 0.12s;
}

.gx-home .gx-signature-card:nth-child(3),
.gx-home .gx-blog-card:nth-child(3),
.gx-home .gx-command-item:nth-child(3),
.gx-simulators-hub .gx-simulator-card:nth-child(3) {
    animation-delay: 0.18s;
}

.gx-home .gx-blog-card:nth-child(4),
.gx-home .gx-vertical-card:nth-child(4),
.gx-simulators-hub .gx-simulator-card:nth-child(4) {
    animation-delay: 0.24s;
}

@media (min-width: 640px) {
    .gx-form-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .gx-form-field-full,
    .gx-check,
    .gx-captcha-row,
    .gx-form-actions {
        grid-column: 1 / -1;
    }
}

@media (min-width: 768px) {
    .gx-shell {
        padding: 28px 0 92px;
    }

    .gx-hero-copy,
    .gx-hero-panel,
    .gx-cta-band,
    .gx-lead-card,
    .gx-lead-aside,
    .gx-command-shell {
        padding: 1.55rem;
    }

    .gx-signature-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .gx-blog-card-featured {
        grid-column: 1 / -1;
        display: grid;
        grid-template-columns: minmax(0, 1.04fr) minmax(0, 0.96fr);
        align-items: stretch;
    }

    .gx-blog-card-featured .gx-blog-image {
        min-height: 100%;
        aspect-ratio: auto;
    }

    .gx-scroll-grid {
        grid-auto-columns: minmax(320px, 1fr);
    }

    .gx-simulator-grid,
    .gx-blog-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (min-width: 992px) {
    .gx-home .gx-hero-grid {
        grid-template-columns: minmax(0, 1.08fr) minmax(360px, 0.92fr);
        align-items: stretch;
    }

    .gx-simulators-hub .gx-hero-grid {
        grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.95fr);
        align-items: stretch;
    }

    .gx-stage-main {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: end;
    }

    .gx-stage-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .gx-command-shell {
        grid-template-columns: minmax(320px, 0.95fr) minmax(0, 1.05fr);
        align-items: start;
    }

    .gx-command-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .gx-lead-grid {
        grid-template-columns: minmax(300px, 0.85fr) minmax(0, 1.15fr);
        align-items: start;
    }

    .gx-section-head-split {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: end;
    }

    .gx-simulator-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (min-width: 1200px) {
    .gx-scroll-grid {
        grid-template-columns: repeat(5, minmax(0, 1fr));
        grid-auto-flow: row;
        overflow: visible;
    }
}

@media (max-width: 575.98px) {
    .gx-hero-copy,
    .gx-hero-panel,
    .gx-simulator-card,
    .gx-blog-body,
    .gx-lead-card,
    .gx-lead-aside,
    .gx-cta-band,
    .gx-command-shell {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .gx-stage-top,
    .gx-stage-footer,
    .gx-blog-top,
    .gx-simulator-top {
        align-items: flex-start;
        flex-direction: column;
    }

    .gx-hero-title {
        max-width: 11ch;
    }

    .gx-stat-grid,
    .gx-hero-metrics {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .gx-simulator-footer {
        align-items: stretch;
    }
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

body.gx-marketing-home {
    background: #f6f7fb;
}

[data-gx-reveal] {
    opacity: 0;
    transform: translateY(24px);
    transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1), transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
}

[data-gx-reveal].is-visible {
    opacity: 1;
    transform: translateY(0);
}

.gx-home .gx-shell {
    padding-top: 0;
}

.gx-sitebar-wrap {
    position: sticky;
    top: 0;
    z-index: 40;
    padding: 14px 0 0;
}

.gx-sitebar {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
    min-height: 78px;
    padding: 0.95rem 1rem;
    border-radius: 26px;
    background: rgba(255, 255, 255, 0.76);
    border: 1px solid rgba(11, 19, 32, 0.08);
    box-shadow: 0 10px 26px rgba(11, 19, 32, 0.05);
    backdrop-filter: blur(18px);
    transition: transform 0.25s ease, box-shadow 0.25s ease, background-color 0.25s ease;
}

.gx-sitebar.is-scrolled {
    background: rgba(255, 255, 255, 0.92);
    box-shadow: 0 18px 38px rgba(11, 19, 32, 0.08);
}

.gx-sitebar-brand,
.gx-sitebar-brand img {
    display: block;
}

.gx-sitebar-brand img {
    width: auto;
    max-width: 156px;
    height: 30px;
    object-fit: contain;
}

.gx-sitebar-menu {
    display: flex;
    flex: 1 1 auto;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.gx-sitebar-nav,
.gx-sitebar-actions {
    display: flex;
    align-items: center;
    gap: 0.85rem;
}

.gx-sitebar-link,
.gx-sitebar-secondary {
    color: var(--gx-ink);
    font-family: var(--gx-font-ui), sans-serif;
    font-size: 0.92rem;
    font-weight: 600;
    line-height: 1;
}

.gx-sitebar-link:hover,
.gx-sitebar-secondary:hover {
    color: var(--gx-brand);
}

.gx-sitebar-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    padding: 0.8rem 1rem;
    border-radius: 14px;
    background: rgba(11, 19, 32, 0.04);
}

.gx-sitebar-toggle {
    display: none;
    flex-direction: column;
    justify-content: center;
    gap: 4px;
    width: 46px;
    height: 46px;
    padding: 0;
    border: 0;
    border-radius: 14px;
    background: rgba(11, 19, 32, 0.04);
}

.gx-sitebar-toggle span {
    display: block;
    width: 18px;
    height: 2px;
    margin: 0 auto;
    border-radius: 999px;
    background: var(--gx-ink);
}

.gx-home .gx-hero {
    padding-top: 18px;
}

.gx-home .gx-hero-frame {
    padding: 1rem;
    border-radius: 38px;
    background:
        radial-gradient(circle at 12% 18%, rgba(107, 70, 255, 0.16), rgba(107, 70, 255, 0) 26%),
        radial-gradient(circle at 88% 16%, rgba(0, 212, 255, 0.12), rgba(0, 212, 255, 0) 26%),
        linear-gradient(180deg, rgba(255, 255, 255, 0.94) 0%, rgba(244, 247, 252, 0.94) 100%);
    border: 1px solid rgba(255, 255, 255, 0.78);
    box-shadow: 0 28px 70px rgba(11, 19, 32, 0.08);
}

.gx-home .gx-hero-frame::before {
    background:
        radial-gradient(circle at 22% 18%, rgba(107, 70, 255, 0.12), rgba(107, 70, 255, 0) 22%),
        linear-gradient(135deg, rgba(255, 255, 255, 0.34), rgba(255, 255, 255, 0));
}

.gx-home .gx-hero-frame::after {
    width: 400px;
    height: 400px;
    right: -12%;
    bottom: -24%;
    background: radial-gradient(circle, rgba(107, 70, 255, 0.14) 0%, rgba(0, 212, 255, 0.04) 42%, rgba(0, 212, 255, 0) 70%);
    filter: blur(12px);
}

.gx-home .gx-hero-layout {
    display: grid;
    gap: 1.35rem;
    align-items: center;
}

.gx-home .gx-hero-copy {
    padding: 0.3rem;
}

.gx-home .gx-eyebrow {
    color: #5b46cf;
}

.gx-home .gx-eyebrow::before {
    background: linear-gradient(90deg, var(--gx-brand) 0%, var(--gx-brand-strong) 100%);
    box-shadow: 0 0 0 6px rgba(107, 70, 255, 0.12);
}

.gx-home .gx-hero-title {
    max-width: 9ch;
    color: var(--gx-ink);
    font-size: clamp(2.55rem, 7vw, 5.8rem);
}

.gx-home .gx-hero-text {
    max-width: 58ch;
    color: rgba(11, 19, 32, 0.72);
}

.gx-home .gx-actions {
    margin-top: 1.55rem;
}

.gx-home .gx-hero-proof {
    margin-top: 1.3rem;
}

.gx-home .gx-hero-proof-item {
    background: rgba(107, 70, 255, 0.08);
    border-color: rgba(107, 70, 255, 0.1);
    color: var(--gx-ink);
}

.gx-home .gx-hero-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.8rem;
    margin-top: 1.45rem;
}

.gx-home .gx-hero-stat {
    padding: 1rem;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.78);
    border: 1px solid rgba(11, 19, 32, 0.07);
    box-shadow: 0 8px 24px rgba(11, 19, 32, 0.05);
}

.gx-home .gx-hero-stat strong {
    display: block;
    color: var(--gx-ink);
    font-family: var(--gx-font-display), sans-serif;
    font-size: 1.45rem;
    font-weight: 700;
    line-height: 1;
    letter-spacing: -0.05em;
}

.gx-home .gx-hero-stat span {
    display: block;
    margin-top: 0.42rem;
    color: var(--gx-ink-soft);
    font-size: 0.82rem;
    line-height: 1.45;
}

.gx-home .gx-hero-visual {
    position: relative;
    min-height: 420px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.gx-hero-glow {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}

.gx-hero-glow-a {
    top: 30px;
    right: 26px;
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(0, 212, 255, 0.2) 0%, rgba(0, 212, 255, 0) 70%);
}

.gx-hero-glow-b {
    left: 10px;
    bottom: 40px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(107, 70, 255, 0.18) 0%, rgba(107, 70, 255, 0) 70%);
}

.gx-phone-mockup {
    position: relative;
    z-index: 2;
    width: min(100%, 380px);
}

.gx-phone-shell {
    position: relative;
    max-width: 340px;
    margin: 0 auto;
    padding: 14px 12px 14px;
    border-radius: 38px;
    background:
        linear-gradient(180deg, #14182b 0%, #30265f 50%, #123f77 100%);
    box-shadow: 0 28px 80px rgba(11, 19, 32, 0.2);
}

.gx-phone-camera {
    width: 35%;
    height: 26px;
    margin: 0 auto 12px;
    border-radius: 999px;
    background: rgba(3, 6, 15, 0.92);
}

.gx-phone-screen {
    padding: 1rem;
    border-radius: 28px;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(246, 248, 255, 0.95) 100%);
}

.gx-phone-screen-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.65rem;
}

.gx-phone-pill {
    display: inline-flex;
    align-items: center;
    min-height: 30px;
    padding: 0.35rem 0.6rem;
    border-radius: 999px;
    background: rgba(107, 70, 255, 0.1);
    color: #5339cb;
    font-size: 0.74rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.gx-phone-pill-soft {
    background: rgba(11, 19, 32, 0.05);
    color: var(--gx-ink-soft);
}

.gx-phone-card {
    border-radius: 24px;
}

.gx-phone-card-main {
    display: grid;
    gap: 0.65rem;
    margin-top: 0.9rem;
    padding: 1rem;
    background: linear-gradient(90deg, #6b46ff 0%, #00d4ff 100%);
    color: #ffffff;
    box-shadow: 0 14px 30px rgba(107, 70, 255, 0.2);
}

.gx-phone-kicker {
    margin: 0;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    opacity: 0.76;
}

.gx-phone-card-main strong {
    display: block;
    font-size: 1.25rem;
    line-height: 1.15;
    letter-spacing: -0.04em;
}

.gx-phone-card-main span {
    font-size: 0.88rem;
    line-height: 1.55;
    opacity: 0.92;
}

.gx-phone-chart {
    margin-top: 0.9rem;
    padding: 0.95rem;
    border-radius: 22px;
    background: rgba(11, 19, 32, 0.03);
    border: 1px solid rgba(11, 19, 32, 0.05);
}

.gx-phone-chart-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.6rem;
    color: var(--gx-ink);
    font-size: 0.84rem;
    font-weight: 600;
}

.gx-phone-chart-top strong {
    font-size: 0.9rem;
}

.gx-phone-bars {
    display: flex;
    align-items: flex-end;
    gap: 0.45rem;
    min-height: 90px;
    margin-top: 0.8rem;
}

.gx-phone-bars span {
    flex: 1 1 0;
    border-radius: 999px 999px 14px 14px;
    background: linear-gradient(180deg, rgba(107, 70, 255, 0.78) 0%, rgba(0, 212, 255, 0.9) 100%);
}

.gx-phone-mini-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.65rem;
    margin-top: 0.9rem;
}

.gx-phone-mini {
    padding: 0.8rem 0.7rem;
    border-radius: 18px;
    background: rgba(11, 19, 32, 0.04);
    border: 1px solid rgba(11, 19, 32, 0.05);
}

.gx-phone-mini small,
.gx-value-mark,
.gx-feature-mark {
    font-family: var(--gx-font-ui), sans-serif;
}

.gx-phone-mini small {
    display: block;
    color: #5b46cf;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.gx-phone-mini strong {
    display: block;
    margin-top: 0.35rem;
    color: var(--gx-ink);
    font-size: 0.92rem;
    line-height: 1.2;
}

.gx-phone-mini span {
    display: block;
    margin-top: 0.28rem;
    color: var(--gx-ink-soft);
    font-size: 0.74rem;
    line-height: 1.4;
}

.gx-float-card {
    position: absolute;
    z-index: 3;
    max-width: 200px;
    padding: 0.8rem 0.95rem;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.82);
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 16px 34px rgba(11, 19, 32, 0.08);
    backdrop-filter: blur(16px);
    color: var(--gx-ink);
    font-size: 0.86rem;
    font-weight: 600;
    line-height: 1.45;
}

.gx-float-card-left {
    left: 0;
    bottom: 86px;
}

.gx-float-card-right {
    right: 4px;
    top: 78px;
}

.gx-trust-strip {
    padding-top: 0.85rem;
}

.gx-trust-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
}

.gx-trust-item {
    display: inline-flex;
    align-items: center;
    min-height: 44px;
    padding: 0.8rem 1rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.82);
    border: 1px solid rgba(11, 19, 32, 0.06);
    box-shadow: 0 8px 24px rgba(11, 19, 32, 0.05);
    color: var(--gx-ink-soft);
    font-size: 0.88rem;
    font-weight: 600;
}

.gx-solutions-section,
.gx-value-band,
.gx-banner-section {
    padding-top: 1.8rem;
}

.gx-feature-grid,
.gx-value-grid {
    display: grid;
    gap: 1rem;
}

.gx-feature-card {
    --gx-accent: var(--gx-brand);
    position: relative;
    display: flex;
    flex-direction: column;
    min-height: 100%;
    padding: 1.2rem;
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(11, 19, 32, 0.07);
    box-shadow: 0 8px 24px rgba(11, 19, 32, 0.05);
    overflow: hidden;
}

.gx-feature-card::before {
    content: "";
    position: absolute;
    top: -40px;
    right: -30px;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: radial-gradient(circle, var(--gx-accent) 0%, rgba(255, 255, 255, 0) 70%);
    opacity: 0.12;
}

.gx-feature-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 36px rgba(11, 19, 32, 0.08);
}

.gx-feature-mark,
.gx-value-mark {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 52px;
    min-height: 52px;
    border-radius: 16px;
    background: linear-gradient(135deg, var(--gx-brand) 0%, var(--gx-brand-strong) 100%);
    color: #ffffff;
    font-size: 0.84rem;
    font-weight: 700;
    letter-spacing: 0.12em;
}

.gx-feature-title {
    margin: 0;
    font-family: var(--gx-font-display), sans-serif;
    font-size: 1.48rem;
    line-height: 1.08;
    letter-spacing: -0.04em;
    color: var(--gx-ink);
}

.gx-value-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.gx-value-card {
    padding: 1.2rem;
    border-radius: 24px;
    background: linear-gradient(145deg, #0f1730 0%, #32226f 62%, #0f82c6 100%);
    box-shadow: 0 20px 46px rgba(11, 19, 32, 0.12);
    color: #ffffff;
}

.gx-value-title {
    margin: 0.95rem 0 0;
    font-family: var(--gx-font-ui), sans-serif;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.01em;
}

.gx-value-text {
    margin: 0.55rem 0 0;
    color: rgba(255, 255, 255, 0.78);
    line-height: 1.68;
}

.gx-simulator-mark {
    background: rgba(107, 70, 255, 0.08);
    color: #5b46cf;
}

.gx-legacy-pill {
    color: #5b46cf;
    background: rgba(107, 70, 255, 0.08);
}

.gx-blog-label {
    background: rgba(255, 255, 255, 0.18);
    color: #ffffff;
}

.gx-blog-card-featured {
    background: linear-gradient(145deg, #0f1730 0%, #2f226f 62%, #0d7bb7 100%);
}

.gx-home .gx-blog-card-featured .gx-blog-kicker,
.gx-home .gx-blog-card-featured .gx-blog-meta,
.gx-home .gx-blog-card-featured .gx-blog-summary {
    color: rgba(255, 255, 255, 0.76);
}

.gx-banner-cta {
    display: grid;
    gap: 1rem;
    padding: 1.3rem;
    border-radius: 30px;
    background: linear-gradient(90deg, #6b46ff 0%, #00d4ff 100%);
    box-shadow: 0 20px 44px rgba(107, 70, 255, 0.18);
    color: #ffffff;
}

.gx-banner-cta .gx-eyebrow,
.gx-banner-cta .gx-section-title,
.gx-banner-cta .gx-section-text {
    color: #ffffff;
}

.gx-banner-cta .gx-section-text {
    opacity: 0.88;
}

.gx-banner-cta .gx-btn-secondary {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.2);
}

.gx-home .gx-lead-aside {
    background: linear-gradient(160deg, #111830 0%, #342577 56%, #00aee0 100%);
}

.gx-home .gx-contact-chip {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.14);
}

body.gx-marketing-home #footer {
    margin-top: 3.5rem;
    background: transparent;
}

body.gx-marketing-home #footer .footer-inner,
body.gx-marketing-home #footer > .container {
    padding-top: 2.6rem;
    padding-bottom: 2rem;
    border-radius: 32px 32px 0 0;
    background: linear-gradient(160deg, #0d1424 0%, #1e2050 58%, #0f6ea2 100%);
    box-shadow: 0 -12px 40px rgba(11, 19, 32, 0.08);
}

body.gx-marketing-home #footer .footer-copyright,
body.gx-marketing-home #footer .footer-bottom {
    background: transparent;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}

body.gx-marketing-home #footer,
body.gx-marketing-home #footer .widget-title,
body.gx-marketing-home #footer .title,
body.gx-marketing-home #footer h4,
body.gx-marketing-home #footer h5,
body.gx-marketing-home #footer a,
body.gx-marketing-home #footer p,
body.gx-marketing-home #footer li,
body.gx-marketing-home #footer .copyright {
    color: #ffffff;
}

body.gx-marketing-home #footer .footer-about,
body.gx-marketing-home #footer .description,
body.gx-marketing-home #footer .copyright,
body.gx-marketing-home #footer .nav-footer a,
body.gx-marketing-home #footer .footer-widget p,
body.gx-marketing-home #footer .f-random-list .title a {
    color: rgba(255, 255, 255, 0.74);
}

body.gx-marketing-home #footer .newsletter-input,
body.gx-marketing-home #footer .newsletter-inputs .form-input,
body.gx-marketing-home #footer .newsletter-button {
    border-radius: 14px;
}

body.gx-marketing-home #footer .newsletter-button,
body.gx-marketing-home #footer .btn-custom {
    background: linear-gradient(90deg, #6b46ff 0%, #00d4ff 100%);
    border-color: transparent;
    color: #ffffff;
}

@media (min-width: 768px) {
    .gx-home .gx-hero-layout {
        grid-template-columns: minmax(0, 1.02fr) minmax(340px, 0.98fr);
    }

    .gx-feature-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .gx-banner-cta {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
    }
}

@media (min-width: 1200px) {
    .gx-feature-grid {
        grid-template-columns: repeat(5, minmax(0, 1fr));
    }
}

@media (max-width: 991.98px) {
    .gx-sitebar-toggle {
        display: inline-flex;
    }

    .gx-sitebar-menu {
        position: absolute;
        top: calc(100% + 0.75rem);
        left: 0;
        right: 0;
        display: none;
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
        padding: 1rem;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid rgba(11, 19, 32, 0.08);
        box-shadow: 0 20px 46px rgba(11, 19, 32, 0.08);
    }

    .gx-sitebar.is-open .gx-sitebar-menu {
        display: flex;
    }

    .gx-sitebar-nav,
    .gx-sitebar-actions {
        flex-direction: column;
        align-items: stretch;
        width: 100%;
    }

    .gx-sitebar-link,
    .gx-sitebar-secondary {
        display: block;
        width: 100%;
        padding: 0.25rem 0;
    }

    .gx-home .gx-hero-layout {
        grid-template-columns: 1fr;
    }

    .gx-float-card {
        display: none;
    }
}

@media (max-width: 767.98px) {
    .gx-home .gx-hero-frame {
        padding: 0.9rem;
        border-radius: 28px;
    }

    .gx-home .gx-hero-title {
        max-width: 10ch;
        font-size: clamp(2.45rem, 10vw, 3.55rem);
    }

    .gx-home .gx-hero-stat {
        padding: 0.9rem 0.8rem;
    }

    .gx-phone-shell {
        max-width: 320px;
    }

    .gx-trust-list {
        overflow-x: auto;
        flex-wrap: nowrap;
        padding-bottom: 0.2rem;
        -webkit-overflow-scrolling: touch;
    }

    .gx-feature-grid {
        grid-auto-flow: column;
        grid-auto-columns: minmax(84%, 1fr);
        overflow-x: auto;
        padding-bottom: 0.2rem;
        scroll-snap-type: x proximity;
        -webkit-overflow-scrolling: touch;
    }

    .gx-feature-card {
        scroll-snap-align: start;
    }

    .gx-value-grid {
        grid-template-columns: 1fr;
    }

    .gx-banner-cta .gx-actions,
    .gx-sitebar-actions .gx-btn {
        width: 100%;
    }
}

@media (prefers-reduced-motion: reduce) {
    .gx-marketing *,
    .gx-marketing *::before,
    .gx-marketing *::after {
        animation: none !important;
        transition: none !important;
        scroll-behavior: auto !important;
    }
}
</style>
