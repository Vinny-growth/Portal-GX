<?= view('marketing/_shared_styles'); ?>
<style>
.gx-wealth {
    background:
        radial-gradient(circle at top left, rgba(199,160,83,0.18), transparent 32%),
        linear-gradient(180deg, #fbfaf6 0%, #ffffff 22%, #f7f8fa 100%);
}

.gx-wealth .gx-hero {
    position: relative;
    padding-top: 108px;
    padding-bottom: 88px;
    overflow: hidden;
}

.gx-wealth .gx-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        linear-gradient(135deg, rgba(0,42,85,0.08), transparent 45%),
        radial-gradient(circle at right top, rgba(199,160,83,0.16), transparent 28%);
    pointer-events: none;
}

.gx-wealth-hero-panel,
.gx-wealth-diagnostic-card,
.gx-wealth-insights-card,
.gx-wealth-deliverable-card,
.gx-wealth-schedule-card {
    position: relative;
    border: 1px solid rgba(0,42,85,0.08);
    background: rgba(255,255,255,0.92);
    box-shadow: 0 28px 60px rgba(0,42,85,0.08);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
}

.gx-wealth-hero-panel {
    border-radius: 24px;
    padding: 28px;
}

.gx-wealth-panel-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 22px;
}

.gx-wealth-panel-kicker {
    margin: 0 0 4px;
    font-size: 12px;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--gx-text-tertiary);
}

.gx-wealth-panel-title {
    margin: 0;
    font-family: var(--gx-font-heading);
    font-size: clamp(22px, 3vw, 30px);
    line-height: 1.05;
    color: var(--gx-navy);
}

.gx-wealth-panel-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 7px 12px;
    border-radius: 999px;
    background: rgba(199,160,83,0.14);
    color: #8f6c2e;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.gx-wealth-mini-grid,
.gx-wealth-kpi-stack,
.gx-wealth-form-grid,
.gx-wealth-feature-grid,
.gx-wealth-deliverables-grid,
.gx-wealth-input-grid {
    display: grid;
    gap: 16px;
}

.gx-wealth-mini-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    margin-bottom: 22px;
}

.gx-wealth-mini-card {
    padding: 18px;
    border-radius: 20px;
    background: linear-gradient(180deg, rgba(0,42,85,0.04), rgba(0,42,85,0.01));
    border: 1px solid rgba(0,42,85,0.06);
}

.gx-wealth-mini-card span,
.gx-wealth-kpi-card span,
.gx-wealth-field span,
.gx-wealth-form-note,
.gx-wealth-insight-caption {
    display: block;
    color: var(--gx-text-tertiary);
    font-size: 13px;
}

.gx-wealth-mini-card strong,
.gx-wealth-kpi-card strong {
    display: block;
    margin-top: 8px;
    color: var(--gx-navy);
    font-family: var(--gx-font-heading);
    font-size: 19px;
    line-height: 1.25;
}

.gx-wealth-path {
    display: grid;
    gap: 10px;
}

.gx-wealth-path-step {
    position: relative;
    padding: 12px 14px 12px 42px;
    border-radius: 16px;
    background: rgba(255,255,255,0.75);
    border: 1px solid rgba(0,42,85,0.08);
    color: var(--gx-text-secondary);
    font-size: 14px;
}

.gx-wealth-path-step::before {
    content: "";
    position: absolute;
    left: 16px;
    top: 50%;
    width: 12px;
    height: 12px;
    border-radius: 999px;
    transform: translateY(-50%);
    background: rgba(0,42,85,0.18);
}

.gx-wealth-path-step.is-active {
    background: linear-gradient(90deg, rgba(199,160,83,0.18), rgba(0,42,85,0.05));
    color: var(--gx-navy);
    border-color: rgba(199,160,83,0.35);
}

.gx-wealth-path-step.is-active::before {
    background: var(--gx-gold);
}

.gx-wealth-member-progress {
    padding: 18px;
    border-radius: 20px;
    background: linear-gradient(180deg, rgba(0,42,85,0.06), rgba(0,42,85,0.01));
    border: 1px solid rgba(0,42,85,0.06);
}

.gx-wealth-member-progress strong {
    display: block;
    margin-bottom: 8px;
    font-family: var(--gx-font-heading);
    color: var(--gx-navy);
    font-size: 22px;
}

.gx-wealth-progress-bar {
    width: 100%;
    height: 10px;
    border-radius: 999px;
    background: rgba(0,42,85,0.08);
    overflow: hidden;
}

.gx-wealth-progress-fill {
    height: 100%;
    border-radius: inherit;
    background: linear-gradient(90deg, var(--gx-gold), #ddbc76);
}

.gx-wealth-auth-note {
    margin-top: 20px;
    padding: 16px 18px;
    border-radius: 18px;
    border: 1px solid rgba(199,160,83,0.24);
    background: rgba(199,160,83,0.08);
    color: var(--gx-text-secondary);
}

.gx-wealth-auth-note strong {
    color: var(--gx-navy);
}

.gx-wealth-diagnostic-grid,
.gx-wealth-schedule-shell {
    display: grid;
    grid-template-columns: minmax(0, 1.18fr) minmax(320px, 0.82fr);
    gap: 28px;
    align-items: start;
}

.gx-wealth-diagnostic-card,
.gx-wealth-insights-card,
.gx-wealth-schedule-card {
    border-radius: 28px;
    padding: 28px;
}

.gx-wealth-objective-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 22px;
}

.gx-wealth-objective {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    padding: 0 18px;
    border-radius: 999px;
    border: 1px solid rgba(0,42,85,0.12);
    background: #fff;
    color: var(--gx-text-secondary);
    font-family: var(--gx-font-heading);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.gx-wealth-objective:hover,
.gx-wealth-objective.is-active {
    border-color: rgba(199,160,83,0.42);
    background: rgba(199,160,83,0.12);
    color: var(--gx-navy);
}

.gx-wealth-input-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.gx-wealth-feature-grid,
.gx-wealth-deliverables-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.gx-wealth-kpi-stack {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    margin: 22px 0;
}

.gx-wealth-kpi-card {
    padding: 18px;
    border-radius: 20px;
    background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(0,42,85,0.03));
    border: 1px solid rgba(0,42,85,0.08);
}

.gx-wealth-insight-text {
    margin: 0;
    color: var(--gx-text-secondary);
}

.gx-wealth-feature-card,
.gx-wealth-deliverable-card {
    border-radius: 24px;
    padding: 24px;
    border: 1px solid rgba(0,42,85,0.08);
    background: rgba(255,255,255,0.92);
}

.gx-wealth-feature-card strong,
.gx-wealth-deliverable-card h3,
.gx-wealth-form-title {
    display: block;
    margin-bottom: 10px;
    color: var(--gx-navy);
    font-family: var(--gx-font-heading);
    font-size: 22px;
    line-height: 1.15;
}

.gx-wealth-feature-card p,
.gx-wealth-deliverable-card p,
.gx-wealth-form-copy,
.gx-wealth-schedule-list {
    margin: 0;
    color: var(--gx-text-secondary);
}

.gx-wealth-chip-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 18px;
}

.gx-wealth-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 14px;
    border-radius: 999px;
    background: rgba(0,42,85,0.05);
    color: var(--gx-text-secondary);
    font-size: 13px;
    font-weight: 600;
}

.gx-wealth-form-shell {
    display: grid;
    gap: 20px;
}

.gx-wealth-form-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.gx-wealth-field {
    display: block;
}

.gx-wealth-field-full {
    grid-column: 1 / -1;
}

.gx-wealth-field span {
    margin-bottom: 8px;
    font-weight: 700;
    color: var(--gx-navy);
}

.gx-wealth-field input,
.gx-wealth-field select,
.gx-wealth-field textarea {
    width: 100%;
    border: 1px solid rgba(0,42,85,0.12);
    border-radius: 16px;
    background: rgba(255,255,255,0.96);
    color: var(--gx-text);
    font-family: var(--gx-font-body);
    font-size: 15px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}

.gx-wealth-field input,
.gx-wealth-field select {
    height: 54px;
    padding: 0 16px;
}

.gx-wealth-field textarea {
    min-height: 118px;
    padding: 14px 16px;
    resize: vertical;
}

.gx-wealth-field input:focus,
.gx-wealth-field select:focus,
.gx-wealth-field textarea:focus {
    outline: none;
    border-color: rgba(199,160,83,0.75);
    box-shadow: 0 0 0 4px rgba(199,160,83,0.12);
}

.gx-wealth-form-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.gx-wealth-form-feedback {
    min-height: 22px;
    margin: 0;
    color: #a53f2b;
    font-size: 14px;
}

.gx-wealth-form-shell.is-success .gx-wealth-form {
    display: none;
}

.gx-wealth-form-success {
    padding: 22px;
    border-radius: 22px;
    border: 1px solid rgba(47,179,68,0.24);
    background: rgba(47,179,68,0.08);
}

.gx-wealth-form-success strong {
    display: block;
    margin-bottom: 8px;
    color: #176a2d;
    font-family: var(--gx-font-heading);
    font-size: 22px;
}

.gx-wealth-form-success p {
    margin: 0 0 16px;
    color: #245d31;
}

.gx-wealth-form-success .gx-btn {
    min-width: 220px;
}

.gx-wealth-form .gx-check {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin: 0;
    color: var(--gx-text-secondary);
    font-size: 14px;
}

.gx-wealth-form .gx-check input {
    width: 18px;
    height: 18px;
    margin-top: 2px;
    accent-color: var(--gx-gold);
}

.gx-wealth-form .gx-check a {
    color: var(--gx-navy);
    text-decoration: underline;
}

.gx-wealth-page .gx-hp {
    position: absolute;
    left: -9999px;
    width: 1px;
    height: 1px;
    opacity: 0;
    pointer-events: none;
}

.gx-wealth-faq-list {
    display: grid;
    gap: 14px;
}

.gx-wealth-faq-item {
    border-radius: 22px;
    border: 1px solid rgba(0,42,85,0.08);
    background: rgba(255,255,255,0.9);
    overflow: hidden;
}

.gx-wealth-faq-item summary {
    list-style: none;
    cursor: pointer;
    padding: 20px 24px;
    font-family: var(--gx-font-heading);
    font-size: 18px;
    color: var(--gx-navy);
}

.gx-wealth-faq-item summary::-webkit-details-marker {
    display: none;
}

.gx-wealth-faq-item p {
    margin: 0;
    padding: 0 24px 22px;
    color: var(--gx-text-secondary);
}

.gx-wealth-schedule-hero {
    padding-top: 110px;
    padding-bottom: 84px;
}

.gx-wealth-schedule-copy {
    display: grid;
    gap: 20px;
}

.gx-wealth-schedule-list {
    padding-left: 18px;
}

.gx-wealth-schedule-list li + li {
    margin-top: 10px;
}

.gx-wealth-sticky-cta {
    position: fixed;
    left: 16px;
    right: 16px;
    bottom: 16px;
    z-index: 110;
    display: none;
}

.gx-wealth-sticky-cta .gx-btn {
    width: 100%;
    height: 52px;
    border-radius: 18px;
    box-shadow: 0 18px 40px rgba(0,42,85,0.18);
}

@media (max-width: 991px) {
    .gx-wealth-diagnostic-grid,
    .gx-wealth-schedule-shell,
    .gx-wealth-feature-grid,
    .gx-wealth-deliverables-grid {
        grid-template-columns: 1fr;
    }

    .gx-wealth-mini-grid,
    .gx-wealth-kpi-stack,
    .gx-wealth-form-grid,
    .gx-wealth-input-grid {
        grid-template-columns: 1fr;
    }

    .gx-wealth-sticky-cta {
        display: block;
    }
}

@media (max-width: 767px) {
    .gx-wealth .gx-hero {
        padding-top: 96px;
        padding-bottom: 72px;
    }

    .gx-wealth-hero-panel,
    .gx-wealth-diagnostic-card,
    .gx-wealth-insights-card,
    .gx-wealth-schedule-card {
        padding: 22px;
        border-radius: 22px;
    }

    .gx-wealth-panel-top {
        flex-direction: column;
        align-items: flex-start;
    }

    .gx-wealth-form-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .gx-wealth-form-actions .gx-btn {
        width: 100%;
    }
}

/* ══════════════════════════════════════════════════════════════════════════ */
/* Nexus Brutalist — Wealth                                                     */
/* Alinha a /wealth ao design system GX (colors_and_type.css + ui_kits/nexus):  */
/* cantos 0px, sombras hard-offset, Inter 900 UPPERCASE nos títulos, números    */
/* em JetBrains Mono tabular, watermark GXC e tech-brackets. Substitui o antigo  */
/* tema "Vault Modernism" (serifa/glass/soft). A base de marketing já é          */
/* brutalista — aqui só reforçamos os componentes específicos do wealth e        */
/* deixamos a base (hero-title, section-title, botões, labels) reaparecer.       */
/* ══════════════════════════════════════════════════════════════════════════ */

/* Página: fundo claro sóbrio, sem glow difuso */
.gx-wealth {
    font-family: var(--gx-font-body);
    background:
        radial-gradient(ellipse 820px 360px at 90% 3%, rgba(201,169,106,0.08) 0%, transparent 58%),
        linear-gradient(180deg, #ffffff 0%, var(--gx-bg-warm) 100%);
}

/* Hero: restaura o watermark "GXC" (o tema anterior o trocou por um gradiente) */
.gx-wealth .gx-hero::before {
    content: "GXC";
    inset: auto;
    top: 56px;
    right: -40px;
    left: auto;
    bottom: auto;
    background: none;
    font-family: var(--gx-font-heading);
    font-size: clamp(180px, 22vw, 260px);
    font-weight: 900;
    letter-spacing: -0.06em;
    line-height: 0.8;
    color: var(--gx-navy);
    opacity: 0.04;
    white-space: nowrap;
    z-index: 0;
}

/* ── Cartões e painéis: 0px, borda 1px, sombra hard-offset, sem blur ────────── */
.gx-wealth-hero-panel,
.gx-wealth-diagnostic-card,
.gx-wealth-insights-card,
.gx-wealth-deliverable-card,
.gx-wealth-feature-card,
.gx-wealth-schedule-card {
    position: relative;
    border-radius: 0;
    border: 1px solid var(--gx-border);
    background: var(--gx-bg);
    box-shadow: var(--gx-shadow-sm);
    backdrop-filter: none;
    -webkit-backdrop-filter: none;
}

/* Tech-brackets (L 12×12) que acendem no hover — feature + entregáveis */
.gx-wealth-feature-card::before,
.gx-wealth-feature-card::after,
.gx-wealth-deliverable-card::before,
.gx-wealth-deliverable-card::after {
    content: "";
    position: absolute;
    width: 12px;
    height: 12px;
    border-color: transparent;
    transition: border-color 0.25s ease;
    pointer-events: none;
    z-index: 1;
}
.gx-wealth-feature-card::before,
.gx-wealth-deliverable-card::before {
    top: 0;
    left: 0;
    border-top: 2px solid;
    border-left: 2px solid;
}
.gx-wealth-feature-card::after,
.gx-wealth-deliverable-card::after {
    bottom: 0;
    right: 0;
    border-bottom: 2px solid;
    border-right: 2px solid;
}
.gx-wealth-feature-card:hover,
.gx-wealth-deliverable-card:hover {
    border-color: var(--gx-navy-15);
    box-shadow: var(--gx-shadow-card-hover);
    transform: translate(-2px, -2px);
    transition: transform 0.2s var(--gx-ease), box-shadow 0.2s var(--gx-ease), border-color 0.2s ease;
}
.gx-wealth-feature-card:hover::before,
.gx-wealth-feature-card:hover::after,
.gx-wealth-deliverable-card:hover::before,
.gx-wealth-deliverable-card:hover::after {
    border-color: var(--gx-gold);
}

/* ── Hero panel: kicker / título / badge ────────────────────────────────────── */
.gx-wealth-panel-kicker {
    font-family: var(--gx-font-heading);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--gx-gold-hover);
}
.gx-wealth-panel-title {
    font-family: var(--gx-font-heading);
    font-weight: 800;
    font-size: clamp(22px, 3vw, 30px);
    line-height: 1.08;
    letter-spacing: -0.02em;
    color: var(--gx-navy);
}
.gx-wealth-panel-title em,
.gx-wealth-panel-title i {
    font-style: normal;
    color: var(--gx-gold-hover);
}
.gx-wealth-panel-badge {
    border-radius: 0;
    background: var(--gx-navy);
    color: var(--gx-gold-soft);
    border: none;
    font-family: var(--gx-font-heading);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    padding: 7px 12px;
}

/* Mini-cards (hero panel) — rótulo uppercase + frase em Inter 800 (não é número) */
.gx-wealth-mini-card {
    border-radius: 0;
    border: 1px solid var(--gx-border);
    background: var(--gx-bg-warm);
    box-shadow: none;
    padding: 18px;
}
.gx-wealth-mini-card span {
    font-family: var(--gx-font-heading);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--gx-gold-hover);
}
.gx-wealth-mini-card strong {
    font-family: var(--gx-font-heading);
    font-weight: 800;
    font-size: 17px;
    line-height: 1.2;
    letter-spacing: -0.01em;
    color: var(--gx-navy);
}

/* ── KPI cards: números em JetBrains Mono tabular + acento navy no topo ──────── */
.gx-wealth-kpi-card {
    border-radius: 0;
    border: 1px solid var(--gx-border);
    border-top: 2px solid var(--gx-navy);
    background: var(--gx-bg-warm);
    box-shadow: none;
    padding: 18px;
}
.gx-wealth-kpi-card span {
    font-family: var(--gx-font-heading);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--gx-gold-hover);
}
.gx-wealth-kpi-card strong {
    font-family: var(--gx-font-mono);
    font-variant-numeric: tabular-nums;
    font-weight: 900;
    font-size: 26px;
    line-height: 1.1;
    letter-spacing: -0.02em;
    color: var(--gx-navy);
}

.gx-wealth-insight-text { color: var(--gx-text-secondary); }
.gx-wealth-insight-caption { color: var(--gx-text-tertiary); }

/* ── Path steps: retângulo 0px (marcador em ponto — pill permitido p/ dot) ───── */
.gx-wealth-path-step {
    border-radius: 0;
    border: 1px solid var(--gx-border);
    background: var(--gx-bg);
    font-family: var(--gx-font-body);
    color: var(--gx-text-secondary);
}
.gx-wealth-path-step.is-active {
    background: var(--gx-gold-light);
    border-color: var(--gx-gold);
    border-left: 2px solid var(--gx-gold);
    color: var(--gx-navy);
    font-weight: 600;
}

/* Member progress */
.gx-wealth-member-progress {
    border-radius: 0;
    border: 1px solid var(--gx-border);
    border-top: 2px solid var(--gx-navy);
    background: var(--gx-bg-warm);
    box-shadow: none;
}
.gx-wealth-member-progress strong {
    font-family: var(--gx-font-mono);
    font-variant-numeric: tabular-nums;
    font-weight: 900;
    font-size: 24px;
    letter-spacing: -0.02em;
    color: var(--gx-navy);
}
.gx-wealth-progress-bar {
    border-radius: 0;
    height: 8px;
    background: var(--gx-navy-08);
}
.gx-wealth-progress-fill {
    border-radius: 0;
    background: var(--gx-gold);
}

/* Auth note */
.gx-wealth-auth-note {
    border-radius: 0;
    border: 1px solid var(--gx-border);
    border-left: 2px solid var(--gx-gold);
    background: var(--gx-gold-light);
    color: var(--gx-text-secondary);
}
.gx-wealth-auth-note strong {
    font-family: var(--gx-font-heading);
    font-style: normal;
    font-weight: 800;
    color: var(--gx-navy);
}

/* ── Objectives: chips retangulares uppercase; ativo = navy sólido ──────────── */
.gx-wealth-objective {
    border-radius: 0;
    border: 1px solid var(--gx-navy-15);
    background: var(--gx-bg);
    color: var(--gx-text-secondary);
    font-family: var(--gx-font-heading);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    box-shadow: none;
    transition: all 0.2s var(--gx-ease);
}
.gx-wealth-objective:hover,
.gx-wealth-objective.is-active {
    border-color: var(--gx-navy);
    background: var(--gx-navy);
    color: var(--gx-gold-soft);
    box-shadow: none;
}

/* ── Form fields: contorno brutalista 0px sobre fundo claro ─────────────────── */
.gx-wealth-field span {
    font-family: var(--gx-font-heading);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--gx-navy);
    margin-bottom: 8px;
}
.gx-wealth-field input,
.gx-wealth-field select,
.gx-wealth-field textarea {
    border-radius: 0;
    border: 1px solid var(--gx-navy-15);
    background: var(--gx-bg);
    color: var(--gx-navy);
    font-family: var(--gx-font-body);
    font-size: 15px;
}
.gx-wealth-field input,
.gx-wealth-field select {
    height: 50px;
    padding: 0 14px;
}
.gx-wealth-field textarea {
    min-height: 110px;
    padding: 12px 14px;
}
.gx-wealth-field input:focus,
.gx-wealth-field select:focus,
.gx-wealth-field textarea:focus {
    outline: none;
    border-color: var(--gx-navy);
    box-shadow: var(--gx-shadow-sm);
}

/* Form title */
.gx-wealth-form-title {
    font-family: var(--gx-font-heading);
    font-weight: 800;
    font-size: 22px;
    letter-spacing: -0.02em;
    color: var(--gx-navy);
}

/* ── Feature + deliverable: título de card (800, sentence-case p/ leitura) ───── */
.gx-wealth-feature-card strong,
.gx-wealth-deliverable-card h3 {
    font-family: var(--gx-font-heading);
    font-weight: 800;
    font-size: 21px;
    line-height: 1.15;
    letter-spacing: -0.01em;
    color: var(--gx-navy);
}
.gx-wealth-feature-card p,
.gx-wealth-deliverable-card p {
    font-family: var(--gx-font-body);
    font-size: 14.5px;
    line-height: 1.65;
    color: var(--gx-text-secondary);
}

/* Chips: retângulo uppercase pequeno, champagne */
.gx-wealth-chip {
    border-radius: 0;
    background: var(--gx-gold-light);
    border: 1px solid rgba(201,169,106,0.30);
    color: var(--gx-gold-hover);
    font-family: var(--gx-font-heading);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 8px 14px;
}

/* FAQ */
.gx-wealth-faq-item {
    border-radius: 0;
    border: 1px solid var(--gx-border);
    background: var(--gx-bg);
    box-shadow: none;
}
.gx-wealth-faq-item summary {
    font-family: var(--gx-font-heading);
    font-weight: 700;
    font-size: 18px;
    letter-spacing: -0.01em;
    color: var(--gx-navy);
}
.gx-wealth-faq-item p {
    font-family: var(--gx-font-body);
    color: var(--gx-text-secondary);
}

/* Sticky CTA (mobile) */
.gx-wealth-sticky-cta .gx-btn {
    border-radius: 0;
    box-shadow: var(--gx-shadow-card-hover);
}

/* Form success */
.gx-wealth-form-success {
    border-radius: 0;
}
.gx-wealth-form-success strong {
    font-family: var(--gx-font-heading);
    font-weight: 800;
    font-size: 22px;
    color: #176a2d;
}

/* ── Botões primários: legibilidade ────────────────────────────────────────────
   Em superfície clara o padrão navy + champagne (#dbc7a2) lê como texto escuro/
   marrom sobre navy — pouco funcional como CTA. Usamos navy + texto BRANCO no
   claro, e mantemos o botão-comando dourado + texto navy nas superfícies navy
   (cta-block / lead-aside), com especificidade maior para não colidir. */
.gx-wealth .gx-btn-primary {
    background: var(--gx-navy);
    color: #FFFFFF;
    border-color: var(--gx-navy);
}
.gx-wealth .gx-btn-primary:hover {
    background: var(--gx-navy-deep);
    color: #FFFFFF;
    border-color: var(--gx-navy-deep);
}
.gx-wealth .gx-cta-block .gx-btn-primary,
.gx-wealth .gx-lead-aside .gx-btn-primary {
    background: var(--gx-gold);
    color: var(--gx-navy-deep);
    border-color: var(--gx-gold);
}
.gx-wealth .gx-cta-block .gx-btn-primary:hover,
.gx-wealth .gx-lead-aside .gx-btn-primary:hover {
    background: var(--gx-gold-soft);
    color: var(--gx-navy-deep);
    border-color: var(--gx-gold-soft);
}
</style>
