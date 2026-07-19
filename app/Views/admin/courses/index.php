<?= view('admin/courses/_styles'); ?>
<div class="gxc-wrap">
    <?php if (session()->getFlashdata('success')): ?><div class="gxc-flash"><?= esc(session()->getFlashdata('success')); ?></div><?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?><div class="gxc-flash gxc-flash--err"><?= esc(session()->getFlashdata('error')); ?></div><?php endif; ?>

    <div class="gxc-head">
        <div>
            <div class="gxc-eyebrow">Módulo · Cursos</div>
            <h1 class="gxc-title">Gestão de cursos</h1>
        </div>
        <div class="gxc-actions">
            <a class="gxc-btn gxc-btn--ghost" href="<?= adminUrl('cursos/assinaturas'); ?>">Assinaturas</a>
            <a class="gxc-btn gxc-btn--ghost" href="<?= adminUrl('cursos/niveis'); ?>">Níveis de acesso</a>
            <a class="gxc-btn" href="<?= adminUrl('cursos/novo'); ?>">+ Novo curso</a>
        </div>
    </div>

    <?php if (empty($courses)): ?>
        <div class="gxc-empty">Nenhum curso ainda. Crie o primeiro para montar a trilha.</div>
    <?php else: ?>
    <table class="gxc-table">
        <thead>
            <tr>
                <th>Curso</th><th>Categoria</th><th>Aulas</th><th>XP</th><th>Status</th><th>Destaque</th><th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($courses as $c): ?>
            <tr>
                <td>
                    <strong><?= esc($c['title']); ?></strong><br>
                    <span style="color:var(--fg2);font-size:var(--fs-sm);">/curso/<?= esc($c['slug']); ?></span>
                </td>
                <td><?= esc($c['category'] ?: '—'); ?></td>
                <td class="gxc-num"><?= (int) $c['lesson_count']; ?></td>
                <td class="gxc-num"><?= (int) $c['xp_reward']; ?></td>
                <td>
                    <?php if ($c['is_published']): ?><span class="gxc-badge gxc-badge--on">Publicado</span>
                    <?php else: ?><span class="gxc-badge gxc-badge--off">Rascunho</span><?php endif; ?>
                </td>
                <td><?= $c['is_featured'] ? '★' : '—'; ?></td>
                <td>
                    <div class="gxc-actions">
                        <a class="gxc-btn gxc-btn--ghost gxc-btn--sm" href="<?= adminUrl('cursos/' . $c['id'] . '/editar'); ?>">Editar</a>
                        <form method="post" action="<?= adminUrl('cursos/' . $c['id'] . '/publicar'); ?>"><?= csrf_field(); ?>
                            <button class="gxc-btn gxc-btn--ghost gxc-btn--sm" type="submit"><?= $c['is_published'] ? 'Despublicar' : 'Publicar'; ?></button>
                        </form>
                        <form method="post" action="<?= adminUrl('cursos/' . $c['id'] . '/excluir'); ?>" onsubmit="return confirm('Excluir este curso e todas as suas aulas?');"><?= csrf_field(); ?>
                            <button class="gxc-btn gxc-btn--danger gxc-btn--sm" type="submit">Excluir</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
