<?php
$pageTitle = 'Cursos';
echo view('courses/_head', ['pageTitle' => $pageTitle, 'totalXp' => $totalXp]);
$levelLabels = ['iniciante' => 'Iniciante', 'intermediario' => 'Intermediário', 'avancado' => 'Avançado'];
?>
<style>
    /* HERO cinematográfico (estilo Netflix) */
    .nf-hero{position:relative;min-height:82vh;display:flex;align-items:flex-end;margin:-60px -5vw 0;padding:0 5vw 7vh;overflow:hidden;background:var(--gradient-hero)}
    .nf-hero__bg{position:absolute;inset:0;background-size:cover;background-position:center;animation:nfken 26s ease-in-out infinite alternate;will-change:transform}
    @keyframes nfken{from{transform:scale(1.05)}to{transform:scale(1.17)}}
    .nf-hero__fade{position:absolute;inset:0;background:linear-gradient(90deg,rgba(0,13,35,.94) 0%,rgba(0,13,35,.6) 40%,rgba(0,13,35,.1) 72%),linear-gradient(0deg,var(--gx-primary-dark) 2%,rgba(0,13,35,0) 55%)}
    .nf-hero__wm{position:absolute;right:-1vw;bottom:-5vw;font-weight:900;font-size:26vw;line-height:.8;color:#fff;opacity:.04;letter-spacing:-.05em;pointer-events:none;user-select:none}
    .nf-hero__in{position:relative;z-index:2;max-width:640px}
    .nf-hero__kick{text-transform:uppercase;letter-spacing:var(--ls-widest);font-size:12px;font-weight:800;color:var(--gx-gold)}
    .nf-hero h1{font-size:clamp(40px,6.5vw,88px);font-weight:900;line-height:.94;letter-spacing:var(--ls-tighter);text-transform:uppercase;margin:14px 0 12px}
    .nf-hero p{font-size:clamp(15px,1.4vw,19px);color:#dbe3ec;max-width:56ch;line-height:1.55;text-shadow:0 2px 14px rgba(0,0,0,.6)}
    .nf-hero__meta{display:flex;gap:14px;flex-wrap:wrap;align-items:center;margin:18px 0 26px;font-size:12px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:#c7d2e0}
    .nf-badge{border:1px solid rgba(219,199,162,.5);padding:3px 9px;color:var(--gx-secondary-light);font-weight:700}
    .nf-hero__cta{display:flex;gap:12px;flex-wrap:wrap}
    @media(max-width:640px){.nf-hero{min-height:72vh}.nf-hero__fade{background:linear-gradient(0deg,var(--gx-primary-dark) 8%,rgba(0,13,35,.2) 60%)}}
</style>

<?php if (!empty($featured)):
    $f = $featured;
    $fEnroll = $enrollMap[(int) $f['id']] ?? null;
    $fpct = (int) ($fEnroll['progress_percent'] ?? 0);
?>
<section class="nf-hero">
    <?php if (!empty($f['cover_image'])): ?><div class="nf-hero__bg" style="background-image:url('<?= esc($f['cover_image'], 'attr') ?>')"></div><?php endif; ?>
    <div class="nf-hero__fade"></div>
    <div class="nf-hero__wm">GXC</div>
    <div class="nf-hero__in">
        <div class="nf-hero__kick">Em destaque · <?= esc($f['category'] ?: 'Trilha') ?></div>
        <h1><?= esc($f['title']) ?></h1>
        <p><?= esc($f['subtitle'] ?? $f['description'] ?? '') ?></p>
        <div class="nf-hero__meta">
            <?php if (!empty($f['level'])): ?><span class="nf-badge"><?= esc($levelLabels[$f['level']] ?? $f['level']) ?></span><?php endif; ?>
            <?php if (!empty($f['instructor'])): ?><span>Por <?= esc($f['instructor']) ?></span><?php endif; ?>
            <?php if (!empty($f['estimated_minutes'])): ?><span><?= (int) $f['estimated_minutes'] ?> min</span><?php endif; ?>
            <span class="ac-mono"><?= (int) $f['xp_reward'] ?> XP</span>
            <?php if ($fpct > 0): ?><span style="color:var(--gx-gold)"><?= $fpct ?>% assistido</span><?php endif; ?>
        </div>
        <div class="nf-hero__cta">
            <a class="ac-btn" href="<?= site_url('curso/' . $f['slug']) ?>">▶ <?= $fpct > 0 ? 'Continuar' : 'Começar agora' ?></a>
            <a class="ac-btn ac-btn--ghost" href="<?= site_url('curso/' . $f['slug']) ?>">ⓘ Mais informações</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// helper de fileira/carrossel com setas
$row = function (string $title, array $courses) use ($enrollMap) {
    if (empty($courses)) {
        return;
    }
    echo '<div class="ac-eyebrow">' . esc($title) . '</div>';
    echo '<div class="ac-row"><button class="ac-arrow ac-arrow--l" aria-label="Anterior">‹</button><div class="ac-track">';
    foreach ($courses as $c) {
        echo view('courses/_card', ['c' => $c, 'enroll' => $c['_enroll'] ?? ($enrollMap[(int) $c['id']] ?? null)]);
    }
    echo '</div><button class="ac-arrow ac-arrow--r" aria-label="Próximo">‹</button></div>';
};
?>

<?php if (!empty($continue)) {
    $row('Continuar assistindo', $continue);
} ?>

<?php if (empty($grouped)): ?>
    <div class="ac-eyebrow">Catálogo</div>
    <div class="ac-empty">Nenhum curso publicado ainda. Volte em breve — novas trilhas estão a caminho.</div>
<?php else: ?>
    <?php foreach ($grouped as $category => $courses) {
        $row($category, $courses);
    } ?>
<?php endif; ?>

<?= view('courses/_carousel_js'); ?>

<?php echo view('courses/_foot'); ?>
