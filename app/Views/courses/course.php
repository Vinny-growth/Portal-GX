<?php
$pageTitle = $course['title'];
echo view('courses/_head', ['pageTitle' => $pageTitle, 'totalXp' => null]);
// primeira aula p/ "continuar/começar": primeira não concluída e destravada, senão a 1ª
$continueSlug = null; $firstSlug = null;
foreach ($sections as $s) {
    foreach ($lessonsBySection[(int) $s['id']] ?? [] as $l) {
        if ($firstSlug === null) { $firstSlug = $l['slug']; }
        if ($continueSlug === null && empty($l['_completed']) && empty($l['_locked'])) { $continueSlug = $l['slug']; }
    }
}
$continueSlug = $continueSlug ?: $firstSlug;
$levelLabels = ['iniciante' => 'Iniciante', 'intermediario' => 'Intermediário', 'avancado' => 'Avançado'];
?>
<style>
    /* HERO cinematográfico do curso */
    .cp-hero{position:relative;min-height:74vh;display:flex;align-items:flex-end;margin:-60px -5vw 0;padding:0 5vw 5vh;overflow:hidden;background:var(--gradient-hero)}
    .cp-hero__bg{position:absolute;inset:0;background-size:cover;background-position:center;animation:nfken 26s ease-in-out infinite alternate;will-change:transform}
    @keyframes nfken{from{transform:scale(1.05)}to{transform:scale(1.16)}}
    .cp-hero__fade{position:absolute;inset:0;background:linear-gradient(90deg,rgba(0,13,35,.94),rgba(0,13,35,.55) 45%,rgba(0,13,35,.15) 75%),linear-gradient(0deg,var(--gx-primary-dark) 2%,rgba(0,13,35,0) 55%)}
    .cp-hero__wm{position:absolute;right:-1vw;bottom:-5vw;font-weight:900;font-size:24vw;line-height:.8;color:#fff;opacity:.04;letter-spacing:-.05em;pointer-events:none;user-select:none}
    .cp-hero__in{position:relative;z-index:2;max-width:720px}
    .cp-kick{text-transform:uppercase;letter-spacing:var(--ls-widest);font-size:12px;font-weight:800;color:var(--gx-gold)}
    .cp-hero h1{font-size:clamp(36px,5.5vw,72px);font-weight:900;line-height:.95;letter-spacing:var(--ls-tighter);text-transform:uppercase;margin:12px 0}
    .cp-hero p{font-size:clamp(15px,1.35vw,18px);color:#dbe3ec;max-width:60ch;line-height:1.5;text-shadow:0 2px 12px rgba(0,0,0,.5)}
    .cp-meta{display:flex;gap:22px;flex-wrap:wrap;align-items:center;margin:18px 0}
    .cp-meta b{font-family:var(--font-mono);font-size:22px;font-weight:900;color:#fff}
    .cp-meta small{display:block;font-size:10px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:#9fb0c4;font-family:var(--font-sans)}
    .cp-badge{border:1px solid rgba(219,199,162,.5);padding:3px 9px;color:var(--gx-secondary-light);font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:var(--ls-wide)}
    .cp-progwrap{max-width:520px;margin-top:6px}
    .cp-progwrap .ac-prog{height:8px}
    .cp-cta{margin-top:22px;display:flex;gap:12px;flex-wrap:wrap;align-items:center}
    .jn-cert{margin:var(--space-6) 0;padding:16px 20px;border:1px solid var(--gx-gold);background:rgba(201,169,106,.12);display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
    .jn-tag{display:inline-block;padding:2px 8px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:var(--ls-wide);border:1px solid rgba(219,199,162,.4);color:var(--gx-secondary-light)}
    /* episódios (aulas) em carrossel */
    .ep-card{flex:0 0 288px;scroll-snap-align:start;background:#0a2547;border:1px solid rgba(219,199,162,.15);position:relative;color:inherit;text-decoration:none;display:block;transition:transform .25s var(--transition-smooth,ease),border-color .25s,box-shadow .25s}
    .ep-card:hover{transform:translateY(-5px);border-color:var(--gx-gold);box-shadow:0 16px 34px -14px rgba(0,0,0,.65);z-index:10}
    .ep-card--locked{opacity:.5;filter:grayscale(.45);pointer-events:none}
    .ep-thumb{position:relative;height:158px;overflow:hidden;background:var(--gradient-primary);display:grid;place-items:center}
    .ep-thumb img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .55s var(--transition-smooth,ease)}
    .ep-card:hover .ep-thumb img{transform:scale(1.13)}
    .ep-fade{position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,13,35,0) 50%,rgba(10,37,71,.8));pointer-events:none}
    .ep-num{position:relative;z-index:1;font-family:var(--font-mono);font-size:46px;font-weight:900;color:rgba(255,255,255,.16)}
    .ep-play{position:absolute;inset:0;display:grid;place-items:center;opacity:0;transition:opacity .2s;z-index:2}
    .ep-card:hover .ep-play{opacity:1}
    .ep-play span{width:48px;height:48px;border-radius:var(--radius-pill);background:rgba(201,169,106,.95);color:var(--gx-primary-dark);display:grid;place-items:center;font-size:17px;padding-left:3px;box-shadow:0 6px 16px rgba(0,0,0,.5)}
    .ep-badge{position:absolute;top:8px;right:8px;z-index:3;font-size:14px}
    .ep-tag{position:absolute;top:8px;left:8px;z-index:3;padding:2px 7px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:var(--ls-wide);background:var(--gx-primary-dark);color:var(--gx-gold);border:1px solid rgba(219,199,162,.4)}
    .ep-thumb .ac-prog{position:absolute;left:0;right:0;bottom:0;z-index:3;height:4px;background:rgba(0,0,0,.55)}
    .ep-body{padding:12px 14px 14px}
    .ep-title{font-weight:700;font-size:14px;line-height:1.25;margin-bottom:6px}
    .ep-meta{font-size:11px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:#8ea0b6}
    .ep-meta .done{color:var(--gx-gold)}
</style>

<section class="cp-hero">
    <?php if (!empty($course['cover_image'])): ?><div class="cp-hero__bg" style="background-image:url('<?= esc($course['cover_image'], 'attr') ?>')"></div><?php endif; ?>
    <div class="cp-hero__fade"></div>
    <div class="cp-hero__wm">GXC</div>
    <div class="cp-hero__in">
        <div class="cp-kick"><?= esc($course['category'] ?: 'Trilha') ?><?= !empty($course['instructor']) ? ' · ' . esc($course['instructor']) : '' ?></div>
        <h1><?= esc($course['title']) ?></h1>
        <p><?= esc($course['subtitle'] ?? '') ?></p>
        <div class="cp-meta">
            <?php if (!empty($course['level'])): ?><span class="cp-badge"><?= esc($levelLabels[$course['level']] ?? $course['level']) ?></span><?php endif; ?>
            <span><b><?= (int) $completedCount ?>/<?= (int) $totalLessons ?></b><small>Aulas</small></span>
            <span><b><?= (int) $course['xp_reward'] ?></b><small>XP da trilha</small></span>
            <?php if (!empty($course['estimated_minutes'])): ?><span><b><?= (int) $course['estimated_minutes'] ?></b><small>Minutos</small></span><?php endif; ?>
            <span><b><?= (int) $coursePercent ?>%</b><small>Completo</small></span>
        </div>
        <div class="cp-progwrap"><div class="ac-prog"><i style="width:<?= (int) $coursePercent ?>%"></i></div></div>
        <div class="cp-cta">
            <?php if ($continueSlug): ?><a class="ac-btn" href="<?= site_url('curso/' . $course['slug'] . '/aula/' . $continueSlug) ?>">▶ <?= $coursePercent > 0 ? 'Continuar' : 'Começar' ?> a jornada</a><?php endif; ?>
            <?php if (!empty($certificate)): ?><a class="ac-btn ac-btn--ghost" href="<?= site_url('certificado/' . $certificate['code']) ?>">🏆 Certificado</a><?php endif; ?>
            <?php if (!$canAccessCourse): ?><span class="jn-tag">🔒 Requer acesso</span><?php endif; ?>
        </div>
    </div>
</section>

<?php if (!empty($certificate)): ?>
<div class="jn-cert">
    <div><strong style="color:var(--gx-gold)">🏆 Trilha concluída!</strong> Seu certificado de conclusão está disponível.</div>
    <a class="ac-btn ac-btn--ghost" href="<?= site_url('certificado/' . $certificate['code']) ?>">Ver certificado</a>
</div>
<?php endif; ?>

<?php if (!empty($course['description'])): ?>
<div class="ac-eyebrow">Sobre a trilha</div>
<div class="ac-panel" style="max-width:820px;line-height:1.65;color:#cdd7e4"><?= $course['description'] // conteúdo HTML do admin ?></div>
<?php endif; ?>

<?php if (empty($sections)): ?>
    <div class="ac-eyebrow">A jornada</div>
    <div class="ac-empty">Conteúdo em preparação.</div>
<?php else: ?>
    <?php $globalIdx = 0; foreach ($sections as $s): $ls = $lessonsBySection[(int) $s['id']] ?? []; ?>
        <div class="ac-eyebrow" style="justify-content:space-between"><span><?= esc($s['title']) ?></span><span class="ac-mono" style="color:#7d8da5"><?= count($ls) ?> aulas</span></div>
        <div class="ac-row">
            <button class="ac-arrow ac-arrow--l" aria-label="Anterior">‹</button>
            <div class="ac-track">
                <?php foreach ($ls as $l): $globalIdx++;
                    $locked = !empty($l['_locked']); $done = !empty($l['_completed']);
                    $href = $locked ? null : site_url('curso/' . $course['slug'] . '/aula/' . $l['slug']);
                ?>
                    <a class="ep-card <?= $locked ? 'ep-card--locked' : '' ?>"<?= $href ? ' href="' . $href . '"' : '' ?>>
                        <div class="ep-thumb">
                            <?php if (!empty($l['cover_image'])): ?><img src="<?= esc($l['cover_image'], 'attr') ?>" alt="" loading="lazy"><?php else: ?><span class="ep-num"><?= $globalIdx ?></span><?php endif; ?>
                            <div class="ep-fade"></div>
                            <?php if (!$locked): ?><div class="ep-play"><span>▶</span></div><?php endif; ?>
                            <?php if ($done): ?><span class="ep-badge">✓</span><?php elseif ($locked): ?><span class="ep-badge"><?= $l['_lockReason'] === 'acesso' ? '🔒' : '🔒' ?></span><?php endif; ?>
                            <?php if (!empty($l['is_free_preview'])): ?><span class="ep-tag">Amostra</span><?php endif; ?>
                            <?php if (($l['_percent'] ?? 0) > 0 && !$done): ?><div class="ac-prog"><i style="width:<?= (int) $l['_percent'] ?>%"></i></div><?php endif; ?>
                        </div>
                        <div class="ep-body">
                            <div class="ep-title"><?= $globalIdx ?>. <?= esc($l['title']) ?></div>
                            <div class="ep-meta"><?= esc($l['content_type'] === 'text' ? 'Leitura' : 'Vídeo') ?> · <?= (int) $l['xp_reward'] ?> XP<?php if ($done): ?> · <span class="done">Concluída ✓</span><?php elseif ($locked): ?> · <?= $l['_lockReason'] === 'acesso' ? 'Requer acesso' : 'Bloqueada' ?><?php endif; ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <button class="ac-arrow ac-arrow--r" aria-label="Próximo">‹</button>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php echo view('courses/_carousel_js'); ?>
<?php echo view('courses/_foot'); ?>
