<?php 
    $agg = $agg ?? [];
    $fi = $fi ?? [];
    $expected = (float)($expected ?? 0.02);
    $nw = (float)($agg['net_worth'] ?? 0);
    $income = (float)($agg['income'] ?? 0);
    $expense = (float)($agg['expense'] ?? 0);
    $savings = (float)($agg['savings'] ?? 0);
    $nwNeeded = (float)($fi['nw_needed'] ?? 0);
    $months = $fi['months_to_fi'] ?? null;
    $yrs = is_null($months) ? null : intdiv((int)$months, 12);
    $rem = is_null($months) ? null : ((int)$months % 12);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Resumo'); ?></title>
    <style>
        /* Margens e fonte padrão (compatível com Dompdf) */
        @page { margin: 18mm 16mm; }
        body { font-family: Arial, Helvetica, sans-serif; color:#1f2937; font-size: 12px; }

        /* Paleta (GX): primário e acentos */
        .c-primary { color:#3366ff; }
        .bg-primary { background:#3366ff; color:#fff; }
        .border-primary { border-color:#3366ff; }

        /* Cabeçalho premium */
        .brand { width:100%; }
        .brand-row { width:100%; }
        .brand-left { display:inline-block; width:70%; vertical-align:middle; }
        .brand-right { display:inline-block; width:29%; text-align:right; vertical-align:middle; }
        .brand .logo { height: 26px; }
        .brand .title { font-size: 18px; font-weight: 700; color:#111827; }
        .brandbar { height: 4px; background:#3366ff; border-radius: 3px; margin: 8px 0 0 0; }
        .meta { font-size: 11px; color:#6b7280; margin-top:4px; }

        /* Seções e títulos */
        .section { margin-top: 16px; }
        .h2 { font-size: 13px; font-weight: 700; margin: 0 0 8px 0; color:#111827; text-transform: uppercase; letter-spacing: .02em; }

        /* KPIs como blocos (sem flex para Dompdf) */
        .kpis { margin:0 -6px; }
        .kpi { display:inline-block; width:48%; margin:0 6px 10px 6px; border:1px solid #e5e7eb; border-radius:8px; padding:10px; }
        .kpi .label { font-size: 10.5px; color:#6b7280; }
        .kpi .val { font-size: 16px; font-weight: 700; color:#111827; }

        /* Duas colunas com inline-block (compatível) */
        .grid2 { margin: 0 -6px; }
        .card { display:inline-block; width:49%; margin:0 6px; border:1px solid #e5e7eb; border-radius:8px; padding:12px; vertical-align: top; }

        /* Tabelas */
        .table { width:100%; border-collapse: collapse; font-size: 12px; }
        .table th { background:#f8fafc; font-weight:700; color:#111827; }
        .table th, .table td { border-bottom:1px solid #f3f4f6; padding:6px 6px; text-align:left; }
        .table .strong { font-weight:700; }

        /* Outros */
        .muted { color:#6b7280; }
        .pill { display:inline-block; padding:2px 8px; background:#eef2ff; color:#2536a3; border-radius:999px; font-size:10.5px; font-weight:700; }
        .callout { border-left:3px solid #3366ff; background:#f3f6ff; padding:8px 10px; border-radius:6px; font-size:11.5px; }
        .foot { margin-top: 18px; font-size: 10.5px; color:#9ca3af; text-align:right; }
        .disclaimer { margin-top:10px; font-size: 10px; color:#9ca3af; }
    </style>
    <style media="print">
        .no-print { display:none; }
    </style>
</head>
<body>
    <div class="brand">
        <div class="brand-row">
            <div class="brand-left"><div class="title">GX Capital · Resumo Financeiro</div></div>
            <div class="brand-right"><?php if (!empty($logo)): ?><img class="logo" src="<?= esc($logo); ?>" alt="Logo"><?php endif; ?></div>
        </div>
        <div class="brandbar"></div>
    </div>
    <div class="meta">Gerado em <?= esc($generated_at ?? date('d/m/Y H:i')); ?></div>

    <div class="section">
        <div class="h2">Visão Geral</div>
        <div class="kpis">
            <div class="kpi"><div class="label">Patrimônio Líquido</div><div class="val">R$ <?= number_format($nw, 2, ',', '.'); ?></div></div>
            <div class="kpi"><div class="label">Renda Mensal</div><div class="val">R$ <?= number_format($income, 2, ',', '.'); ?></div></div>
            <div class="kpi"><div class="label">Despesas Mensais</div><div class="val">R$ <?= number_format($expense, 2, ',', '.'); ?></div></div>
            <div class="kpi"><div class="label">Poupança Mensal</div><div class="val">R$ <?= number_format($savings, 2, ',', '.'); ?></div></div>
        </div>
    </div>

    <div class="section grid2">
        <div class="card">
            <div class="h2">Fluxo de Caixa</div>
            <table class="table">
                <tr><th>Item</th><th>Valor</th></tr>
                <tr><td>Renda mensal</td><td>R$ <?= number_format($income, 2, ',', '.'); ?></td></tr>
                <tr><td>Despesas mensais</td><td>R$ <?= number_format($expense, 2, ',', '.'); ?></td></tr>
                <tr><td class="strong">Poupança potencial</td><td class="strong">R$ <?= number_format($savings, 2, ',', '.'); ?></td></tr>
            </table>
        </div>
        <div class="card">
            <div class="h2">Independência Financeira</div>
            <table class="table">
                <tr><td>Retorno real estimado</td><td><?= number_format($expected*100, 2, ',', '.'); ?>% a.a.</td></tr>
                <tr><td>Patrimônio necessário (estim.)</td><td>R$ <?= number_format($nwNeeded, 2, ',', '.'); ?></td></tr>
                <tr>
                    <td>Tempo estimado até FI</td>
                    <td>
                        <?php if (is_null($months)): ?>
                            <span class="pill">Indeterminado</span>
                        <?php elseif ((int)$months === 0): ?>
                            <span class="pill">Já atingido</span>
                        <?php else: ?>
                            <?= (int)$yrs; ?> anos e <?= (int)$rem; ?> meses
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            <div class="callout" style="margin-top:8px;">
                <strong>Nota:</strong> projeções em termos reais (descontada a inflação), considerando aportes constantes e retorno esperado.
            </div>
        </div>
    </div>

    <div class="section">
        <div class="h2">Patrimônio e Alocação</div>
        <table class="table">
            <tr><th>Componente</th><th>Valor</th></tr>
            <tr><td>Ativos financeiros</td><td>R$ <?= number_format((float)($agg['assets_financial'] ?? 0), 2, ',', '.'); ?></td></tr>
            <tr><td>Imóveis</td><td>R$ <?= number_format((float)($agg['assets_realestate'] ?? 0), 2, ',', '.'); ?></td></tr>
            <tr><td>Passivos</td><td>R$ <?= number_format((float)($agg['liabilities'] ?? 0), 2, ',', '.'); ?></td></tr>
            <tr><td class="strong">Patrimônio líquido</td><td class="strong">R$ <?= number_format($nw, 2, ',', '.'); ?></td></tr>
        </table>
        <?php $alloc = $agg['allocation'] ?? []; if (!empty($alloc) && is_array($alloc)): ?>
            <div class="muted" style="margin-top:6px;">Alocação aproximada (por classe):
                <?php $total = array_sum($alloc); $out = []; foreach ($alloc as $k=>$v){ $pct = $total>0 ? (100*$v/$total) : 0; $out[] = esc(ucfirst($k)).' '.number_format($pct,1,',','.').'%'; } echo implode(' • ', $out); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="disclaimer">Este material tem caráter informativo e não constitui recomendação de investimento. Projeções são estimativas sujeitas a variações de mercado.</div>
    <div class="foot">© GX Capital · Documento gerado automaticamente</div>

</body>
</html>
