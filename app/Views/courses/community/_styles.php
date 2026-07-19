<style>
    .cm-layout{display:grid;grid-template-columns:240px 1fr;gap:var(--space-6);align-items:start;margin-top:var(--space-4)}
    .cm-side{position:sticky;top:88px}
    .cm-side h4{font-size:11px;text-transform:uppercase;letter-spacing:var(--ls-widest);color:var(--gx-gold);font-weight:800;margin:0 0 12px}
    .cm-space{display:flex;align-items:center;gap:10px;padding:10px 12px;color:#c7d2e0;border-left:3px solid transparent;font-size:14px}
    .cm-space:hover{background:rgba(219,199,162,.06);color:#fff}
    .cm-space.on{background:rgba(201,169,106,.14);border-left-color:var(--gx-gold);color:#fff}
    .cm-space i{width:22px;text-align:center}
    .cm-composer{background:#0a2547;border:1px solid rgba(219,199,162,.15);padding:var(--space-5);margin-bottom:var(--space-5)}
    .cm-composer textarea,.cm-composer input,.cm-composer select{width:100%;background:#06182f;border:1px solid rgba(219,199,162,.22);color:#eef2f7;padding:11px 13px;font-family:var(--font-sans);margin-bottom:10px}
    .cm-composer textarea{min-height:80px;resize:vertical}
    .cm-post{background:#0a2547;border:1px solid rgba(219,199,162,.15);padding:var(--space-5);margin-bottom:16px;transition:border-color .2s}
    .cm-post:hover{border-color:rgba(219,199,162,.35)}
    .cm-post__head{display:flex;align-items:center;gap:12px;margin-bottom:12px}
    .cm-avatar{width:40px;height:40px;border-radius:var(--radius-pill);background:var(--gradient-secondary);display:grid;place-items:center;font-weight:900;color:var(--gx-primary-dark);font-family:var(--font-mono)}
    .cm-post__who{font-weight:700;font-size:15px}
    .cm-post__meta{font-size:11px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:#8ea0b6}
    .cm-space-badge{display:inline-block;padding:2px 8px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:var(--ls-wide);border:1px solid rgba(219,199,162,.35);color:var(--gx-secondary-light)}
    .cm-post__title{font-size:19px;font-weight:800;margin:4px 0 8px}
    .cm-post__body{color:#cdd7e4;line-height:1.6;white-space:pre-wrap;word-break:break-word}
    .cm-post__foot{display:flex;align-items:center;gap:18px;margin-top:14px;padding-top:12px;border-top:1px solid rgba(219,199,162,.1)}
    .cm-act{display:inline-flex;align-items:center;gap:6px;background:none;border:0;color:#9fb0c4;cursor:pointer;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:var(--ls-wide);padding:0}
    .cm-act:hover{color:var(--gx-gold)}
    .cm-act.on{color:var(--gx-gold)}
    .cm-act b{font-family:var(--font-mono)}
    .cm-pin{color:var(--gx-gold);font-size:11px;text-transform:uppercase;letter-spacing:var(--ls-wide);font-weight:800}
    .cm-comment{display:flex;gap:12px;padding:14px 0;border-bottom:1px solid rgba(219,199,162,.1)}
    .cm-comment__b{flex:1}
    .cm-stats{display:flex;gap:24px;flex-wrap:wrap;background:#0a2547;border:1px solid rgba(219,199,162,.15);padding:var(--space-5);margin-bottom:var(--space-5)}
    .cm-stat b{display:block;font-family:var(--font-mono);font-size:26px;font-weight:900;color:#fff}
    .cm-stat span{font-size:10px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:#9fb0c4}
    .cm-rank{display:flex;align-items:center;gap:14px;padding:14px 18px;background:#0a2547;border:1px solid rgba(219,199,162,.12);margin-bottom:8px}
    .cm-rank.me{border-color:var(--gx-gold);background:rgba(201,169,106,.1)}
    .cm-rank__pos{font-family:var(--font-mono);font-size:20px;font-weight:900;color:var(--gx-gold);width:44px}
    .cm-flash{padding:12px 16px;margin:var(--space-4) 0;background:rgba(22,163,74,.14);border-left:4px solid var(--gx-success);color:#d7ffe4;font-weight:600}
    @media(max-width:820px){.cm-layout{grid-template-columns:1fr}.cm-side{position:static}}
</style>
