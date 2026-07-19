<?= view('admin/courses/_styles'); ?>
<div class="gxc-wrap">
    <?php if (session()->getFlashdata('success')): ?><div class="gxc-flash"><?= esc(session()->getFlashdata('success')); ?></div><?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?><div class="gxc-flash gxc-flash--err"><?= esc(session()->getFlashdata('error')); ?></div><?php endif; ?>

    <div class="gxc-head">
        <div><div class="gxc-eyebrow">Módulo · Cursos</div><h1 class="gxc-title">Comunidade</h1></div>
        <a class="gxc-btn gxc-btn--ghost" href="<?= adminUrl('cursos'); ?>">← Voltar</a>
    </div>

    <div class="gxc-card">
        <div class="gxc-card__eyebrow">Espaços</div>
        <?php if (!empty($spaces)): ?>
        <table class="gxc-table" style="margin-bottom:var(--space-5)">
            <thead><tr><th>Espaço</th><th>Slug</th><th>Ordem</th><th>Ativo</th></tr></thead>
            <tbody>
            <?php foreach ($spaces as $s): ?>
                <tr><td><?= esc($s['icon'] ?: '') ?> <strong><?= esc($s['name']); ?></strong></td><td class="gxc-muted"><?= esc($s['slug']); ?></td><td class="gxc-num"><?= (int) $s['sort']; ?></td><td><?= $s['is_active'] ? '<span class="gxc-badge gxc-badge--on">Sim</span>' : '<span class="gxc-badge gxc-badge--off">Não</span>'; ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <form method="post" action="<?= adminUrl('cursos/comunidade/espaco/salvar'); ?>">
            <?= csrf_field(); ?>
            <div class="gxc-grid">
                <div class="gxc-field"><label class="gxc-label">Nome do espaço</label><input class="gxc-input" name="name" placeholder="Ex.: Apresente-se" required></div>
                <div class="gxc-field"><label class="gxc-label">Ícone (emoji)</label><input class="gxc-input" name="icon" placeholder="👋"></div>
                <div class="gxc-field"><label class="gxc-label">Ordem</label><input class="gxc-input" type="number" name="sort" value="0"></div>
                <div class="gxc-field"><label class="gxc-check" style="margin-top:24px"><input type="checkbox" name="is_active" value="1" checked> Ativo</label></div>
                <div class="gxc-field gxc-field--full"><label class="gxc-label">Descrição</label><input class="gxc-input" name="description"></div>
            </div>
            <div style="margin-top:var(--space-3)"><button class="gxc-btn" type="submit">+ Criar espaço</button></div>
        </form>
    </div>

    <div class="gxc-card">
        <div class="gxc-card__eyebrow">Publicações recentes · moderação</div>
        <?php if (empty($posts)): ?>
            <div class="gxc-empty">Nenhuma publicação.</div>
        <?php else: ?>
        <table class="gxc-table">
            <thead><tr><th>Autor</th><th>Espaço</th><th>Publicação</th><th>👍</th><th>💬</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($posts as $p): ?>
                <tr>
                    <td><?= esc($p['author'] ?? '#' . $p['user_id']); ?></td>
                    <td class="gxc-muted"><?= esc($p['space_name'] ?? ''); ?></td>
                    <td><?= esc(mb_strimwidth((string) ($p['title'] ?: $p['body']), 0, 60, '…')); ?> <?= !empty($p['is_pinned']) ? '<span class="gxc-badge gxc-badge--gold">Fixado</span>' : '' ?></td>
                    <td class="gxc-num"><?= (int) $p['reaction_count']; ?></td>
                    <td class="gxc-num"><?= (int) $p['comment_count']; ?></td>
                    <td>
                        <div class="gxc-actions">
                            <form method="post" action="<?= adminUrl('cursos/comunidade/post/' . $p['id'] . '/fixar'); ?>"><?= csrf_field(); ?><button class="gxc-btn gxc-btn--ghost gxc-btn--sm" type="submit"><?= !empty($p['is_pinned']) ? 'Desafixar' : 'Fixar'; ?></button></form>
                            <form method="post" action="<?= adminUrl('cursos/comunidade/post/' . $p['id'] . '/remover'); ?>" onsubmit="return confirm('Remover esta publicação?');"><?= csrf_field(); ?><button class="gxc-btn gxc-btn--danger gxc-btn--sm" type="submit">Remover</button></form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
