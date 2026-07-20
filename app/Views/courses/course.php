<?php
$pageTitle = $course['title'];
echo view('courses/_head', ['pageTitle' => $pageTitle, 'totalXp' => null]);
$bg = !empty($course['cover_image']) ? 'background-image:linear-gradient(180deg,rgba(0,13,35,.25),rgba(0,13,35,.85)),url(\'' . esc($course['cover_image'], 'attr') . '\');' : '';
// primeira aula p/ "continuar/começar": primeira não concluída e destravada, senão a 1ª
$continueSlug = null; $firstSlug = null;
foreach ($sections as $s) {
    foreach ($lessonsBySection[(int) $s['id']] ?? [] as $l) {
        if ($firstSlug === null) { $firstSlug = $l['slug']; }
        if ($continueSlug === null && empty($l['_completed']) && empty($l['_locked'])) { $continueSlug = $l['slug']; }
    }
}
$continueSlug = $continueSlug ?: $firstSlug;
?>
<style>
    .jn-hero{position:relative;margin:0 -5vw;padding:6vw 5vw 4vw;overflow:hidden;background:<?= $bg ?: 'var(--gradient-hero)' ?>;background-size:cover;background-position:center;}
    .jn-hero__in{position:relative;max-width:760px}
    .jn-kick{text-transform:uppercase;letter-spacing:var(--ls-widest);font-size:12px;font-weight:800;color:var(--gx-gold)}
    .jn-hero h1{font-size:clamp(34px,5vw,64px);font-weight:900;line-height:.96;letter-spacing:var(--ls-tighter);text-transform:uppercase;margin:12px 0}
    .jn-hero p{font-size:17px;color:#cdd7e4;max-width:60ch;line-height:1.5}
    .jn-stats{display:flex;gap:26px;flex-wrap:wrap;margin:22px 0;align-items:center}
    .jn-stat b{display:block;font-family:var(--font-mono);font-size:26px;font-weight:900;color:#fff}
    .jn-stat span{font-size:11px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:#9fb0c4}
    .jn-progwrap{max-width:520px;margin-top:8px}
    .jn-progwrap .ac-prog{height:10px}
    .jn-cert{margin:var(--space-6) 0;padding:16px 20px;border:1px solid var(--gx-gold);background:rgba(201,169,106,.12);display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
    .jn-sec{margin-top:var(--space-8)}
    .jn-sec__title{display:flex;align-items:center;gap:12px;font-size:13px;text-transform:uppercase;letter-spacing:var(--ls-wide);font-weight:800;color:var(--gx-gold);margin-bottom:14px}
    .jn-sec__title span:last-child{color:#7d8da5;font-family:var(--font-mono)}
    .jn-lesson{display:flex;align-items:center;gap:16px;padding:16px 20px;background:#0a2547;border:1px solid rgba(219,199,162,.15);margin-bottom:10px;transition:border-color .2s,transform .2s}
    .jn-lesson:hover{border-color:var(--gx-gold);transform:translateX(3px)}
    .jn-lesson--locked{opacity:.55;filter:grayscale(.3)}
    .jn-dot{flex:0 0 40px;height:40px;display:grid;place-items:center;border:2px solid rgba(219,199,162,.4);border-radius:var(--radius-pill);font-family:var(--font-mono);font-weight:700;font-size:14px;color:#aebbcc}
    .jn-dot--done{background:var(--gx-gold);border-color:var(--gx-gold);color:var(--gx-primary-dark)}
    .jn-lesson__main{flex:1}
    .jn-lesson__t{font-weight:700;font-size:16px}
    .jn-lesson__m{font-size:12px;color:#8ea0b6;text-transform:uppercase;letter-spacing:var(--ls-wide);margin-top:3px}
    .jn-lesson__r{font-size:12px;text-transform:uppercase;letter-spacing:var(--ls-wide);font-weight:700;color:var(--gx-gold)}
    .jn-tag{display:inline-block;padding:2px 8px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:var(--ls-wide);border:1px solid rgba(219,199,162,.4);color:var(--gx-secondary-light)}
</style>

<section class="jn-hero">
    <div class="ac-hero__wm" style="opacity:.05">GXC</div>
    <div class="jn-hero__in">
        <div class="jn-kick"><?= esc($course['category'] ?: 'Trilha') ?><?= !empty($course['instructor']) ? ' · ' . esc($course['instructor']) : '' ?></div>
        <h1><?= esc($course['title']) ?></h1>
        <p><?= esc($course['subtitle'] ?? '') ?></p>
        <div class="jn-stats">
            <div class="jn-stat"><b><?= (int) $completedCount ?>/<?= (int) $totalLessons ?></b><span>Aulas concluídas</span></div>
            <div class="jn-stat"><b><?= (int) $course['xp_reward'] ?></b><span>XP da trilha</span></div>
            <?php if (!empty($course['estimated_minutes'])): ?><div class="jn-stat"><b><?= (int) $course['estimated_minutes'] ?></b><span>Minutos</span></div><?php endif; ?>
        </div>
        <div class="jn-progwrap">
            <div class="ac-prog"><i style="width:<?= (int) $coursePercent ?>%"></i></div>
            <div style="margin-top:8px;font-size:12px;color:#9fb0c4;text-transform:uppercase;letter-spacing:var(--ls-wide)"><span class="ac-mono"><?= (int) $coursePercent ?>%</span> completo</div>
        </div>
        <div style="margin-top:22px;display:flex;gap:12px;flex-wrap:wrap">
            <?php if ($continueSlug): ?><a class="ac-btn" href="<?= site_url('curso/' . $course['slug'] . '/aula/' . $continueSlug) ?>">▶ <?= $coursePercent > 0 ? 'Continuar' : 'Começar' ?> a jornada</a><?php endif; ?>
            <?php if (!$canAccessCourse): ?><span class="jn-tag" style="align-self:center">🔒 Requer acesso</span><?php endif; ?>
        </div>
    </div>
</section>

<?php if (!empty($certificate)): ?>
<div class="jn-cert">
    <div><strong style="color:var(--gx-gold)">🏆 Trilha concluída!</strong> Seu certificado está disponível.</div>
    <a class="ac-btn ac-btn--ghost" href="<?= site_url('certificado/' . $certificate['code']) ?>">Ver certificado</a>
</div>
<?php endif; ?>

<?php if (!empty($course['description'])): ?>
<div class="ac-eyebrow">Sobre a trilha</div>
<div class="ac-panel" style="max-width:820px;line-height:1.65;color:#cdd7e4"><?= $course['description'] // conteúdo HTML do admin ?></div>
<?php endif; ?>

<div class="ac-eyebrow">A jornada</div>
<?php if (empty($sections)): ?>
    <div class="ac-empty">Conteúdo em preparação.</div>
<?php else: ?>
    <?php foreach ($sections as $s): $ls = $lessonsBySection[(int) $s['id']] ?? []; ?>
        <div class="jn-sec">
            <div class="jn-sec__title"><span><?= esc($s['title']) ?></span><span><?= count($ls) ?> aulas</span></div>
            <?php foreach ($ls as $i => $l):
                $locked = !empty($l['_locked']); $done = !empty($l['_completed']);
                $href = $locked ? null : site_url('curso/' . $course['slug'] . '/aula/' . $l['slug']);
                $tag = $locked ? '<span class="jn-tag">' . ($l['_lockReason'] === 'acesso' ? '🔒 Acesso' : '🔒 Bloqueada') . '</span>' : '';
            ?>
                <<?= $href ? 'a href="' . $href . '"' : 'div' ?> class="jn-lesson <?= $locked ? 'jn-lesson--locked' : '' ?>">
                    <div class="jn-dot <?= $done ? 'jn-dot--done' : '' ?>"><?= $done ? '✓' : ($i + 1) ?></div>
                    <?php if (!empty($l['cover_image'])): ?><img src="<?= esc($l['cover_image'], 'attr') ?>" alt="" style="width:72px;height:48px;object-fit:cover;flex:0 0 auto;border:1px solid rgba(219,199,162,.2)"><?php endif; ?>
                    <div class="jn-lesson__main">
                        <div class="jn-lesson__t"><?= esc($l['title']) ?><?= !empty($l['is_free_preview']) ? ' <span class="jn-tag">Amostra</span>' : '' ?></div>
                        <div class="jn-lesson__m"><?= esc($l['content_type'] === 'text' ? 'Leitura' : 'Vídeo') ?> · <?= (int) $l['xp_reward'] ?> XP<?= $done ? ' · Concluída' : ($l['_percent'] > 0 ? ' · ' . (int) $l['_percent'] . '%' : '') ?></div>
                    </div>
                    <div class="jn-lesson__r"><?= $done ? 'Rever' : ($locked ? $tag : 'Assistir →') ?></div>
                </<?= $href ? 'a' : 'div' ?>>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php echo view('courses/_foot'); ?>
