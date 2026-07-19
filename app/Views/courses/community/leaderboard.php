<?php
echo view('courses/_head', ['pageTitle' => $pageTitle, 'totalXp' => $totalXp]);
echo view('courses/community/_styles');
?>
<div class="ac-eyebrow">🏆 Ranking · Top membros por XP</div>
<div style="max-width:720px">
<?php if (empty($leaderboard)): ?>
    <div class="ac-empty">O ranking aparece conforme os membros ganham XP em cursos e na comunidade.</div>
<?php else: ?>
    <?php foreach ($leaderboard as $i => $r): $medal = ['🥇', '🥈', '🥉'][$i] ?? null; ?>
        <div class="cm-rank <?= (int) $r['user_id'] === (int) $meId ? 'me' : '' ?>">
            <div class="cm-rank__pos"><?= $medal ?: ('#' . ($i + 1)) ?></div>
            <a class="cm-avatar" href="<?= site_url('comunidade/membro/' . $r['user_id']) ?>"><?= strtoupper(mb_substr((string) $r['username'], 0, 1) ?: '?') ?></a>
            <div style="flex:1;font-weight:700"><?= esc($r['username'] ?: 'Membro #' . $r['user_id']) ?><?= (int) $r['user_id'] === (int) $meId ? ' <span class="cm-space-badge">você</span>' : '' ?></div>
            <div class="ac-mono" style="font-size:20px;font-weight:900;color:var(--gx-gold)"><?= (int) $r['xp'] ?> XP</div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>
<?php echo view('courses/_foot'); ?>
