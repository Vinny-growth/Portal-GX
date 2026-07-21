<?php
echo view('courses/_head', ['pageTitle' => $pageTitle, 'totalXp' => $totalXp, 'unread' => $unread]);
echo view('courses/community/_styles');
$initial = fn($name) => strtoupper(mb_substr((string) $name, 0, 1) ?: '?');
?>
<?php if ($space && !empty($space['cover_image'])): ?>
<div style="margin-top:var(--space-6);height:180px;background:linear-gradient(180deg,rgba(0,13,35,.15),rgba(0,13,35,.78)),url('<?= esc($space['cover_image'], 'attr') ?>');background-size:cover;background-position:center;display:flex;align-items:flex-end;padding:22px;border:1px solid rgba(219,199,162,.15)">
    <div>
        <div style="font-size:28px;font-weight:900;line-height:1"><?= esc($space['icon'] ?? '') ?> <?= esc($space['name']) ?></div>
        <?php if (!empty($space['description'])): ?><div style="color:#cdd7e4;margin-top:6px"><?= esc($space['description']) ?></div><?php endif; ?>
    </div>
</div>
<?php endif; ?>
<div class="ac-eyebrow" style="justify-content:space-between">
    <span style="display:flex;align-items:center;gap:12px"><?= $space ? esc($space['name']) : 'Comunidade' ?></span>
    <a href="<?= site_url('comunidade/ranking') ?>" style="color:var(--gx-gold);font-size:12px">🏆 Ranking</a>
</div>
<?php if (session()->getFlashdata('success')): ?><div class="cm-flash"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>

<div class="cm-stats">
    <div class="cm-stat"><b class="ac-mono"><?= (int) $stats['xp'] ?></b><span>Seu XP</span></div>
    <div class="cm-stat"><b class="ac-mono"><?= (int) $stats['posts'] ?></b><span>Publicações</span></div>
    <div class="cm-stat"><b class="ac-mono"><?= (int) $stats['comments'] ?></b><span>Comentários</span></div>
</div>

<div class="cm-layout">
    <aside class="cm-side">
        <h4>Espaços</h4>
        <a class="cm-space <?= $space === null ? 'on' : '' ?>" href="<?= site_url('comunidade') ?>"><i>🏠</i> Todos</a>
        <?php foreach ($spaces as $s): ?>
            <a class="cm-space <?= ($space['id'] ?? 0) === $s['id'] ? 'on' : '' ?>" href="<?= site_url('comunidade/espaco/' . $s['slug']) ?>">
                <i><?= esc($s['icon'] ?: '#') ?></i> <?= esc($s['name']) ?>
            </a>
        <?php endforeach; ?>
    </aside>

    <div>
        <!-- composer -->
        <form class="cm-composer" method="post" action="<?= site_url('comunidade/post/criar') ?>">
            <?= csrf_field() ?>
            <select name="space_id" required>
                <option value="">— escolha um espaço —</option>
                <?php foreach ($spaces as $s): ?><option value="<?= (int) $s['id'] ?>" <?= ($space['id'] ?? 0) === $s['id'] ? 'selected' : '' ?>><?= esc($s['name']) ?></option><?php endforeach; ?>
            </select>
            <input name="title" placeholder="Título (opcional)">
            <textarea name="body" placeholder="Compartilhe algo com a comunidade…" required></textarea>
            <button class="ac-btn" type="submit" style="justify-content:center">Publicar · +5 XP</button>
        </form>

        <?php if (empty($posts)): ?>
            <div class="ac-empty">Nenhuma publicação ainda neste espaço. Seja o primeiro!</div>
        <?php else: ?>
            <?php foreach ($posts as $p): $isReacted = in_array((int) $p['id'], $reacted, true); ?>
            <article class="cm-post">
                <div class="cm-post__head">
                    <a class="cm-avatar" href="<?= site_url('comunidade/membro/' . $p['user_id']) ?>"><?= $initial($p['author']) ?></a>
                    <div style="flex:1">
                        <a class="cm-post__who" href="<?= site_url('comunidade/membro/' . $p['user_id']) ?>"><?= esc($p['author'] ?: 'Membro') ?></a>
                        <div class="cm-post__meta"><?= date('d/m/Y H:i', strtotime($p['created_at'] ?? 'now')) ?> · <span class="cm-space-badge"><?= esc($p['space_name'] ?? '') ?></span></div>
                    </div>
                    <?php if (!empty($p['is_pinned'])): ?><span class="cm-pin">📌 Fixado</span><?php endif; ?>
                </div>
                <?php if (!empty($p['title'])): ?><a href="<?= site_url('comunidade/post/' . $p['id']) ?>"><div class="cm-post__title"><?= esc($p['title']) ?></div></a><?php endif; ?>
                <div class="cm-post__body"><?= nl2br(esc(mb_strimwidth((string) $p['body'], 0, 320, '…'))) ?></div>
                <div class="cm-post__foot">
                    <button class="cm-act <?= $isReacted ? 'on' : '' ?>" data-react data-type="post" data-id="<?= (int) $p['id'] ?>">👍 <b><?= (int) $p['reaction_count'] ?></b></button>
                    <a class="cm-act" href="<?= site_url('comunidade/post/' . $p['id']) ?>">💬 <b><?= (int) $p['comment_count'] ?></b></a>
                </div>
            </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php echo view('courses/community/_reactions_js'); ?>
<?php echo view('courses/_foot'); ?>
