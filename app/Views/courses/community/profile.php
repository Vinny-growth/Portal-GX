<?php
echo view('courses/_head', ['pageTitle' => $pageTitle, 'totalXp' => $totalXp]);
echo view('courses/community/_styles');
$name = $profile['display_name'] ?? ('Membro #' . $profileUser);
?>
<div class="ac-eyebrow"><a href="<?= site_url('comunidade') ?>" style="color:var(--gx-gold)">← Comunidade</a></div>

<div class="cm-post" style="max-width:760px;display:flex;gap:20px;align-items:center">
    <div class="cm-avatar" style="width:72px;height:72px;font-size:30px"><?= strtoupper(mb_substr((string) $name, 0, 1) ?: '?') ?></div>
    <div style="flex:1">
        <div style="font-size:24px;font-weight:900"><?= esc($name) ?></div>
        <?php if (!empty($profile['bio'])): ?><div style="color:#cdd7e4;margin-top:4px"><?= esc($profile['bio']) ?></div><?php endif; ?>
        <div style="display:flex;gap:24px;margin-top:12px">
            <div><b class="ac-mono" style="font-size:22px;color:var(--gx-gold)"><?= (int) $stats['xp'] ?></b> <span class="cm-post__meta">XP</span></div>
            <div><b class="ac-mono" style="font-size:22px"><?= (int) $stats['posts'] ?></b> <span class="cm-post__meta">Posts</span></div>
            <div><b class="ac-mono" style="font-size:22px"><?= (int) $stats['comments'] ?></b> <span class="cm-post__meta">Comentários</span></div>
        </div>
    </div>
</div>

<?php if (!empty($achievements)): ?>
<div class="ac-eyebrow">Conquistas</div>
<div style="display:flex;gap:10px;flex-wrap:wrap;max-width:760px">
    <?php foreach ($achievements as $a): ?>
        <span class="cm-space-badge" style="padding:8px 12px;border-color:var(--gx-gold);color:var(--gx-gold)" title="<?= esc($a['description'] ?? '', 'attr') ?>"><?= esc($a['icon'] ?? '🏅') ?> <?= esc($a['name']) ?></span>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="ac-eyebrow">Publicações recentes</div>
<div style="max-width:760px">
<?php if (empty($posts)): ?>
    <div class="ac-empty">Nenhuma publicação ainda.</div>
<?php else: ?>
    <?php foreach ($posts as $p): ?>
        <a href="<?= site_url('comunidade/post/' . $p['id']) ?>"><div class="cm-post">
            <?php if (!empty($p['title'])): ?><div class="cm-post__title" style="font-size:16px"><?= esc($p['title']) ?></div><?php endif; ?>
            <div class="cm-post__body"><?= nl2br(esc(mb_strimwidth((string) $p['body'], 0, 180, '…'))) ?></div>
            <div class="cm-post__meta" style="margin-top:10px"><?= date('d/m/Y', strtotime($p['created_at'] ?? 'now')) ?> · 👍 <?= (int) $p['reaction_count'] ?> · 💬 <?= (int) $p['comment_count'] ?></div>
        </div></a>
    <?php endforeach; ?>
<?php endif; ?>
</div>
<?php echo view('courses/_foot'); ?>
