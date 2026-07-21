<?php
/** @var array $c course; @var array|null $enroll */
$pct = isset($enroll['progress_percent']) ? (int) $enroll['progress_percent'] : null;
$levelLabels = ['iniciante' => 'Iniciante', 'intermediario' => 'Intermediário', 'avancado' => 'Avançado'];
?>
<a class="ac-card" href="<?= site_url('curso/' . $c['slug']) ?>">
    <div class="ac-card__cover">
        <?php if (!empty($c['cover_image'])): ?><img class="ac-card__img" src="<?= esc($c['cover_image'], 'attr') ?>" alt="" loading="lazy"><?php endif; ?>
        <div class="ac-card__grad"></div>
        <?php if (!empty($c['level'])): ?><span class="ac-card__badge"><?= esc($levelLabels[$c['level']] ?? $c['level']) ?></span><?php endif; ?>
        <div class="ac-card__play"><span>▶</span></div>
        <?php if ($pct !== null && $pct > 0): ?><div class="ac-prog"><i style="width:<?= $pct ?>%"></i></div><?php endif; ?>
    </div>
    <div class="ac-card__body">
        <?php if (!empty($c['category'])): ?><div class="ac-card__cat"><?= esc($c['category']) ?></div><?php endif; ?>
        <div class="ac-card__title"><?= esc($c['title']) ?></div>
        <div class="ac-card__sub"><?= esc($c['subtitle'] ?? '') ?></div>
        <div class="ac-card__foot">
            <span class="ac-mono"><?= (int) ($c['xp_reward'] ?? 0) ?> XP<?php if (!empty($c['estimated_minutes'])): ?> · <?= (int) $c['estimated_minutes'] ?> min<?php endif; ?></span>
            <span style="color:var(--gx-gold)"><?= $pct !== null && $pct > 0 ? ($pct >= 100 ? 'Concluído ✓' : 'Continuar →') : 'Iniciar →' ?></span>
        </div>
    </div>
</a>
