<?php
$phoneHref = !empty($baseSettings->contact_phone) ? preg_replace('/[^0-9+]/', '', (string)$baseSettings->contact_phone) : '';
$siteBarLinks = $quickLinks ?? [];
$siteBarLinks[] = ['label' => 'Wealth', 'href' => $wealthUrl];
$verticalMarks = ['CR', 'FX', 'CO', 'SG', 'WM'];
$trustLabels = ['Credito Estruturado', 'Cambio & Trade Finance', 'Consorcios', 'Seguros', 'Wealth Advisory'];
$valueCards = [
    [
        'mark' => 'DX',
        'title' => 'Diagnostico financeiro',
        'text' => 'Mapeamos caixa, exposicao, custo e objetivo antes da recomendacao.'
    ],
    [
        'mark' => 'ST',
        'title' => 'Estruturacao sob medida',
        'text' => 'Comparamos alternativas e montamos a estrutura mais aderente ao momento da empresa.'
    ],
    [
        'mark' => 'EX',
        'title' => 'Execucao consultiva',
        'text' => 'Acompanhamos a implementacao para transformar analise em decisao executada.'
    ],
];
?>
<main class="gx-marketing gx-home">
    <div class="gx-shell">
        <section class="gx-sitebar-wrap">
            <div class="container-xl">
                <div class="gx-sitebar" data-gx-reveal>
                    <a href="<?= langBaseUrl(); ?>" class="gx-sitebar-brand" aria-label="GX Capital">
                        <img src="<?= $darkMode == 1 ? getLogoFooter() : getLogo(); ?>" alt="GX Capital" width="<?= getLogoSize('width'); ?>" height="<?= getLogoSize('height'); ?>">
                    </a>

                    <button type="button" class="gx-sitebar-toggle" aria-expanded="false" aria-controls="gx-home-menu" aria-label="Abrir menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>

                    <div class="gx-sitebar-menu" id="gx-home-menu">
                        <nav class="gx-sitebar-nav" aria-label="Navegacao da home">
                            <?php foreach ($siteBarLinks as $item): ?>
                                <a href="<?= esc($item['href']); ?>" class="gx-sitebar-link"><?= esc($item['label']); ?></a>
                            <?php endforeach; ?>
                        </nav>

                        <div class="gx-sitebar-actions">
                            <?php if (!authCheck() && $generalSettings->registration_system == 1): ?>
                                <a href="#" class="gx-sitebar-secondary" data-bs-toggle="modal" data-bs-target="#modalLogin">Entrar</a>
                            <?php else: ?>
                                <a href="<?= esc($blogUrl); ?>" class="gx-sitebar-secondary">Blog</a>
                            <?php endif; ?>
                            <a href="#fale-especialista" class="gx-btn gx-btn-primary">Comecar agora</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="gx-hero">
            <div class="container-xl">
                <div class="gx-hero-frame">
                    <div class="gx-hero-layout">
                        <div class="gx-hero-copy" data-gx-reveal>
                            <p class="gx-eyebrow">Estruturacao financeira com profundidade tecnica</p>
                            <h1 class="gx-hero-title">Solucoes sofisticadas para capital, protecao e patrimonio.</h1>
                            <p class="gx-hero-text">
                                A GX Capital estrutura credito, cambio, consorcios, seguros e investimentos para empresas, empresarios e
                                familias empresarias que precisam decidir com mais clareza, previsibilidade e visao de longo prazo.
                            </p>

                            <div class="gx-actions">
                                <a href="#fale-especialista" class="gx-btn gx-btn-primary">Falar com especialista</a>
                                <a href="<?= esc($simulatorsHubUrl); ?>" class="gx-btn gx-btn-ghost">Explorar simuladores</a>
                            </div>

                            <div class="gx-hero-proof" aria-label="Diferenciais">
                                <span class="gx-hero-proof-item">Diagnostico financeiro e patrimonial</span>
                                <span class="gx-hero-proof-item">Estruturacao sob medida</span>
                                <span class="gx-hero-proof-item">Acompanhamento consultivo</span>
                            </div>

                            <div class="gx-hero-stats" aria-label="Indicadores institucionais">
                                <?php foreach ($heroStats as $stat): ?>
                                    <article class="gx-hero-stat">
                                        <strong><?= esc((string)$stat['value']); ?></strong>
                                        <span><?= esc($stat['label']); ?></span>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="gx-hero-visual" data-gx-reveal data-gx-delay="120">
                            <span class="gx-hero-glow gx-hero-glow-a"></span>
                            <span class="gx-hero-glow gx-hero-glow-b"></span>

                            <div class="gx-phone-mockup">
                                <div class="gx-phone-shell">
                                    <div class="gx-phone-camera"></div>
                                    <div class="gx-phone-screen">
                                        <div class="gx-phone-screen-head">
                                            <span class="gx-phone-pill">GX Capital</span>
                                            <span class="gx-phone-pill gx-phone-pill-soft">Advisory</span>
                                        </div>

                                        <div class="gx-phone-card gx-phone-card-main">
                                            <p class="gx-phone-kicker">Visao integrada</p>
                                            <strong>Caixa, risco e patrimonio vistos como um sistema.</strong>
                                            <span>Compare alternativas, custos e cenarios antes da negociacao.</span>
                                        </div>

                                        <div class="gx-phone-chart">
                                            <div class="gx-phone-chart-top">
                                                <span>Leitura de cenario</span>
                                                <strong>Mais previsibilidade</strong>
                                            </div>
                                            <div class="gx-phone-bars" aria-hidden="true">
                                                <span style="height: 38%;"></span>
                                                <span style="height: 66%;"></span>
                                                <span style="height: 54%;"></span>
                                                <span style="height: 82%;"></span>
                                                <span style="height: 60%;"></span>
                                            </div>
                                        </div>

                                        <div class="gx-phone-mini-grid">
                                            <article class="gx-phone-mini">
                                                <small>FX</small>
                                                <strong>Hedge</strong>
                                                <span>margem protegida</span>
                                            </article>
                                            <article class="gx-phone-mini">
                                                <small>CAP</small>
                                                <strong>Funding</strong>
                                                <span>custo comparado</span>
                                            </article>
                                            <article class="gx-phone-mini">
                                                <small>WM</small>
                                                <strong>Wealth</strong>
                                                <span>visao patrimonial</span>
                                            </article>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <article class="gx-float-card gx-float-card-left">Risco cambial com leitura de cenario</article>
                            <article class="gx-float-card gx-float-card-right">Funding comparado antes da negociacao</article>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="gx-trust-strip">
            <div class="container-xl">
                <div class="gx-trust-list" data-gx-reveal>
                    <?php foreach ($trustLabels as $label): ?>
                        <span class="gx-trust-item"><?= esc($label); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="gx-section gx-solutions-section" id="verticais" aria-labelledby="gx-verticais-title">
            <div class="container-xl">
                <div class="gx-section-head gx-section-head-split">
                    <div>
                        <p class="gx-eyebrow">Verticais de negocio</p>
                        <h2 id="gx-verticais-title" class="gx-section-title">Frentes especializadas para momentos diferentes da empresa.</h2>
                    </div>
                    <p class="gx-section-text">
                        Da liquidez de curto prazo ao patrimonio de longo prazo, estruturamos solucoes financeiras com leitura tecnica e
                        criterio executivo.
                    </p>
                </div>

                <div class="gx-feature-grid">
                    <?php foreach ($businessVerticals as $index => $vertical): ?>
                        <article class="gx-feature-card" style="--gx-accent: <?= esc($vertical['accent']); ?>;" data-gx-reveal data-gx-delay="<?= esc((string)(($index % 3) * 70)); ?>">
                            <span class="gx-feature-mark"><?= esc($verticalMarks[$index] ?? str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                            <p class="gx-card-kicker"><?= esc($vertical['eyebrow']); ?></p>
                            <h2 class="gx-feature-title"><?= esc($vertical['title']); ?></h2>
                            <p class="gx-card-text"><?= esc($vertical['description']); ?></p>
                            <a href="<?= esc($vertical['link_url']); ?>" class="gx-text-link"><?= esc($vertical['link_label']); ?></a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="gx-section gx-value-band" aria-labelledby="gx-value-title">
            <div class="container-xl">
                <div class="gx-section-head gx-section-head-split">
                    <div>
                        <p class="gx-eyebrow">Como atuamos</p>
                        <h2 id="gx-value-title" class="gx-section-title">Da leitura do problema a implementacao da estrutura.</h2>
                    </div>
                    <p class="gx-section-text">
                        Combinamos analise financeira, modelagem e acompanhamento executivo para reduzir ruido e ganhar velocidade na decisao.
                    </p>
                </div>

                <div class="gx-value-grid">
                    <?php foreach ($valueCards as $index => $card): ?>
                        <article class="gx-value-card" data-gx-reveal data-gx-delay="<?= esc((string)($index * 80)); ?>">
                            <span class="gx-value-mark"><?= esc($card['mark']); ?></span>
                            <h3 class="gx-value-title"><?= esc($card['title']); ?></h3>
                            <p class="gx-value-text"><?= esc($card['text']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="gx-section" id="simuladores" aria-labelledby="gx-simuladores-title">
            <div class="container-xl">
                <div class="gx-section-head gx-section-head-split">
                    <div>
                        <p class="gx-eyebrow">Simuladores</p>
                        <h2 id="gx-simuladores-title" class="gx-section-title">Ferramentas para antecipar custo, risco e estrutura.</h2>
                        <p class="gx-section-text">
                            Use os simuladores para testar cenarios antes da conversa comercial e chegar com a demanda mais madura.
                        </p>
                    </div>
                    <a href="<?= esc($simulatorsHubUrl); ?>" class="gx-btn gx-btn-ghost">Ver todos os simuladores</a>
                </div>

                <?= view('marketing/_simulator_grid', [
                    'simulators' => $simulators,
                    'showPath' => false,
                    'showLegacyBadge' => false
                ]); ?>
            </div>
        </section>

        <section class="gx-section" id="blog-tecnico" aria-labelledby="gx-blog-title">
            <div class="container-xl">
                <div class="gx-section-head gx-section-head-split">
                    <div>
                        <p class="gx-eyebrow">Conteudo tecnico</p>
                        <h2 id="gx-blog-title" class="gx-section-title">Analises para acompanhar funding, cambio, patrimonio e mercado.</h2>
                        <p class="gx-section-text">
                            Publicamos leituras praticas para quem decide com responsabilidade financeira e precisa de contexto para agir.
                        </p>
                    </div>
                    <a href="<?= esc($blogUrl); ?>" class="gx-btn gx-btn-ghost">Acessar blog</a>
                </div>

                <div class="gx-blog-grid">
                    <?php foreach ($latestPosts as $index => $post): ?>
                        <article class="gx-blog-card<?= $index === 0 ? ' gx-blog-card-featured' : ''; ?>" data-gx-reveal data-gx-delay="<?= esc((string)($index * 70)); ?>">
                            <?php if (checkPostImg($post)): ?>
                                <a href="<?= generatePostURL($post); ?>" class="gx-blog-image"<?php postURLNewTab($post); ?>>
                                    <img
                                        src="<?= getPostImage($post, 'mid'); ?>"
                                        alt="<?= esc($post->title); ?>"
                                        width="416"
                                        height="247"
                                        loading="lazy"
                                        decoding="async">
                                </a>
                            <?php endif; ?>
                            <div class="gx-blog-body">
                                <div class="gx-blog-top">
                                    <a href="<?= generateCategoryURLById($post->category_id, $baseCategories); ?>" class="gx-blog-kicker" style="--gx-badge: <?= esc($post->category_color); ?>;">
                                        <?= esc($post->category_name); ?>
                                    </a>
                                    <?php if ($index === 0): ?>
                                        <span class="gx-blog-label">Destaque editorial</span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="gx-blog-title">
                                    <a href="<?= generatePostURL($post); ?>"<?php postURLNewTab($post); ?>><?= esc(characterLimiter($post->title, 110, '...')); ?></a>
                                </h3>
                                <p class="gx-blog-meta"><?= loadView('post/_post_meta', ['postItem' => $post]); ?></p>
                                <p class="gx-blog-summary"><?= esc(characterLimiter($post->summary, 160, '...')); ?></p>
                                <a href="<?= generatePostURL($post); ?>" class="gx-text-link"<?php postURLNewTab($post); ?>>Ler analise</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="gx-section gx-banner-section" aria-labelledby="gx-banner-title">
            <div class="container-xl">
                <div class="gx-banner-cta" data-gx-reveal>
                    <div>
                        <p class="gx-eyebrow">Atendimento consultivo</p>
                        <h2 id="gx-banner-title" class="gx-section-title">Leve a demanda financeira para a frente certa desde o primeiro contato.</h2>
                        <p class="gx-section-text">
                            Se a pauta envolver funding, hedge, recebiveis, consorcio, seguros ou patrimonio, centralize a conversa com a GX
                            Capital.
                        </p>
                    </div>
                    <div class="gx-actions">
                        <a href="#fale-especialista" class="gx-btn gx-btn-primary">Falar com o time</a>
                        <a href="<?= esc($simulatorsHubUrl); ?>" class="gx-btn gx-btn-secondary">Abrir simuladores</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="gx-section gx-lead-section" id="fale-especialista" aria-labelledby="gx-lead-title">
            <div class="container-xl">
                <div class="gx-lead-grid">
                    <aside class="gx-lead-aside" data-gx-reveal>
                        <p class="gx-eyebrow">Fale com a GX Capital</p>
                        <h2 id="gx-lead-title" class="gx-section-title">Converse com um especialista.</h2>
                        <p class="gx-section-text">
                            Compartilhe o contexto da empresa, da operacao ou do objetivo patrimonial e direcionamos a conversa para a
                            vertical mais aderente.
                        </p>
                        <div class="gx-contact-list">
                            <?php if (!empty($baseSettings->contact_phone)): ?>
                                <a href="<?= !empty($phoneHref) ? 'tel:' . esc($phoneHref) : '#'; ?>" class="gx-contact-chip"><?= esc($baseSettings->contact_phone); ?></a>
                            <?php endif; ?>
                            <?php if (!empty($baseSettings->contact_email)): ?>
                                <a href="mailto:<?= esc($baseSettings->contact_email); ?>" class="gx-contact-chip"><?= esc($baseSettings->contact_email); ?></a>
                            <?php endif; ?>
                            <a href="<?= esc($simulatorsHubUrl); ?>" class="gx-contact-chip">Ver simuladores</a>
                            <a href="<?= esc($blogUrl); ?>" class="gx-contact-chip">Explorar blog</a>
                        </div>
                    </aside>

                    <div class="gx-lead-card" data-gx-reveal data-gx-delay="100">
                        <?= view('marketing/_specialist_form', [
                            'formId' => 'gx-home-specialist-form',
                            'heading' => 'Leve sua demanda para o time GX Capital',
                            'description' => 'Informe a estrutura, operacao ou objetivo patrimonial. O retorno parte da vertical mais aderente.',
                            'buttonLabel' => 'Solicitar contato',
                            'messagePlaceholder' => 'Ex.: preciso revisar hedge cambial, custo de capital, recebiveis, consorcio, seguros ou carteira de investimentos.'
                        ]); ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var home = document.querySelector('.gx-home');
    if (!home) {
        return;
    }

    var sitebar = document.querySelector('.gx-sitebar');
    var toggle = document.querySelector('.gx-sitebar-toggle');
    var menu = document.querySelector('.gx-sitebar-menu');

    if (sitebar) {
        var onScroll = function () {
            if (window.scrollY > 12) {
                sitebar.classList.add('is-scrolled');
            } else {
                sitebar.classList.remove('is-scrolled');
            }
        };
        onScroll();
        window.addEventListener('scroll', onScroll, {passive: true});
    }

    if (toggle && menu && sitebar) {
        toggle.addEventListener('click', function () {
            var isOpen = sitebar.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        menu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                sitebar.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    var revealNodes = document.querySelectorAll('[data-gx-reveal]');
    if ('IntersectionObserver' in window && revealNodes.length > 0) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }
                var delay = entry.target.getAttribute('data-gx-delay');
                if (delay) {
                    entry.target.style.transitionDelay = delay + 'ms';
                }
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, {threshold: 0.16, rootMargin: '0px 0px -40px 0px'});

        revealNodes.forEach(function (node) {
            observer.observe(node);
        });
    } else {
        revealNodes.forEach(function (node) {
            node.classList.add('is-visible');
        });
    }
});
</script>
