<?php
echo view('courses/_head', ['pageTitle' => $pageTitle, 'totalXp' => null]);
$fmtMoney = function ($v, $cur) {
    if (function_exists('brandMoney')) { return brandMoney($v); }
    $sym = ['BRL' => 'R$', 'MXN' => '$', 'USD' => 'US$', 'EUR' => '€'][$cur] ?? ($cur . ' ');
    return $sym . ' ' . number_format((float) $v, 2, ',', '.');
};
$srcLabel = ['paid' => 'Assinatura paga', 'client_comp' => 'Cortesia (cliente GX)', 'manual' => 'Concessão manual'];
$statusLabel = ['active' => 'Ativa', 'grace' => 'Em carência', 'expired' => 'Expirada', 'canceled' => 'Cancelada'];
?>
<style>
    .sb-grid{display:grid;grid-template-columns:1fr 1fr;gap:var(--space-6);align-items:start}
    .sb-card{background:#0a2547;border:1px solid rgba(219,199,162,.15);padding:var(--space-8)}
    .sb-card--gold{border-color:var(--gx-gold);background:linear-gradient(160deg,#0a2547,#0c3163)}
    .sb-price{font-family:var(--font-mono);font-size:44px;font-weight:900;color:var(--gx-gold);line-height:1}
    .sb-status{display:inline-block;padding:5px 12px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:var(--ls-wide)}
    .sb-status--on{background:var(--gx-success);color:#fff}
    .sb-status--off{background:rgba(219,199,162,.2);color:var(--gx-secondary-light)}
    .sb-list{list-style:none;padding:0;margin:16px 0 0;font-size:14px;color:#cdd7e4}
    .sb-list li{padding:8px 0;border-bottom:1px solid rgba(219,199,162,.1);display:flex;justify-content:space-between;gap:12px}
    .sb-flash{padding:12px 16px;margin:var(--space-4) 0;background:rgba(22,163,74,.15);border-left:4px solid var(--gx-success);color:#d7ffe4;font-weight:600}
    .sb-field{display:flex;flex-direction:column;gap:6px;margin-bottom:14px}
    .sb-field label{font-size:11px;text-transform:uppercase;letter-spacing:var(--ls-wide);color:#9fb0c4;font-weight:700}
    .sb-field input,.sb-field select{padding:11px 13px;background:#06182f;border:1px solid rgba(219,199,162,.25);color:#eef2f7;font-family:var(--font-sans)}
    .sb-feat{list-style:none;padding:0;margin:14px 0 22px;font-size:14px;color:#cdd7e4}
    .sb-feat li{padding:6px 0}.sb-feat li::before{content:'✓ ';color:var(--gx-gold);font-weight:900}
    @media(max-width:760px){.sb-grid{grid-template-columns:1fr}}
</style>

<div class="ac-eyebrow">Minha assinatura</div>
<?php if (session()->getFlashdata('success')): ?><div class="sb-flash"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>

<div class="sb-grid">
    <!-- status atual -->
    <div class="sb-card <?= $isActive ? 'sb-card--gold' : '' ?>">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:16px">
            <strong style="font-size:18px">Status do acesso</strong>
            <span class="sb-status <?= $isActive ? 'sb-status--on' : 'sb-status--off' ?>"><?= $isActive ? 'Acesso completo' : 'Sem acesso pago' ?></span>
        </div>
        <?php if (!empty($membership)): ?>
            <ul class="sb-list">
                <li><span>Situação</span><strong><?= esc($statusLabel[$membership['status']] ?? $membership['status']) ?></strong></li>
                <li><span>Origem</span><strong><?= esc($srcLabel[$membership['source']] ?? $membership['source']) ?></strong></li>
                <?php if (!empty($membership['client_active'])): ?><li><span>Cliente GX</span><strong style="color:var(--gx-gold)">Ativo (cortesia)</strong></li><?php endif; ?>
                <?php if (!empty($membership['paid_until'])): ?><li><span>Pago até</span><strong class="ac-mono"><?= date('d/m/Y', strtotime($membership['paid_until'])) ?></strong></li><?php endif; ?>
                <?php if (!empty($membership['access_until']) && empty($membership['client_active'])): ?><li><span>Acesso até</span><strong class="ac-mono"><?= date('d/m/Y', strtotime($membership['access_until'])) ?></strong></li><?php endif; ?>
            </ul>
        <?php else: ?>
            <p style="color:#9fb0c4;line-height:1.6">Você ainda não possui uma assinatura. Ative para desbloquear todos os cursos e a comunidade.</p>
        <?php endif; ?>
    </div>

    <!-- plano / assinar -->
    <?php if (!$isActive): ?>
    <div class="sb-card sb-card--gold">
        <div style="text-transform:uppercase;letter-spacing:var(--ls-widest);font-size:11px;font-weight:800;color:var(--gx-gold)"><?= esc($plan['title']) ?></div>
        <div style="margin:14px 0 4px"><span class="sb-price"><?= $fmtMoney($plan['amount'], $plan['currency']) ?></span> <span style="color:#9fb0c4">/ano</span></div>
        <ul class="sb-feat">
            <li>Acesso a todos os cursos e trilhas</li>
            <li>Certificados de conclusão</li>
            <li>Jornada gamificada com XP e conquistas</li>
            <li>Comunidade (em breve)</li>
        </ul>
        <form method="post" action="<?= site_url('assinatura/iniciar') ?>">
            <?= csrf_field() ?>
            <div class="sb-field">
                <label>Documento (CPF/CURP)</label>
                <input name="document" inputmode="numeric" placeholder="Somente números" required>
            </div>
            <div class="sb-field">
                <label>Tipo</label>
                <select name="doc_type"><option value="cpf">CPF (Brasil)</option><option value="curp">CURP (México)</option><option value="rfc">RFC (México)</option></select>
            </div>
            <button class="ac-btn" type="submit" style="width:100%;justify-content:center">Assinar via <?= esc(ucfirst($gateway)) ?> →</button>
            <p style="font-size:11px;color:#7d8da5;margin:12px 0 0;text-transform:uppercase;letter-spacing:var(--ls-wide)">Cobrança anual · cancele quando quiser</p>
        </form>
    </div>
    <?php else: ?>
    <div class="sb-card">
        <strong style="font-size:18px;color:var(--gx-gold)">🎉 Tudo liberado</strong>
        <p style="color:#cdd7e4;line-height:1.6;margin-top:10px">Sua assinatura está ativa. Aproveite todos os cursos, trilhas e certificados.</p>
        <a class="ac-btn ac-btn--ghost" href="<?= site_url('cursos') ?>">Explorar cursos →</a>
    </div>
    <?php endif; ?>
</div>

<?php if (!empty($payments)): ?>
<div class="ac-eyebrow">Histórico de pagamentos</div>
<div class="sb-card">
    <ul class="sb-list">
        <?php foreach ($payments as $p): ?>
            <li>
                <span><?= date('d/m/Y', strtotime($p['created_at'] ?? 'now')) ?> · <?= esc(ucfirst($p['gateway'])) ?></span>
                <strong class="ac-mono"><?= $fmtMoney($p['amount'], $p['currency']) ?> · <?= esc($p['status']) ?></strong>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php echo view('courses/_foot'); ?>
