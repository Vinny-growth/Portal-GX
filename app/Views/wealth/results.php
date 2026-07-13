<section class="section section-page">
    <div class="container-xl">
        <div class="row">
            <h1 class="page-title"><?= lang('Wealth.res_page_title'); ?></h1>
            <div class="page-content">
                <?php 
                    $hasData = false; 
                    $af = (float)($agg['assets_financial'] ?? 0); 
                    $ar = (float)($agg['assets_realestate'] ?? 0); 
                    $li = (float)($agg['liabilities'] ?? 0); 
                    $in = (float)($agg['income'] ?? 0); 
                    $ex = (float)($agg['expense'] ?? 0); 
                    if (($af+$ar+$li+$in+$ex) > 0.0) { $hasData = true; }
                ?>
                <?php if (!$hasData): ?>
                    <div class="alert alert-info"><?= lang('Wealth.res_nodata'); ?></div>
                    <p><a href="<?= base_url('wealth/conversa'); ?>" class="btn btn-lg btn-custom"><?= lang('Wealth.res_back'); ?></a></p>
                <?php endif; ?>
                <style>
                    /* Forçar contraste de botões nesta página */
                    .page-content .btn-custom { background:#3366ff; color:#fff !important; border:1px solid #3366ff; }
                    .page-content .btn-custom:hover { background:#254eda; border-color:#254eda; color:#fff !important; }
                    .page-content .btn-warning { color:#111 !important; }
                    .wm-card { border:1px solid #eee; border-radius:8px; padding:16px; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,0.04);} 
                    .wm-kpi { display:flex; align-items:center; gap:12px; }
                    .wm-kpi .dot{ width:10px; height:10px; border-radius:50%; display:inline-block; }
                    .wm-kpi .val{ font-size:20px; font-weight:600; }
                </style>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="wm-card">
                            <h4><?= lang('Wealth.res_summary'); ?></h4>
                            <ul>
                                <li><?= lang('Wealth.res_patr_fin'); ?> <?= brandMoney($agg['assets_financial'] ?? 0, 2); ?></li>
                                <li><?= lang('Wealth.res_patr_imob'); ?> <?= brandMoney($agg['assets_realestate'] ?? 0, 2); ?></li>
                                <li><?= lang('Wealth.res_passivos'); ?> <?= brandMoney($agg['liabilities'] ?? 0, 2); ?></li>
                                <li><strong><?= lang('Wealth.res_patr_liq'); ?> <?= brandMoney($agg['net_worth'] ?? 0, 2); ?></strong></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="wm-card">
                            <h4><?= lang('Wealth.res_cashflow'); ?></h4>
                            <div class="wm-kpi"><span class="dot" style="background:#3366ff"></span><div><div class="text-muted"><?= lang('Wealth.res_renda'); ?></div><div class="val"><?= brandMoney($agg['income'] ?? 0, 2); ?></div></div></div>
                            <div class="wm-kpi" style="margin-top:8px;"><span class="dot" style="background:#ff3366"></span><div><div class="text-muted"><?= lang('Wealth.res_despesas'); ?></div><div class="val"><?= brandMoney($agg['expense'] ?? 0, 2); ?></div></div></div>
                            <div class="wm-kpi" style="margin-top:8px;"><span class="dot" style="background:#2fb344"></span><div><div class="text-muted"><?= lang('Wealth.res_poupanca'); ?></div><div class="val"><?= brandMoney($agg['savings'] ?? 0, 2); ?></div></div></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="wm-card">
                            <h4><?= lang('Wealth.res_aloc'); ?></h4>
                            <?php $hasAlloc = !empty($agg['allocation']) && is_array($agg['allocation']); ?>
                            <?php if ($hasAlloc): ?>
                                <canvas id="allocChart" height="200"></canvas>
                            <?php else: ?>
                                <p class="text-muted"><?= lang('Wealth.res_no_aloc'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="wm-card">
                            <h4><?= lang('Wealth.res_evol'); ?></h4>
                            <canvas id="projChart" height="220"></canvas>
                            <small class="text-muted"><?= lang('Wealth.res_ret_real'); ?> <span id="wm-expected-return"></span> | <?= lang('Wealth.res_nw_needed'); ?> <?= brandCurrencySymbol(); ?> <span id="wm-nw-needed"></span></small>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h4><?= lang('Wealth.res_metas'); ?></h4>
                    <?php if (!empty($goals)): foreach ($goals as $g): ?>
                        <div class="mb-2">
                            <strong><?= esc($g->nome_meta); ?></strong> — <?= lang('Wealth.res_obj'); ?> <?= brandMoney($g->valor_objetivo, 2); ?> <?= lang('Wealth.res_em'); ?> <?= (int)$g->prazo_meses; ?> <?= lang('Wealth.res_meses'); ?>
                        </div>
                    <?php endforeach; else: ?>
                        <p><?= lang('Wealth.res_no_meta'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <div class="wm-card">
                        <h4><?= lang('Wealth.res_fi_title'); ?></h4>
                        <?php 
                            $months = $fi['months_to_fi'] ?? null; 
                            $yrs = is_null($months) ? null : intdiv($months, 12); 
                            $rem = is_null($months) ? null : ($months % 12);
                        ?>
                        <p>
                            <?php if (is_null($months)): ?>
                                <?= lang('Wealth.res_fi_indet'); ?>
                            <?php elseif ($months == 0): ?>
                                <?= lang('Wealth.res_fi_done'); ?>
                            <?php else: ?>
                                <?= lang('Wealth.res_fi_est_1'); ?> <?= (int)$yrs; ?> <?= lang('Wealth.res_fi_est_2'); ?> <?= (int)$rem; ?> <?= lang('Wealth.res_fi_est_3'); ?>
                            <?php endif; ?>
                        </p>
                        <h4><?= lang('Wealth.res_recom'); ?></h4>
                        <ul>
                            <?php
                            $recs = [];
                            if (($agg['liabilities'] ?? 0) > 0) { $recs[] = lang('Wealth.res_rec_divida'); }
                            if (($agg['assets_financial'] ?? 0) > 0 && count($agg['allocation'] ?? []) < 2) { $recs[] = lang('Wealth.res_rec_diversif'); }
                            if (!empty($profile) && $profile->perfil_risco) { $recs[] = strtr(lang('Wealth.res_rec_perfil'), ['{p}' => esc($profile->perfil_risco)]); }
                            if (($agg['savings'] ?? 0) > 0) { $recs[] = lang('Wealth.res_rec_poupanca'); }
                            if (empty($recs)) { $recs[] = lang('Wealth.res_rec_disciplina'); }
                            foreach ($recs as $r): ?>
                                <li><?= esc($r); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="wm-card" style="border: none; background: linear-gradient(135deg,#f8f9ff 0%, #eef5ff 100%);">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <?php 
                                    $rCtaTitle = $copy['results']['cta_title'] ?? lang('Wealth.res_cta_title');
                                    $rCtaText  = $copy['results']['cta_text'] ?? lang('Wealth.res_cta_text');
                                    $rBullets  = $copy['results']['cta_bullets'] ?? [
                                        lang('Wealth.res_cta_b1'),
                                        lang('Wealth.res_cta_b2'),
                                        lang('Wealth.res_cta_b3')
                                    ];
                                ?>
                                <h4 style="margin-top:0;"><?= esc($rCtaTitle); ?></h4>
                                <p style="margin-bottom:8px;"><?= esc($rCtaText); ?></p>
                                <ul style="margin-bottom:0;">
                                    <?php if (is_array($rBullets)): foreach ($rBullets as $rb): ?>
                                        <li><?= esc($rb); ?></li>
                                    <?php endforeach; endif; ?>
                                </ul>
                            </div>
                            <div class="col-md-4 text-md-end" style="margin-top:12px;">
                                <?php $rBtn = $copy['results']['cta_button_label'] ?? lang('Wealth.res_cta_btn'); ?>
                                <a id="wm-cta-results" class="btn btn-lg btn-custom" href="<?= base_url('wealth/agendar'); ?>"><?= esc($rBtn); ?></a>
                                <?php if (!empty($show_cta_senior) && $show_cta_senior): ?>
                                    <?php $rBtnSenior = $copy['results']['cta_button_senior_label'] ?? lang('Wealth.res_cta_senior'); ?>
                                    <div style="margin-top:8px;"><a id="wm-cta-senior" class="btn btn-lg btn-warning" href="<?= base_url('wealth/agendar'); ?>"><?= esc($rBtnSenior); ?></a></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <a class="btn btn-lg btn-custom" href="<?= base_url('wealth/agendar'); ?>"><?= lang('Wealth.res_agendar'); ?></a>
                    <?php if (!empty($show_cta_senior) && $show_cta_senior): ?>
                        <a class="btn btn-lg btn-warning" href="<?= base_url('wealth/agendar'); ?>"><?= lang('Wealth.res_senior2'); ?></a>
                    <?php endif; ?>
                </div>

                <div class="mb-5">
                    <a class="btn btn-default" href="<?= base_url('wealth/resultado/pdf'); ?>"><?= lang('Wealth.res_pdf'); ?></a>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="<?= base_url('assets/admin/plugins/chart/chart.min.js'); ?>"></script>
<script>
    (function(){
        var L = <?= json_encode([
            'aa' => lang('Wealth.res_js_aa'),
            'anos' => lang('Wealth.res_js_anos'),
            'chart_patr' => lang('Wealth.res_js_chart_patr'),
            'chart_fi' => lang('Wealth.res_js_chart_fi'),
            'nodata' => lang('Wealth.res_js_nodata'),
        ], JSON_UNESCAPED_UNICODE); ?>;
        // Allocation chart
        var alloc = <?= json_encode($agg['allocation'] ?? []); ?>;
        var labels = Object.keys(alloc||{});
        var data = Object.values(alloc||{});
        var ctx = document.getElementById('allocChart');
        if (ctx && labels.length) {
            new Chart(ctx, {type:'pie', data:{labels: labels, datasets:[{data: data, backgroundColor:[
                '#3366ff','#2fb344','#ff6b6b','#ffa94d','#845ef7','#12b886','#4dabf7'
            ]}]}});
        }

        // Projection to FI
        var fi = <?= json_encode($fi ?? []); ?> || {};
        var expectedRealAnnual = parseFloat(<?= json_encode($expected_return ?? 0.02); ?>) || 0.02;
        var years = (fi.years && fi.years.length) ? fi.years : [0,3,5,10];
        var proj = Array.isArray(fi.proj) ? fi.proj : [];
        var threshold = Array.isArray(fi.threshold) ? fi.threshold : [];
        // Harmonizar tamanhos
        if (proj.length && years.length && proj.length !== years.length) { var n = Math.min(proj.length, years.length); proj = proj.slice(0,n); years = years.slice(0,n); threshold = (threshold||[]).slice(0,n); }
        var nwNeeded = fi.nw_needed || 0;
        var elEr = document.getElementById('wm-expected-return'); if (elEr) { elEr.textContent = (expectedRealAnnual*100).toFixed(2) + L.aa; }
        var elNw = document.getElementById('wm-nw-needed'); if (elNw) { elNw.textContent = (nwNeeded||0).toLocaleString('<?= brandLocaleFull(); ?>', {minimumFractionDigits:2}); }
        var labelsY = years.map(function(y){ return y + L.anos; });
        var ctx2 = document.getElementById('projChart');
        if (ctx2 && proj.length) {
            new Chart(ctx2, {type:'line', data:{labels: labelsY, datasets:[
                {label: L.chart_patr, data: proj, borderColor:'#3366ff', backgroundColor:'rgba(51,102,255,0.15)', tension:0.2, fill:true},
                {label: L.chart_fi, data: threshold, borderColor:'#ff6b6b', backgroundColor:'rgba(255,107,107,0.08)', borderDash:[6,6], tension:0.0, fill:false}
            ]}, options:{plugins:{legend:{display:true}}, scales:{y:{ticks:{callback: function(value){return value.toLocaleString('<?= brandLocaleFull(); ?>');}}}}});
        } else {
            if (ctx2) { ctx2.outerHTML = '<p class="text-muted">' + L.nodata + '</p>'; }
        }

        // Track CTA clicks
        function track(name){
            try {
                var fd=new FormData(); fd.append('name','start_signup'); fd.append('<?= csrf_token(); ?>','<?= csrf_hash(); ?>');
                fetch('<?= base_url('WealthManager/trackEvent'); ?>',{method:'POST', body:fd});
            } catch(e){}
        }
        var cta = document.getElementById('wm-cta-results'); if (cta) cta.addEventListener('click', function(){ track('cta_results'); });
        var ctaS = document.getElementById('wm-cta-senior'); if (ctaS) ctaS.addEventListener('click', function(){ track('cta_senior'); });
    })();
</script>
