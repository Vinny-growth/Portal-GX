<?php
// Reusable status hero — accepts $eyebrow, $headline, $message, $ctaText, $ctaUrl, $icon (text/glyph)
?>
<style>
    .status-hero {
        background: var(--gradient-hero);
        color: var(--fg-on-dark);
        padding: var(--space-24) 0;
        position: relative;
        overflow: hidden;
        min-height: 60vh;
        display: flex;
        align-items: center;
        border-bottom: 4px solid var(--gx-secondary-dark);
    }
    .status-hero .watermark {
        position: absolute; right: -2vw; top: 50%; transform: translateY(-50%);
        font-family: var(--font-display); font-weight: 900;
        font-size: clamp(180px, 26vw, 340px);
        line-height: 0.8; letter-spacing: -0.06em;
        color: var(--gx-secondary-light); opacity: 0.04;
        pointer-events: none; user-select: none; white-space: nowrap;
    }
    .status-hero .gx-container { position: relative; z-index: 2; max-width: 760px; }
    .status-hero .status-eyebrow {
        font-size: var(--fs-sm); font-weight: var(--fw-bold);
        letter-spacing: var(--ls-widest); text-transform: uppercase;
        color: var(--gx-secondary-light);
        margin-bottom: var(--space-6);
    }
    .status-hero h1 {
        font-family: var(--font-sans); font-weight: 900;
        font-size: clamp(40px, 5.5vw, 72px);
        line-height: 0.92; letter-spacing: var(--ls-tighter);
        text-transform: uppercase; margin: 0 0 var(--space-6);
    }
    .status-hero p {
        font-size: var(--fs-md); line-height: 1.55;
        color: var(--gx-secondary-light); max-width: 580px;
        margin: 0 0 var(--space-8);
    }
    .status-hero .status-glyph {
        font-family: var(--font-mono); font-weight: 900;
        font-size: var(--fs-2xl); color: var(--gx-gold);
        letter-spacing: var(--ls-widest);
        margin-bottom: var(--space-5);
    }
    .status-btn {
        display: inline-flex; align-items: center; gap: var(--space-2);
        background: var(--gx-secondary-dark); color: var(--fg-on-dark);
        font-size: var(--fs-sm); font-weight: 900;
        letter-spacing: var(--ls-widest); text-transform: uppercase;
        padding: var(--space-5) var(--space-8); border: 0; cursor: pointer;
        transition: var(--transition-smooth); text-decoration: none;
    }
    .status-btn:hover {
        background: var(--gx-gold); transform: translate(-2px, -2px);
        box-shadow: var(--shadow-card-hover); color: var(--fg-on-dark);
    }
    .status-btn.ghost {
        background: transparent; border: 1px solid var(--gx-secondary-light);
        color: var(--gx-secondary-light); margin-left: var(--space-3);
    }
    .status-btn.ghost:hover { background: var(--gx-secondary-light); color: var(--gx-primary-dark); }

    .status-meta {
        margin-top: var(--space-10);
        padding-top: var(--space-5);
        border-top: 1px solid rgba(219,199,162,0.18);
        display: flex; gap: var(--space-8); flex-wrap: wrap;
    }
    .status-meta .meta-label {
        font-size: var(--fs-xs); font-weight: var(--fw-bold);
        letter-spacing: var(--ls-widest); text-transform: uppercase;
        color: var(--gx-secondary-light); opacity: 0.6;
        margin-bottom: 4px;
    }
    .status-meta .meta-value {
        font-family: var(--font-mono); font-variant-numeric: tabular-nums;
        font-size: var(--fs-md); color: var(--fg-on-dark);
    }
</style>

<section class="status-hero">
    <div class="watermark">GXC</div>
    <div class="gx-container">
        <?php if (!empty($glyph)): ?>
            <div class="status-glyph"><?= esc($glyph); ?></div>
        <?php endif; ?>
        <div class="status-eyebrow gx-eyebrow-bar"><?= esc($eyebrow ?? 'Newsletter GX Capital'); ?></div>
        <h1><?= esc($headline); ?></h1>
        <p><?= $message; // pode conter <strong> ?></p>
        <div>
            <?php if (!empty($ctaText) && !empty($ctaUrl)): ?>
                <a href="<?= esc($ctaUrl); ?>" class="status-btn"><?= esc($ctaText); ?> &rarr;</a>
            <?php endif; ?>
            <?php if (!empty($secondaryCtaText) && !empty($secondaryCtaUrl)): ?>
                <a href="<?= esc($secondaryCtaUrl); ?>" class="status-btn ghost"><?= esc($secondaryCtaText); ?></a>
            <?php endif; ?>
        </div>
        <?php if (!empty($meta)): ?>
            <div class="status-meta">
                <?php foreach ($meta as $m): ?>
                    <div>
                        <div class="meta-label"><?= esc($m['label']); ?></div>
                        <div class="meta-value"><?= esc($m['value']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
