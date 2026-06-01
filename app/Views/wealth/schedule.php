<main class="gx-marketing gx-wealth">
    <nav class="gx-nav" id="gx-nav">
        <div class="gx-nav-inner">
            <a href="<?= langBaseUrl(); ?>" class="gx-nav-brand" aria-label="GX Capital">
                <img src="<?= getLogo(); ?>" alt="GX Capital" width="<?= getLogoSize('width'); ?>" height="<?= getLogoSize('height'); ?>">
            </a>

            <div class="gx-nav-links" id="gx-nav-links">
                <a href="<?= esc($wealthScheduleUrl); ?>" class="gx-nav-link">Agendar</a>
                <a href="<?= esc(base_url('wealth') . '#diagnostico'); ?>" class="gx-nav-link">Diagnóstico</a>
                <a href="<?= esc(base_url('wealth') . '#metodo'); ?>" class="gx-nav-link">Método</a>
                <a href="<?= esc(base_url('wealth') . '#faq'); ?>" class="gx-nav-link">FAQ</a>
                <div class="gx-nav-menu-extra">
                    <a href="<?= esc(base_url('wealth')); ?>" class="gx-nav-link">Voltar para /wealth</a>
                    <?php if (!empty($isAuthenticated)): ?>
                        <a href="<?= esc($wealthConversationUrl); ?>" class="gx-btn gx-btn-primary" data-wealth-track="wealth_continue_area">Área completa</a>
                    <?php else: ?>
                        <a href="<?= esc(base_url('wealth') . '#fale-com-especialista'); ?>" class="gx-btn gx-btn-primary" data-wealth-track="start_signup">Voltar para a landing</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="gx-nav-right">
                <a href="<?= esc(base_url('wealth')); ?>" class="gx-nav-link">/wealth</a>
                <?php if (!empty($isAuthenticated)): ?>
                    <a href="<?= esc($wealthConversationUrl); ?>" class="gx-btn gx-btn-primary" data-wealth-track="wealth_continue_area">Área completa</a>
                <?php else: ?>
                    <a href="<?= esc(base_url('wealth') . '#fale-com-especialista'); ?>" class="gx-btn gx-btn-primary" data-wealth-track="start_signup">Voltar para a landing</a>
                <?php endif; ?>
                <button type="button" class="gx-nav-toggle" id="gx-nav-toggle" aria-expanded="false" aria-controls="gx-nav-links" aria-label="Menu">
                    <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
            </div>
        </div>
    </nav>

    <section class="gx-section gx-wealth-schedule-hero">
        <div class="gx-wrap">
            <div class="gx-wealth-schedule-shell">
                <div class="gx-wealth-schedule-copy" data-gx-reveal>
                    <div>
                        <p class="gx-label">Contato Consultivo</p>
                        <h1 class="gx-section-title">Agende um diagnóstico patrimonial com contexto e próximos passos.</h1>
                    </div>

                    <p class="gx-section-desc">
                        Esta etapa é para quem já quer iniciar a conversa com um especialista. Informe objetivo, faixa patrimonial e o melhor formato de retorno.
                    </p>

                    <ul class="gx-wealth-schedule-list">
                        <li>Leitura rápida do cenário informado antes do primeiro retorno.</li>
                        <li>Contato orientado a contexto, não disparo genérico.</li>
                        <li>Prioridade para patrimônio, liquidez, renda, crescimento e legado.</li>
                    </ul>

                    <div class="gx-wealth-chip-list">
                        <span class="gx-wealth-chip">Retorno consultivo</span>
                        <span class="gx-wealth-chip">Sem compromisso</span>
                        <span class="gx-wealth-chip">Diagnóstico inicial objetivo</span>
                    </div>
                </div>

                <div class="gx-wealth-schedule-card" data-gx-reveal data-gx-delay="100">
                    <?= view('wealth/_lead_form', [
                        'formId' => 'gx-wealth-schedule-form',
                        'sourcePage' => 'schedule',
                        'formTitle' => 'Enviar contexto para o time GX Capital',
                        'formDescription' => 'Quanto mais claro o objetivo, melhor o primeiro direcionamento consultivo.',
                        'submitLabel' => 'Solicitar contato consultivo',
                        'activeLang' => $activeLang,
                        'blogUrl' => $blogUrl,
                    ]); ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?= view('wealth/_scripts'); ?>
