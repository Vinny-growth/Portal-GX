<?php
$bodyContent = '';
ob_start();
?>
<style>
    /* ============ HERO ============ */
    .hero-newsletter {
        position: relative;
        background: var(--gradient-hero);
        color: var(--fg-on-dark);
        padding: var(--space-24) 0 var(--space-20);
        overflow: hidden;
        border-bottom: 4px solid var(--gx-secondary-dark);
    }
    .hero-newsletter .watermark {
        position: absolute;
        top: 50%;
        right: -3vw;
        transform: translateY(-50%);
        font-family: var(--font-display);
        font-weight: 900;
        font-size: clamp(200px, 28vw, 380px);
        line-height: 0.8;
        letter-spacing: -0.06em;
        color: var(--gx-secondary-light);
        opacity: 0.04;
        pointer-events: none;
        user-select: none;
        white-space: nowrap;
    }
    .hero-newsletter .hero-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center right;
        opacity: 0.18;
        mix-blend-mode: screen;
        pointer-events: none;
        z-index: 1;
    }
    .hero-newsletter .gx-container { position: relative; z-index: 2; display: grid; grid-template-columns: 1.2fr 1fr; gap: var(--space-16); align-items: center; }
    .hero-eyebrow {
        color: var(--gx-secondary-light);
        font-size: var(--fs-sm);
        font-weight: var(--fw-bold);
        letter-spacing: var(--ls-widest);
        text-transform: uppercase;
        margin-bottom: var(--space-6);
    }
    .hero-newsletter h1 {
        font-family: var(--font-sans);
        font-weight: 900;
        font-size: clamp(40px, 5.5vw, 72px);
        line-height: 0.92;
        letter-spacing: var(--ls-tighter);
        text-transform: uppercase;
        margin: 0 0 var(--space-6);
        color: var(--fg-on-dark);
    }
    .hero-newsletter .subhead {
        font-size: var(--fs-md);
        line-height: 1.55;
        color: var(--gx-secondary-light);
        max-width: 540px;
        margin: 0 0 var(--space-8);
    }
    .hero-stats { display: flex; gap: var(--space-10); flex-wrap: wrap; padding-top: var(--space-6); border-top: 1px solid rgba(219,199,162,0.18); }
    .hero-stats .stat-label {
        font-size: var(--fs-xs);
        font-weight: var(--fw-bold);
        letter-spacing: var(--ls-widest);
        text-transform: uppercase;
        color: var(--gx-secondary-light);
        opacity: 0.7;
        margin-bottom: var(--space-2);
    }
    .hero-stats .stat-value {
        font-family: var(--font-mono);
        font-variant-numeric: tabular-nums;
        font-weight: 900;
        font-size: var(--fs-2xl);
        color: var(--fg-on-dark);
        line-height: 1;
    }

    /* ============ FORM CARD ============ */
    .form-card {
        background: var(--bg1);
        color: var(--fg1);
        padding: var(--space-10);
        box-shadow: var(--shadow-elevated);
        position: relative;
    }
    .form-card::before, .form-card::after {
        content: '';
        position: absolute;
        width: 14px;
        height: 14px;
        border-color: var(--gx-secondary-dark);
        border-style: solid;
        border-width: 0;
        pointer-events: none;
    }
    .form-card::before { top: -1px; left: -1px; border-top-width: 2px; border-left-width: 2px; }
    .form-card::after  { bottom: -1px; right: -1px; border-bottom-width: 2px; border-right-width: 2px; }
    .form-card .form-eyebrow {
        font-size: var(--fs-xs);
        font-weight: var(--fw-bold);
        letter-spacing: var(--ls-widest);
        text-transform: uppercase;
        color: var(--gx-secondary-dark);
        margin-bottom: var(--space-3);
    }
    .form-card h3 {
        font-size: var(--fs-xl);
        font-weight: 900;
        line-height: 1.15;
        margin: 0 0 var(--space-6);
        text-transform: uppercase;
        letter-spacing: -0.01em;
    }
    .form-row { display: flex; flex-direction: column; gap: var(--space-2); margin-bottom: var(--space-5); }
    .form-row label {
        font-size: var(--fs-xs);
        font-weight: var(--fw-bold);
        letter-spacing: var(--ls-widest);
        text-transform: uppercase;
        color: var(--fg2);
    }
    .form-row input[type="email"] {
        appearance: none;
        font-family: var(--font-sans);
        font-size: var(--fs-md);
        font-weight: var(--fw-medium);
        padding: var(--space-4);
        border: 1px solid var(--gx-border);
        background: var(--bg1);
        color: var(--fg1);
        border-radius: 0;
        outline: none;
        transition: var(--transition-smooth);
    }
    .form-row input[type="email"]:focus {
        border-color: var(--gx-primary);
        box-shadow: 4px 4px 0 0 rgba(12,49,99,0.18);
    }
    .form-row input[name="url"] { position: absolute; left: -9999px; }

    .line-grid { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-2); }
    .line-check {
        display: flex;
        align-items: flex-start;
        gap: var(--space-3);
        padding: var(--space-4);
        border: 1px solid var(--gx-border);
        cursor: pointer;
        transition: var(--transition-smooth);
        background: var(--bg1);
    }
    .line-check:hover { border-color: var(--gx-primary); }
    .line-check input { margin: 0; margin-top: 2px; accent-color: var(--gx-primary); }
    .line-check input:checked + .line-check-text .line-name { color: var(--gx-primary); }
    .line-check input:checked ~ * .line-name { color: var(--gx-primary); }
    .line-check:has(input:checked) { border-color: var(--gx-primary); background: var(--bg2); }
    .line-check-text { display: flex; flex-direction: column; gap: 2px; }
    .line-name { font-size: var(--fs-base); font-weight: var(--fw-bold); color: var(--fg1); }
    .line-desc { font-size: var(--fs-xs); color: var(--fg2); }

    .btn-primary-cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: var(--space-2);
        width: 100%;
        background: var(--gx-primary);
        color: var(--fg-on-primary);
        font-family: var(--font-sans);
        font-size: var(--fs-sm);
        font-weight: 900;
        letter-spacing: var(--ls-widest);
        text-transform: uppercase;
        padding: var(--space-5) var(--space-6);
        border: 0;
        cursor: pointer;
        transition: var(--transition-smooth);
        border-radius: 0;
    }
    .btn-primary-cta:hover { background: var(--gx-primary-dark); box-shadow: var(--shadow-card-hover); transform: translate(-2px, -2px); }
    .btn-primary-cta:active { transform: translate(0, 0); box-shadow: 2px 2px 0 0 rgba(12,49,99,0.3); }

    .social-proof {
        margin-top: var(--space-5);
        padding-top: var(--space-5);
        border-top: 1px solid var(--gx-border);
        font-size: var(--fs-xs);
        font-weight: var(--fw-bold);
        letter-spacing: var(--ls-wider);
        text-transform: uppercase;
        color: var(--fg2);
        text-align: center;
    }

    /* ============ VALUE PROP SECTION ============ */
    .value-section { padding: var(--space-20) 0; background: var(--bg1); }
    .value-section h2 {
        font-size: clamp(28px, 4vw, 48px);
        font-weight: 900;
        letter-spacing: var(--ls-tighter);
        text-transform: uppercase;
        line-height: 1;
        margin: var(--space-3) 0 var(--space-12);
        max-width: 720px;
    }
    .value-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-6); }
    .value-card {
        background: var(--bg1);
        border: 1px solid var(--gx-border);
        padding: var(--space-8);
        position: relative;
        transition: var(--transition-smooth);
    }
    .value-card::before, .value-card::after {
        content: '';
        position: absolute;
        width: 12px; height: 12px;
        border-color: var(--gx-primary);
        border-style: solid;
        border-width: 0;
        opacity: 0;
        transition: var(--transition-smooth);
    }
    .value-card::before { top: 8px; left: 8px; border-top-width: 2px; border-left-width: 2px; }
    .value-card::after  { bottom: 8px; right: 8px; border-bottom-width: 2px; border-right-width: 2px; }
    .value-card:hover { box-shadow: 0 0 20px -10px rgba(12,49,99,0.3); }
    .value-card:hover::before, .value-card:hover::after { opacity: 1; }
    .value-card .num {
        font-family: var(--font-mono);
        font-weight: 900;
        font-size: var(--fs-3xl);
        color: var(--gx-secondary-dark);
        line-height: 1;
        margin-bottom: var(--space-5);
    }
    .value-card h3 {
        font-size: var(--fs-lg);
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: -0.01em;
        margin: 0 0 var(--space-3);
    }
    .value-card p { font-size: var(--fs-base); color: var(--fg2); margin: 0; line-height: 1.6; }

    /* ============ MAGNETS SECTION ============ */
    .magnets-section {
        background: var(--bg2);
        padding: var(--space-20) 0;
        border-top: 1px solid var(--gx-border);
    }
    .magnets-section h2 {
        font-size: clamp(28px, 4vw, 48px);
        font-weight: 900;
        letter-spacing: var(--ls-tighter);
        text-transform: uppercase;
        line-height: 1;
        margin: var(--space-3) 0 var(--space-4);
    }
    .magnets-section .lead { font-size: var(--fs-md); color: var(--fg2); margin: 0 0 var(--space-10); max-width: 640px; }
    .magnet-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--space-6); }
    .magnet-card {
        background: var(--bg1);
        border: 1px solid var(--gx-border);
        padding: 0;
        overflow: hidden;
        transition: var(--transition-smooth);
    }
    .magnet-card:hover { box-shadow: 4px 4px 0 0 rgba(12,49,99,0.2); transform: translate(-2px, -2px); }
    .magnet-cover { aspect-ratio: 4/3; background: var(--bg-dark); display: flex; align-items: center; justify-content: center; position: relative; }
    .magnet-cover img { width: 100%; height: 100%; object-fit: cover; }
    .magnet-cover-placeholder {
        font-family: var(--font-display);
        font-weight: 900;
        font-size: 64px;
        color: var(--gx-secondary-light);
        opacity: 0.4;
        letter-spacing: -0.04em;
    }
    .magnet-card .body { padding: var(--space-5); }
    .magnet-card .line-tag {
        font-size: var(--fs-xs);
        font-weight: var(--fw-bold);
        letter-spacing: var(--ls-widest);
        text-transform: uppercase;
        color: var(--gx-secondary-dark);
        margin-bottom: var(--space-2);
    }
    .magnet-card h3 {
        font-size: var(--fs-md);
        font-weight: 900;
        line-height: 1.25;
        margin: 0 0 var(--space-2);
    }
    .magnet-card p { font-size: var(--fs-sm); color: var(--fg2); margin: 0; line-height: 1.5; }

    /* ============ CLOSING CTA ============ */
    .closing-cta {
        background: var(--gx-secondary-dark);
        color: var(--fg-on-dark);
        padding: var(--space-16) 0;
        position: relative;
        overflow: hidden;
    }
    .closing-cta .gx-container { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; gap: var(--space-8); flex-wrap: wrap; }
    .closing-cta h2 {
        font-size: clamp(28px, 4vw, 44px);
        font-weight: 900;
        letter-spacing: var(--ls-tighter);
        text-transform: uppercase;
        line-height: 1;
        margin: 0;
        max-width: 640px;
    }
    .closing-cta .btn-back {
        background: var(--gx-primary-dark);
        color: var(--fg-on-dark);
        font-size: var(--fs-sm);
        font-weight: 900;
        letter-spacing: var(--ls-widest);
        text-transform: uppercase;
        padding: var(--space-5) var(--space-8);
        border: 0;
        cursor: pointer;
        transition: var(--transition-smooth);
    }
    .closing-cta .btn-back:hover { background: var(--gx-primary); transform: translate(-2px, -2px); box-shadow: var(--shadow-card-hover); }

    /* ============ EDITORIAL / TIME ============ */
    .editorial-section { padding: var(--space-20) 0; background: var(--bg1); border-top: 1px solid var(--gx-border); }
    .editorial-section .grid { display: grid; grid-template-columns: 1fr 1.4fr; gap: var(--space-16); align-items: start; }
    .editorial-section h2 {
        font-size: clamp(28px, 4vw, 48px); font-weight: 900;
        letter-spacing: var(--ls-tighter); text-transform: uppercase;
        line-height: 1; margin: var(--space-3) 0 var(--space-6);
    }
    .editorial-section .lead {
        font-size: var(--fs-md); color: var(--fg2); line-height: 1.65;
        margin: 0 0 var(--space-5);
    }
    .editorial-section .lead.text-body { color: var(--fg1); }
    .editorial-section .methodology { display: flex; flex-direction: column; gap: var(--space-6); margin-top: var(--space-8); }
    .editorial-section .method-step {
        display: grid; grid-template-columns: 60px 1fr; gap: var(--space-5);
        padding-top: var(--space-4); border-top: 1px solid var(--gx-border);
    }
    .editorial-section .method-step:first-child { border-top: 0; padding-top: 0; }
    .editorial-section .method-step .num {
        font-family: var(--font-mono); font-weight: 900;
        font-size: var(--fs-2xl); color: var(--gx-secondary-dark); line-height: 1;
    }
    .editorial-section .method-step h3 {
        font-size: var(--fs-md); font-weight: 700; text-transform: uppercase;
        letter-spacing: var(--ls-wider); margin: 0 0 var(--space-2); color: var(--fg1);
    }
    .editorial-section .method-step p {
        margin: 0; font-size: var(--fs-sm); color: var(--fg2); line-height: 1.55;
    }

    /* ============ EDIÇÕES RECENTES ============ */
    .editions-section {
        background: var(--bg-dark); color: var(--fg-on-dark);
        padding: var(--space-20) 0; border-top: 4px solid var(--gx-secondary-dark);
        position: relative; overflow: hidden;
    }
    .editions-section .watermark {
        position: absolute; right: -2vw; top: 50%; transform: translateY(-50%);
        font-family: var(--font-display); font-weight: 900;
        font-size: clamp(160px, 22vw, 300px);
        line-height: 0.8; letter-spacing: -0.06em;
        color: var(--gx-secondary-light); opacity: 0.04;
        pointer-events: none; user-select: none; white-space: nowrap;
    }
    .editions-section .gx-container { position: relative; z-index: 2; }
    .editions-section h2 {
        font-size: clamp(28px, 4vw, 48px); font-weight: 900;
        letter-spacing: var(--ls-tighter); text-transform: uppercase;
        line-height: 1; margin: var(--space-3) 0 var(--space-4); color: var(--fg-on-dark);
    }
    .editions-section .lead { color: var(--gx-secondary-light); font-size: var(--fs-md); max-width: 640px; margin: 0 0 var(--space-10); }
    .editions-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0; border-top: 1px solid rgba(219,199,162,0.18); }
    .edition-row {
        display: grid; grid-template-columns: 130px 1fr;
        gap: var(--space-5); padding: var(--space-5) var(--space-4);
        border-bottom: 1px solid rgba(219,199,162,0.18);
        align-items: start;
    }
    .edition-row:nth-child(odd) { border-right: 1px solid rgba(219,199,162,0.18); }
    .edition-row .meta {
        font-family: var(--font-mono); font-variant-numeric: tabular-nums;
        font-size: var(--fs-xs); font-weight: 700;
        color: var(--gx-gold); letter-spacing: var(--ls-wider);
        text-transform: uppercase;
    }
    .edition-row .meta .line { color: var(--gx-secondary-light); display: block; margin-top: 4px; font-size: 9px; }
    .edition-row .subject {
        font-size: var(--fs-base); font-weight: 700; line-height: 1.4;
        color: var(--fg-on-dark); margin: 0 0 var(--space-2);
    }
    .edition-row .preheader { font-size: var(--fs-xs); color: var(--gx-secondary-light); opacity: 0.75; margin: 0; line-height: 1.4; }

    @media (max-width: 960px) {
        .editorial-section .grid { grid-template-columns: 1fr; gap: var(--space-8); }
        .editions-grid { grid-template-columns: 1fr; }
        .edition-row:nth-child(odd) { border-right: 0; }
        .hero-newsletter .gx-container { grid-template-columns: 1fr; gap: var(--space-10); }
        .value-grid { grid-template-columns: 1fr; }
        .line-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- HERO -->
<section class="hero-newsletter">
    <?php if (!empty($settings->landing_hero_image)): ?>
        <img src="/<?= esc(ltrim($settings->landing_hero_image, '/')); ?>"
             alt="Inteligência financeira GX Capital"
             class="hero-img"
             width="1536" height="1024"
             loading="eager" fetchpriority="high" decoding="async">
    <?php endif; ?>
    <div class="watermark" aria-hidden="true">GXC</div>
    <div class="gx-container">
        <div class="hero-left">
            <div class="hero-eyebrow gx-eyebrow-bar"><?= esc($settings->landing_eyebrow); ?></div>
            <h1><?= esc($settings->landing_headline); ?></h1>
            <p class="subhead"><?= esc($settings->landing_subheadline); ?></p>
            <div class="hero-stats">
                <div>
                    <div class="stat-label">Frequência</div>
                    <div class="stat-value">3×</div>
                    <div class="stat-label" style="margin-top:4px;opacity:0.5;">por dia</div>
                </div>
                <div>
                    <div class="stat-label">Tempo de leitura</div>
                    <div class="stat-value">90s</div>
                    <div class="stat-label" style="margin-top:4px;opacity:0.5;">por edição</div>
                </div>
                <div>
                    <div class="stat-label">Cancelar</div>
                    <div class="stat-value">1×</div>
                    <div class="stat-label" style="margin-top:4px;opacity:0.5;">clique</div>
                </div>
            </div>
        </div>

        <!-- FORM CARD -->
        <form class="form-card" action="/newsletter/subscribe" method="post">
            <div class="form-eyebrow">Comece agora</div>
            <h3>Inscreva-se gratuitamente</h3>

            <div class="form-row">
                <label for="nl-email">Email corporativo</label>
                <input type="email" id="nl-email" name="email" required placeholder="voce@empresa.com">
                <input type="text" name="url" tabindex="-1" autocomplete="off">
            </div>

            <?php if (!empty($lines)): ?>
            <div class="form-row">
                <label>Quais frentes você acompanha</label>
                <div class="line-grid">
                    <?php foreach ($lines as $line):
                        $checked = ($preselectedLineId === (int) $line->id) ? 'checked' : '';
                    ?>
                    <label class="line-check">
                        <input type="checkbox" name="line_ids[]" value="<?= (int) $line->id; ?>" <?= $checked; ?>>
                        <span class="line-check-text">
                            <span class="line-name"><?= esc($line->name); ?></span>
                            <?php if (!empty($line->description)): ?>
                                <span class="line-desc"><?= esc(mb_substr($line->description, 0, 60)); ?></span>
                            <?php endif; ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <button type="submit" class="btn-primary-cta">
                <?= esc($settings->landing_cta_text ?: 'Inscrever-me'); ?> &rarr;
            </button>

            <?php
            // Prova social dinâmica se houver número significativo, senão fallback ao texto configurado
            $proofText = $settings->landing_social_proof;
            if (!empty($subscribersActive) && $subscribersActive >= 50) {
                $proofText = '+' . number_format($subscribersActive, 0, ',', '.') . ' executivos recebem a newsletter da GX Capital';
            }
            ?>
            <?php if (!empty($proofText)): ?>
                <div class="social-proof"><?= esc($proofText); ?></div>
            <?php endif; ?>
        </form>
    </div>
</section>

<!-- VALUE PROPS -->
<section class="value-section">
    <div class="gx-container">
        <div class="gx-eyebrow-bar on-light" style="font-size:var(--fs-sm);font-weight:var(--fw-bold);letter-spacing:var(--ls-widest);text-transform:uppercase;color:var(--gx-primary);">
            O que muda para você
        </div>
        <h2>Decisão informada antes do mercado reagir.</h2>
        <div class="value-grid">
            <div class="value-card">
                <div class="num">01</div>
                <h3>Câmbio em tempo real</h3>
                <p>Movimentações de dólar e juros explicadas em 90 segundos — com a leitura GX por trás dos números.</p>
            </div>
            <div class="value-card">
                <div class="num">02</div>
                <h3>Crédito acionável</h3>
                <p>Linhas Pronampe, ProCred 360, BNDES e debêntures — quando entrar, quanto pagar, como estruturar.</p>
            </div>
            <div class="value-card">
                <div class="num">03</div>
                <h3>Economia decifrada</h3>
                <p>Boletim Focus, IPCA, decisões do Copom e contas externas — traduzidos para a planilha do seu CFO.</p>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($magnetsByLine)): ?>
<!-- MAGNETS -->
<section class="magnets-section">
    <div class="gx-container">
        <div class="gx-eyebrow-bar on-light" style="font-size:var(--fs-sm);font-weight:var(--fw-bold);letter-spacing:var(--ls-widest);text-transform:uppercase;color:var(--gx-primary);">
            Bônus de inscrição
        </div>
        <h2>Materiais exclusivos no seu inbox.</h2>
        <p class="lead">Ao se inscrever em uma das frentes acima, você recebe o material correspondente direto no email — gratuito, sem follow-up agressivo.</p>
        <div class="magnet-grid">
            <?php foreach ($lines as $line):
                if (empty($magnetsByLine[$line->id])) continue;
                $m = $magnetsByLine[$line->id];
            ?>
            <div class="magnet-card">
                <div class="magnet-cover">
                    <?php if (!empty($m->cover_image)): ?>
                        <img src="<?= esc($m->cover_image); ?>" alt="">
                    <?php else: ?>
                        <span class="magnet-cover-placeholder">GXC</span>
                    <?php endif; ?>
                </div>
                <div class="body">
                    <div class="line-tag"><?= esc($line->name); ?></div>
                    <h3><?= esc($m->title); ?></h3>
                    <?php if (!empty($m->description)): ?>
                        <p><?= esc(mb_substr($m->description, 0, 120)); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- EDITORIAL / METODOLOGIA -->
<section class="editorial-section">
    <div class="gx-container">
        <div class="grid">
            <div>
                <div class="gx-eyebrow-bar on-light" style="font-size:var(--fs-sm);font-weight:var(--fw-bold);letter-spacing:var(--ls-widest);text-transform:uppercase;color:var(--gx-primary);">
                    Como produzimos
                </div>
                <h2>Curadoria editorial assistida por IA.</h2>
                <p class="lead text-body">
                    A newsletter da GX Capital combina <strong>jornalismo financeiro tradicional</strong>
                    com <strong>inteligência artificial proprietária</strong>. Toda edição passa por três
                    filtros antes de chegar ao seu inbox — seleção temática, redação editorial e revisão humana.
                </p>
                <p class="lead">
                    Cobrimos quatro frentes: <strong>câmbio</strong> (dólar, hedge, importação/exportação),
                    <strong>crédito empresarial</strong> (Pronampe, ProCred 360, linhas BNDES, debêntures),
                    <strong>economia brasileira</strong> (Selic, IPCA, Focus, balança comercial) e
                    <strong>consórcio</strong> (matemática, comparação com financiamento, blindagem patrimonial).
                </p>
                <p class="lead">
                    Nossa proposta é tirar a barreira entre o noticiário macro e a planilha do seu CFO:
                    cada edição traz números do dia, contexto e — quando aplicável — uma ação prática
                    que você pode levar para a reunião da semana.
                </p>
            </div>
            <div>
                <div class="gx-eyebrow-bar on-light" style="font-size:var(--fs-sm);font-weight:var(--fw-bold);letter-spacing:var(--ls-widest);text-transform:uppercase;color:var(--gx-primary);">
                    Metodologia em 4 etapas
                </div>
                <div class="methodology">
                    <div class="method-step">
                        <div class="num">01</div>
                        <div>
                            <h3>Captura de sinais</h3>
                            <p>Monitoramento de RSS, comunicados oficiais (BCB, IBGE, Fazenda, BNDES), Google Trends financeiros, e feed institucional dos principais agentes do mercado.</p>
                        </div>
                    </div>
                    <div class="method-step">
                        <div class="num">02</div>
                        <div>
                            <h3>Filtro de relevância</h3>
                            <p>Classificador treinado com a linha editorial da GX Capital descarta ruído (especulação, clickbait, eventos sem impacto direto) e mantém só o que importa para tesouraria, crédito empresarial e câmbio.</p>
                        </div>
                    </div>
                    <div class="method-step">
                        <div class="num">03</div>
                        <div>
                            <h3>Redação editorial</h3>
                            <p>Cada edição é redigida por modelo de linguagem instruído com o tom GX (executivo, direto, factual) e auditado contra fonte primária — sem invenção de números nem extrapolação.</p>
                        </div>
                    </div>
                    <div class="method-step">
                        <div class="num">04</div>
                        <div>
                            <h3>Revisão humana</h3>
                            <p>Antes do disparo, um analista da GX valida números, contexto e tom. Erro de dado é vetor de credibilidade — preferimos atrasar uma edição a deixar passar.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($recentSends)): ?>
<!-- EDIÇÕES RECENTES -->
<section class="editions-section">
    <div class="watermark" aria-hidden="true">EDIÇÕES</div>
    <div class="gx-container">
        <div class="gx-eyebrow-bar" style="font-size:var(--fs-sm);font-weight:var(--fw-bold);letter-spacing:var(--ls-widest);text-transform:uppercase;color:var(--gx-secondary-light);">
            Últimas edições publicadas
        </div>
        <h2>O que saiu da redação esta semana.</h2>
        <p class="lead">Uma amostra real do que você recebe ao se inscrever — assuntos, gancho e tom editorial.</p>
        <div class="editions-grid">
            <?php foreach ($recentSends as $s):
                $sentTs = $s->sent_at ? strtotime($s->sent_at) : time();
            ?>
            <article class="edition-row">
                <div class="meta">
                    <?= esc(date('d/m', $sentTs)); ?>
                    <span class="line"><?= esc($s->line_name ?? 'Editorial'); ?></span>
                </div>
                <div>
                    <h3 class="subject"><?= esc($s->subject); ?></h3>
                    <?php if (!empty($s->preheader)): ?>
                        <p class="preheader"><?= esc(mb_substr($s->preheader, 0, 140)); ?></p>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FAQ -->
<style>
    .faq-section { padding: var(--space-20) 0; background: var(--bg1); border-top: 1px solid var(--gx-border); }
    .faq-section h2 {
        font-size: clamp(28px, 4vw, 48px); font-weight: 900;
        letter-spacing: var(--ls-tighter); text-transform: uppercase;
        line-height: 1; margin: var(--space-3) 0 var(--space-10);
    }
    .faq-list { display: flex; flex-direction: column; gap: 0; max-width: 880px; }
    .faq-item { border-bottom: 1px solid var(--gx-border); padding: var(--space-6) 0; }
    .faq-item:first-child { border-top: 1px solid var(--gx-border); }
    .faq-item summary {
        list-style: none; cursor: pointer; display: flex; align-items: flex-start; justify-content: space-between; gap: var(--space-6);
        font-size: var(--fs-lg); font-weight: 700; color: var(--fg1);
        letter-spacing: var(--ls-tight); line-height: 1.3;
    }
    .faq-item summary::-webkit-details-marker { display: none; }
    .faq-item summary::after {
        content: '+'; font-family: var(--font-mono); font-size: 28px; font-weight: 900;
        color: var(--gx-secondary-dark); line-height: 1; flex-shrink: 0;
        transition: var(--transition-smooth);
    }
    .faq-item[open] summary::after { content: '×'; color: var(--gx-primary); }
    .faq-item .answer { padding-top: var(--space-4); font-size: var(--fs-base); color: var(--fg2); line-height: 1.65; max-width: 720px; }
    .faq-item .answer p { margin: 0 0 var(--space-3); }
    .faq-item .answer p:last-child { margin: 0; }
</style>
<?php
$faqs = [
    [
        'q' => 'O que eu vou receber na newsletter GX Capital?',
        'a' => 'Briefings curtos (90 segundos de leitura) sobre câmbio, crédito empresarial, economia brasileira e consórcio. Cada edição traz os destaques mais relevantes do dia para tesoureiros, CFOs e empresários que tomam decisões financeiras — selecionados por IA editorial e validados pelo nosso time.',
    ],
    [
        'q' => 'Com que frequência os emails são enviados?',
        'a' => 'Até 3 envios por dia, distribuídos entre manhã, almoço e fim de tarde. Você escolhe quais frentes editoriais quer acompanhar (Câmbio, Crédito, Economia, Consórcio) e só recebe o conteúdo correspondente. Sem floods.',
    ],
    [
        'q' => 'A inscrição é gratuita?',
        'a' => 'Sim, totalmente gratuita. Além disso, ao se inscrever em uma das frentes, você recebe um material exclusivo (PDF) com análises práticas — como guias de hedge cambial, comparativos de linhas de crédito BNDES/Pronampe, ou matemática completa de consórcio vs financiamento.',
    ],
    [
        'q' => 'Como posso cancelar a inscrição?',
        'a' => 'Todo email da GX Capital traz um link de cancelamento ao final. Um clique e você é removido imediatamente da lista — sem perguntas, sem retenção forçada, sem tentativas de "última chance".',
    ],
    [
        'q' => 'Quem produz o conteúdo?',
        'a' => 'A curadoria é feita pelo time editorial da GX Capital, com leitura crítica de profissionais de mercado em câmbio, crédito corporativo e investimentos. A redação de cada edição é assistida por IA proprietária treinada com a linha editorial da casa, sempre revisada antes do envio.',
    ],
    [
        'q' => 'Vocês vão me enviar propaganda ou compartilhar meu email?',
        'a' => 'Não. Não vendemos, alugamos nem compartilhamos seu email com terceiros. Você recebe apenas conteúdo editorial e, ocasionalmente, comunicações sobre serviços diretamente relacionados — sempre com opção clara de opt-out.',
    ],
];
?>
<section class="faq-section">
    <div class="gx-container">
        <div class="gx-eyebrow-bar on-light" style="font-size:var(--fs-sm);font-weight:var(--fw-bold);letter-spacing:var(--ls-widest);text-transform:uppercase;color:var(--gx-primary);">
            Perguntas frequentes
        </div>
        <h2>Tudo que você precisa saber.</h2>
        <div class="faq-list">
            <?php foreach ($faqs as $i => $faq): ?>
                <details class="faq-item" <?= $i === 0 ? 'open' : ''; ?>>
                    <summary><?= esc($faq['q']); ?></summary>
                    <div class="answer">
                        <p><?= esc($faq['a']); ?></p>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CLOSING CTA -->
<section class="closing-cta">
    <div class="gx-container">
        <h2>Comece a receber a inteligência GX hoje.</h2>
        <a href="#nl-email" class="btn-back" onclick="document.getElementById('nl-email').focus(); return false;">Inscrever-me agora &rarr;</a>
    </div>
</section>

<?php
$bodyContent = ob_get_clean();

// JSON-LD structured data
$canonicalUrl = function_exists('base_url') ? base_url('newsletter') : 'https://gx.capital/newsletter';
$heroOgImage = !empty($settings->landing_hero_image)
    ? rtrim(base_url(), '/') . '/' . ltrim($settings->landing_hero_image, '/')
    : (function_exists('base_url') ? base_url('assets/logo-app-blue.png') : null);

$ldGraph = [
    [
        '@type' => 'Organization',
        '@id'   => rtrim(base_url(), '/') . '/#organization',
        'name'  => 'GX Capital',
        'url'   => rtrim(base_url(), '/') . '/',
        'logo'  => [
            '@type' => 'ImageObject',
            'url'   => rtrim(base_url(), '/') . '/assets/logo-app-blue.png',
        ],
        'sameAs' => array_values(array_filter([
            $generalSettings->facebook_page ?? null,
            $generalSettings->twitter_page ?? null,
            $generalSettings->instagram_page ?? null,
            $generalSettings->linkedin_page ?? null,
            $generalSettings->youtube_page ?? null,
        ])),
    ],
    [
        '@type' => 'WebPage',
        '@id'   => $canonicalUrl . '#webpage',
        'url'   => $canonicalUrl,
        'name'  => $settings->landing_headline,
        'description' => $settings->landing_subheadline,
        'inLanguage' => 'pt-BR',
        'isPartOf' => ['@id' => rtrim(base_url(), '/') . '/#website'],
        'about' => [
            '@type' => 'Thing',
            'name'  => 'Newsletter financeira GX Capital',
        ],
        'primaryImageOfPage' => $heroOgImage ? ['@type' => 'ImageObject', 'url' => $heroOgImage] : null,
    ],
    [
        '@type' => 'WebSite',
        '@id'   => rtrim(base_url(), '/') . '/#website',
        'url'   => rtrim(base_url(), '/') . '/',
        'name'  => 'GX Capital',
        'publisher' => ['@id' => rtrim(base_url(), '/') . '/#organization'],
        'inLanguage' => 'pt-BR',
    ],
    [
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Início', 'item' => rtrim(base_url(), '/') . '/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Newsletter', 'item' => $canonicalUrl],
        ],
    ],
    [
        '@type' => 'FAQPage',
        'mainEntity' => array_map(function ($faq) {
            return [
                '@type' => 'Question',
                'name'  => $faq['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
            ];
        }, $faqs),
    ],
];
$jsonLd = json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => array_map(function ($n) { return array_filter($n); }, $ldGraph),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

echo view('newsletter/_layout', [
    'title' => $settings->landing_headline ?? 'Newsletter',
    'description' => $settings->landing_subheadline ?? '',
    'canonical' => $canonicalUrl,
    'ogImage' => $heroOgImage,
    'preloadHero' => !empty($settings->landing_hero_image) ? '/' . ltrim($settings->landing_hero_image, '/') : null,
    'jsonLd' => $jsonLd,
    'bodyContent' => $bodyContent,
]);
?>
