<?php
$topicFronts = $topicFronts ?? [];
$hubStats = $hubStats ?? [];
$legacySimulators = $legacySimulators ?? [];
$simulators = $simulators ?? [];
$contactChannels = $contactChannels ?? [];
$contactPhone = trim((string)($contactChannels['phone'] ?? (!empty($baseSettings->contact_phone) ? $baseSettings->contact_phone : '')));
$contactPhoneHref = !empty($contactChannels['phone_href']) ? $contactChannels['phone_href'] : (!empty($contactPhone) ? 'tel:' . preg_replace('/[^0-9+]/', '', $contactPhone) : '');
$contactEmail = trim((string)($contactChannels['email'] ?? (!empty($baseSettings->contact_email) ? $baseSettings->contact_email : '')));
$whatsAppUrl = $whatsAppUrl ?? '';
$whatsAppMessage = $whatsAppMessage ?? '';
$whatsAppIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20.52 3.48A11.8 11.8 0 0 0 12.08 0C5.55 0 .24 5.31.24 11.84c0 2.08.54 4.11 1.58 5.89L0 24l6.46-1.69a11.8 11.8 0 0 0 5.62 1.43h.01c6.53 0 11.84-5.31 11.84-11.84 0-3.16-1.23-6.13-3.41-8.42Zm-8.44 18.26h-.01a9.84 9.84 0 0 1-5.01-1.37l-.36-.22-3.84 1 1.03-3.74-.24-.38a9.8 9.8 0 0 1-1.51-5.2C2.14 6.42 6.66 1.9 12.08 1.9c2.63 0 5.1 1.02 6.96 2.88a9.78 9.78 0 0 1 2.89 6.97c0 5.42-4.42 9.99-9.85 9.99Zm5.39-7.41c-.29-.14-1.71-.84-1.98-.94-.26-.1-.45-.14-.64.14-.19.29-.74.94-.91 1.13-.17.19-.34.22-.63.07-.29-.14-1.21-.45-2.31-1.45-.85-.76-1.42-1.69-1.59-1.98-.17-.29-.02-.44.13-.58.13-.13.29-.34.43-.5.14-.17.19-.29.29-.48.1-.19.05-.36-.02-.5-.07-.14-.64-1.55-.87-2.12-.23-.55-.47-.48-.64-.49h-.55c-.19 0-.5.07-.76.36-.26.29-.99.97-.99 2.37s1.01 2.75 1.15 2.94c.14.19 1.98 3.03 4.79 4.25.67.29 1.2.47 1.61.6.68.22 1.3.19 1.79.12.55-.08 1.71-.7 1.95-1.37.24-.67.24-1.24.17-1.37-.07-.12-.26-.19-.55-.34Z"/></svg>';

$navLinks = [
    ['label' => 'Início', 'href' => $homeUrl],
    ['label' => 'Frentes', 'href' => '#frentes-simuladores'],
    ['label' => 'Catálogo', 'href' => '#catalogo-completo'],
    ['label' => 'Destaques', 'href' => '#destaques-cambio'],
    ['label' => 'Especialista', 'href' => '#fale-especialista'],
];
?>
<main class="gx-marketing gx-home gx-simulators-hub">
    <nav class="gx-nav" id="gx-nav">
        <div class="gx-nav-inner">
            <a href="<?= esc($homeUrl); ?>" class="gx-nav-brand" aria-label="GX Capital">
                <img src="<?= getLogo(); ?>" alt="GX Capital" width="<?= getLogoSize('width'); ?>" height="<?= getLogoSize('height'); ?>">
            </a>

            <div class="gx-nav-links" id="gx-nav-links">
                <?php foreach ($navLinks as $item): ?>
                    <a href="<?= esc($item['href']); ?>" class="gx-nav-link"><?= esc($item['label']); ?></a>
                <?php endforeach; ?>
                <div class="gx-nav-menu-extra">
                    <a href="<?= esc($blogUrl); ?>" class="gx-nav-link">Blog</a>
                    <a href="#fale-especialista" class="gx-btn gx-btn-primary">Falar com especialista</a>
                </div>
            </div>

            <div class="gx-nav-right">
                <a href="<?= esc($blogUrl); ?>" class="gx-nav-link">Blog</a>
                <a href="#fale-especialista" class="gx-btn gx-btn-primary">Falar com especialista</a>
                <button type="button" class="gx-nav-toggle" id="gx-nav-toggle" aria-expanded="false" aria-controls="gx-nav-links" aria-label="Menu">
                    <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
            </div>
        </div>
    </nav>

    <section class="gx-hero">
        <div class="gx-hero-inner">
            <div class="gx-hero-content" data-gx-reveal>
                <div class="gx-hero-badge">
                    <span class="gx-hero-badge-dot"></span>
                    Central de simuladores GX Capital
                </div>

                <h1 class="gx-hero-title">
                    Encontre o simulador certo para câmbio, consórcio, crédito e estruturação financeira.
                </h1>

                <p class="gx-hero-sub">
                    Comece pela frente que mais se aproxima da sua necessidade, compare cenários com mais clareza e, quando fizer sentido,
                    leve a demanda direto para um especialista da GX Capital.
                </p>

                <div class="gx-hero-cta">
                    <a href="#fale-especialista" class="gx-btn gx-btn-primary gx-btn-lg">Falar com especialista</a>
                    <?php if (!empty($whatsAppUrl)): ?>
                        <a href="<?= esc($whatsAppUrl); ?>" target="_blank" rel="noopener" class="gx-btn gx-btn-whatsapp gx-btn-lg"><?= $whatsAppIcon; ?>Chamar no WhatsApp</a>
                    <?php else: ?>
                        <a href="#catalogo-completo" class="gx-btn gx-btn-ghost gx-btn-lg">Ver catálogo completo</a>
                    <?php endif; ?>
                </div>

                <div class="gx-hero-proof">
                    <div class="gx-hero-proof-item">
                        <strong>Câmbio e trade finance</strong>
                        <span>Uma frente dedicada para avaliar exposição, hedge, trade e funding internacional.</span>
                    </div>
                    <div class="gx-hero-proof-item">
                        <strong>Consórcio estratégico</strong>
                        <span>Planeje aquisição, contemplação e fluxo com uma jornada própria para o produto.</span>
                    </div>
                    <div class="gx-hero-proof-item">
                        <strong>Crédito e capital</strong>
                        <span>Ferramentas para custo de capital, mercado de capitais e antecipação em um mesmo ambiente.</span>
                    </div>
                </div>
            </div>

            <aside class="gx-hero-aside" data-gx-reveal data-gx-delay="150">
                <div class="gx-hero-visual-card">
                    <div class="gx-visual-header">
                        <span class="gx-visual-title">Soluções em um só lugar</span>
                        <span class="gx-visual-badge">Hub</span>
                    </div>

                    <?php if (!empty($hubStats)): ?>
                        <div class="gx-stat-grid">
                            <?php foreach ($hubStats as $stat): ?>
                                <div class="gx-stat-card">
                                    <span class="gx-stat-value"><?= esc((string)$stat['value']); ?></span>
                                    <span class="gx-stat-label"><?= esc($stat['label']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="gx-note-card" style="margin-top: 16px;">
                        <p class="gx-eyebrow">Como usar melhor</p>
                        <p class="gx-card-text">
                            Se a dúvida ainda estiver aberta, escolha primeiro a frente da operação. Se a necessidade já estiver clara,
                            avance para o simulador específico e peça uma leitura consultiva ao time.
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <div class="gx-strip">
        <div class="gx-strip-inner" data-gx-reveal>
            <span class="gx-strip-lead">Fluxo recomendado</span>
            <span class="gx-strip-item">Escolha a frente temática</span>
            <span class="gx-strip-item">Abra o simulador adequado ao caso</span>
            <span class="gx-strip-item">Envie o cenário para o especialista</span>
            <span class="gx-strip-item">Avance com leitura consultiva e execução</span>
        </div>
    </div>

    <section class="gx-section gx-section-alt" id="frentes-simuladores">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label">Frentes e hubs</p>
                    <h2 class="gx-section-title">Comece pelo assunto da demanda e depois aprofunde no simulador específico.</h2>
                </div>
                <p class="gx-section-desc">
                    Cada frente reúne as ferramentas mais aderentes ao tipo de decisão que a empresa precisa tomar, reduzindo ruído e acelerando o primeiro diagnóstico.
                </p>
            </div>

            <div class="gx-simulator-grid" data-gx-reveal data-gx-delay="80">
                <?php foreach ($topicFronts as $front): ?>
                    <article class="gx-simulator-card">
                        <div class="gx-simulator-top">
                            <span class="gx-simulator-mark"><?= esc($front['label'] ?? 'GX'); ?></span>
                            <span class="gx-legacy-pill"><?= esc($front['badge'] ?? 'Frente'); ?></span>
                        </div>
                        <p class="gx-card-kicker"><?= esc($front['eyebrow'] ?? ''); ?></p>
                        <h3 class="gx-simulator-title"><?= esc($front['title'] ?? ''); ?></h3>
                        <p class="gx-simulator-meta"><?= esc($front['description'] ?? ''); ?></p>
                        <div class="gx-simulator-footer">
                            <?php if (!empty($front['path'])): ?>
                                <span class="gx-simulator-path"><?= esc($front['path']); ?></span>
                            <?php endif; ?>
                            <a href="<?= esc($front['url'] ?? '#'); ?>" class="gx-text-link"><?= esc($front['cta'] ?? 'Abrir'); ?></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="gx-section" id="catalogo-completo">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label">Catálogo completo</p>
                    <h2 class="gx-section-title">Explore todas as ferramentas públicas disponíveis hoje na plataforma.</h2>
                </div>
                <p class="gx-section-desc">
                    Use este catálogo para sair de uma dúvida ampla e chegar rapidamente no estudo mais aderente ao seu contexto operacional ou financeiro.
                </p>
            </div>

            <div data-gx-reveal data-gx-delay="80">
                <?= view('marketing/_simulator_grid', ['simulators' => $simulators, 'showPath' => true, 'showLegacyBadge' => true]); ?>
            </div>
        </div>
    </section>

    <section class="gx-section gx-section-alt" id="destaques-cambio">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label">Destaques em câmbio</p>
                    <h2 class="gx-section-title">Se a sua pauta envolve dólar, hedge ou funding internacional, comece por estes estudos.</h2>
                </div>
                <p class="gx-section-desc">
                    Eles costumam ser a melhor porta de entrada para empresas que precisam entender exposição cambial, proteção de margem ou viabilidade de funding em moeda forte.
                </p>
            </div>

            <div data-gx-reveal data-gx-delay="80">
                <?= view('marketing/_simulator_grid', ['simulators' => $legacySimulators, 'showPath' => false, 'showLegacyBadge' => false]); ?>
            </div>
        </div>
    </section>

    <section class="gx-lead-section" id="fale-especialista">
        <div class="gx-wrap">
            <div class="gx-lead-grid">
                <aside class="gx-lead-aside" data-gx-reveal>
                    <p class="gx-label">Apoio consultivo</p>
                    <h2 class="gx-section-title">Quer acelerar a conversa com um especialista?</h2>
                    <p class="gx-section-desc">
                        Conte o contexto da empresa, da operação ou do objetivo financeiro. A GX Capital organiza o próximo passo já na vertical
                        mais aderente e evita que a conversa comece genérica.
                    </p>

                    <div class="gx-contact-highlight">
                        <span class="gx-fx-live-eyebrow">Contato rápido</span>
                        <strong>Fale agora com o time comercial.</strong>
                        <p>Você pode abrir a conversa por formulário ou já iniciar o atendimento no WhatsApp com mensagem pronta.</p>
                        <div class="gx-contact-cta-grid">
                            <a href="#gx-hub-specialist-form" class="gx-btn gx-btn-primary">Pedir contato do especialista</a>
                            <?php if (!empty($whatsAppUrl)): ?>
                                <a href="<?= esc($whatsAppUrl); ?>" target="_blank" rel="noopener" class="gx-btn gx-btn-whatsapp"><?= $whatsAppIcon; ?>Chamar no WhatsApp</a>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($whatsAppUrl)): ?>
                            <p class="gx-contact-note">Mensagem pronta com origem da central de simuladores.</p>
                        <?php endif; ?>
                    </div>

                    <div class="gx-contact-list">
                        <?php if (!empty($contactPhone)): ?>
                            <a href="<?= !empty($contactPhoneHref) ? esc($contactPhoneHref) : '#'; ?>" class="gx-contact-chip"><?= esc($contactPhone); ?></a>
                        <?php endif; ?>
                        <?php if (!empty($contactEmail)): ?>
                            <a href="mailto:<?= esc($contactEmail); ?>" class="gx-contact-chip"><?= esc($contactEmail); ?></a>
                        <?php endif; ?>
                        <?php if (!empty($whatsAppUrl)): ?>
                            <a href="<?= esc($whatsAppUrl); ?>" target="_blank" rel="noopener" class="gx-contact-chip"><?= $whatsAppIcon; ?>WhatsApp</a>
                        <?php endif; ?>
                        <a href="<?= esc($blogUrl); ?>" class="gx-contact-chip">Abrir blog</a>
                    </div>
                </aside>

                <div class="gx-lead-card" data-gx-reveal data-gx-delay="100">
                    <?= view('marketing/_specialist_form', [
                        'formId' => 'gx-hub-specialist-form',
                        'heading' => 'Leve a demanda para o time GX Capital',
                        'description' => 'Descreva a operação, o simulador usado ou a estrutura em análise. O retorno parte da frente mais aderente.',
                        'buttonLabel' => 'Receber retorno consultivo',
                        'messagePlaceholder' => 'Ex.: cheguei pelo hub de simuladores e preciso decidir entre hedge cambial, consórcio, custo de capital ou antecipação.'
                    ]); ?>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
(function() {
    var nav = document.getElementById('gx-nav');
    var toggle = document.getElementById('gx-nav-toggle');
    var links = document.getElementById('gx-nav-links');
    var raf = window.requestAnimationFrame ? window.requestAnimationFrame.bind(window) : function(cb) {
        return setTimeout(cb, 16);
    };

    if (nav) {
        var navTicking = false;
        var navScrolled = false;
        var syncNavState = function(force) {
            var nextScrolled = window.scrollY > 20;
            if (force || nextScrolled !== navScrolled) {
                navScrolled = nextScrolled;
                if (navScrolled) {
                    nav.classList.add('is-scrolled');
                } else {
                    nav.classList.remove('is-scrolled');
                }
            }
            navTicking = false;
        };
        syncNavState(true);
        window.addEventListener('scroll', function() {
            if (navTicking) {
                return;
            }
            navTicking = true;
            raf(syncNavState);
        }, {passive: true});
    }

    if (toggle && links && nav) {
        var setMenuState = function(open) {
            if (open) {
                nav.classList.add('is-open');
            } else {
                nav.classList.remove('is-open');
            }
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        };
        toggle.addEventListener('click', function() {
            setMenuState(!nav.classList.contains('is-open'));
        });
        links.querySelectorAll('a').forEach(function(anchor) {
            anchor.addEventListener('click', function() {
                setMenuState(false);
            });
        });
    }

    var nodes = document.querySelectorAll('[data-gx-reveal]');
    if ('IntersectionObserver' in window && nodes.length) {
        var revealQueue = [];
        var revealScheduled = false;
        var flushRevealQueue = function() {
            revealScheduled = false;
            while (revealQueue.length) {
                var item = revealQueue.shift();
                if (item.delay) {
                    item.node.style.transitionDelay = item.delay + 'ms';
                }
                item.node.classList.add('is-visible');
            }
        };
        var scheduleReveal = function(node, delay) {
            revealQueue.push({node: node, delay: delay});
            if (!revealScheduled) {
                revealScheduled = true;
                raf(flushRevealQueue);
            }
        };
        var obs = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (!entry.isIntersecting) {
                    return;
                }
                scheduleReveal(entry.target, entry.target.getAttribute('data-gx-delay'));
                obs.unobserve(entry.target);
            });
        }, {threshold: 0.1, rootMargin: '0px 0px -40px 0px'});
        nodes.forEach(function(node) {
            if (node.getBoundingClientRect().top > window.innerHeight * 0.85) {
                node.classList.add('gx-reveal-armed');
                obs.observe(node);
            } else {
                node.classList.add('is-visible');
            }
        });
    } else {
        nodes.forEach(function(node) {
            node.classList.add('is-visible');
        });
    }
})();
</script>
<script>
(function() {
    /* Track simulator card clicks as select_content */
    document.querySelectorAll('.gx-simulator-card a, .gx-sim-card a').forEach(function(link) {
        link.addEventListener('click', function() {
            var card = link.closest('.gx-simulator-card, .gx-sim-card');
            var title = card ? (card.querySelector('.gx-simulator-title, .gx-sim-title') || {}).textContent : '';
            title = (title || '').trim();
            if (typeof gxFbq === 'function') gxFbq('track', 'ViewContent', { content_name: title, content_category: 'Simuladores Hub' });
            if (typeof gxGtag === 'function') gxGtag('event', 'select_content', { content_type: 'simulator', item_id: title });
        });
    });
})();
</script>
