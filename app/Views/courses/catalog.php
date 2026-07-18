<?php $pageTitle = 'Cursos'; echo view('courses/_head', ['pageTitle' => $pageTitle, 'totalXp' => $totalXp]); ?>

<?php if (!empty($featured)):
    $f = $featured;
    $bg = !empty($f['cover_image']) ? 'background-image:linear-gradient(180deg,rgba(0,13,35,.2),rgba(0,13,35,.8)),url(\'' . esc($f['cover_image'], 'attr') . '\');' : '';
    $fEnroll = $enrollMap[(int) $f['id']] ?? null;
?>
<section class="ac-hero" style="<?= $bg ?>">
    <div class="ac-hero__wm">GXC</div>
    <div class="ac-hero__in">
        <div class="ac-hero__kick">Em destaque · Trilha</div>
        <h1><?= esc($f['title']) ?></h1>
        <p><?= esc($f['subtitle'] ?? $f['description'] ?? '') ?></p>
        <div class="ac-hero__meta">
            <?php if (!empty($f['instructor'])): ?><span>Por <?= esc($f['instructor']) ?></span><?php endif; ?>
            <?php if (!empty($f['estimated_minutes'])): ?><span><?= (int) $f['estimated_minutes'] ?> min</span><?php endif; ?>
            <span class="ac-mono"><?= (int) $f['xp_reward'] ?> XP</span>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap">
            <a class="ac-btn" href="<?= site_url('curso/' . $f['slug']) ?>">▶ <?= ($fEnroll['progress_percent'] ?? 0) > 0 ? 'Continuar jornada' : 'Começar agora' ?></a>
            <a class="ac-btn ac-btn--ghost" href="<?= site_url('curso/' . $f['slug']) ?>">Ver a trilha</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (empty($grouped)): ?>
    <div class="ac-eyebrow">Catálogo</div>
    <div class="ac-empty">Nenhum curso publicado ainda. Volte em breve — novas trilhas estão a caminho.</div>
<?php else: ?>
    <?php foreach ($grouped as $category => $courses): ?>
        <div class="ac-eyebrow"><?= esc($category) ?></div>
        <div class="ac-row">
            <div class="ac-track">
                <?php foreach ($courses as $c): ?>
                    <?= view('courses/_card', ['c' => $c, 'enroll' => $enrollMap[(int) $c['id']] ?? null]) ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php echo view('courses/_foot'); ?>
