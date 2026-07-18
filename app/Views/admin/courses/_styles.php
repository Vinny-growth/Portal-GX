<link rel="stylesheet" href="/colors_and_type.css">
<style>
    .gxc-wrap { font-family: var(--font-sans); color: var(--fg1); }
    .gxc-head { display:flex; align-items:flex-end; justify-content:space-between; gap:var(--space-4); margin-bottom:var(--space-6); flex-wrap:wrap; }
    .gxc-eyebrow { display:flex; align-items:center; gap:var(--space-3); text-transform:uppercase; letter-spacing:var(--ls-widest); font-size:var(--fs-sm); font-weight:700; color:var(--gx-secondary-dark); }
    .gxc-eyebrow::before { content:''; width:32px; height:2px; background:var(--gx-primary); display:inline-block; }
    .gxc-title { font-size:var(--fs-2xl); font-weight:900; text-transform:uppercase; letter-spacing:var(--ls-tight); margin:var(--space-2) 0 0; line-height:1; }
    .gxc-btn { display:inline-block; padding:10px 18px; font-size:var(--fs-sm); font-weight:700; text-transform:uppercase; letter-spacing:var(--ls-wider); border:1px solid var(--gx-primary); background:var(--gx-primary); color:var(--gx-secondary-light); text-decoration:none; transition:box-shadow .2s var(--transition-smooth,ease), transform .2s var(--transition-smooth,ease); cursor:pointer; }
    .gxc-btn:hover { box-shadow:var(--shadow-card-hover); transform:translate(-1px,-1px); color:var(--gx-secondary-light); }
    .gxc-btn--ghost { background:#fff; color:var(--gx-primary); }
    .gxc-btn--gold { background:var(--gx-secondary-dark); border-color:var(--gx-secondary-dark); color:#fff; }
    .gxc-btn--sm { padding:5px 12px; font-size:var(--fs-xs); }
    .gxc-btn--danger { background:#fff; border-color:var(--gx-danger); color:var(--gx-danger); }
    .gxc-table { width:100%; border-collapse:collapse; border:1px solid var(--gx-border); background:#fff; }
    .gxc-table th { background:var(--bg2); text-align:left; padding:12px 14px; font-size:var(--fs-xs); text-transform:uppercase; letter-spacing:var(--ls-widest); font-weight:800; color:var(--gx-primary); border-bottom:1px solid var(--gx-border); }
    .gxc-table td { padding:12px 14px; border-bottom:1px solid var(--gx-border); font-size:var(--fs-base); vertical-align:middle; }
    .gxc-table tr:hover td { background:var(--bg2); }
    .gxc-num { font-family:var(--font-mono); font-variant-numeric:tabular-nums; font-weight:700; }
    .gxc-badge { display:inline-block; padding:3px 9px; font-size:var(--fs-xs); font-weight:700; text-transform:uppercase; letter-spacing:var(--ls-wide); }
    .gxc-badge--on { background:var(--gx-success); color:#fff; }
    .gxc-badge--off { background:var(--bg3); color:var(--fg2); }
    .gxc-badge--gold { background:var(--gx-secondary-dark); color:#fff; }
    .gxc-flash { padding:12px 16px; margin-bottom:var(--space-4); font-weight:600; border-left:4px solid var(--gx-primary); background:var(--bg2); }
    .gxc-flash--err { border-left-color:var(--gx-danger); }
    .gxc-empty { padding:var(--space-12); text-align:center; color:var(--fg2); border:1px dashed var(--gx-border); background:#fff; }
    .gxc-actions { display:flex; gap:6px; flex-wrap:wrap; align-items:center; }
    .gxc-actions form { display:inline; margin:0; }
    /* cards / panels */
    .gxc-card { background:#fff; border:1px solid var(--gx-border); padding:var(--space-6); margin-bottom:var(--space-5); }
    .gxc-card__eyebrow { display:flex; align-items:center; gap:var(--space-3); text-transform:uppercase; letter-spacing:var(--ls-widest); font-size:var(--fs-sm); font-weight:800; color:var(--gx-secondary-dark); margin-bottom:var(--space-5); }
    .gxc-card__eyebrow::before { content:''; width:32px; height:2px; background:var(--gx-primary); display:inline-block; }
    /* forms */
    .gxc-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:var(--space-4); }
    .gxc-field { display:flex; flex-direction:column; gap:6px; margin-bottom:var(--space-3); }
    .gxc-field--full { grid-column:1 / -1; }
    .gxc-label { font-size:var(--fs-xs); text-transform:uppercase; letter-spacing:var(--ls-wide); font-weight:700; color:var(--fg2); }
    .gxc-input, .gxc-select, .gxc-textarea { font-family:var(--font-sans); font-size:var(--fs-base); padding:9px 11px; border:1px solid var(--gx-border); background:#fff; color:var(--fg1); border-radius:0; }
    .gxc-input:focus, .gxc-select:focus, .gxc-textarea:focus { outline:none; border-color:var(--gx-primary); box-shadow:var(--shadow-card); }
    .gxc-textarea { min-height:90px; resize:vertical; }
    .gxc-check { display:flex; align-items:center; gap:8px; font-size:var(--fs-base); }
    .gxc-section { border:1px solid var(--gx-border); margin-bottom:var(--space-4); background:#fff; }
    .gxc-section__head { display:flex; align-items:center; justify-content:space-between; gap:var(--space-3); padding:12px 16px; background:var(--gradient-primary); color:var(--gx-secondary-light); }
    .gxc-section__head strong { font-weight:800; letter-spacing:var(--ls-wide); }
    .gxc-lesson { display:flex; align-items:center; justify-content:space-between; gap:var(--space-3); padding:10px 16px; border-top:1px solid var(--gx-border); }
    .gxc-lesson:hover { background:var(--bg2); }
    .gxc-inline-form { padding:14px 16px; background:var(--bg2); border-top:1px dashed var(--gx-border); }
    .gxc-muted { color:var(--fg2); font-size:var(--fs-sm); }
    details.gxc-det > summary { cursor:pointer; padding:8px 16px; font-size:var(--fs-xs); text-transform:uppercase; letter-spacing:var(--ls-wide); font-weight:700; color:var(--gx-primary); list-style:none; }
    details.gxc-det > summary::-webkit-details-marker { display:none; }
</style>
