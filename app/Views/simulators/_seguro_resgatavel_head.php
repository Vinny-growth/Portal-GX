<?= view('marketing/_shared_styles'); ?>
<link rel="preload" as="style" href="<?= base_url('colors_and_type.css'); ?>">
<link rel="stylesheet" href="<?= base_url('colors_and_type.css'); ?>">
<style>
/* ==========================================================================
   Simulador de Seguro de Vida Resgatável — GX Nexus (brutalismo financeiro)
   Tudo scoped em .gx-srs; tokens de colors_and_type.css.
   ========================================================================== */
.gx-srs { font-family: var(--font-sans); color: var(--fg1); background: var(--bg1); }
.gx-srs *, .gx-srs *::before, .gx-srs *::after { box-sizing: border-box; }
.gx-srs ::selection { background: var(--gx-secondary-light); color: var(--gx-primary-dark); }
.gx-srs-wrap { max-width: 1160px; margin: 0 auto; padding: 0 var(--space-6); }

/* ---- top bar ---- */
.gx-srs-topbar { background: var(--gx-primary-dark); color: var(--fg-on-dark); border-bottom: 2px solid var(--gx-secondary-dark); }
.gx-srs-topbar .gx-srs-wrap { display: flex; align-items: center; justify-content: space-between; height: 64px; }
.gx-srs-brand { display: inline-flex; align-items: center; text-decoration: none; }
.gx-srs-brand img { height: 34px; width: auto; display: block; }
.gx-srs-topbar a.gx-srs-back { color: var(--gx-secondary-light); text-decoration: none; font-size: var(--fs-sm); font-weight: var(--fw-bold); letter-spacing: var(--ls-wider); text-transform: uppercase; }
.gx-srs-topbar a.gx-srs-back:hover { color: #fff; }

/* ---- hero ---- */
.gx-srs-hero { position: relative; overflow: hidden; background: var(--gradient-hero); color: var(--fg-on-dark); padding: var(--space-20) 0 var(--space-16); }
.gx-srs-hero.has-img { background-size: cover; background-position: center right; background-repeat: no-repeat; }
@media (max-width: 720px) { .gx-srs-hero.has-img { background-position: 70% center; } }
.gx-srs-hero .gx-srs-wrap { position: relative; z-index: 2; }
.gx-srs-watermark { position: absolute; top: 50%; right: -2%; transform: translateY(-50%); font-family: var(--font-display); font-weight: var(--fw-black); font-size: 220px; line-height: 1; color: #fff; opacity: 0.05; z-index: 1; pointer-events: none; letter-spacing: var(--ls-tighter); }
.gx-srs-eyebrow { display: inline-flex; align-items: center; gap: var(--space-3); font-size: var(--fs-sm); font-weight: var(--fw-bold); letter-spacing: var(--ls-widest); text-transform: uppercase; color: var(--gx-secondary-light); margin-bottom: var(--space-5); }
.gx-srs-eyebrow::before { content: ""; width: 36px; height: 2px; background: var(--gx-gold); display: inline-block; }
.gx-srs-headline { font-family: var(--font-display); font-weight: var(--fw-black); font-size: clamp(40px, 6vw, 80px); line-height: 0.92; letter-spacing: var(--ls-tighter); text-transform: uppercase; max-width: 16ch; margin: 0 0 var(--space-6); }
.gx-srs-headline em { font-style: normal; color: var(--gx-gold); }
.gx-srs-sub { font-size: var(--fs-md); line-height: var(--lh-relaxed); max-width: 60ch; color: rgba(255,255,255,0.82); }
.gx-srs-hero-signals { display: flex; flex-wrap: wrap; gap: var(--space-3); margin-top: var(--space-8); }
.gx-srs-chip { font-size: var(--fs-xs); font-weight: var(--fw-bold); letter-spacing: var(--ls-wider); text-transform: uppercase; color: var(--gx-secondary-light); border: 1px solid rgba(219,199,162,0.4); padding: var(--space-2) var(--space-3); }

/* ---- main shell ---- */
.gx-srs-main { padding: var(--space-16) 0 var(--space-20); background: var(--bg2); }
.gx-srs-shell { display: grid; grid-template-columns: 380px 1fr; gap: var(--space-6); align-items: start; }
@media (max-width: 900px) { .gx-srs-shell { grid-template-columns: 1fr; } .gx-srs-watermark { font-size: 120px; } }

/* ---- card ---- */
.gx-srs-card { position: relative; background: var(--bg1); border: 1px solid var(--gx-border); box-shadow: var(--shadow-card); padding: var(--space-6); transition: var(--transition-smooth); }
.gx-srs-card-title { display: flex; align-items: center; gap: var(--space-3); margin: 0 0 var(--space-5); }
.gx-srs-card-title::before { content: ""; width: 32px; height: 2px; background: var(--gx-primary); }
.gx-srs-card-title span { font-size: var(--fs-sm); font-weight: var(--fw-black); letter-spacing: var(--ls-widest); text-transform: uppercase; color: var(--gx-secondary-dark); }

/* ---- form ---- */
.gx-srs-field { margin-bottom: var(--space-5); }
.gx-srs-field > label { display: block; font-size: var(--fs-xs); font-weight: var(--fw-bold); letter-spacing: var(--ls-widest); text-transform: uppercase; color: var(--fg2); margin-bottom: var(--space-2); }
.gx-srs-input, .gx-srs select { width: 100%; height: 48px; border: 1px solid var(--gx-border); border-radius: 0; padding: 0 var(--space-4); font-family: var(--font-mono); font-variant-numeric: tabular-nums; font-size: var(--fs-md); color: var(--fg1); background: var(--bg1); transition: var(--transition-fast); }
.gx-srs-input:focus, .gx-srs select:focus { outline: none; border-color: var(--gx-primary); box-shadow: var(--shadow-gx); }
.gx-srs-hint { font-size: var(--fs-xs); color: var(--fg3); margin-top: var(--space-1); }

/* diagnóstico — seções e layout */
.gx-srs-intro { font-size: var(--fs-sm); line-height: var(--lh-normal); color: var(--fg2); margin: 0 0 var(--space-6); }
.gx-srs-section { padding-bottom: var(--space-4); margin-bottom: var(--space-5); border-bottom: 1px solid var(--gx-border); }
.gx-srs-section-label { font-size: var(--fs-xs); font-weight: var(--fw-black); letter-spacing: var(--ls-widest); text-transform: uppercase; color: var(--gx-secondary-dark); margin-bottom: var(--space-4); }
.gx-srs-grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4); }
.gx-srs-grid2 .gx-srs-field { margin-bottom: var(--space-3); }
@media (max-width: 520px) { .gx-srs-grid2 { grid-template-columns: 1fr; } }

/* caixa de proteção recomendada */
.gx-srs-reco { background: var(--gx-primary); color: #fff; padding: var(--space-5); border-bottom: 4px solid var(--gx-gold); margin-bottom: var(--space-5); }
.gx-srs-reco-label { font-size: var(--fs-xs); font-weight: var(--fw-bold); letter-spacing: var(--ls-widest); text-transform: uppercase; color: var(--gx-secondary-light); margin-bottom: var(--space-2); }
.gx-srs-reco-insight { font-size: var(--fs-sm); line-height: var(--lh-normal); color: rgba(255,255,255,0.9); }
.gx-srs-reco-insight strong { color: var(--gx-gold); font-family: var(--font-mono); font-weight: var(--fw-bold); }
.gx-srs-reco .gx-srs-field > label { color: var(--gx-secondary-light); }
.gx-srs-reco-edit { text-transform: none; letter-spacing: 0; font-weight: var(--fw-regular); color: rgba(255,255,255,0.55); }
.gx-srs-check { display: flex; align-items: flex-start; gap: var(--space-2); font-size: var(--fs-sm); color: rgba(255,255,255,0.92); cursor: pointer; }
.gx-srs-check input { margin-top: 3px; width: 16px; height: 16px; accent-color: var(--gx-gold); flex: 0 0 auto; }
.gx-srs-check strong { color: var(--gx-gold); }

/* segmented toggle */
.gx-srs-seg { display: grid; grid-auto-flow: column; grid-auto-columns: 1fr; border: 1px solid var(--gx-border); }
.gx-srs-seg button { appearance: none; border: 0; background: var(--bg1); color: var(--fg2); height: 46px; font-family: var(--font-sans); font-weight: var(--fw-bold); font-size: var(--fs-sm); letter-spacing: var(--ls-wide); text-transform: uppercase; cursor: pointer; transition: var(--transition-fast); }
.gx-srs-seg button + button { border-left: 1px solid var(--gx-border); }
.gx-srs-seg button[aria-pressed="true"] { background: var(--gx-primary); color: var(--fg-on-primary); }

/* buttons */
.gx-srs-btn { display: inline-flex; align-items: center; justify-content: center; gap: var(--space-2); width: 100%; height: 52px; border: 0; border-radius: 0; font-family: var(--font-sans); font-weight: var(--fw-bold); font-size: var(--fs-sm); letter-spacing: var(--ls-wider); text-transform: uppercase; cursor: pointer; transition: var(--transition-smooth); }
.gx-srs-btn-primary { background: var(--gx-primary); color: var(--fg-on-primary); }
.gx-srs-btn-primary:hover { box-shadow: var(--shadow-card-hover); transform: translate(-1px,-1px); }
.gx-srs-btn-gold { background: var(--gx-secondary-dark); color: #fff; }
.gx-srs-btn-gold:hover { box-shadow: var(--shadow-card-hover); transform: translate(-1px,-1px); }
.gx-srs-btn:disabled { opacity: 0.55; cursor: not-allowed; transform: none; box-shadow: none; }
.gx-srs-btn-ghost { background: transparent; border: 1px solid var(--gx-border); color: var(--fg1); }

/* ---- result panel ---- */
.gx-srs-result { display: none; }
.gx-srs-result.is-on { display: block; }
.gx-srs-placeholder { display: flex; align-items: center; justify-content: center; min-height: 360px; border: 1px dashed var(--gx-border); color: var(--fg3); font-size: var(--fs-sm); letter-spacing: var(--ls-wide); text-transform: uppercase; text-align: center; padding: var(--space-8); }

.gx-srs-chartwrap { position: relative; min-height: 340px; }
.gx-srs-chart-canvas { transition: filter 0.4s ease; }
.gx-srs-locked .gx-srs-chart-canvas { filter: blur(7px); pointer-events: none; }

/* lock overlay */
.gx-srs-lock { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: var(--space-4); padding: var(--space-6); background: linear-gradient(180deg, rgba(0,13,35,0.05), rgba(0,13,35,0.35)); }
.gx-srs-result:not(.gx-srs-locked) .gx-srs-lock { display: none; }
.gx-srs-lock-badge { width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; background: var(--gx-secondary-dark); color: #fff; }
.gx-srs-lock-badge svg { width: 26px; height: 26px; }
.gx-srs-lock h3 { font-family: var(--font-sans); font-weight: var(--fw-black); font-size: var(--fs-xl); letter-spacing: var(--ls-tight); color: var(--gx-primary-dark); margin: 0; max-width: 22ch; text-transform: uppercase; }
.gx-srs-lock p { font-size: var(--fs-sm); color: var(--fg2); max-width: 42ch; margin: 0; }
.gx-srs-lock .gx-srs-btn { width: auto; padding: 0 var(--space-8); }

/* KPI tiles */
.gx-srs-kpis { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-4); margin-top: var(--space-6); }
@media (max-width: 620px) { .gx-srs-kpis { grid-template-columns: 1fr; } }
.gx-srs-kpi { padding: var(--space-5); border: 1px solid var(--gx-border); background: var(--bg1); }
.gx-srs-kpi.is-dark { background: var(--gx-secondary-dark); border-bottom: 4px solid var(--gx-primary); color: #fff; }
.gx-srs-kpi.is-navy { background: var(--gx-primary); border-bottom: 4px solid var(--gx-gold); color: #fff; }
.gx-srs-kpi-label { font-size: var(--fs-xs); font-weight: var(--fw-bold); letter-spacing: var(--ls-widest); text-transform: uppercase; opacity: 0.8; margin-bottom: var(--space-2); }
.gx-srs-kpi-value { font-family: var(--font-mono); font-variant-numeric: tabular-nums; font-weight: var(--fw-black); font-size: var(--fs-2xl); line-height: 1; }
.gx-srs-kpi-sub { font-size: var(--fs-xs); opacity: 0.7; margin-top: var(--space-2); }

/* blur for sensitive values */
.gx-srs-blur { filter: blur(8px); user-select: none; pointer-events: none; }

/* legend */
.gx-srs-legend { display: flex; gap: var(--space-6); margin-top: var(--space-4); font-size: var(--fs-sm); }
.gx-srs-legend span { display: inline-flex; align-items: center; gap: var(--space-2); color: var(--fg2); font-weight: var(--fw-semibold); }
.gx-srs-dot { width: 12px; height: 12px; display: inline-block; }
.gx-srs-dot.red { background: var(--gx-danger); }
.gx-srs-dot.green { background: var(--gx-success); }

/* disclaimer */
.gx-srs-disclaimer { margin-top: var(--space-6); font-size: var(--fs-xs); line-height: var(--lh-normal); color: var(--fg3); border-top: 1px solid var(--gx-border); padding-top: var(--space-4); }

/* status / celebration */
.gx-srs-status { margin-top: var(--space-4); padding: var(--space-4); font-size: var(--fs-sm); display: none; }
.gx-srs-status.is-error { display: block; background: #fdecec; color: var(--gx-danger); border: 1px solid var(--gx-danger); }
.gx-srs-celebrate { display: none; margin-top: var(--space-6); padding: var(--space-6); background: var(--gx-primary); color: #fff; border-bottom: 4px solid var(--gx-gold); }
.gx-srs-celebrate.is-on { display: block; }
.gx-srs-celebrate .gx-srs-eyebrow { color: var(--gx-gold); }

/* ---- modal ---- */
.gx-srs-modal { position: fixed; inset: 0; z-index: 9999; display: none; align-items: center; justify-content: center; padding: var(--space-4); background: rgba(0,13,35,0.6); backdrop-filter: blur(2px); }
.gx-srs-modal.is-open { display: flex; }
.gx-srs-dialog { width: 100%; max-width: 460px; background: var(--bg1); border: 1px solid var(--gx-border); box-shadow: var(--shadow-elevated); padding: var(--space-8); position: relative; }
.gx-srs-dialog h2 { font-family: var(--font-sans); font-weight: var(--fw-black); font-size: var(--fs-xl); text-transform: uppercase; letter-spacing: var(--ls-tight); margin: 0 0 var(--space-2); color: var(--gx-primary-dark); }
.gx-srs-dialog p { font-size: var(--fs-sm); color: var(--fg2); margin: 0 0 var(--space-6); }
.gx-srs-close { position: absolute; top: var(--space-4); right: var(--space-4); background: none; border: 0; font-size: 22px; line-height: 1; cursor: pointer; color: var(--fg2); }
.gx-srs-dialog .gx-phone-row { display: flex; gap: var(--space-2); }
.gx-srs-dialog .gx-phone-country { flex: 0 0 38%; }
.gx-srs-dialog .gx-phone-number { flex: 1; }
.gx-srs-dialog .gx-phone-field { margin-bottom: var(--space-5); }
.gx-srs-dialog .gx-phone-field > label { display:block; font-size: var(--fs-xs); font-weight: var(--fw-bold); letter-spacing: var(--ls-widest); text-transform: uppercase; color: var(--fg2); margin-bottom: var(--space-2); }

/* ===== Relatório (documento de entrega) ===== */
.gx-srs-reportwrap { background: var(--bg2); padding: var(--space-12) 0 var(--space-20); }
.gx-srs-report { max-width: 920px; margin: 0 auto; background: #fff; border: 1px solid var(--gx-border); box-shadow: var(--shadow-elevated); }
.gx-srs-report-toolbar { display: flex; align-items: center; justify-content: space-between; gap: var(--space-4); padding: var(--space-4) var(--space-6); background: var(--bg2); border-bottom: 1px solid var(--gx-border); }
.gx-srs-report-toolbar .gx-srs-eyebrow { margin: 0; }

.gx-srs-report-cover { position: relative; overflow: hidden; background: var(--gradient-hero); background-size: cover; background-position: center right; background-repeat: no-repeat; color: #fff; min-height: 360px; display: flex; align-items: flex-end; }
.gx-srs-cover-inner { position: relative; z-index: 1; padding: var(--space-10) var(--space-8); max-width: 72%; }
@media (max-width: 640px) { .gx-srs-cover-inner { max-width: 100%; } .gx-srs-report-cover { min-height: 300px; } }
.gx-srs-cover-logo { height: 38px; width: auto; display: block; margin-bottom: var(--space-6); }
.gx-srs-cover-kicker { font-size: var(--fs-sm); font-weight: var(--fw-bold); letter-spacing: var(--ls-widest); text-transform: uppercase; color: var(--gx-secondary-light); margin-bottom: var(--space-3); }
.gx-srs-cover-title { font-family: var(--font-display); font-weight: var(--fw-black); font-size: clamp(28px, 4.2vw, 46px); line-height: 0.98; letter-spacing: var(--ls-tighter); text-transform: uppercase; margin: 0 0 var(--space-5); }
.gx-srs-cover-meta { font-size: var(--fs-xs); letter-spacing: var(--ls-wide); text-transform: uppercase; color: rgba(255,255,255,0.65); display: flex; flex-direction: column; gap: 2px; border-left: 2px solid var(--gx-gold); padding-left: var(--space-4); }
.gx-srs-cover-meta strong { color: #fff; font-size: var(--fs-md); font-weight: var(--fw-bold); text-transform: none; letter-spacing: 0; }
.gx-srs-cover-meta span { font-family: var(--font-mono); }

.gx-srs-rp-section { padding: var(--space-7) var(--space-8); border-bottom: 1px solid var(--gx-border); }
.gx-srs-rp-label { display: flex; align-items: center; gap: var(--space-3); font-size: var(--fs-sm); font-weight: var(--fw-black); letter-spacing: var(--ls-widest); text-transform: uppercase; color: var(--gx-secondary-dark); margin-bottom: var(--space-5); }
.gx-srs-rp-label::before { content: ""; width: 28px; height: 2px; background: var(--gx-primary); }

.gx-srs-rp-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-4) var(--space-6); }
@media (max-width: 640px) { .gx-srs-rp-grid { grid-template-columns: 1fr 1fr; } }
.gx-srs-rp-item .k { font-size: var(--fs-xs); font-weight: var(--fw-bold); letter-spacing: var(--ls-wider); text-transform: uppercase; color: var(--fg2); }
.gx-srs-rp-item .v { font-family: var(--font-mono); font-variant-numeric: tabular-nums; font-weight: var(--fw-bold); font-size: var(--fs-md); color: var(--fg1); margin-top: 2px; }
.gx-srs-rp-item .v.txt { font-family: var(--font-sans); }

.gx-srs-rp-reco { display: flex; align-items: baseline; gap: var(--space-6); flex-wrap: wrap; background: var(--gx-primary); color: #fff; padding: var(--space-6); border-bottom: 4px solid var(--gx-gold); }
.gx-srs-rp-reco-num { font-family: var(--font-mono); font-variant-numeric: tabular-nums; font-weight: var(--fw-black); font-size: var(--fs-3xl); line-height: 1; color: var(--gx-gold); white-space: nowrap; }
.gx-srs-rp-reco-txt { flex: 1 1 320px; margin: 0; font-size: var(--fs-sm); line-height: var(--lh-relaxed); color: rgba(255,255,255,0.92); }

.gx-srs-rp-kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--space-4); margin-bottom: var(--space-6); }
@media (max-width: 640px) { .gx-srs-rp-kpis { grid-template-columns: 1fr 1fr; } }
.gx-srs-rp-kpi { border: 1px solid var(--gx-border); padding: var(--space-4); }
.gx-srs-rp-kpi .k { font-size: 9px; font-weight: var(--fw-bold); letter-spacing: var(--ls-widest); text-transform: uppercase; color: var(--fg2); }
.gx-srs-rp-kpi .v { font-family: var(--font-mono); font-variant-numeric: tabular-nums; font-weight: var(--fw-black); font-size: var(--fs-lg); color: var(--gx-primary); margin-top: var(--space-1); }

.gx-srs-rp-table { width: 100%; border-collapse: collapse; font-size: var(--fs-sm); margin-top: var(--space-2); }
.gx-srs-rp-table th { text-align: left; font-size: var(--fs-xs); font-weight: var(--fw-bold); letter-spacing: var(--ls-wide); text-transform: uppercase; color: var(--fg2); background: var(--bg2); padding: var(--space-2) var(--space-3); border: 1px solid var(--gx-border); }
.gx-srs-rp-table td { padding: var(--space-2) var(--space-3); border: 1px solid var(--gx-border); }
.gx-srs-rp-table td.num { font-family: var(--font-mono); font-variant-numeric: tabular-nums; text-align: right; }
.gx-srs-rp-table tr.hi td { background: rgba(201,169,106,0.12); font-weight: var(--fw-bold); }
.gx-srs-rp-chartwrap { min-height: 320px; margin-bottom: var(--space-3); }
.gx-srs-rp-caption { font-size: var(--fs-xs); line-height: var(--lh-normal); color: var(--fg2); margin: 0 0 var(--space-5); }
.gx-srs-rp-caption strong { color: var(--gx-secondary-dark); font-family: var(--font-mono); }
.gx-srs-rp-caption b { color: var(--fg1); }

.gx-srs-rp-suc p { margin: 0; font-size: var(--fs-sm); line-height: var(--lh-relaxed); color: var(--fg1); }
.gx-srs-rp-suc strong { color: var(--gx-secondary-dark); font-family: var(--font-mono); }

.gx-srs-rp-cta { padding: var(--space-8); background: var(--gx-secondary-dark); color: #fff; }
.gx-srs-rp-cta h3 { font-family: var(--font-sans); font-weight: var(--fw-black); font-size: var(--fs-lg); text-transform: uppercase; letter-spacing: var(--ls-tight); margin: 0 0 var(--space-4); }
.gx-srs-rp-bullets { list-style: none; margin: 0 0 var(--space-6); padding: 0; display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-3); }
@media (max-width: 640px) { .gx-srs-rp-bullets { grid-template-columns: 1fr; } }
.gx-srs-rp-bullets li { position: relative; padding-left: var(--space-5); font-size: var(--fs-sm); line-height: var(--lh-snug); color: rgba(255,255,255,0.92); }
.gx-srs-rp-bullets li::before { content: ""; position: absolute; left: 0; top: 6px; width: 8px; height: 8px; background: var(--gx-gold); }

.gx-srs-report-foot { padding: var(--space-5) var(--space-8); font-size: var(--fs-xs); line-height: var(--lh-normal); color: var(--fg3); }

/* impressão: isola o relatório como documento A4 */
@media print {
  body { background: #fff !important; }
  body * { visibility: hidden !important; }
  #gx-srs-reportwrap, #gx-srs-reportwrap * { visibility: visible !important; }
  #gx-srs-reportwrap { position: absolute; left: 0; top: 0; width: 100%; padding: 0; background: #fff; }
  .gx-srs-report { max-width: none; border: 0; box-shadow: none; }
  .gx-srs-noprint { display: none !important; }
  .gx-srs-rp-section, .gx-srs-rp-cta { break-inside: avoid; page-break-inside: avoid; }
  .gx-srs-report-cover, .gx-srs-rp-reco, .gx-srs-rp-cta, .gx-srs-rp-suc { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  .gx-srs-report-cover { min-height: 240px; }
}
</style>
