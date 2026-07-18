<?= view('admin/courses/_styles'); ?>
<div class="gxc-wrap">
    <?php if (session()->getFlashdata('success')): ?><div class="gxc-flash"><?= esc(session()->getFlashdata('success')); ?></div><?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?><div class="gxc-flash gxc-flash--err"><?= esc(session()->getFlashdata('error')); ?></div><?php endif; ?>

    <div class="gxc-head">
        <div>
            <div class="gxc-eyebrow">Módulo · Cursos</div>
            <h1 class="gxc-title">Níveis de acesso</h1>
        </div>
        <a class="gxc-btn gxc-btn--ghost" href="<?= adminUrl('cursos'); ?>">← Voltar</a>
    </div>

    <p class="gxc-muted" style="margin-bottom:var(--space-5);max-width:70ch;">Níveis de acesso liberam conteúdo por <strong>grant manual</strong> (rank maior = mais acesso). O conteúdo pago via <strong>membership anual</strong> chega na Fase 4b — aqui o acesso vem de nível manual ou de conteúdo livre.</p>

    <div class="gxc-card">
        <div class="gxc-card__eyebrow">Níveis cadastrados</div>
        <?php if (empty($levels)): ?>
            <div class="gxc-empty">Nenhum nível ainda. Crie o primeiro (ex.: "Nível 1", rank 1).</div>
        <?php else: ?>
        <table class="gxc-table">
            <thead><tr><th>Nível</th><th>Slug</th><th>Rank</th><th>Descrição</th></tr></thead>
            <tbody>
            <?php foreach ($levels as $L): ?>
                <tr>
                    <td><strong><?= esc($L['name']); ?></strong></td>
                    <td class="gxc-muted"><?= esc($L['slug']); ?></td>
                    <td class="gxc-num"><?= (int) $L['rank']; ?></td>
                    <td><?= esc($L['description'] ?: '—'); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <div class="gxc-grid" style="align-items:start;">
        <form class="gxc-card" method="post" action="<?= adminUrl('cursos/niveis/salvar'); ?>">
            <?= csrf_field(); ?>
            <div class="gxc-card__eyebrow">Novo nível</div>
            <div class="gxc-field"><label class="gxc-label">Nome</label><input class="gxc-input" name="name" placeholder="Ex.: Nível 1" required></div>
            <div class="gxc-field"><label class="gxc-label">Rank (peso)</label><input class="gxc-input" type="number" name="rank" value="1"></div>
            <div class="gxc-field"><label class="gxc-label">Descrição</label><input class="gxc-input" name="description"></div>
            <div style="margin-top:var(--space-4);"><button class="gxc-btn" type="submit">Salvar nível</button></div>
        </form>

        <form class="gxc-card" method="post" action="<?= adminUrl('cursos/acesso/conceder'); ?>">
            <?= csrf_field(); ?>
            <div class="gxc-card__eyebrow">Conceder acesso a um aluno</div>
            <div class="gxc-field"><label class="gxc-label">ID do usuário</label><input class="gxc-input" type="number" name="user_id" placeholder="ID do usuário" required></div>
            <div class="gxc-field"><label class="gxc-label">Nível</label>
                <select class="gxc-select" name="access_level_id" required>
                    <option value="">— selecione —</option>
                    <?php foreach ($levels as $L): ?><option value="<?= (int) $L['id']; ?>"><?= esc($L['name']); ?> (rank <?= (int) $L['rank']; ?>)</option><?php endforeach; ?>
                </select>
            </div>
            <div style="margin-top:var(--space-4);"><button class="gxc-btn gxc-btn--gold" type="submit">Conceder acesso</button></div>
        </form>
    </div>
</div>
