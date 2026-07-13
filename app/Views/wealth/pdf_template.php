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
<html lang="<?= brandLocaleFull(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? lang('Wealth.pdf_title')); ?></title>
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
            <div class="brand-left"><div class="title"><?= brandLang('Wealth.pdf_brand_title'); ?></div></div>
            <div class="brand-right"><?php if (!empty($logo)): ?><img class="logo" src="<?= esc($logo); ?>" alt="Logo"><?php endif; ?></div>
        </div>
        <div class="brandbar"></div>
    </div>
    <div class="meta"><?= lang('Wealth.pdf_gerado'); ?> <?= esc($generated_at ?? date('d/m/Y H:i')); ?></div>

    <div class="section">
        <div class="h2"><?= lang('Wealth.pdf_visao'); ?></div>
        <div class="kpis">
            <div class="kpi"><div class="label"><?= lang('Wealth.pdf_patr_liq'); ?></div><div class="val"><?= brandMoney($nw, 2); ?></div></div>
            <div class="kpi"><div class="label"><?= lang('Wealth.pdf_renda'); ?></div><div class="val"><?= brandMoney($income, 2); ?></div></div>
            <div class="kpi"><div class="label"><?= lang('Wealth.pdf_despesas'); ?></div><div class="val"><?= brandMoney($expense, 2); ?></div></div>
            <div class="kpi"><div class="label"><?= lang('Wealth.pdf_poupanca'); ?></div><div class="val"><?= brandMoney($savings, 2); ?></div></div>
        </div>
    </div>

    <div class="section grid2">
        <div class="card">
            <div class="h2"><?= lang('Wealth.pdf_cashflow'); ?></div>
            <table class="table">
                <tr><th><?= lang('Wealth.pdf_th_item'); ?></th><th><?= lang('Wealth.pdf_th_valor'); ?></th></tr>
                <tr><td><?= lang('Wealth.pdf_renda2'); ?></td><td><?= brandMoney($income, 2); ?></td></tr>
                <tr><td><?= lang('Wealth.pdf_despesas2'); ?></td><td><?= brandMoney($expense, 2); ?></td></tr>
                <tr><td class="strong"><?= lang('Wealth.pdf_poupanca2'); ?></td><td class="strong"><?= brandMoney($savings, 2); ?></td></tr>
            </table>
        </div>
        <div class="card">
            <div class="h2"><?= lang('Wealth.pdf_fi'); ?></div>
            <table class="table">
                <tr><td><?= lang('Wealth.pdf_ret'); ?></td><td><?= brandNumberFormat($expected*100, 2); ?><?= lang('Wealth.pdf_aa'); ?></td></tr>
                <tr><td><?= lang('Wealth.pdf_nw_needed'); ?></td><td><?= brandMoney($nwNeeded, 2); ?></td></tr>
                <tr>
                    <td><?= lang('Wealth.pdf_tempo'); ?></td>
                    <td>
                        <?php if (is_null($months)): ?>
                            <span class="pill"><?= lang('Wealth.pdf_indet'); ?></span>
                        <?php elseif ((int)$months === 0): ?>
                            <span class="pill"><?= lang('Wealth.pdf_atingido'); ?></span>
                        <?php else: ?>
                            <?= (int)$yrs; ?> <?= lang('Wealth.pdf_anos_e'); ?> <?= (int)$rem; ?> <?= lang('Wealth.pdf_meses'); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            <div class="callout" style="margin-top:8px;">
                <strong><?= lang('Wealth.pdf_nota'); ?></strong> <?= lang('Wealth.pdf_nota_text'); ?>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="h2"><?= lang('Wealth.pdf_patr_aloc'); ?></div>
        <table class="table">
            <tr><th><?= lang('Wealth.pdf_th_comp'); ?></th><th><?= lang('Wealth.pdf_th_valor'); ?></th></tr>
            <tr><td><?= lang('Wealth.pdf_ativos'); ?></td><td><?= brandMoney((float)($agg['assets_financial'] ?? 0), 2); ?></td></tr>
            <tr><td><?= lang('Wealth.pdf_imoveis'); ?></td><td><?= brandMoney((float)($agg['assets_realestate'] ?? 0), 2); ?></td></tr>
            <tr><td><?= lang('Wealth.pdf_passivos'); ?></td><td><?= brandMoney((float)($agg['liabilities'] ?? 0), 2); ?></td></tr>
            <tr><td class="strong"><?= lang('Wealth.pdf_patr_liq2'); ?></td><td class="strong"><?= brandMoney($nw, 2); ?></td></tr>
        </table>
        <?php $alloc = $agg['allocation'] ?? []; if (!empty($alloc) && is_array($alloc)): ?>
            <div class="muted" style="margin-top:6px;"><?= lang('Wealth.pdf_aloc_aprox'); ?>
                <?php $total = array_sum($alloc); $out = []; foreach ($alloc as $k=>$v){ $pct = $total>0 ? (100*$v/$total) : 0; $out[] = esc(ucfirst($k)).' '.brandNumberFormat($pct,1).'%'; } echo implode(' • ', $out); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="disclaimer"><?= lang('Wealth.pdf_disclaimer'); ?></div>
    <div class="foot"><?= brandLang('Wealth.pdf_foot'); ?></div>

</body>
</html>
