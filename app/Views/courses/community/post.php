<?php
echo view('courses/_head', ['pageTitle' => $pageTitle, 'totalXp' => $totalXp, 'unread' => $unread]);
echo view('courses/community/_styles');
$initial = fn($name) => strtoupper(mb_substr((string) $name, 0, 1) ?: '?');
?>
<div class="ac-eyebrow"><a href="<?= site_url('comunidade' . (!empty($post['space_slug']) ? '/espaco/' . $post['space_slug'] : '')) ?>" style="color:var(--gx-gold)">← <?= esc($post['space_name'] ?? 'Comunidade') ?></a></div>
<?php if (session()->getFlashdata('success')): ?><div class="cm-flash"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>

<article class="cm-post" style="max-width:760px">
    <div class="cm-post__head">
        <a class="cm-avatar" href="<?= site_url('comunidade/membro/' . $post['user_id']) ?>"><?= $initial($post['author']) ?></a>
        <div style="flex:1">
            <a class="cm-post__who" href="<?= site_url('comunidade/membro/' . $post['user_id']) ?>"><?= esc($post['author'] ?: 'Membro') ?></a>
            <div class="cm-post__meta"><?= date('d/m/Y H:i', strtotime($post['created_at'] ?? 'now')) ?> · <span class="cm-space-badge"><?= esc($post['space_name'] ?? '') ?></span></div>
        </div>
        <?php if (!empty($post['is_pinned'])): ?><span class="cm-pin">📌 Fixado</span><?php endif; ?>
    </div>
    <?php if (!empty($post['title'])): ?><div class="cm-post__title"><?= esc($post['title']) ?></div><?php endif; ?>
    <div class="cm-post__body"><?= nl2br(esc((string) $post['body'])) ?></div>
    <div class="cm-post__foot">
        <button class="cm-act <?= $reacted ? 'on' : '' ?>" data-react data-type="post" data-id="<?= (int) $post['id'] ?>">👍 <b><?= (int) $post['reaction_count'] ?></b></button>
        <span class="cm-act">💬 <b><?= (int) $post['comment_count'] ?></b></span>
    </div>
</article>

<div class="ac-eyebrow">Comentários</div>
<div class="cm-post" style="max-width:760px">
    <form method="post" action="<?= site_url('comunidade/comentar') ?>" style="margin-bottom:var(--space-5)">
        <?= csrf_field() ?>
        <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
        <textarea name="body" required placeholder="Escreva um comentário… (+2 XP)" style="width:100%;background:#06182f;border:1px solid rgba(219,199,162,.22);color:#eef2f7;padding:11px 13px;min-height:70px;font-family:var(--font-sans)"></textarea>
        <div style="margin-top:10px"><button class="ac-btn" type="submit">Comentar</button></div>
    </form>

    <?php if (empty($comments)): ?>
        <p style="color:#8ea0b6">Sem comentários ainda. Comece a conversa!</p>
    <?php else: ?>
        <?php foreach ($comments as $c): ?>
            <div class="cm-comment">
                <a class="cm-avatar" style="width:34px;height:34px" href="<?= site_url('comunidade/membro/' . $c['user_id']) ?>"><?= $initial($c['author']) ?></a>
                <div class="cm-comment__b">
                    <div><strong><?= esc($c['author'] ?: 'Membro') ?></strong> <span class="cm-post__meta"><?= date('d/m/Y H:i', strtotime($c['created_at'] ?? 'now')) ?></span></div>
                    <div style="color:#cdd7e4;line-height:1.55;margin-top:4px;white-space:pre-wrap"><?= nl2br(esc((string) $c['body'])) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php echo view('courses/community/_reactions_js'); ?>
<?php echo view('courses/_foot'); ?>
