<section class="section section-page">
    <div class="container-xl">
        <div class="row">
            <h1 class="page-title">Wealth Manager</h1>
            <div class="page-content">
                <?php $l = $landing ?? null; ?>
                <div class="mb-4">
                    <h2><?= esc($l['headline'] ?? 'Gestão de Patrimônio Inteligente'); ?></h2>
                    <p><?= esc($l['subheadline'] ?? 'Planejamento financeiro com projeções e recomendações.'); ?></p>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4"><strong>1.</strong> Responda algumas perguntas</div>
                    <div class="col-md-4"><strong>2.</strong> Veja seu resumo e projeções</div>
                    <div class="col-md-4"><strong>3.</strong> Agende uma consultoria gratuita</div>
                </div>
                <div class="mb-4">
                    <a class="btn btn-lg btn-custom" href="<?= base_url('wealth/conversa'); ?>" id="wm-start-btn">Começar Agora</a>
                </div>
                <div class="mb-4">
                    <h3>FAQ</h3>
                    <div>
                        <?php if (!empty($l['faq']) && is_array($l['faq'])): foreach ($l['faq'] as $item): ?>
                            <p><strong><?= esc($item['q'] ?? ''); ?></strong><br><?= esc($item['a'] ?? ''); ?></p>
                        <?php endforeach; else: ?>
                            <p><strong>O que é?</strong><br> Uma experiência interativa para mapear seu perfil financeiro.</p>
                            <p><strong>É gratuito?</strong><br> Sim, a primeira sessão é gratuita.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mb-5">
                    <?php $labelHeaderCta = $copy['landing']['header_cta_label'] ?? 'Agendar reunião gratuita com consultor'; ?>
                    <a class="btn btn-outline-primary" id="wm-landing-consult" href="<?= base_url('wealth/agendar'); ?>"><?= esc($labelHeaderCta); ?></a>
                </div>

                <div class="mb-5" style="border:1px solid #eee; border-radius:10px; padding:20px; background:linear-gradient(135deg,#f8f9ff 0%,#eef5ff 100%);">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <?php 
                                $ctaTitle = $copy['landing']['cta_block_title'] ?? 'Traga sua liberdade financeira para mais perto';
                                $ctaSub = $copy['landing']['cta_block_subtitle'] ?? 'Simulamos sua jornada até a FI e mostramos como encurtar esse prazo com um plano de aportes e alocação sob medida.';
                                $bullets = $copy['landing']['cta_block_bullets'] ?? [
                                    'Diagnóstico rápido do seu potencial de poupança',
                                    'Alocação alinhada ao seu perfil de risco',
                                    'Plano de ação para metas prioritárias'
                                ];
                            ?>
                            <h3 style="margin-top:0;"><?= esc($ctaTitle); ?></h3>
                            <p style="margin-bottom:8px;"><?= esc($ctaSub); ?></p>
                            <ul style="margin-bottom:0;">
                                <?php if (is_array($bullets)): foreach ($bullets as $b): ?>
                                    <li><?= esc($b); ?></li>
                                <?php endforeach; endif; ?>
                            </ul>
                        </div>
                        <div class="col-md-4 text-md-end" style="margin-top:12px;">
                            <?php $ctaBtn = $copy['landing']['cta_block_button'] ?? 'Agendar consultoria'; ?>
                            <a class="btn btn-lg btn-custom" id="wm-landing-cta-consult" href="<?= base_url('wealth/agendar'); ?>"><?= esc($ctaBtn); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    (function(){
        var btn = document.getElementById('wm-start-btn');
        if (!btn) return;
        btn.addEventListener('click', function(){
            try {
                fetch('<?= base_url('WealthManager/trackEvent'); ?>', {method:'POST', body:(function(){var f=new FormData();f.append('name','start_signup');f.append('<?= csrf_token(); ?>','<?= csrf_hash(); ?>');return f;})()});
            } catch(e){}
        });
        var btn2 = document.getElementById('wm-landing-consult');
        if (btn2) btn2.addEventListener('click', function(){
            try { var f=new FormData(); f.append('name','start_signup'); f.append('<?= csrf_token(); ?>','<?= csrf_hash(); ?>'); fetch('<?= base_url('WealthManager/trackEvent'); ?>',{method:'POST',body:f}); } catch(e){}
        });
        var btn3 = document.getElementById('wm-landing-cta-consult');
        if (btn3) btn3.addEventListener('click', function(){
            try { var f=new FormData(); f.append('name','start_signup'); f.append('<?= csrf_token(); ?>','<?= csrf_hash(); ?>'); fetch('<?= base_url('WealthManager/trackEvent'); ?>',{method:'POST',body:f}); } catch(e){}
        });
    })();
</script>
