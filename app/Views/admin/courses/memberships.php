<?= view('admin/courses/_styles'); ?>
<div class="gxc-wrap">
    <?php if (session()->getFlashdata('success')): ?><div class="gxc-flash"><?= esc(session()->getFlashdata('success')); ?></div><?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?><div class="gxc-flash gxc-flash--err"><?= esc(session()->getFlashdata('error')); ?></div><?php endif; ?>

    <div class="gxc-head">
        <div>
            <div class="gxc-eyebrow">Módulo · Cursos</div>
            <h1 class="gxc-title">Assinaturas</h1>
        </div>
        <a class="gxc-btn gxc-btn--ghost" href="<?= adminUrl('cursos'); ?>">← Voltar</a>
    </div>

    <p class="gxc-muted" style="margin-bottom:var(--space-5);max-width:74ch;">Um membership único (acesso completo) por pessoa, casado por <strong>documento nacional</strong>. Origem: <strong>paga</strong> (assinatura anual via gateway), <strong>cortesia</strong> (cliente GX ativo, via webhook do CRM) ou <strong>manual</strong> (concessão abaixo). A regra de corte ao cancelar cliente é aplicada automaticamente.</p>

    <div class="gxc-card">
        <div class="gxc-card__eyebrow">Memberships</div>
        <?php if (empty($memberships)): ?>
            <div class="gxc-empty">Nenhuma assinatura ainda.</div>
        <?php else: ?>
        <table class="gxc-table">
            <thead><tr><th>Documento</th><th>Usuário</th><th>Origem</th><th>Cliente GX</th><th>Acesso</th><th>Pago até</th><th>Status</th></tr></thead>
            <tbody>
            <?php $src = ['paid' => 'Paga', 'client_comp' => 'Cortesia', 'manual' => 'Manual']; ?>
            <?php foreach ($memberships as $m): ?>
                <tr>
                    <td class="gxc-num"><strong><?= esc($m['document']); ?></strong> <span class="gxc-muted"><?= esc($m['doc_type']); ?></span></td>
                    <td class="gxc-num"><?= $m['user_id'] ? '#' . (int) $m['user_id'] : '—'; ?></td>
                    <td><?= esc($src[$m['source']] ?? $m['source']); ?></td>
                    <td><?= !empty($m['client_active']) ? '<span class="gxc-badge gxc-badge--gold">Ativo</span>' : '—'; ?></td>
                    <td><?php if (!empty($m['_active'])): ?><span class="gxc-badge gxc-badge--on">Liberado</span><?php else: ?><span class="gxc-badge gxc-badge--off">Sem acesso</span><?php endif; ?></td>
                    <td class="gxc-num"><?= !empty($m['paid_until']) ? date('d/m/Y', strtotime($m['paid_until'])) : '—'; ?></td>
                    <td><?= esc($m['status']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <form class="gxc-card" method="post" action="<?= adminUrl('cursos/assinaturas/conceder'); ?>" style="max-width:520px">
        <?= csrf_field(); ?>
        <div class="gxc-card__eyebrow">Conceder membership manual</div>
        <div class="gxc-field"><label class="gxc-label">Documento (CPF/CURP)</label><input class="gxc-input" name="document" placeholder="Somente números" required></div>
        <div class="gxc-grid">
            <div class="gxc-field"><label class="gxc-label">Tipo</label>
                <select class="gxc-select" name="doc_type"><option value="cpf">CPF</option><option value="curp">CURP</option><option value="rfc">RFC</option></select>
            </div>
            <div class="gxc-field"><label class="gxc-label">Duração (meses)</label><input class="gxc-input" type="number" name="months" value="12"></div>
        </div>
        <div class="gxc-field"><label class="gxc-label">ID do usuário (opcional)</label><input class="gxc-input" type="number" name="user_id" placeholder="vincula o membership ao usuário"></div>
        <div style="margin-top:var(--space-4);"><button class="gxc-btn gxc-btn--gold" type="submit">Conceder</button></div>
    </form>
</div>
