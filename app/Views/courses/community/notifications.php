<?php
echo view('courses/_head', ['pageTitle' => $pageTitle, 'totalXp' => $totalXp]);
echo view('courses/community/_styles');
?>
<div class="ac-eyebrow">🔔 Notificações</div>
<div style="max-width:640px">
<?php if (empty($notifications)): ?>
    <div class="ac-empty">Nenhuma notificação por aqui.</div>
<?php else: ?>
    <?php foreach ($notifications as $n): ?>
        <a href="<?= site_url($n['link'] ?? 'comunidade') ?>">
            <div class="cm-post" style="padding:14px 18px;<?= empty($n['is_read']) ? 'border-left:3px solid var(--gx-gold);' : '' ?>">
                <div><strong><?= esc($n['actor'] ?: 'Alguém') ?></strong> <?= esc($n['message'] ?? '') ?></div>
                <div class="cm-post__meta" style="margin-top:4px"><?= date('d/m/Y H:i', strtotime($n['created_at'] ?? 'now')) ?></div>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>
</div>
<?php echo view('courses/_foot'); ?>
