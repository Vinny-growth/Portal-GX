<?php
$l = is_array($landing ?? null) ? $landing : [];
$heroTitle = trim((string)($l['headline'] ?? 'Seu patrimônio precisa de estratégia, não de improviso.'));
$heroSubtitle = trim((string)($l['subheadline'] ?? 'Diagnóstico patrimonial, leitura de liquidez, alocação e próximos passos consultivos para famílias, executivos e empresários.'));
$heroBadge = $copy['landing']['hero_badge'] ?? 'Wealth Advisory | GX Capital';
$primaryCta = $copy['landing']['primary_cta_label'] ?? 'Quero meu diagnóstico';
$secondaryCta = $copy['landing']['secondary_cta_label'] ?? 'Entender o método';
$leadFormTitle = $copy['landing']['form_title'] ?? 'Receba um diagnóstico consultivo inicial';
$leadFormDescription = $copy['landing']['form_description'] ?? 'Preencha os dados principais e descreva o contexto. O retorno vem com direcionamento patrimonial e próximos passos possíveis.';
$leadFormButton = $copy['landing']['form_button_label'] ?? 'Falar com um especialista';
$faqItems = !empty($l['faq']) && is_array($l['faq']) ? $l['faq'] : [
    ['q' => 'Para quem a consultoria é indicada?', 'a' => 'Para famílias, executivos e empresários que querem organizar patrimônio, renda, liquidez e decisões financeiras com visão integrada.'],
    ['q' => 'Preciso transferir a carteira para ter um diagnóstico?', 'a' => 'Não. O diagnóstico inicial parte do seu contexto atual e identifica onde estão travas, desalinhamentos e prioridades.'],
    ['q' => 'O que recebo após o primeiro contato?', 'a' => 'Uma leitura consultiva do caso, hipóteses de ganho de eficiência e indicação objetiva dos próximos movimentos possíveis.'],
    ['q' => 'A análise inclui fluxo de caixa, patrimônio e metas?', 'a' => 'Sim. O foco é conectar patrimônio, liquidez, objetivos e ritmo de construção para evitar decisões isoladas.'],
];
$signalCards = [
    ['title' => 'Liquidez parada', 'text' => 'Caixa excessivo ou desalocado corrói eficiência e reduz capacidade de execução futura.'],
    ['title' => 'Carteira sem tese', 'text' => 'Ativos acumulados ao longo do tempo, sem uma lógica clara de função, risco e objetivo.'],
    ['title' => 'Metas concorrentes', 'text' => 'Proteção, renda, crescimento e legado disputam capital sem hierarquia definida.'],
    ['title' => 'Decisão fragmentada', 'text' => 'Patrimônio pessoal, empresa, dívidas e reservas andam separados e geram ruído de estratégia.'],
];
$deliverables = [
    ['title' => 'Mapa patrimonial e de liquidez', 'text' => 'Leitura do que precisa ficar líquido, protegido, produtivo ou reorganizado.', 'chips' => ['Reserva e caixa', 'Patrimônio produtivo', 'Risco e concentração']],
    ['title' => 'Tese de alocação e prioridades', 'text' => 'Hipóteses de melhoria para renda, proteção, crescimento e eficiência do capital.', 'chips' => ['Renda recorrente', 'Proteção', 'Sequência de execução']],
    ['title' => 'Plano executivo de próximos passos', 'text' => 'Um caminho claro do que fazer primeiro, depois e o que merece acompanhamento consultivo.', 'chips' => ['Ações imediatas', 'Ganho potencial', 'Acompanhamento']],
];
$contactPhone = !empty($baseSettings->contact_phone) ? preg_replace('/[^0-9+]/', '', (string)$baseSettings->contact_phone) : '';
?>
<main class="gx-marketing gx-wealth">
    <nav class="gx-nav" id="gx-nav">
        <div class="gx-nav-inner">
            <a href="<?= langBaseUrl(); ?>" class="gx-nav-brand" aria-label="GX Capital">
                <img src="<?= getLogo(); ?>" alt="GX Capital" width="<?= getLogoSize('width'); ?>" height="<?= getLogoSize('height'); ?>">
            </a>

            <div class="gx-nav-links" id="gx-nav-links">
                <a href="#diagnostico" class="gx-nav-link">Diagnóstico</a>
                <a href="#metodo" class="gx-nav-link">Método</a>
                <a href="#entregaveis" class="gx-nav-link">Entregáveis</a>
                <a href="#faq" class="gx-nav-link">FAQ</a>
                <div class="gx-nav-menu-extra">
                    <?php if (!empty($isAuthenticated)): ?>
                        <a href="<?= esc($wealthConversationUrl); ?>" class="gx-nav-link" data-wealth-track="wealth_continue_area">Área completa</a>
                    <?php elseif (($generalSettings->registration_system ?? 0) == 1): ?>
                        <a href="#" class="gx-nav-link" data-bs-toggle="modal" data-bs-target="#modalLogin">Entrar</a>
                    <?php else: ?>
                        <a href="<?= esc($blogUrl); ?>" class="gx-nav-link">Blog</a>
                    <?php endif; ?>
                    <a href="#fale-com-especialista" class="gx-btn gx-btn-primary" data-wealth-track="start_signup"><?= esc($primaryCta); ?></a>
                </div>
            </div>

            <div class="gx-nav-right">
                <?php if (!empty($isAuthenticated)): ?>
                    <a href="<?= esc($wealthConversationUrl); ?>" class="gx-nav-link" data-wealth-track="wealth_continue_area">Área completa</a>
                <?php elseif (($generalSettings->registration_system ?? 0) == 1): ?>
                    <a href="#" class="gx-nav-link" data-bs-toggle="modal" data-bs-target="#modalLogin">Entrar</a>
                <?php else: ?>
                    <a href="<?= esc($blogUrl); ?>" class="gx-nav-link">Blog</a>
                <?php endif; ?>
                <a href="#fale-com-especialista" class="gx-btn gx-btn-primary" id="gx-wealth-primary-cta" data-wealth-track="start_signup"><?= esc($primaryCta); ?></a>
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
                    <?= esc($heroBadge); ?>
                </div>

                <h1 class="gx-hero-title"><?= esc($heroTitle); ?></h1>
                <p class="gx-hero-sub"><?= esc($heroSubtitle); ?></p>

                <div class="gx-hero-cta">
                    <a href="#diagnostico" class="gx-btn gx-btn-primary gx-btn-lg" data-wealth-track="start_signup"><?= esc($primaryCta); ?></a>
                    <a href="#metodo" class="gx-btn gx-btn-ghost gx-btn-lg"><?= esc($secondaryCta); ?></a>
                </div>

                <div class="gx-hero-proof">
                    <div class="gx-hero-proof-item">
                        <strong>Diagnóstico em 60s</strong>
                        <span>Uma leitura inicial para entender direção, gap e urgência consultiva.</span>
                    </div>
                    <div class="gx-hero-proof-item">
                        <strong>Visão integrada</strong>
                        <span>Patrimônio, fluxo, liquidez e objetivos tratados como sistema, não como peças soltas.</span>
                    </div>
                    <div class="gx-hero-proof-item">
                        <strong>Plano acionável</strong>
                        <span>Próximos passos objetivos para blindagem, renda, crescimento e legado.</span>
                    </div>
                </div>

                <?php if (!empty($isAuthenticated)): ?>
                    <div class="gx-wealth-auth-note">
                        <strong>Área completa já disponível.</strong>
                        Você pode seguir com o diagnóstico guiado ou abrir seu panorama em
                        <a href="<?= esc($wealthConversationUrl); ?>" data-wealth-track="wealth_continue_area">conversa</a>.
                    </div>
                <?php endif; ?>
            </div>

            <div class="gx-hero-aside" data-gx-reveal data-gx-delay="140">
                <div class="gx-wealth-hero-panel">
                    <div class="gx-wealth-panel-top">
                        <div>
                            <p class="gx-wealth-panel-kicker">Leitura inicial</p>
                            <h2 class="gx-wealth-panel-title">Patrimônio, renda e liquidez em contexto.</h2>
                        </div>
                        <span class="gx-wealth-panel-badge">Consultivo</span>
                    </div>

                    <div class="gx-wealth-mini-grid">
                        <div class="gx-wealth-mini-card">
                            <span>Foco</span>
                            <strong>Blindar, organizar e acelerar o capital</strong>
                        </div>
                        <div class="gx-wealth-mini-card">
                            <span>Saída</span>
                            <strong>Próximo passo claro e priorizado</strong>
                        </div>
                        <div class="gx-wealth-mini-card">
                            <span>Escopo</span>
                            <strong>Fluxo, liquidez, alocação e metas</strong>
                        </div>
                        <div class="gx-wealth-mini-card">
                            <span>Timing</span>
                            <strong>Diagnóstico rápido e retorno consultivo</strong>
                        </div>
                    </div>

                    <?php if (!empty($memberProgress)): ?>
                        <div class="gx-wealth-member-progress">
                            <strong><?= esc((string)$memberProgress['pct']); ?>% do mapeamento concluído</strong>
                            <div class="gx-wealth-progress-bar">
                                <div class="gx-wealth-progress-fill" style="width: <?= esc((string)$memberProgress['pct']); ?>%;"></div>
                            </div>
                            <span class="gx-wealth-insight-caption"><?= esc((string)$memberProgress['score']); ?> de <?= esc((string)$memberProgress['total']); ?> etapas preenchidas na área completa.</span>
                        </div>
                    <?php else: ?>
                        <div class="gx-wealth-path">
                            <div class="gx-wealth-path-step is-active">1. Diagnóstico patrimonial e de liquidez</div>
                            <div class="gx-wealth-path-step">2. Tese de organização e alocação</div>
                            <div class="gx-wealth-path-step">3. Plano executivo com prioridade de ações</div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="gx-hero-stat-row">
                    <div class="gx-hero-metric">
                        <strong>60s</strong>
                        <span>para medir o gap inicial</span>
                    </div>
                    <div class="gx-hero-metric">
                        <strong>3</strong>
                        <span>frentes de decisão patrimonial</span>
                    </div>
                    <div class="gx-hero-metric">
                        <strong>360°</strong>
                        <span>visão integrada do capital</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="gx-strip">
        <div class="gx-strip-inner" data-gx-reveal>
            <span class="gx-strip-lead">Onde a consultoria atua com mais impacto:</span>
            <span class="gx-strip-item">Liquidez e reserva</span>
            <span class="gx-strip-item">Alocação e renda</span>
            <span class="gx-strip-item">Proteção patrimonial</span>
            <span class="gx-strip-item">Prioridade de metas</span>
            <span class="gx-strip-item">Legado e sucessão</span>
        </div>
    </div>

    <section class="gx-section" id="diagnostico">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label">Diagnóstico Rápido</p>
                    <h2 class="gx-section-title">Meça o tamanho do gap entre patrimônio atual e padrão de vida desejado.</h2>
                </div>
                <p class="gx-section-desc">
                    A conta abaixo não substitui uma consultoria, mas expõe rapidamente se o seu patrimônio está organizado para sustentar objetivos, renda e liquidez com eficiência.
                </p>
            </div>

            <div class="gx-wealth-diagnostic-grid">
                <article class="gx-wealth-diagnostic-card" data-gx-reveal>
                    <div class="gx-wealth-objective-grid">
                        <button type="button" class="gx-wealth-objective is-active" data-wealth-objective="Blindar patrimônio e liquidez">Blindar patrimônio e liquidez</button>
                        <button type="button" class="gx-wealth-objective" data-wealth-objective="Gerar mais renda recorrente">Gerar mais renda recorrente</button>
                        <button type="button" class="gx-wealth-objective" data-wealth-objective="Organizar crescimento e alocação">Organizar crescimento e alocação</button>
                        <button type="button" class="gx-wealth-objective" data-wealth-objective="Planejar legado e sucessão">Planejar legado e sucessão</button>
                    </div>

                    <div class="gx-wealth-input-grid">
                        <label class="gx-wealth-field">
                            <span>Patrimônio investido hoje</span>
                            <input type="number" id="gx-wealth-invested" min="0" step="1000" value="250000">
                        </label>

                        <label class="gx-wealth-field">
                            <span>Aporte mensal atual</span>
                            <input type="number" id="gx-wealth-monthly-invest" min="0" step="100" value="5000">
                        </label>

                        <label class="gx-wealth-field gx-wealth-field-full">
                            <span>Custo de vida mensal que o patrimônio deveria sustentar</span>
                            <input type="number" id="gx-wealth-monthly-cost" min="0" step="100" value="18000">
                        </label>
                    </div>

                    <div class="gx-wealth-chip-list">
                        <span class="gx-wealth-chip">Capital alvo usa referência conservadora de retirada</span>
                        <span class="gx-wealth-chip">Projeção considera ritmo constante de aportes</span>
                        <span class="gx-wealth-chip">Leitura inicial para priorização consultiva</span>
                    </div>
                </article>

                <aside class="gx-wealth-insights-card" data-gx-reveal data-gx-delay="100">
                    <p class="gx-label">Leitura Inicial</p>
                    <h3 class="gx-section-title" style="margin-bottom:0;">Seu patrimônio deveria produzir clareza, não ruído.</h3>

                    <div class="gx-wealth-kpi-stack">
                        <div class="gx-wealth-kpi-card">
                            <span>Capital alvo estimado</span>
                            <strong id="gx-wealth-target-capital">R$ 0</strong>
                        </div>
                        <div class="gx-wealth-kpi-card">
                            <span>Projeção em 10 anos</span>
                            <strong id="gx-wealth-projection-10y">R$ 0</strong>
                        </div>
                        <div class="gx-wealth-kpi-card">
                            <span>Cobertura estimada da meta</span>
                            <strong id="gx-wealth-coverage">0,0%</strong>
                        </div>
                        <div class="gx-wealth-kpi-card">
                            <span>Gap patrimonial projetado</span>
                            <strong id="gx-wealth-gap">R$ 0</strong>
                        </div>
                    </div>

                    <p class="gx-wealth-insight-text" id="gx-wealth-insight-text">
                        Informe os dados principais para estimar o capital-alvo e a distância entre o ritmo atual e o padrão de vida desejado.
                    </p>

                    <div style="margin-top:22px;">
                        <a href="#fale-com-especialista" class="gx-btn gx-btn-primary gx-btn-lg" data-wealth-track="start_signup">Receber leitura consultiva</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <div class="gx-divider"></div>

    <section class="gx-section gx-section-alt">
        <div class="gx-wrap">
            <div class="gx-section-header is-centered" data-gx-reveal>
                <p class="gx-label">Sinais de Ineficiência</p>
                <h2 class="gx-section-title">O patrimônio costuma perder eficiência quando a estratégia fica fragmentada.</h2>
                <p class="gx-section-desc" style="margin-left:auto;margin-right:auto;">
                    Esses são os cenários mais comuns em famílias e empresários com patrimônio relevante, mas sem coordenação consultiva contínua.
                </p>
            </div>

            <div class="gx-wealth-feature-grid" data-gx-reveal data-gx-delay="90">
                <?php foreach ($signalCards as $card): ?>
                    <article class="gx-wealth-feature-card">
                        <strong><?= esc($card['title']); ?></strong>
                        <p><?= esc($card['text']); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <div class="gx-divider"></div>

    <section class="gx-section" id="metodo">
        <div class="gx-wrap">
            <div class="gx-section-header is-centered" data-gx-reveal>
                <p class="gx-label">Método Consultivo</p>
                <h2 class="gx-section-title">Como a GX Capital estrutura o diagnóstico patrimonial.</h2>
                <p class="gx-section-desc" style="margin-left:auto;margin-right:auto;">
                    A ideia não é adicionar mais produto ao patrimônio, e sim construir ordem, tese e priorização.
                </p>
            </div>

            <div class="gx-process-grid">
                <article class="gx-process-card" data-gx-reveal>
                    <span class="gx-process-num">01</span>
                    <h3 class="gx-process-title">Mapa de patrimônio, liquidez e fluxo</h3>
                    <p class="gx-process-desc">Entender onde o capital está, que função cada bloco cumpre e quais decisões estão travando eficiência.</p>
                </article>

                <article class="gx-process-card" data-gx-reveal data-gx-delay="100">
                    <span class="gx-process-num">02</span>
                    <h3 class="gx-process-title">Tese de organização e alocação</h3>
                    <p class="gx-process-desc">Definir o que precisa ser protegido, o que deve gerar renda e o que merece assumir risco de crescimento.</p>
                </article>

                <article class="gx-process-card" data-gx-reveal data-gx-delay="180">
                    <span class="gx-process-num">03</span>
                    <h3 class="gx-process-title">Plano executivo de próximos passos</h3>
                    <p class="gx-process-desc">Uma sequência objetiva de ações para ganhar clareza, liquidez, proteção e consistência patrimonial.</p>
                </article>
            </div>
        </div>
    </section>

    <div class="gx-divider"></div>

    <section class="gx-section gx-section-alt" id="entregaveis">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label">Entregáveis</p>
                    <h2 class="gx-section-title">O que destrava quando o patrimônio passa a obedecer uma estratégia.</h2>
                </div>
                <p class="gx-section-desc">
                    O ganho raramente está em um único ativo. Ele aparece quando liquidez, renda, proteção e prioridade passam a conversar.
                </p>
            </div>

            <div class="gx-wealth-deliverables-grid" data-gx-reveal data-gx-delay="80">
                <?php foreach ($deliverables as $item): ?>
                    <article class="gx-wealth-deliverable-card">
                        <h3><?= esc($item['title']); ?></h3>
                        <p><?= esc($item['text']); ?></p>
                        <div class="gx-wealth-chip-list">
                            <?php foreach ($item['chips'] as $chip): ?>
                                <span class="gx-wealth-chip"><?= esc($chip); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if (!empty($isAuthenticated)): ?>
        <div class="gx-divider"></div>

        <section class="gx-section">
            <div class="gx-wrap">
                <div class="gx-cta-block" data-gx-reveal>
                    <div class="gx-cta-content">
                        <p class="gx-label">Área Completa</p>
                        <h2 class="gx-section-title">Continue a análise detalhada com o mapeamento guiado.</h2>
                        <p class="gx-section-desc" style="margin-left:auto;margin-right:auto;">
                            Se você já tem acesso, avance pelo diagnóstico estruturado e abra o panorama consolidado do patrimônio.
                        </p>
                        <div class="gx-cta-actions">
                            <a href="<?= esc($wealthConversationUrl); ?>" class="gx-btn gx-btn-primary gx-btn-lg" data-wealth-track="wealth_continue_area">Continuar diagnóstico</a>
                            <a href="<?= esc($wealthResultsUrl); ?>" class="gx-btn gx-btn-ghost gx-btn-lg">Ver meu panorama</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <div class="gx-divider"></div>

    <section class="gx-section" id="faq">
        <div class="gx-wrap">
            <div class="gx-section-header is-centered" data-gx-reveal>
                <p class="gx-label">FAQ</p>
                <h2 class="gx-section-title">Perguntas recorrentes antes do primeiro diagnóstico.</h2>
                <p class="gx-section-desc" style="margin-left:auto;margin-right:auto;">
                    O objetivo é reduzir fricção, qualificar o contexto e tornar a conversa seguinte objetiva.
                </p>
            </div>

            <div class="gx-wealth-faq-list" data-gx-reveal data-gx-delay="80">
                <?php foreach ($faqItems as $index => $item): ?>
                    <details class="gx-wealth-faq-item" <?= $index === 0 ? 'open' : ''; ?>>
                        <summary><?= esc($item['q'] ?? ''); ?></summary>
                        <p><?= esc($item['a'] ?? ''); ?></p>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="gx-lead-section" id="fale-com-especialista">
        <div class="gx-wrap">
            <div class="gx-lead-grid">
                <aside class="gx-lead-aside" data-gx-reveal>
                    <div class="gx-aside-icon" aria-hidden="true">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="rgba(199,160,83,0.6)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h10"/><rect x="3" y="4" width="18" height="16" rx="2"/></svg>
                    </div>
                    <p class="gx-label">Próximo Passo</p>
                    <h2 class="gx-section-title">Conecte o diagnóstico rápido a uma leitura consultiva.</h2>
                    <p class="gx-section-desc" style="color:rgba(255,255,255,0.62);">
                        Informe o contexto principal. A equipe retorna com uma leitura inicial e possíveis próximos movimentos para organizar o patrimônio.
                    </p>

                    <div class="gx-contact-list">
                        <span class="gx-contact-chip">Diagnóstico inicial rápido</span>
                        <span class="gx-contact-chip">Contato consultivo</span>
                        <span class="gx-contact-chip">Sem compromisso</span>
                        <?php if (!empty($baseSettings->contact_phone)): ?>
                            <a href="<?= !empty($contactPhone) ? 'tel:' . esc($contactPhone) : '#'; ?>" class="gx-contact-chip">
                                <?= esc($baseSettings->contact_phone); ?>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($baseSettings->contact_email)): ?>
                            <a href="mailto:<?= esc($baseSettings->contact_email); ?>" class="gx-contact-chip">
                                <?= esc($baseSettings->contact_email); ?>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($isAuthenticated)): ?>
                            <a href="<?= esc($wealthConversationUrl); ?>" class="gx-contact-chip" data-wealth-track="wealth_continue_area">Abrir área completa</a>
                        <?php endif; ?>
                    </div>
                </aside>

                <div class="gx-lead-card" data-gx-reveal data-gx-delay="100">
                    <?= view('wealth/_lead_form', [
                        'formId' => 'gx-wealth-lead-form',
                        'sourcePage' => 'landing',
                        'formTitle' => $leadFormTitle,
                        'formDescription' => $leadFormDescription,
                        'submitLabel' => $leadFormButton,
                        'activeLang' => $activeLang,
                        'blogUrl' => $blogUrl,
                    ]); ?>
                </div>
            </div>
        </div>
    </section>
</main>

<div class="gx-wealth-sticky-cta">
    <a href="#fale-com-especialista" class="gx-btn gx-btn-primary" data-wealth-track="start_signup"><?= esc($primaryCta); ?></a>
</div>

<?= view('wealth/_scripts'); ?>
