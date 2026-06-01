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

/* ══════════════════════════════════════════════════════════ */
/* Vault Modernism — Wealth                                     */
/* ══════════════════════════════════════════════════════════ */

.gx-wealth {
    font-family: var(--gx-font-sans-refined);
    background:
        radial-gradient(ellipse 900px 420px at 85% 12%, rgba(199,160,83,0.12) 0%, transparent 65%),
        radial-gradient(ellipse 700px 500px at 10% 100%, rgba(0,42,85,0.04) 0%, transparent 70%),
        linear-gradient(180deg, #FFFFFF 0%, var(--gx-vellum) 100%);
}

.gx-wealth .gx-hero::before {
    background:
        linear-gradient(135deg, rgba(199,160,83,0.06), transparent 45%),
        radial-gradient(circle at right top, rgba(199,160,83,0.14), transparent 28%);
}

.gx-wealth .gx-hero::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, rgba(199,160,83,0.35) 50%, transparent 100%);
    z-index: 2;
}

/* Hero title — Instrument Serif */
.gx-wealth .gx-hero-title {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: clamp(42px, 6vw, 72px);
    line-height: 1;
    letter-spacing: -0.02em;
    color: var(--gx-navy-deep);
}

.gx-wealth .gx-hero-title em,
.gx-wealth .gx-hero-title i {
    font-style: italic;
    color: var(--gx-gold-etched);
}

.gx-wealth .gx-hero-sub {
    font-family: var(--gx-font-sans-refined);
    font-size: 17px;
    line-height: 1.6;
    letter-spacing: -0.003em;
    color: var(--gx-navy-70);
}

.gx-wealth .gx-label {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    letter-spacing: 0.22em;
    color: var(--gx-gold-etched);
}

.gx-wealth .gx-label::before {
    width: 28px;
    background: var(--gx-gold);
}

/* Panels — hairline frame em vez de rounded soft */
.gx-wealth-hero-panel,
.gx-wealth-diagnostic-card,
.gx-wealth-insights-card,
.gx-wealth-deliverable-card,
.gx-wealth-schedule-card,
.gx-wealth-feature-card {
    border-radius: 2px;
    border: 1px solid rgba(0,42,85,0.08);
    background: linear-gradient(180deg, rgba(255,255,255,0.96) 0%, var(--gx-ivory) 100%);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.85),
        0 0 0 1px rgba(199,160,83,0.18),
        0 32px 72px rgba(5,25,52,0.1);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
}

.gx-wealth-hero-panel {
    padding: 36px;
}

.gx-wealth-diagnostic-card,
.gx-wealth-insights-card,
.gx-wealth-schedule-card {
    padding: 36px;
}

.gx-wealth-panel-kicker {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    letter-spacing: 0.22em;
    color: var(--gx-gold-etched);
    text-transform: uppercase;
}

.gx-wealth-panel-title {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: clamp(26px, 3.2vw, 36px);
    line-height: 1.05;
    letter-spacing: -0.015em;
    color: var(--gx-navy-deep);
}

.gx-wealth-panel-title em,
.gx-wealth-panel-title i {
    font-style: italic;
    color: var(--gx-gold-etched);
}

.gx-wealth-panel-badge {
    border-radius: 2px;
    background: rgba(199,160,83,0.12);
    color: var(--gx-gold-etched);
    border: 1px solid rgba(199,160,83,0.25);
    font-family: var(--gx-font-sans-refined);
    font-size: 10.5px;
    font-weight: 600;
    letter-spacing: 0.22em;
    padding: 6px 12px;
}

/* Mini cards — hairline */
.gx-wealth-mini-card {
    padding: 22px 20px;
    border-radius: 2px;
    background: rgba(255,255,255,0.75);
    border: 1px solid rgba(0,42,85,0.08);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.7),
        0 1px 0 0 rgba(199,160,83,0.1);
}

.gx-wealth-mini-card span {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    letter-spacing: 0.22em;
    color: var(--gx-gold-etched);
    text-transform: uppercase;
}

.gx-wealth-mini-card strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 24px;
    line-height: 1.1;
    color: var(--gx-navy-deep);
    font-feature-settings: "onum" 1;
    font-variant-numeric: oldstyle-nums;
    letter-spacing: -0.015em;
}

/* KPI cards */
.gx-wealth-kpi-card {
    padding: 22px 20px;
    border-radius: 2px;
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-ivory) 100%);
    border: 1px solid rgba(0,42,85,0.08);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.7);
}

.gx-wealth-kpi-card span {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    letter-spacing: 0.22em;
    color: var(--gx-gold-etched);
    text-transform: uppercase;
}

.gx-wealth-kpi-card strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 28px;
    color: var(--gx-navy-deep);
    font-feature-settings: "onum" 1;
    font-variant-numeric: oldstyle-nums;
    letter-spacing: -0.015em;
}

/* Path steps */
.gx-wealth-path-step {
    border-radius: 2px;
    border: 1px solid rgba(0,42,85,0.08);
    background: rgba(255,255,255,0.85);
    font-family: var(--gx-font-sans-refined);
    font-size: 14px;
    padding: 14px 16px 14px 44px;
}

.gx-wealth-path-step::before {
    left: 18px;
    width: 10px;
    height: 10px;
    background: rgba(0,42,85,0.18);
    box-shadow: 0 0 0 3px rgba(0,42,85,0.06);
}

.gx-wealth-path-step.is-active {
    background: linear-gradient(90deg, rgba(199,160,83,0.14), rgba(251,247,238,0.6));
    border-color: rgba(199,160,83,0.35);
    color: var(--gx-navy-deep);
}

.gx-wealth-path-step.is-active::before {
    background: var(--gx-gold);
    box-shadow: 0 0 0 3px rgba(199,160,83,0.2);
}

/* Member progress */
.gx-wealth-member-progress {
    padding: 22px 20px;
    border-radius: 2px;
    background: linear-gradient(180deg, #FFFFFF 0%, var(--gx-ivory) 100%);
    border: 1px solid rgba(0,42,85,0.08);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.7);
}

.gx-wealth-member-progress strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 26px;
    letter-spacing: -0.015em;
    color: var(--gx-navy-deep);
    font-feature-settings: "onum" 1;
    font-variant-numeric: oldstyle-nums;
}

.gx-wealth-progress-bar {
    height: 6px;
    background: rgba(0,42,85,0.08);
    border-radius: 999px;
}

.gx-wealth-progress-fill {
    background: linear-gradient(90deg, var(--gx-gold-etched) 0%, var(--gx-gold) 100%);
}

/* Auth note */
.gx-wealth-auth-note {
    border-radius: 2px;
    border: 1px solid rgba(199,160,83,0.28);
    background: rgba(199,160,83,0.08);
    border-left: 2px solid var(--gx-gold);
    padding: 18px 22px;
}

.gx-wealth-auth-note strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-style: italic;
    color: var(--gx-navy-deep);
}

/* Objectives (chips) */
.gx-wealth-objective {
    border-radius: 2px;
    border: 1px solid rgba(0,42,85,0.14);
    background: #FFFFFF;
    color: var(--gx-navy-70);
    font-family: var(--gx-font-sans-refined);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 0 18px;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.7);
}

.gx-wealth-objective:hover,
.gx-wealth-objective.is-active {
    border-color: rgba(199,160,83,0.42);
    background: rgba(251,247,238,0.8);
    color: var(--gx-navy-deep);
    box-shadow:
        inset 0 0 0 1px rgba(199,160,83,0.18),
        0 0 0 1px rgba(199,160,83,0.2);
}

/* Form fields — editorial minimal */
.gx-wealth-field span {
    font-family: var(--gx-font-sans-refined);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--gx-navy-70);
    margin-bottom: 10px;
}

.gx-wealth-field input,
.gx-wealth-field select,
.gx-wealth-field textarea {
    height: auto;
    min-height: 42px;
    padding: 10px 0 8px;
    border: none;
    border-bottom: 1px solid rgba(0,42,85,0.18);
    border-radius: 0;
    background: transparent;
    font-family: var(--gx-font-sans-refined);
    font-size: 16px;
    color: var(--gx-navy-deep);
}

.gx-wealth-field textarea {
    min-height: 96px;
    padding: 12px 0;
}

.gx-wealth-field input:focus,
.gx-wealth-field select:focus,
.gx-wealth-field textarea:focus {
    outline: none;
    border-bottom-color: var(--gx-gold);
    box-shadow: 0 1px 0 0 var(--gx-gold);
}

/* Form title */
.gx-wealth-form-title {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 24px;
    letter-spacing: -0.015em;
    color: var(--gx-navy-deep);
}

.gx-wealth-form-copy {
    font-family: var(--gx-font-sans-refined);
}

/* Feature + deliverable cards */
.gx-wealth-feature-card strong,
.gx-wealth-deliverable-card h3 {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 24px;
    line-height: 1.1;
    letter-spacing: -0.01em;
    color: var(--gx-navy-deep);
}

.gx-wealth-feature-card p,
.gx-wealth-deliverable-card p {
    font-family: var(--gx-font-sans-refined);
    font-size: 14.5px;
    line-height: 1.6;
    color: var(--gx-navy-70);
}

.gx-wealth-feature-card {
    padding: 30px 26px;
    transition: transform 0.35s var(--gx-ease), border-color 0.35s ease, box-shadow 0.35s ease;
}

.gx-wealth-feature-card:hover {
    transform: translateY(-4px);
    border-color: rgba(199,160,83,0.4);
    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,0.9),
        0 0 0 1px rgba(199,160,83,0.3),
        0 40px 80px rgba(5,25,52,0.12);
}

/* Chip list */
.gx-wealth-chip {
    border-radius: 2px;
    background: rgba(199,160,83,0.08);
    border: 1px solid rgba(199,160,83,0.2);
    color: var(--gx-navy-70);
    font-family: var(--gx-font-sans-refined);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.08em;
    padding: 9px 16px;
}

/* FAQ */
.gx-wealth-faq-item {
    border-radius: 2px;
    border: 1px solid rgba(0,42,85,0.08);
    background: rgba(255,255,255,0.92);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.6);
}

.gx-wealth-faq-item summary {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 19px;
    letter-spacing: -0.01em;
    color: var(--gx-navy-deep);
    padding: 22px 26px;
}

.gx-wealth-faq-item p {
    font-family: var(--gx-font-sans-refined);
    padding: 0 26px 24px;
}

/* Sticky CTA */
.gx-wealth-sticky-cta .gx-btn {
    border-radius: 2px;
    font-family: var(--gx-font-sans-refined);
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    font-size: 12.5px;
    box-shadow: 0 18px 40px rgba(199,160,83,0.25);
}

/* Section titles dentro wealth */
.gx-wealth .gx-section-title {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: clamp(30px, 4.2vw, 52px);
    line-height: 1.03;
    letter-spacing: -0.015em;
    color: var(--gx-navy-deep);
}

.gx-wealth .gx-section-title em,
.gx-wealth .gx-section-title i {
    font-style: italic;
    color: var(--gx-gold-etched);
}

.gx-wealth .gx-section-desc {
    font-family: var(--gx-font-sans-refined);
    font-size: 16px;
    line-height: 1.65;
    color: var(--gx-navy-70);
}

/* Buttons — vault treatment */
.gx-wealth .gx-btn {
    font-family: var(--gx-font-sans-refined);
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    font-size: 12.5px;
}

.gx-wealth .gx-btn-lg {
    height: 54px;
    padding: 0 32px;
    font-size: 13px;
    letter-spacing: 0.14em;
}

.gx-wealth .gx-btn-primary {
    background: var(--gx-gold-etched);
    color: #FFFFFF;
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,0.25),
        0 14px 30px rgba(199,160,83,0.22);
}

.gx-wealth .gx-btn-primary:hover {
    background: var(--gx-navy-deep);
    color: #FFFFFF;
}

.gx-wealth .gx-btn-ghost {
    border: 1px solid rgba(0,42,85,0.22);
    color: var(--gx-navy-deep);
    box-shadow: inset 0 0 0 1px rgba(199,160,83,0.1);
}

.gx-wealth .gx-btn-ghost:hover {
    border-color: var(--gx-gold);
    background: rgba(251,247,238,0.8);
    color: var(--gx-navy-deep);
}

/* Form success */
.gx-wealth-form-success {
    border-radius: 2px;
    border: 1px solid rgba(47,179,68,0.28);
    background: rgba(47,179,68,0.08);
}

.gx-wealth-form-success strong {
    font-family: var(--gx-font-display);
    font-weight: 400;
    font-size: 24px;
    color: #176a2d;
}
</style>
