<?php
$pageTitle = 'Meus cursos';
echo view('courses/_head', ['pageTitle' => $pageTitle, 'totalXp' => $totalXp]);
// separa em andamento / concluídos
$inProgress = [];
$completed = [];
foreach ($courses as $c) {
    $pct = (int) ($c['_enroll']['progress_percent'] ?? 0);
    $isDone = $pct >= 100 || (($c['_enroll']['status'] ?? '') === 'completed');
    if ($isDone) {
        $completed[] = $c;
    } else {
        $inProgress[] = $c;
    }
}
$row = function (string $title, array $list) {
    if (empty($list)) {
        return;
    }
    echo '<div class="ac-eyebrow">' . esc($title) . '</div>';
    echo '<div class="ac-row"><button class="ac-arrow ac-arrow--l" aria-label="Anterior">‹</button><div class="ac-track">';
    foreach ($list as $c) {
        echo view('courses/_card', ['c' => $c, 'enroll' => $c['_enroll'] ?? null]);
    }
    echo '</div><button class="ac-arrow ac-arrow--r" aria-label="Próximo">‹</button></div>';
};
?>

<div class="ac-eyebrow">Sua jornada</div>
<div class="ac-panel" style="display:flex;gap:var(--space-10);flex-wrap:wrap;align-items:center;margin-bottom:var(--space-6)">
    <div><div style="font-family:var(--font-mono);font-size:40px;font-weight:900;color:var(--gx-gold)"><?= (int) $totalXp ?></div><div style="font-size:11px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:#9fb0c4">XP total</div></div>
    <div><div style="font-family:var(--font-mono);font-size:40px;font-weight:900;color:#fff"><?= count($courses) ?></div><div style="font-size:11px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:#9fb0c4">Cursos iniciados</div></div>
    <div><div style="font-family:var(--font-mono);font-size:40px;font-weight:900;color:#fff"><?= count($completed) ?></div><div style="font-size:11px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:#9fb0c4">Concluídos</div></div>
    <?php if (!empty($achievements)): ?>
    <div style="flex:1;min-width:220px">
        <div style="font-size:11px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:#9fb0c4;margin-bottom:8px">Conquistas</div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <?php foreach ($achievements as $a): ?>
                <span class="jn-tag" style="padding:6px 10px;border-color:var(--gx-gold);color:var(--gx-gold)" title="<?= esc($a['description'] ?? '', 'attr') ?>"><?= esc($a['icon'] ?? '🏅') ?> <?= esc($a['name']) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if (empty($courses)): ?>
    <div class="ac-eyebrow">Continuar aprendendo</div>
    <div class="ac-empty">Você ainda não iniciou nenhum curso. <a href="<?= site_url('cursos') ?>" style="color:var(--gx-gold)">Explorar o catálogo →</a></div>
<?php else: ?>
    <?php $row('Continuar assistindo', $inProgress); ?>
    <?php $row('Concluídos', $completed); ?>
<?php endif; ?>

<style>.jn-tag{display:inline-block;padding:2px 8px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:var(--ls-wide);border:1px solid rgba(219,199,162,.4);color:var(--gx-secondary-light)}</style>
<?php echo view('courses/_carousel_js'); ?>
<?php echo view('courses/_foot'); ?>
