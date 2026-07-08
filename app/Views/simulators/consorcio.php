<?php
$contactChannels = $contactChannels ?? [];
$contactPhone = trim((string)($contactChannels['phone'] ?? (!empty($baseSettings->contact_phone) ? $baseSettings->contact_phone : '')));
$contactPhoneHref = !empty($contactChannels['phone_href']) ? preg_replace('/^tel:/', '', (string)$contactChannels['phone_href']) : (!empty($contactPhone) ? preg_replace('/[^0-9+]/', '', (string)$contactPhone) : '');
$contactEmail = trim((string)($contactChannels['email'] ?? (!empty($baseSettings->contact_email) ? $baseSettings->contact_email : '')));
$whatsAppUrl = $whatsAppUrl ?? '';
$whatsAppBaseUrl = $whatsAppBaseUrl ?? '';
$whatsAppDefaultMessage = $whatsAppDefaultMessage ?? '';
$whatsAppIcon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20.52 3.48A11.8 11.8 0 0 0 12.08 0C5.55 0 .24 5.31.24 11.84c0 2.08.54 4.11 1.58 5.89L0 24l6.46-1.69a11.8 11.8 0 0 0 5.62 1.43h.01c6.53 0 11.84-5.31 11.84-11.84 0-3.16-1.23-6.13-3.41-8.42Zm-8.44 18.26h-.01a9.84 9.84 0 0 1-5.01-1.37l-.36-.22-3.84 1 1.03-3.74-.24-.38a9.8 9.8 0 0 1-1.51-5.2C2.14 6.42 6.66 1.9 12.08 1.9c2.63 0 5.1 1.02 6.96 2.88a9.78 9.78 0 0 1 2.89 6.97c0 5.42-4.42 9.99-9.85 9.99Zm5.39-7.41c-.29-.14-1.71-.84-1.98-.94-.26-.1-.45-.14-.64.14-.19.29-.74.94-.91 1.13-.17.19-.34.22-.63.07-.29-.14-1.21-.45-2.31-1.45-.85-.76-1.42-1.69-1.59-1.98-.17-.29-.02-.44.13-.58.13-.13.29-.34.43-.5.14-.17.19-.29.29-.48.1-.19.05-.36-.02-.5-.07-.14-.64-1.55-.87-2.12-.23-.55-.47-.48-.64-.49h-.55c-.19 0-.5.07-.76.36-.26.29-.99.97-.99 2.37s1.01 2.75 1.15 2.94c.14.19 1.98 3.03 4.79 4.25.67.29 1.2.47 1.61.6.68.22 1.3.19 1.79.12.55-.08 1.71-.7 1.95-1.37.24-.67.24-1.24.17-1.37-.07-.12-.26-.19-.55-.34Z"/></svg>';
$termsHref = !empty($termsUrl) ? $termsUrl : '#';
$navLinks = [
    ['label' => 'A Mesa', 'href' => '#quem-somos'],
    ['label' => 'Estratégias', 'href' => '#estrategias'],
    ['label' => 'Simulador', 'href' => '#simulador'],
    ['label' => 'Tecnologia', 'href' => '#tecnologia-ia'],
    ['label' => 'Especialista', 'href' => '#fale-especialista'],
];
$heroProof = [
    [
        'title' => '20+ administradoras',
        'text' => 'Você não fica preso a uma prateleira. Comparamos as melhores opções do mercado.',
    ],
    [
        'title' => '1.000+ grupos analisados',
        'text' => 'IA cruza prazo, taxa e assembleia para achar o grupo certo para o seu perfil.',
    ],
    [
        'title' => 'Resultado em minutos',
        'text' => 'Diagnóstico imediato com números reais para decidir com segurança.',
    ],
];
$heroSignals = [
    'Veja a parcela real antes de assinar qualquer proposta.',
    'Entenda quanto de lance você precisa para contemplar no prazo desejado.',
    'Compare o custo total do consórcio com o financiamento lado a lado.',
];
$scenarioCards = [
    'compare' => [
        'mark' => 'CF',
        'eyebrow' => 'Mais popular',
        'title' => 'Consórcio x financiamento',
        'description' => 'Já tem entrada? Veja em números se o consórcio reduz a parcela mensal e o custo total comparado ao financiamento.',
        'bullets' => [
            'Descubra a diferença real de parcela entre consórcio e financiamento.',
            'Veja quanto sua entrada acelera a contemplação como lance.',
            'Saiba exatamente quanto você economiza no desembolso total.',
        ],
        'button' => 'Comparar agora',
    ],
    'planned' => [
        'mark' => 'PLN',
        'eyebrow' => 'Compra inteligente',
        'title' => 'Comprar imóvel de forma planejada',
        'description' => 'Quer comprar sem apertar o caixa? Veja se seu ritmo mensal sustenta a contemplação no prazo que você precisa.',
        'bullets' => [
            'Saiba quanto de reserva você terá para dar lance no mês certo.',
            'Veja se parcela + reserva cabem no seu orçamento confortável.',
            'Descubra se sobra ou aperta antes de se comprometer.',
        ],
        'button' => 'Planejar minha compra',
    ],
    'investor' => [
        'mark' => 'INV',
        'eyebrow' => 'Alta rentabilidade',
        'title' => 'Investir para revender após contemplação',
        'description' => 'Use o consórcio como alavanca patrimonial. Veja se a margem de revenda compensa o capital investido.',
        'bullets' => [
            'Calcule o capital total que você precisa mobilizar até a venda.',
            'Veja a margem líquida real depois de todos os custos de saída.',
            'Descubra o ROI da operação sobre o seu dinheiro travado.',
        ],
        'button' => 'Calcular retorno',
    ],
];
$aiSteps = [
    [
        'step' => '01',
        'title' => 'IA filtra os melhores grupos para você',
        'description' => 'Em vez de pesquisar dezenas de opções, a inteligência artificial elimina os grupos incompatíveis e destaca os que mais se encaixam no seu objetivo.',
        'items' => [
            'Prazos e taxas compatíveis com o valor que você precisa.',
            'Grupos com histórico favorável de contemplação.',
            'Alinhamento com seu objetivo: compra, revenda ou comparativo.',
        ],
    ],
    [
        'step' => '02',
        'title' => 'Monta o plano de contemplação sob medida',
        'description' => 'A partir do seu caixa, ritmo mensal e prazo desejado, a IA calcula a melhor estratégia de lance para contemplar com o menor custo possível.',
        'items' => [
            'Quanto dar de lance para contemplar no prazo certo.',
            'Impacto real da parcela no seu fluxo mensal.',
            'Ajustes para acelerar a contemplação ou proteger caixa.',
        ],
    ],
    [
        'step' => '03',
        'title' => 'Especialista valida e você decide com segurança',
        'description' => 'O plano gerado pela IA passa pela análise do especialista, que confere administradora, grupo e timing antes de você dar o próximo passo.',
        'items' => [
            'Plano de contemplação claro, sem jargão técnico.',
            'Riscos mapeados e alternativas para cada cenário.',
            'Você decide com todas as informações na mesa.',
        ],
    ],
];
?>
<main class="gx-marketing gx-consorcio" data-gx-consorcio-page>
    <nav class="gx-nav" id="gx-nav">
        <div class="gx-nav-inner">
            <a href="<?= langBaseUrl(); ?>" class="gx-nav-brand" aria-label="GX Capital">
                <img src="<?= getLogo(); ?>" alt="GX Capital" width="<?= getLogoSize('width'); ?>" height="<?= getLogoSize('height'); ?>">
            </a>

            <div class="gx-nav-links" id="gx-nav-links">
                <?php foreach ($navLinks as $item): ?>
                    <a href="<?= esc($item['href']); ?>" class="gx-nav-link"><?= esc($item['label']); ?></a>
                <?php endforeach; ?>
                <div class="gx-nav-menu-extra">
                    <a href="<?= esc($blogUrl); ?>" class="gx-nav-link">Blog</a>
                    <a href="#fale-especialista" class="gx-btn gx-btn-primary">Receber meu plano</a>
                </div>
            </div>

            <div class="gx-nav-right">
                <a href="<?= esc($blogUrl); ?>" class="gx-nav-link">Blog</a>
                <a href="#fale-especialista" class="gx-btn gx-btn-primary">Receber meu plano</a>
                <button type="button" class="gx-nav-toggle" id="gx-nav-toggle" aria-expanded="false" aria-controls="gx-nav-links" aria-label="Menu">
                    <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
            </div>
        </div>
    </nav>

    <section class="gx-consorcio-hero" id="visao-geral">
        <div class="gx-wrap">
            <div class="gx-consorcio-hero-grid">
                <div data-gx-reveal>
                    <div class="gx-consorcio-badge">Exclusivo GX Capital &mdash; simulador com inteligência artificial</div>
                    <h1 class="gx-consorcio-title">Consórcio ou financiamento? Simule e descubra.</h1>
                    <p class="gx-consorcio-copy">
                        A GX Capital cruza 20+ administradoras com IA para encontrar a rota de contemplação mais rápida para o seu caso.
                    </p>
                    <div class="gx-consorcio-actions">
                        <a href="#simulador" class="gx-btn gx-btn-primary gx-btn-lg">Simular agora &mdash; grátis</a>
                        <p class="gx-consorcio-reassurance">Gratuito &bull; Sem compromisso &bull; Resultado em minutos</p>
                    </div>
                    <div class="gx-consorcio-proof-grid">
                        <?php foreach ($heroProof as $item): ?>
                            <div class="gx-consorcio-proof-item">
                                <strong><?= esc($item['title']); ?></strong>
                                <span><?= esc($item['text']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <aside class="gx-consorcio-hero-card gx-mini-sim" data-gx-reveal data-gx-delay="120">
                    <div class="gx-consorcio-hero-card-top">
                        <span>Simulação rápida</span>
                        <span>GX Capital</span>
                    </div>
                    <div class="gx-mini-sim-body">
                        <div class="gx-mini-sim-field">
                            <label for="gx-mini-sim-credit">Quanto você quer de carta de crédito?</label>
                            <output id="gx-mini-sim-credit-value" class="gx-mini-sim-output">R$ 450.000</output>
                            <input type="range" id="gx-mini-sim-credit" min="50000" max="1500000" step="50000" value="450000" class="gx-mini-sim-range">
                            <div class="gx-mini-sim-range-labels"><span>R$ 50 mil</span><span>R$ 1,5 mi</span></div>
                        </div>
                        <div class="gx-mini-sim-field">
                            <label for="gx-mini-sim-months">Em quanto tempo quer ser contemplado?</label>
                            <output id="gx-mini-sim-months-value" class="gx-mini-sim-output">18 meses</output>
                            <input type="range" id="gx-mini-sim-months" min="12" max="200" step="1" value="18" class="gx-mini-sim-range">
                            <div class="gx-mini-sim-range-labels"><span>12 meses</span><span>200 meses</span></div>
                        </div>
                        <button type="button" id="gx-mini-sim-btn" class="gx-btn gx-btn-primary gx-btn-lg gx-mini-sim-btn">Ver minha economia estimada &rarr;</button>
                    </div>
                    <div id="gx-mini-sim-result" class="gx-mini-sim-result" hidden>
                        <strong id="gx-mini-sim-result-text">Você pode economizar até R$ 0</strong>
                        <p>em relação ao financiamento tradicional.</p>
                        <a href="#fale-especialista" class="gx-text-link">Quer o plano detalhado? &rarr;</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="gx-consorcio-quick-lead" id="quick-lead" data-gx-reveal>
        <div class="gx-wrap">
            <div class="gx-quick-lead-card">
                <div class="gx-quick-lead-text">
                    <h2>Prefere que um especialista simule para você?</h2>
                    <p>Deixe seu WhatsApp e receba um plano personalizado em até 24h. Sem compromisso.</p>
                </div>
                <form id="gx-quick-lead-form" class="gx-quick-lead-form" novalidate>
                    <input type="hidden" name="lead_origin" value="Simulador de Consórcio - Quick Form Hero">
                    <input type="hidden" name="landing_page" value="<?= esc(current_url()); ?>">
                    <input type="hidden" name="sim_data" value="">
                    <input type="hidden" name="meta_content_name" value="Simulador de Consórcio - Quick Form Hero">
                    <input type="hidden" name="meta_content_category" value="Consórcio Estruturado">
                    <input type="hidden" name="meta_currency" value="BRL">
                    <input type="hidden" name="meta_value" value="">
                    <div class="gx-quick-lead-fields">
                        <input type="text" name="name" placeholder="Seu nome" maxlength="199" autocomplete="name" required class="gx-quick-lead-input">
                        <input type="tel" name="phone" placeholder="(00) 00000-0000" maxlength="16" autocomplete="tel" inputmode="numeric" required class="gx-quick-lead-input" data-gx-quick-phone>
                        <input type="hidden" name="phone_country" value="BR">
                        <button type="submit" class="gx-btn gx-btn-whatsapp gx-btn-lg gx-quick-lead-btn">Quero meu plano gratuito</button>
                    </div>
                    <p class="gx-quick-lead-trust">&#128274; Seus dados estão seguros. Sem spam.</p>
                    <div id="gx-quick-lead-status" class="gx-consorcio-lead-status" aria-live="polite" hidden></div>
                </form>
            </div>
        </div>
    </section>

    <div class="gx-strip">
        <div class="gx-strip-inner" data-gx-reveal>
            <span class="gx-strip-lead">4 passos</span>
            <span class="gx-strip-item">Escolha seu objetivo</span>
            <span class="gx-strip-item">Ajuste os números</span>
            <span class="gx-strip-item">Veja o resultado ao vivo</span>
            <span class="gx-strip-item">Receba o plano do especialista</span>
        </div>
    </div>

    <section class="gx-section" id="quem-somos">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label">Quem está por trás do simulador</p>
                    <h2 class="gx-section-title">Consórcio com estratégia de contemplação, não com parcela genérica.</h2>
                </div>
                <p class="gx-section-desc">
                    A GX Capital é uma boutique financeira fundada por <strong>Vinicius Teixeira</strong>, com mais de 16 anos de atuação em mercado financeiro. A frente de consórcio nasceu da mesma lógica que a mesa de câmbio e crédito: comparar dezenas de opções, filtrar com tecnologia e recomendar a estrutura mais eficiente para cada perfil.
                </p>
            </div>

            <div class="gx-grid-3 gx-consorcio-authority-grid" data-gx-reveal data-gx-delay="80">
                <article class="gx-card">
                    <div class="gx-card-icon">CF</div>
                    <p class="gx-card-label">Comparativo estruturado</p>
                    <h3 class="gx-card-title">Consórcio x financiamento com números reais, não com propaganda.</h3>
                    <p class="gx-card-desc">A decisão entre consórcio e financiamento depende de entrada disponível, custo total, prazo e capacidade de lance. O simulador coloca os dois lado a lado para que a conta fale por si.</p>
                </article>
                <article class="gx-card">
                    <div class="gx-card-icon">PLN</div>
                    <p class="gx-card-label">Planejamento de contemplação</p>
                    <h3 class="gx-card-title">Rota para contemplar no prazo certo, com o grupo certo.</h3>
                    <p class="gx-card-desc">A IA cruza mais de 20 administradoras e 1.000+ grupos para encontrar a combinação de taxa, assembleia e estratégia de lance mais aderente ao seu fluxo de caixa e objetivo.</p>
                </article>
                <article class="gx-card">
                    <div class="gx-card-icon">INV</div>
                    <p class="gx-card-label">Tese de investimento</p>
                    <h3 class="gx-card-title">Consórcio como veículo de retorno, não só de compra.</h3>
                    <p class="gx-card-desc">Para quem quer contemplar e revender com margem, o simulador projeta ROI líquido, custo de carregamento e break-even. O especialista valida a tese antes de você entrar no grupo.</p>
                </article>
            </div>

            <div class="gx-fx-boutique-note" data-gx-reveal data-gx-delay="140">
                <p>
                    <strong>Modelo independente:</strong> a GX Capital não é administradora de consórcio e não tem cota própria para vender. Isso permite recomendar o grupo, a administradora e a estratégia de lance mais eficiente para o seu caso, sem viés comercial. Os simuladores abaixo são o primeiro passo para dimensionar a operação antes de falar com o especialista.
                </p>
            </div>
        </div>
    </section>

    <?php
    $testimonialsCfg = $consorcioConfig['testimonials'] ?? [];
    $testimonialsEnabled = !empty($testimonialsCfg['enabled']);
    $testimonialsItems = array_values(array_filter($testimonialsCfg['items'] ?? [], function ($t) {
        return !empty($t['enabled']) && (!empty($t['name']) || !empty($t['text']));
    }));
    $testimonialsItems = array_slice($testimonialsItems, 0, 3);
    ?>
    <?php if ($testimonialsEnabled && !empty($testimonialsItems)): ?>
    <section class="gx-section gx-section-alt gx-consorcio-testimonials" id="depoimentos">
        <div class="gx-wrap">
            <div class="gx-section-header is-centered" data-gx-reveal>
                <p class="gx-label"><?= esc($testimonialsCfg['label'] ?? 'Quem já simulou'); ?></p>
                <h2 class="gx-section-title"><?= esc($testimonialsCfg['title'] ?? 'Quem já simulou, aprovou'); ?></h2>
            </div>
            <div class="gx-testimonial-grid" data-gx-reveal data-gx-delay="80">
                <?php foreach ($testimonialsItems as $t): ?>
                    <article class="gx-testimonial-card">
                        <?php if (!empty($t['photo_url'])): ?>
                            <div class="gx-testimonial-avatar gx-testimonial-avatar-photo" style="background-image:url('<?= esc(base_url($t['photo_url'])); ?>');" aria-hidden="true"></div>
                        <?php else: ?>
                            <div class="gx-testimonial-avatar" aria-hidden="true"></div>
                        <?php endif; ?>
                        <div class="gx-testimonial-stars" aria-label="5 estrelas">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                        <blockquote><?= esc($t['text'] ?? ''); ?></blockquote>
                        <footer><strong><?= esc($t['name'] ?? ''); ?></strong> <?php if (!empty($t['city'])): ?><span><?= esc($t['city']); ?></span><?php endif; ?></footer>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <div class="gx-divider"></div>

    <section class="gx-section" id="estrategias">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label">Escolha sua jornada</p>
                    <h2 class="gx-section-title">Qual é o seu objetivo com o consórcio? Comece pelo cenário certo.</h2>
                </div>
                <p class="gx-section-desc">
                    Cada objetivo pede uma conta diferente. Escolha o cenário que mais se parece com o seu momento
                    e veja os números que realmente importam para a sua decisão.
                </p>
            </div>

            <div class="gx-consorcio-track-grid" data-gx-reveal data-gx-delay="100">
                <?php foreach ($scenarioCards as $key => $card): ?>
                    <article class="gx-consorcio-track-card<?= $key === 'compare' ? ' is-active' : ''; ?>" data-gx-scenario-card="<?= esc($key); ?>">
                        <div class="gx-simulator-top">
                            <span class="gx-simulator-mark"><?= esc($card['mark']); ?></span>
                            <span class="gx-legacy-pill">Simulação gratuita</span>
                        </div>
                        <p class="gx-card-kicker"><?= esc($card['eyebrow']); ?></p>
                        <h3 class="gx-simulator-title"><?= esc($card['title']); ?></h3>
                        <p class="gx-simulator-meta"><?= esc($card['description']); ?></p>
                        <ul>
                            <?php foreach ($card['bullets'] as $bullet): ?>
                                <li><?= esc($bullet); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="gx-consorcio-card-action">
                            <button type="button" class="gx-text-link" data-gx-scenario-jump="<?= esc($key); ?>"><?= esc($card['button']); ?></button>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="gx-section gx-section-alt" id="simulador">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label">Simulador ao vivo</p>
                    <h2 class="gx-section-title">Ajuste os números do seu caso e veja o resultado mudar em tempo real.</h2>
                </div>
                <p class="gx-section-desc">
                    Mexa nos campos abaixo e acompanhe o impacto na parcela, no lance e no custo total. Quando estiver satisfeito,
                    envie a simulação e receba o plano de contemplação do especialista.
                </p>
            </div>

            <div class="gx-consorcio-workbench">
                <div class="gx-consorcio-main-card" data-gx-reveal data-gx-delay="80">
                    <div class="gx-consorcio-switcher" role="tablist" aria-label="Escolha a jornada do simulador">
                        <?php foreach ($scenarioCards as $key => $card): ?>
                            <button
                                type="button"
                                class="gx-consorcio-switch<?= $key === 'compare' ? ' is-active' : ''; ?>"
                                data-gx-scenario-trigger="<?= esc($key); ?>"
                                role="tab"
                                aria-selected="<?= $key === 'compare' ? 'true' : 'false'; ?>">
                                <strong><?= esc($card['title']); ?></strong>
                                <span><?= esc($card['eyebrow']); ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="gx-consorcio-info-bar">
                        <strong>Os resultados atualizam ao vivo.</strong>
                        <span>Mude qualquer campo e veja o impacto na parcela, no custo total e na viabilidade da contemplação.</span>
                    </div>

                    <form class="gx-consorcio-form-grid" id="gx-consorcio-simulator-form" novalidate>
                        <section class="gx-consorcio-fieldset">
                            <div class="gx-consorcio-fieldset-head">
                                <h3>Dados da cota</h3>
                                <p>Informe o valor do bem e as condições do grupo. Esses campos valem para qualquer cenário.</p>
                            </div>
                            <div class="gx-consorcio-field-grid">
                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Valor da carta de crédito</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix">R$</span>
                                        <input type="number" name="credit_value" min="50000" step="1000" value="450000" inputmode="decimal">
                                    </div>
                                    <small>Use o valor do imóvel ou do ativo que pretende adquirir.</small>
                                </label>

                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Prazo do grupo</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix is-empty" aria-hidden="true"></span>
                                        <input type="number" name="term_months" min="24" max="240" step="1" value="180" inputmode="numeric">
                                        <span class="gx-consorcio-input-suffix">meses</span>
                                    </div>
                                    <small>Prazo total de pagamento da cota.</small>
                                </label>

                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Taxa administrativa</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix is-empty" aria-hidden="true"></span>
                                        <input type="number" name="admin_fee_pct" min="0" max="30" step="0.1" value="18" inputmode="decimal">
                                        <span class="gx-consorcio-input-suffix">%</span>
                                    </div>
                                    <small>Percentual total de taxa administrativa considerado na estrutura.</small>
                                </label>

                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Fundo de reserva</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix is-empty" aria-hidden="true"></span>
                                        <input type="number" name="reserve_fee_pct" min="0" max="15" step="0.1" value="2" inputmode="decimal">
                                        <span class="gx-consorcio-input-suffix">%</span>
                                    </div>
                                    <small>Percentual adicional para reserva e composição do custo.</small>
                                </label>

                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Meta de contemplação</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix is-empty" aria-hidden="true"></span>
                                        <input type="number" name="target_month" min="1" max="120" step="1" value="18" inputmode="numeric">
                                        <span class="gx-consorcio-input-suffix">mês</span>
                                    </div>
                                    <small>Janela que você deseja mirar para a contemplação.</small>
                                </label>
                            </div>
                        </section>

                        <section class="gx-consorcio-fieldset" data-gx-scenario-panel="compare">
                            <div class="gx-consorcio-fieldset-head">
                                <h3>Compare com o financiamento</h3>
                                <p>Informe quanto você tem de entrada e as condições do financiamento para ver a diferença real.</p>
                            </div>
                            <div class="gx-consorcio-field-grid">
                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Recurso disponível para entrada/lance</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix">R$</span>
                                        <input type="number" name="entry_value" min="0" step="1000" value="90000" inputmode="decimal">
                                    </div>
                                    <small>Valor que você já teria para usar no início da operação.</small>
                                </label>

                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Taxa anual do financiamento</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix is-empty" aria-hidden="true"></span>
                                        <input type="number" name="financing_rate_annual" min="0" max="40" step="0.1" value="11.5" inputmode="decimal">
                                        <span class="gx-consorcio-input-suffix">% a.a.</span>
                                    </div>
                                    <small>Taxa nominal usada para o comparativo da linha de crédito tradicional.</small>
                                </label>

                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Prazo do financiamento</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix is-empty" aria-hidden="true"></span>
                                        <input type="number" name="financing_term_months" min="12" max="420" step="1" value="360" inputmode="numeric">
                                        <span class="gx-consorcio-input-suffix">meses</span>
                                    </div>
                                    <small>Prazo total do contrato de financiamento usado como referência.</small>
                                </label>

                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Custos iniciais do financiamento</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix is-empty" aria-hidden="true"></span>
                                        <input type="number" name="financing_fee_pct" min="0" max="10" step="0.1" value="3" inputmode="decimal">
                                        <span class="gx-consorcio-input-suffix">%</span>
                                    </div>
                                    <small>Inclua tarifas, seguros embutidos ou custos acessórios de contratação.</small>
                                </label>
                            </div>
                        </section>

                        <section class="gx-consorcio-fieldset" data-gx-scenario-panel="planned" hidden>
                            <div class="gx-consorcio-fieldset-head">
                                <h3>Planeje sua compra</h3>
                                <p>Informe quanto consegue guardar por mês e veja se dá para contemplar no prazo que você precisa.</p>
                            </div>
                            <div class="gx-consorcio-field-grid">
                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Reserva disponível hoje</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix">R$</span>
                                        <input type="number" name="available_bid" min="0" step="1000" value="60000" inputmode="decimal">
                                    </div>
                                    <small>Valor já disponível para compor a estratégia de lance.</small>
                                </label>

                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Reserva mensal para lance</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix">R$</span>
                                        <input type="number" name="monthly_reserve" min="0" step="100" value="3500" inputmode="decimal">
                                    </div>
                                    <small>Quanto você consegue acumular por mês além da parcela.</small>
                                </label>

                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Orçamento mensal confortável</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix">R$</span>
                                        <input type="number" name="monthly_budget" min="0" step="100" value="7000" inputmode="decimal">
                                    </div>
                                    <small>Teto que você deseja respeitar somando parcela e formação de reserva.</small>
                                </label>

                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Correção anual esperada do ativo</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix is-empty" aria-hidden="true"></span>
                                        <input type="number" name="expected_correction_annual" min="0" max="20" step="0.1" value="5" inputmode="decimal">
                                        <span class="gx-consorcio-input-suffix">% a.a.</span>
                                    </div>
                                    <small>Serve para ajustar o valor de referência do imóvel até a contemplação.</small>
                                </label>
                            </div>
                        </section>

                        <section class="gx-consorcio-fieldset" data-gx-scenario-panel="investor" hidden>
                            <div class="gx-consorcio-fieldset-head">
                                <h3>Calcule o retorno da revenda</h3>
                                <p>Informe o capital disponível e a margem esperada para ver se a operação vale o investimento.</p>
                            </div>
                            <div class="gx-consorcio-field-grid is-wide">
                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Caixa para lance próprio</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix">R$</span>
                                        <input type="number" name="bid_cash" min="0" step="1000" value="120000" inputmode="decimal">
                                    </div>
                                    <small>Capital que você pretende usar para acelerar a contemplação.</small>
                                </label>

                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Margem esperada na revenda</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix is-empty" aria-hidden="true"></span>
                                        <input type="number" name="resale_margin_pct" min="0" max="50" step="0.1" value="18" inputmode="decimal">
                                        <span class="gx-consorcio-input-suffix">%</span>
                                    </div>
                                    <small>Margem bruta estimada entre aquisição e revenda.</small>
                                </label>

                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Custos de saída e transação</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix is-empty" aria-hidden="true"></span>
                                        <input type="number" name="sale_cost_pct" min="0" max="15" step="0.1" value="6" inputmode="decimal">
                                        <span class="gx-consorcio-input-suffix">%</span>
                                    </div>
                                    <small>Inclua corretagem, impostos, documentação e atritos da operação.</small>
                                </label>

                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Meses até a revenda após contemplação</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix is-empty" aria-hidden="true"></span>
                                        <input type="number" name="holding_months" min="1" max="60" step="1" value="8" inputmode="numeric">
                                        <span class="gx-consorcio-input-suffix">meses</span>
                                    </div>
                                    <small>Horizonte entre contemplação, aquisição e venda do ativo.</small>
                                </label>

                                <label class="gx-consorcio-field">
                                    <span class="gx-consorcio-field-label">Custo mensal de carregamento</span>
                                    <div class="gx-consorcio-input-shell">
                                        <span class="gx-consorcio-input-prefix">R$</span>
                                        <input type="number" name="holding_cost_monthly" min="0" step="100" value="900" inputmode="decimal">
                                    </div>
                                    <small>Condomínio, manutenção, carência de aluguel ou outro custo de carregamento.</small>
                                </label>
                            </div>
                        </section>
                    </form>
                </div>

                <div class="gx-consorcio-side">
                    <aside class="gx-consorcio-results-card" data-gx-reveal data-gx-delay="110" aria-live="polite">
                        <div class="gx-consorcio-results-head">
                            <div>
                                <p class="gx-card-kicker" id="gx-consorcio-result-kicker">Comparativo estratégico</p>
                                <h3 id="gx-consorcio-result-headline">Use a sua entrada como lance e compare o consórcio com o financiamento.</h3>
                                <p id="gx-consorcio-result-body">Ajuste os campos do seu caso para acompanhar parcela indicativa, custo total e nível de esforço até a contemplação.</p>
                            </div>
                            <span class="gx-consorcio-status-chip">Ao vivo</span>
                        </div>

                        <div class="gx-consorcio-kpi-grid">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <article class="gx-consorcio-kpi">
                                    <span id="gx-consorcio-kpi-label-<?= $i; ?>">Indicador</span>
                                    <strong id="gx-consorcio-kpi-value-<?= $i; ?>">-</strong>
                                    <p id="gx-consorcio-kpi-copy-<?= $i; ?>">-</p>
                                </article>
                            <?php endfor; ?>
                        </div>

                        <div class="gx-consorcio-band">
                            <article class="gx-consorcio-band-card">
                                <span id="gx-consorcio-band-label-a">Estratégia</span>
                                <strong id="gx-consorcio-band-value-a">-</strong>
                                <p id="gx-consorcio-band-copy-a">-</p>
                            </article>
                            <article class="gx-consorcio-band-card">
                                <span id="gx-consorcio-band-label-b">Leitura GX</span>
                                <strong id="gx-consorcio-band-value-b">-</strong>
                                <p id="gx-consorcio-band-copy-b">-</p>
                            </article>
                        </div>

                        <div class="gx-consorcio-insights">
                            <h4>Leitura executiva inicial</h4>
                            <ul class="gx-consorcio-insight-list">
                                <li id="gx-consorcio-insight-1">-</li>
                                <li id="gx-consorcio-insight-2">-</li>
                                <li id="gx-consorcio-insight-3">-</li>
                            </ul>
                        </div>

                        <p class="gx-consorcio-disclaimer">
                            Simulação indicativa. A recomendação final depende de grupo, administradora, histórico de assembleia e estratégia de lance disponível no momento da contratação.
                        </p>
                    </aside>

                    <aside class="gx-lead-card gx-consorcio-lead-card" id="fale-especialista" data-gx-reveal data-gx-delay="160">
                        <div class="gx-form-intro">
                            <p class="gx-label">Último passo</p>
                            <h2 class="gx-form-title">Transforme essa simulação em um plano real de contemplação.</h2>
                            <p class="gx-form-copy">Envie seus dados e um especialista vai analisar seu cenário, selecionar os melhores grupos e montar a estratégia de lance para o seu caso.</p>
                        </div>

                        <?php if (!empty($whatsAppUrl)): ?>
                            <div class="gx-contact-highlight">
                                <span class="gx-fx-live-eyebrow">Resposta rápida</span>
                                <strong>Quer falar agora com o especialista?</strong>
                                <p>A conversa já começa com os dados do seu cenário. Sem precisar repetir informações.</p>
                                <div class="gx-contact-cta-grid">
                                    <a href="<?= esc($whatsAppUrl); ?>" target="_blank" rel="noopener" class="gx-btn gx-btn-whatsapp" data-gx-whatsapp-link><?= $whatsAppIcon; ?>Falar pelo WhatsApp</a>
                                    <a href="#gx-consorcio-lead-form" class="gx-btn gx-btn-primary">Enviar simulação</a>
                                </div>
                                <p class="gx-contact-note" data-gx-whatsapp-copy>Atendimento em horário comercial com especialista dedicado.</p>
                            </div>
                        <?php endif; ?>

                        <div class="gx-consorcio-context-box">
                            <span>Cenário atual</span>
                            <strong id="gx-consorcio-lead-scenario">Consórcio x financiamento</strong>
                            <p id="gx-consorcio-lead-hook">Vamos comparar sua entrada, o custo mensal e o desembolso total antes da decisão.</p>
                        </div>

                        <ul class="gx-consorcio-promise-list">
                            <li>Você recebe a seleção dos melhores grupos para o seu objetivo e perfil.</li>
                            <li>Saiba exatamente quanto dar de lance e quanto pagar por mês sem apertar.</li>
                            <li>Plano de contemplação completo, não apenas uma parcela estimada.</li>
                        </ul>

                        <div class="gx-consorcio-inline-links">
                            <?php if (!empty($contactPhone)): ?>
                                <a href="<?= !empty($contactPhoneHref) ? 'tel:' . esc($contactPhoneHref) : '#'; ?>" class="gx-consorcio-inline-link"><?= esc($contactPhone); ?></a>
                            <?php endif; ?>
                            <?php if (!empty($contactEmail)): ?>
                                <a href="mailto:<?= esc($contactEmail); ?>" class="gx-consorcio-inline-link"><?= esc($contactEmail); ?></a>
                            <?php endif; ?>
                            <?php if (!empty($whatsAppUrl)): ?>
                                <a href="<?= esc($whatsAppUrl); ?>" target="_blank" rel="noopener" class="gx-consorcio-inline-link" data-gx-whatsapp-link>WhatsApp</a>
                            <?php endif; ?>
                            <a href="<?= esc($simulatorsHubUrl); ?>" class="gx-consorcio-inline-link">Ver outros simuladores</a>
                        </div>

                        <form id="gx-consorcio-lead-form" class="gx-consorcio-lead-form" novalidate>
                            <div id="gx-consorcio-lead-status" class="gx-consorcio-lead-status" aria-live="polite" hidden></div>
                            <input type="hidden" name="lead_origin" id="gx-consorcio-lead-origin" value="Simulador de Consórcio - Consórcio x financiamento">
                            <input type="hidden" name="landing_page" id="gx-consorcio-landing-page" value="<?= esc(current_url()); ?>">
                            <input type="hidden" name="sim_data" id="gx-consorcio-sim-data" value="">
                            <input type="hidden" name="meta_content_name" id="gx-consorcio-content-name" value="Simulador de Consórcio - Consórcio x financiamento">
                            <input type="hidden" name="meta_content_category" value="Consórcio Estruturado">
                            <input type="hidden" name="meta_currency" value="BRL">
                            <input type="hidden" name="meta_value" id="gx-consorcio-meta-value" value="">
                            <input type="hidden" name="generated_message" id="gx-consorcio-generated-message" value="">

                            <div class="gx-form-field">
                                <label for="gx-consorcio-name">Nome</label>
                                <input id="gx-consorcio-name" type="text" name="name" maxlength="199" autocomplete="name" required>
                            </div>

                            <div class="gx-form-field">
                                <label for="gx-consorcio-email">E-mail</label>
                                <input id="gx-consorcio-email" type="email" name="email" maxlength="199" autocomplete="email" inputmode="email" required>
                            </div>

                            <?= view('partials/_lead_phone_field', [
                                'fieldIdPrefix' => 'gx-consorcio-phone',
                                'wrapperClass' => 'gx-form-field',
                                'hint' => 'Selecione o país e informe o telefone principal para retorno.',
                            ]); ?>

                            <div class="gx-form-field">
                                <label for="gx-consorcio-notes">Observações adicionais</label>
                                <textarea id="gx-consorcio-notes" name="notes" rows="4" placeholder="Se quiser, detalhe o prazo desejado, o tipo de imóvel ou a tese de revenda."></textarea>
                            </div>

                            <label class="gx-check">
                                <input type="checkbox" required>
                                <span>
                                    Li e concordo com os
                                    <a href="<?= esc($termsHref); ?>" target="_blank" rel="noopener">termos e condições</a>.
                                </span>
                            </label>

                            <div class="gx-form-actions">
                                <button type="submit" class="gx-btn gx-btn-primary gx-form-submit" id="gx-consorcio-submit">Receber meu plano de contemplação</button>
                                <p class="gx-form-note">Retorno consultivo e personalizado. Nada de proposta genérica.</p>
                            </div>
                        </form>

                        <div id="gx-consorcio-schedule-step" class="gx-consorcio-schedule-step" hidden>
                            <div class="gx-schedule-icon" aria-hidden="true">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="rgba(201,169,106,0.8)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="10 14 12 16 16 12"/></svg>
                            </div>
                            <span class="gx-schedule-badge">Próximo passo</span>
                            <h3 class="gx-schedule-title">Simulação recebida. Agende com o especialista.</h3>
                            <p class="gx-schedule-desc">Seus dados já foram enviados. Escolha o melhor horário para conversar com o especialista em consórcio e transformar a simulação em plano de contemplação.</p>
                            <a id="gx-consorcio-schedule-link" href="#" target="_blank" rel="noopener" class="gx-btn gx-btn-primary gx-btn-lg gx-schedule-cta">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                Agendar conversa com especialista
                            </a>
                            <div class="gx-schedule-alt">
                                <p>Prefere outro canal?</p>
                                <div class="gx-schedule-alt-links">
                                    <?php if (!empty($whatsAppUrl)): ?>
                                        <a href="<?= esc($whatsAppUrl); ?>" target="_blank" rel="noopener" class="gx-btn gx-btn-whatsapp gx-btn-sm" data-gx-whatsapp-link><?= $whatsAppIcon; ?>WhatsApp</a>
                                    <?php endif; ?>
                                    <?php if (!empty($contactPhone)): ?>
                                        <a href="<?= !empty($contactPhoneHref) ? 'tel:' . esc($contactPhoneHref) : '#'; ?>" class="gx-btn gx-btn-ghost gx-btn-sm"><?= esc($contactPhone); ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <p class="gx-schedule-note">Seus dados já estarão preenchidos na agenda. Basta escolher data e horário.</p>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    <section class="gx-section" id="tecnologia-ia">
        <div class="gx-wrap">
            <div class="gx-section-header is-split" data-gx-reveal>
                <div>
                    <p class="gx-label">Como funciona</p>
                    <h2 class="gx-section-title">Da simulação ao plano de contemplação em 3 passos.</h2>
                </div>
                <p class="gx-section-desc">
                    A IA faz o trabalho pesado de análise e comparação. O especialista garante que o plano final faz sentido para o seu momento, seu caixa e seu objetivo.
                </p>
            </div>

            <div class="gx-consorcio-ai-grid" data-gx-reveal data-gx-delay="90">
                <?php foreach ($aiSteps as $step): ?>
                    <article class="gx-consorcio-ai-card">
                        <span class="gx-consorcio-ai-step"><?= esc($step['step']); ?></span>
                        <div>
                            <h3><?= esc($step['title']); ?></h3>
                            <p><?= esc($step['description']); ?></p>
                        </div>
                        <ul>
                            <?php foreach ($step['items'] as $item): ?>
                                <li><?= esc($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="gx-section gx-section-alt">
        <div class="gx-wrap">
            <div class="gx-consorcio-callout" data-gx-reveal>
                <div>
                    <p class="gx-label">Pronto para avançar?</p>
                    <h2>Você já tem os números. Agora deixe o especialista montar o plano para contemplar.</h2>
                    <p>
                        Envie sua simulação e receba a análise completa com os melhores grupos, a estratégia de lance ideal
                        e um cronograma realista de contemplação para o seu caso.
                    </p>
                </div>
                <div class="gx-consorcio-callout-actions">
                    <a href="#fale-especialista" class="gx-btn gx-btn-primary gx-btn-lg">Receber meu plano de contemplação</a>
                    <?php if (!empty($whatsAppUrl)): ?>
                        <a href="<?= esc($whatsAppUrl); ?>" target="_blank" rel="noopener" class="gx-btn gx-btn-whatsapp gx-btn-lg" data-gx-whatsapp-link><?= $whatsAppIcon; ?>Chamar no WhatsApp</a>
                    <?php endif; ?>
                    <a href="<?= esc($contactUrl ?: '#fale-especialista'); ?>" class="gx-btn gx-btn-ghost gx-btn-lg">Ir para contato</a>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($whatsAppUrl)): ?>
    <a href="<?= esc($whatsAppUrl); ?>" target="_blank" rel="noopener" class="gx-fab-whatsapp" id="gx-fab-whatsapp" aria-label="Falar pelo WhatsApp" data-gx-whatsapp-link>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20.52 3.48A11.8 11.8 0 0 0 12.08 0C5.55 0 .24 5.31.24 11.84c0 2.08.54 4.11 1.58 5.89L0 24l6.46-1.69a11.8 11.8 0 0 0 5.62 1.43h.01c6.53 0 11.84-5.31 11.84-11.84 0-3.16-1.23-6.13-3.41-8.42Zm-8.44 18.26h-.01a9.84 9.84 0 0 1-5.01-1.37l-.36-.22-3.84 1 1.03-3.74-.24-.38a9.8 9.8 0 0 1-1.51-5.2C2.14 6.42 6.66 1.9 12.08 1.9c2.63 0 5.1 1.02 6.96 2.88a9.78 9.78 0 0 1 2.89 6.97c0 5.42-4.42 9.99-9.85 9.99Zm5.39-7.41c-.29-.14-1.71-.84-1.98-.94-.26-.1-.45-.14-.64.14-.19.29-.74.94-.91 1.13-.17.19-.34.22-.63.07-.29-.14-1.21-.45-2.31-1.45-.85-.76-1.42-1.69-1.59-1.98-.17-.29-.02-.44.13-.58.13-.13.29-.34.43-.5.14-.17.19-.29.29-.48.1-.19.05-.36-.02-.5-.07-.14-.64-1.55-.87-2.12-.23-.55-.47-.48-.64-.49h-.55c-.19 0-.5.07-.76.36-.26.29-.99.97-.99 2.37s1.01 2.75 1.15 2.94c.14.19 1.98 3.03 4.79 4.25.67.29 1.2.47 1.61.6.68.22 1.3.19 1.79.12.55-.08 1.71-.7 1.95-1.37.24-.67.24-1.24.17-1.37-.07-.12-.26-.19-.55-.34Z"/></svg>
    </a>
    <?php endif; ?>

    <div id="gx-exit-popup" class="gx-exit-overlay" hidden>
        <div class="gx-exit-card">
            <button type="button" class="gx-exit-close" id="gx-exit-close" aria-label="Fechar">&times;</button>
            <h2>Espera! Quer receber uma simulação personalizada?</h2>
            <p>Nosso especialista faz a simulação para você em até 24h.</p>
            <form id="gx-exit-form" class="gx-exit-form" novalidate>
                <input type="hidden" name="lead_origin" value="Simulador de Consórcio - Exit Intent">
                <input type="hidden" name="landing_page" value="<?= esc(current_url()); ?>">
                <input type="hidden" name="meta_content_name" value="Simulador de Consórcio - Exit Intent">
                <input type="hidden" name="meta_content_category" value="Consórcio Estruturado">
                <input type="hidden" name="meta_currency" value="BRL">
                <input type="hidden" name="meta_value" value="">
                <input type="hidden" name="phone_country" value="BR">
                <input type="tel" name="phone" placeholder="(00) 00000-0000" maxlength="16" autocomplete="tel" inputmode="numeric" required class="gx-exit-input" data-gx-exit-phone>
                <button type="submit" class="gx-btn gx-btn-whatsapp gx-btn-lg gx-exit-submit">Receber minha simulação</button>
            </form>
            <button type="button" class="gx-exit-dismiss" id="gx-exit-dismiss">Não, obrigado</button>
        </div>
    </div>
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
                nav.classList.toggle('is-scrolled', navScrolled);
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
            nav.classList.toggle('is-open', open);
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

    var revealNodes = document.querySelectorAll('[data-gx-reveal]');
    if ('IntersectionObserver' in window && revealNodes.length) {
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
        var revealObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (!entry.isIntersecting) {
                    return;
                }
                scheduleReveal(entry.target, entry.target.getAttribute('data-gx-delay'));
                revealObserver.unobserve(entry.target);
            });
        }, {threshold: 0.1, rootMargin: '0px 0px -40px 0px'});
        revealNodes.forEach(function(node) {
            revealObserver.observe(node);
        });
    } else {
        revealNodes.forEach(function(node) {
            node.classList.add('is-visible');
        });
    }

    var simulatorForm = document.getElementById('gx-consorcio-simulator-form');
    var leadForm = document.getElementById('gx-consorcio-lead-form');
    if (!simulatorForm || !leadForm) {
        return;
    }

    var whatsAppBaseUrl = <?= json_encode($whatsAppBaseUrl, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var whatsAppDefaultMessage = <?= json_encode($whatsAppDefaultMessage, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    var scenarioMeta = {
        compare: {
            label: 'Consórcio x financiamento',
            kicker: 'Comparativo estratégico',
            hook: 'Veja se o consórcio reduz sua parcela e o custo total comparado ao financiamento.',
            notePlaceholder: 'Ex.: tenho R$ 90 mil de entrada e quero saber se consórcio sai mais barato que financiamento.',
            contentName: 'Simulador de Consórcio - Comparativo com financiamento'
        },
        planned: {
            label: 'Compra planejada de imóvel',
            kicker: 'Compra planejada',
            hook: 'Descubra se seu ritmo mensal sustenta a contemplação no prazo que você precisa.',
            notePlaceholder: 'Ex.: quero comprar em até 18 meses, consigo guardar R$ 3.500/mês além da parcela.',
            contentName: 'Simulador de Consórcio - Compra planejada'
        },
        investor: {
            label: 'Investimento para revenda',
            kicker: 'Tese de investimento',
            hook: 'Calcule se a margem de revenda compensa o capital investido na operação.',
            notePlaceholder: 'Ex.: tenho R$ 120 mil para lance e busco margem de 18% na revenda após contemplação.',
            contentName: 'Simulador de Consórcio - Tese de revenda'
        }
    };

    var state = {
        scenario: 'compare',
        lastSnapshot: null
    };

    var scenarioTriggers = Array.prototype.slice.call(document.querySelectorAll('[data-gx-scenario-trigger]'));
    var scenarioCards = Array.prototype.slice.call(document.querySelectorAll('[data-gx-scenario-card]'));
    var scenarioPanels = Array.prototype.slice.call(document.querySelectorAll('[data-gx-scenario-panel]'));
    var scenarioJumps = Array.prototype.slice.call(document.querySelectorAll('[data-gx-scenario-jump]'));
    var resultKicker = document.getElementById('gx-consorcio-result-kicker');
    var resultHeadline = document.getElementById('gx-consorcio-result-headline');
    var resultBody = document.getElementById('gx-consorcio-result-body');
    var leadScenario = document.getElementById('gx-consorcio-lead-scenario');
    var leadHook = document.getElementById('gx-consorcio-lead-hook');
    var leadOriginInput = document.getElementById('gx-consorcio-lead-origin');
    var simDataInput = document.getElementById('gx-consorcio-sim-data');
    var metaValueInput = document.getElementById('gx-consorcio-meta-value');
    var contentNameInput = document.getElementById('gx-consorcio-content-name');
    var generatedMessageInput = document.getElementById('gx-consorcio-generated-message');
    var landingPageInput = document.getElementById('gx-consorcio-landing-page');
    var notesField = document.getElementById('gx-consorcio-notes');
    var leadStatus = document.getElementById('gx-consorcio-lead-status');
    var submitButton = document.getElementById('gx-consorcio-submit');
    var submitDefaultText = submitButton ? submitButton.textContent : '';
    var scheduleStep = document.getElementById('gx-consorcio-schedule-step');
    var scheduleLink = document.getElementById('gx-consorcio-schedule-link');
    var scheduleBaseUrl = <?= json_encode('https://app.gx.capital/agenda-publica/especialista-consorcio'); ?>;
    var kpiLabels = [1, 2, 3, 4].map(function(index) {
        return document.getElementById('gx-consorcio-kpi-label-' + index);
    });
    var kpiValues = [1, 2, 3, 4].map(function(index) {
        return document.getElementById('gx-consorcio-kpi-value-' + index);
    });
    var kpiCopies = [1, 2, 3, 4].map(function(index) {
        return document.getElementById('gx-consorcio-kpi-copy-' + index);
    });
    var bandLabelA = document.getElementById('gx-consorcio-band-label-a');
    var bandValueA = document.getElementById('gx-consorcio-band-value-a');
    var bandCopyA = document.getElementById('gx-consorcio-band-copy-a');
    var bandLabelB = document.getElementById('gx-consorcio-band-label-b');
    var bandValueB = document.getElementById('gx-consorcio-band-value-b');
    var bandCopyB = document.getElementById('gx-consorcio-band-copy-b');
    var insightNodes = [1, 2, 3].map(function(index) {
        return document.getElementById('gx-consorcio-insight-' + index);
    });

    function updateWhatsAppLink(message, label) {
        if (!whatsAppBaseUrl) {
            return;
        }
        var resolvedMessage = String(message || whatsAppDefaultMessage || '').trim();
        if (!resolvedMessage) {
            return;
        }
        var href = whatsAppBaseUrl + '?text=' + encodeURIComponent(resolvedMessage);
        document.querySelectorAll('[data-gx-whatsapp-link]').forEach(function(node) {
            node.setAttribute('href', href);
        });
        document.querySelectorAll('[data-gx-whatsapp-copy]').forEach(function(node) {
            node.textContent = label ? ('Mensagem pronta para iniciar a conversa sobre ' + label.toLowerCase() + '.') : 'Mensagem pronta para iniciar a conversa com o especialista.';
        });
    }

    function clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    function toNumber(value) {
        var normalized = parseFloat(String(value || '').replace(',', '.'));
        return Number.isFinite(normalized) ? normalized : 0;
    }

    function toInteger(value) {
        return Math.round(toNumber(value));
    }

    function currency(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
            maximumFractionDigits: 0
        }).format(Number.isFinite(value) ? value : 0);
    }

    function signedCurrency(value) {
        var prefix = value > 0 ? '+' : '';
        return prefix + currency(value);
    }

    function percent(value, fractionDigits) {
        return new Intl.NumberFormat('pt-BR', {
            minimumFractionDigits: fractionDigits || 0,
            maximumFractionDigits: fractionDigits || 0
        }).format(Number.isFinite(value) ? value : 0) + '%';
    }

    function ratioPercent(value, fractionDigits) {
        return percent((Number.isFinite(value) ? value : 0) * 100, fractionDigits);
    }

    function monthlyRateFromAnnual(annualRate) {
        if (annualRate <= 0) {
            return 0;
        }
        return Math.pow(1 + (annualRate / 100), 1 / 12) - 1;
    }

    function pmt(rate, periods, principal) {
        if (periods <= 0) {
            return 0;
        }
        if (rate === 0) {
            return principal / periods;
        }
        var factor = Math.pow(1 + rate, periods);
        return principal * rate * factor / (factor - 1);
    }

    function getFieldValue(name) {
        return simulatorForm.elements[name] ? simulatorForm.elements[name].value : '';
    }

    function commonMetrics() {
        var creditValue = Math.max(toNumber(getFieldValue('credit_value')), 0);
        var termMonths = clamp(toInteger(getFieldValue('term_months')) || 1, 24, 240);
        var adminFeePct = clamp(toNumber(getFieldValue('admin_fee_pct')), 0, 30);
        var reserveFeePct = clamp(toNumber(getFieldValue('reserve_fee_pct')), 0, 15);
        var targetMonth = clamp(toInteger(getFieldValue('target_month')) || 1, 1, termMonths);
        var totalQuotaCost = creditValue * (1 + ((adminFeePct + reserveFeePct) / 100));
        var monthlyInstallment = termMonths > 0 ? totalQuotaCost / termMonths : 0;
        var feeCost = totalQuotaCost - creditValue;

        if (simulatorForm.elements.term_months) {
            simulatorForm.elements.term_months.value = termMonths;
        }
        if (simulatorForm.elements.target_month) {
            simulatorForm.elements.target_month.value = targetMonth;
        }

        return {
            creditValue: creditValue,
            termMonths: termMonths,
            adminFeePct: adminFeePct,
            reserveFeePct: reserveFeePct,
            targetMonth: targetMonth,
            totalQuotaCost: totalQuotaCost,
            monthlyInstallment: monthlyInstallment,
            feeCost: feeCost
        };
    }

    function compareScenario(common) {
        var entryValue = Math.max(toNumber(getFieldValue('entry_value')), 0);
        var financingRateAnnual = clamp(toNumber(getFieldValue('financing_rate_annual')), 0, 40);
        var financingTermMonths = clamp(toInteger(getFieldValue('financing_term_months')) || 1, 12, 420);
        var financingFeePct = clamp(toNumber(getFieldValue('financing_fee_pct')), 0, 10);
        var financingPrincipal = Math.max(common.creditValue - entryValue, 0);
        var financingBase = financingPrincipal * (1 + (financingFeePct / 100));
        var financingInstallment = pmt(monthlyRateFromAnnual(financingRateAnnual), financingTermMonths, financingBase);
        var financingTotal = entryValue + (financingInstallment * financingTermMonths);
        var consortiumTotalPocket = common.totalQuotaCost + entryValue;
        var totalDifference = financingTotal - consortiumTotalPocket;
        var monthlyRelief = financingInstallment - common.monthlyInstallment;
        var entryCoverage = common.creditValue > 0 ? entryValue / common.creditValue : 0;
        var projectedCapitalToTarget = entryValue + (common.monthlyInstallment * common.targetMonth);

        var headline;
        if (totalDifference >= 0) {
            headline = 'O consórcio preserva mais caixa ao longo da operação do que o financiamento usado como referência.';
        } else {
            headline = 'Com esta configuração, o custo total fica pressionado. A escolha do grupo e da estratégia de lance passa a ser decisiva.';
        }

        var insights = [];
        insights.push('Usando a entrada como lance, a pressão mensal indicativa do consórcio fica em ' + signedCurrency(monthlyRelief) + ' frente ao financiamento comparado.');
        if (entryCoverage < 0.1) {
            insights.push('Sua entrada representa apenas ' + ratioPercent(entryCoverage, 1) + ' da carta. Para contemplar no prazo-alvo, a seleção do grupo precisa ser mais criteriosa.');
        } else {
            insights.push('A entrada cobre cerca de ' + ratioPercent(entryCoverage, 1) + ' da carta. Isso já permite trabalhar contemplação com estratégia, não apenas com sorteio.');
        }
        insights.push('Até o mês ' + common.targetMonth + ', o capital mobilizado estimado pelo consórcio ficaria em ' + currency(projectedCapitalToTarget) + '.');

        return {
            headline: headline,
            body: 'Esta leitura compara o financiamento tradicional com a estrutura de consórcio considerando sua entrada como lance próprio e o custo-base da cota.',
            metrics: [
                {
                    label: 'Parcela do consórcio',
                    value: currency(common.monthlyInstallment),
                    copy: 'Base indicativa da cota antes de ajustes finos por grupo.'
                },
                {
                    label: 'Parcela do financiamento',
                    value: currency(financingInstallment),
                    copy: 'PMT estimada com taxa e prazo informados.'
                },
                {
                    label: 'Diferença mensal',
                    value: signedCurrency(monthlyRelief),
                    copy: 'Folga mensal favorável ao consórcio quando positiva.'
                },
                {
                    label: 'Diferença total',
                    value: signedCurrency(totalDifference),
                    copy: 'Comparação entre desembolso total do financiamento e da estratégia de consórcio.'
                }
            ],
            band: [
                {
                    label: 'Entrada/lance',
                    value: currency(entryValue),
                    copy: 'Equivale a ' + ratioPercent(entryCoverage, 1) + ' do valor da carta.'
                },
                {
                    label: 'Custo-base da cota',
                    value: currency(common.totalQuotaCost),
                    copy: 'Carta + taxas consideradas na estrutura: ' + percent(common.adminFeePct + common.reserveFeePct, 1) + '.'
                }
            ],
            insights: insights,
            leadMessage: 'Olá! Vim pelo simulador de consórcio da GX Capital e quero validar uma comparação entre consórcio e financiamento. Minha simulação indica parcela do consórcio em ' + currency(common.monthlyInstallment) + ' e diferença total em ' + signedCurrency(totalDifference) + '.',
            metaValue: common.creditValue,
            contentName: scenarioMeta.compare.contentName,
            inputs: {
                entryValue: entryValue,
                financingRateAnnual: financingRateAnnual,
                financingTermMonths: financingTermMonths,
                financingFeePct: financingFeePct
            },
            outputs: {
                financingInstallment: financingInstallment,
                financingTotal: financingTotal,
                consortiumTotalPocket: consortiumTotalPocket,
                totalDifference: totalDifference,
                monthlyRelief: monthlyRelief,
                projectedCapitalToTarget: projectedCapitalToTarget,
                entryCoverage: entryCoverage
            }
        };
    }

    function plannedScenario(common) {
        var availableBid = Math.max(toNumber(getFieldValue('available_bid')), 0);
        var monthlyReserve = Math.max(toNumber(getFieldValue('monthly_reserve')), 0);
        var monthlyBudget = Math.max(toNumber(getFieldValue('monthly_budget')), 0);
        var expectedCorrectionAnnual = clamp(toNumber(getFieldValue('expected_correction_annual')), 0, 20);
        var adjustedCreditValue = common.creditValue * Math.pow(1 + (expectedCorrectionAnnual / 100), common.targetMonth / 12);
        var reserveProjection = availableBid + (monthlyReserve * common.targetMonth);
        var modeledBid = Math.min(reserveProjection * 0.94, adjustedCreditValue * 0.35);
        var bidCoverage = adjustedCreditValue > 0 ? modeledBid / adjustedCreditValue : 0;
        var monthlyCommitment = common.monthlyInstallment + monthlyReserve;
        var monthlyBalance = monthlyBudget - monthlyCommitment;

        var headline;
        if (monthlyBalance >= 0 && bidCoverage >= 0.15) {
            headline = 'Há base para montar uma compra planejada com esforço mensal controlado.';
        } else if (monthlyBalance < 0) {
            headline = 'O fluxo mensal projetado fica acima do teto informado. A estrutura precisa de ajuste antes de avançar.';
        } else {
            headline = 'O fluxo comporta a operação, mas a reserva para lance ainda está curta para a meta de contemplação informada.';
        }

        var insights = [];
        insights.push('Até o mês ' + common.targetMonth + ', sua reserva projetada para lance chegaria a ' + currency(reserveProjection) + '.');
        insights.push('O lance modelado neste cenário cobre aproximadamente ' + ratioPercent(bidCoverage, 1) + ' do valor corrigido do ativo até a contemplação.');
        if (monthlyBalance >= 0) {
            insights.push('Somando parcela e formação de reserva, ainda sobrariam ' + currency(monthlyBalance) + ' por mês dentro do orçamento informado.');
        } else {
            insights.push('Parcela e reserva juntas excedem seu orçamento confortável em ' + currency(Math.abs(monthlyBalance)) + ' por mês.');
        }

        return {
            headline: headline,
            body: 'A leitura observa se a parcela, a formação de reserva e a correção esperada do ativo conversam com a data que você pretende comprar.',
            metrics: [
                {
                    label: 'Parcela estimada',
                    value: currency(common.monthlyInstallment),
                    copy: 'Base mensal da cota simulada.'
                },
                {
                    label: 'Reserva até a meta',
                    value: currency(reserveProjection),
                    copy: 'Caixa previsto para sustentar o lance no prazo-alvo.'
                },
                {
                    label: 'Lance modelado',
                    value: currency(modeledBid),
                    copy: 'Faixa inicial de lance usando sua reserva projetada.'
                },
                {
                    label: 'Folga mensal',
                    value: signedCurrency(monthlyBalance),
                    copy: 'Diferença entre teto confortável e compromisso mensal total.'
                }
            ],
            band: [
                {
                    label: 'Valor corrigido do ativo',
                    value: currency(adjustedCreditValue),
                    copy: 'Projeção considerando correção de ' + percent(expectedCorrectionAnnual, 1) + ' ao ano.'
                },
                {
                    label: 'Compromisso mensal total',
                    value: currency(monthlyCommitment),
                    copy: 'Parcela da cota mais reserva para lance.'
                }
            ],
            insights: insights,
            leadMessage: 'Olá! Vim pelo simulador de consórcio da GX Capital e quero estruturar uma compra planejada com consórcio. Minha simulação indica reserva projetada de ' + currency(reserveProjection) + ' até o mês ' + common.targetMonth + ' e folga mensal de ' + signedCurrency(monthlyBalance) + '.',
            metaValue: common.creditValue,
            contentName: scenarioMeta.planned.contentName,
            inputs: {
                availableBid: availableBid,
                monthlyReserve: monthlyReserve,
                monthlyBudget: monthlyBudget,
                expectedCorrectionAnnual: expectedCorrectionAnnual
            },
            outputs: {
                adjustedCreditValue: adjustedCreditValue,
                reserveProjection: reserveProjection,
                modeledBid: modeledBid,
                bidCoverage: bidCoverage,
                monthlyCommitment: monthlyCommitment,
                monthlyBalance: monthlyBalance
            }
        };
    }

    function investorScenario(common) {
        var bidCash = Math.max(toNumber(getFieldValue('bid_cash')), 0);
        var resaleMarginPct = clamp(toNumber(getFieldValue('resale_margin_pct')), 0, 50);
        var saleCostPct = clamp(toNumber(getFieldValue('sale_cost_pct')), 0, 15);
        var holdingMonths = clamp(toInteger(getFieldValue('holding_months')) || 1, 1, 60);
        var holdingCostMonthly = Math.max(toNumber(getFieldValue('holding_cost_monthly')), 0);
        var capitalMobilized = bidCash + (common.monthlyInstallment * (common.targetMonth + holdingMonths));
        var grossGain = common.creditValue * (resaleMarginPct / 100);
        var saleCost = common.creditValue * (saleCostPct / 100);
        var holdingCost = holdingCostMonthly * holdingMonths;
        var netGain = grossGain - saleCost - holdingCost;
        var roi = capitalMobilized > 0 ? netGain / capitalMobilized : 0;
        var leverage = capitalMobilized > 0 ? common.creditValue / capitalMobilized : 0;
        var bidCoverage = common.creditValue > 0 ? bidCash / common.creditValue : 0;

        var headline;
        if (netGain > 0 && roi >= 0.12) {
            headline = 'A tese mostra margem líquida positiva sobre o capital mobilizado, mas precisa de validação fina de grupo e saída.';
        } else if (netGain > 0) {
            headline = 'Há ganho potencial, mas a margem sobre o capital travado ainda está apertada para uma tese confortável.';
        } else {
            headline = 'Nesta configuração, a margem esperada não cobre custos de transação e carregamento.';
        }

        var insights = [];
        insights.push('Até a revenda, o capital mobilizado estimado nesta estrutura seria de ' + currency(capitalMobilized) + '.');
        insights.push('A margem líquida indicativa após custos fica em ' + currency(netGain) + ', equivalente a ' + ratioPercent(roi, 1) + ' sobre o capital mobilizado.');
        if (bidCoverage < 0.15) {
            insights.push('O lance próprio informado cobre apenas ' + ratioPercent(bidCoverage, 1) + ' da carta. Para uma tese de revenda, isso exige seleção ainda mais criteriosa de grupos.');
        } else {
            insights.push('O lance próprio cobre cerca de ' + ratioPercent(bidCoverage, 1) + ' da carta, nível que já permite discutir timing com mais seriedade.');
        }

        return {
            headline: headline,
            body: 'Aqui o foco é entender se o ganho de revenda compensa o tempo, o capital imobilizado e os atritos de saída da operação.',
            metrics: [
                {
                    label: 'Parcela estimada',
                    value: currency(common.monthlyInstallment),
                    copy: 'Base mensal da cota simulada.'
                },
                {
                    label: 'Capital mobilizado',
                    value: currency(capitalMobilized),
                    copy: 'Lance próprio mais parcelas até a revenda.'
                },
                {
                    label: 'Margem líquida',
                    value: signedCurrency(netGain),
                    copy: 'Margem após custos de saída e carregamento.'
                },
                {
                    label: 'ROI indicativo',
                    value: ratioPercent(roi, 1),
                    copy: 'Retorno estimado sobre o capital mobilizado.'
                }
            ],
            band: [
                {
                    label: 'Alavancagem da tese',
                    value: leverage.toFixed(2) + 'x',
                    copy: 'Relação entre valor da carta e capital próprio mobilizado até a venda.'
                },
                {
                    label: 'Custo de carregamento',
                    value: currency(holdingCost),
                    copy: 'Custo acumulado em ' + holdingMonths + ' meses após contemplação.'
                }
            ],
            insights: insights,
            leadMessage: 'Olá! Vim pelo simulador de consórcio da GX Capital e quero validar uma tese de consórcio para revenda. Minha simulação mostra capital mobilizado de ' + currency(capitalMobilized) + ' e margem líquida estimada de ' + signedCurrency(netGain) + '.',
            metaValue: common.creditValue,
            contentName: scenarioMeta.investor.contentName,
            inputs: {
                bidCash: bidCash,
                resaleMarginPct: resaleMarginPct,
                saleCostPct: saleCostPct,
                holdingMonths: holdingMonths,
                holdingCostMonthly: holdingCostMonthly
            },
            outputs: {
                capitalMobilized: capitalMobilized,
                grossGain: grossGain,
                saleCost: saleCost,
                holdingCost: holdingCost,
                netGain: netGain,
                roi: roi,
                leverage: leverage,
                bidCoverage: bidCoverage
            }
        };
    }

    function buildSnapshot() {
        var common = commonMetrics();
        var scenarioResult = state.scenario === 'planned'
            ? plannedScenario(common)
            : (state.scenario === 'investor' ? investorScenario(common) : compareScenario(common));
        var snapshot = {
            scenario: state.scenario,
            scenario_label: scenarioMeta[state.scenario].label,
            generated_at: new Date().toISOString(),
            common: common,
            scenario_inputs: scenarioResult.inputs,
            outputs: scenarioResult.outputs,
            lead_message: scenarioResult.leadMessage
        };

        state.lastSnapshot = {
            view: scenarioResult,
            data: snapshot
        };

        return state.lastSnapshot;
    }

    function renderResult(result) {
        if (!result) {
            return;
        }

        resultKicker.textContent = scenarioMeta[state.scenario].kicker;
        resultHeadline.textContent = result.headline;
        resultBody.textContent = result.body;
        leadScenario.textContent = scenarioMeta[state.scenario].label;
        leadHook.textContent = scenarioMeta[state.scenario].hook;

        result.metrics.forEach(function(metric, index) {
            if (!kpiLabels[index] || !kpiValues[index] || !kpiCopies[index]) {
                return;
            }
            kpiLabels[index].textContent = metric.label;
            kpiValues[index].textContent = metric.value;
            kpiCopies[index].textContent = metric.copy;
        });

        bandLabelA.textContent = result.band[0].label;
        bandValueA.textContent = result.band[0].value;
        bandCopyA.textContent = result.band[0].copy;
        bandLabelB.textContent = result.band[1].label;
        bandValueB.textContent = result.band[1].value;
        bandCopyB.textContent = result.band[1].copy;

        result.insights.forEach(function(text, index) {
            if (insightNodes[index]) {
                insightNodes[index].textContent = text;
            }
        });

        if (notesField) {
            notesField.placeholder = scenarioMeta[state.scenario].notePlaceholder;
        }
        if (leadOriginInput) {
            leadOriginInput.value = 'Simulador de Consórcio - ' + scenarioMeta[state.scenario].label;
        }
        if (landingPageInput) {
            landingPageInput.value = window.location.href;
        }
        if (simDataInput) {
            simDataInput.value = JSON.stringify(state.lastSnapshot.data);
        }
        if (metaValueInput) {
            metaValueInput.value = String(Math.round(result.metaValue || 0));
        }
        if (contentNameInput) {
            contentNameInput.value = result.contentName;
        }
        if (generatedMessageInput) {
            generatedMessageInput.value = result.leadMessage;
        }
        updateWhatsAppLink(result.leadMessage, scenarioMeta[state.scenario].label);
    }

    function syncScenarioUi() {
        scenarioTriggers.forEach(function(trigger) {
            var isActive = trigger.getAttribute('data-gx-scenario-trigger') === state.scenario;
            trigger.classList.toggle('is-active', isActive);
            trigger.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        scenarioCards.forEach(function(card) {
            card.classList.toggle('is-active', card.getAttribute('data-gx-scenario-card') === state.scenario);
        });

        scenarioPanels.forEach(function(panel) {
            panel.hidden = panel.getAttribute('data-gx-scenario-panel') !== state.scenario;
        });
    }

    function rerender() {
        syncScenarioUi();
        var snapshot = buildSnapshot();
        renderResult(snapshot.view);
    }

    function activateScenario(nextScenario, shouldScroll) {
        if (!scenarioMeta[nextScenario]) {
            return;
        }
        state.scenario = nextScenario;
        rerender();
        if (shouldScroll) {
            var target = document.getElementById('simulador');
            if (target) {
                target.scrollIntoView({behavior: 'smooth', block: 'start'});
            }
        }
    }

    scenarioTriggers.forEach(function(trigger) {
        trigger.addEventListener('click', function() {
            activateScenario(trigger.getAttribute('data-gx-scenario-trigger'), false);
        });
    });

    scenarioJumps.forEach(function(button) {
        button.addEventListener('click', function() {
            activateScenario(button.getAttribute('data-gx-scenario-jump'), true);
        });
    });

    simulatorForm.addEventListener('input', rerender);
    simulatorForm.addEventListener('change', rerender);

    function setLeadStatus(type, message) {
        if (!leadStatus) {
            return;
        }
        leadStatus.hidden = false;
        leadStatus.innerHTML = '<div class="alert alert-' + type + '">' + message + '</div>';
    }

    function appendUrlParams(payload) {
        var params = new URLSearchParams(window.location.search);
        ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function(key) {
            if (params.has(key) && !payload.get(key)) {
                payload.append(key, params.get(key));
            }
        });
    }

    leadForm.addEventListener('submit', function(event) {
        event.preventDefault();

        if (!leadForm.reportValidity()) {
            return;
        }

        rerender();

        var snapshot = state.lastSnapshot;
        var payload = new FormData(leadForm);
        var notes = String(payload.get('notes') || '').trim();
        var generatedMessage = String(payload.get('generated_message') || '').trim();
        payload.delete('notes');
        payload.delete('generated_message');
        payload.append('message', notes ? (generatedMessage + ' Observações do cliente: ' + notes) : generatedMessage);
        payload.append('observations', notes ? notes : generatedMessage);
        payload.append('origin', payload.get('lead_origin') || ('Simulador de Consórcio - ' + scenarioMeta[state.scenario].label));
        payload.append('origem', payload.get('lead_origin') || ('Simulador de Consórcio - ' + scenarioMeta[state.scenario].label));
        payload.set('landing_page', window.location.href);
        payload.set('sim_data', JSON.stringify(snapshot.data));
        payload.set('meta_content_name', contentNameInput.value || scenarioMeta[state.scenario].contentName);
        payload.set('meta_value', metaValueInput.value || String(Math.round(snapshot.data.common.creditValue || 0)));
        payload.set('meta_currency', 'BRL');
        payload.set('meta_content_category', 'Consórcio Estruturado');
        appendUrlParams(payload);

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Enviando...';
        }
        if (leadStatus) {
            leadStatus.hidden = true;
            leadStatus.innerHTML = '';
        }

        fetch(<?= json_encode(base_url('api/save-simulator-lead')); ?>, {
            method: 'POST',
            body: payload,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function(response) {
            return response.text().then(function(text) {
                var data = {};
                try {
                    data = JSON.parse(text);
                } catch (error) {
                    data = {status: 'error', message: 'Não foi possível interpretar a resposta do servidor.'};
                }
                return {ok: response.ok, data: data};
            });
        }).then(function(result) {
            if (!result.ok || result.data.status !== 'success') {
                throw new Error(result.data.message || 'Não foi possível enviar sua solicitação agora.');
            }

            if (scheduleStep && scheduleLink) {
                var nameVal = (document.getElementById('gx-consorcio-name') || {}).value || '';
                var emailVal = (document.getElementById('gx-consorcio-email') || {}).value || '';
                var phoneInput = document.getElementById('gx-consorcio-phone-input');
                var phoneCountry = leadForm.querySelector('input[name="phone_country"]');
                var phoneVal = phoneInput ? phoneInput.value || '' : '';
                var phoneCountryVal = phoneCountry ? phoneCountry.value || '' : '';
                var fullPhone = phoneCountryVal && phoneVal ? phoneCountryVal + phoneVal.replace(/\D/g, '') : phoneVal;

                var agendaParams = new URLSearchParams();
                if (nameVal) agendaParams.set('name', nameVal);
                if (emailVal) agendaParams.set('email', emailVal);
                if (fullPhone) agendaParams.set('phone', fullPhone);
                agendaParams.set('source', 'simulador-consorcio');
                agendaParams.set('scenario', scenarioMeta[state.scenario].label);

                var separator = scheduleBaseUrl.indexOf('?') !== -1 ? '&' : '?';
                scheduleLink.href = scheduleBaseUrl + separator + agendaParams.toString();

                leadForm.hidden = true;
                scheduleStep.hidden = false;
                scheduleStep.scrollIntoView({behavior: 'smooth', block: 'center'});
            } else {
                setLeadStatus('success', 'Recebemos sua simulação. Um especialista da GX Capital vai cruzar grupos, lance e prazo para retornar com um plano de contemplação.');
            }

            if (submitButton) {
                submitButton.textContent = 'Solicitação enviada';
            }
        }).catch(function(error) {
            setLeadStatus('danger', error.message || 'Não foi possível enviar sua solicitação agora.');
            if (submitButton) {
                submitButton.textContent = submitDefaultText;
            }
        }).finally(function() {
            if (submitButton) {
                submitButton.disabled = false;
                if (submitButton.textContent === 'Enviando...') {
                    submitButton.textContent = submitDefaultText;
                }
            }
        });
    });

    rerender();

    /* ── Facebook Pixel + Google events (Priority 9) ── */
    /* Use global gxFbq/gxGtag if available, otherwise fallback to local */
    var fbqSafe = typeof gxFbq === 'function' ? gxFbq : function(action, event, params) {
        if (typeof fbq === 'function') {
            if (action === 'trackCustom') {
                fbq('trackCustom', event, params || {});
            } else {
                fbq('track', event, params || {});
            }
        }
    };
    var gtagSafe = typeof gxGtag === 'function' ? gxGtag : function() {
        if (typeof gtag === 'function') gtag.apply(null, arguments);
    };

    var simulatorInteracted = false;
    simulatorForm.addEventListener('input', function() {
        if (!simulatorInteracted) {
            simulatorInteracted = true;
            fbqSafe('track', 'ViewContent', { content_name: 'Simulador Consórcio Interação' });
            gtagSafe('event', 'view_item', { content_type: 'simulator', item_id: 'simulador_consorcio' });
        }
    });

    document.querySelectorAll('[data-gx-whatsapp-link]').forEach(function(link) {
        link.addEventListener('click', function() {
            fbqSafe('track', 'Contact', { content_name: 'WhatsApp Simulador' });
        });
    });

    /* ── Priority 6: Mini simulator logic ── */
    var miniCredit = document.getElementById('gx-mini-sim-credit');
    var miniMonths = document.getElementById('gx-mini-sim-months');
    var miniCreditVal = document.getElementById('gx-mini-sim-credit-value');
    var miniMonthsVal = document.getElementById('gx-mini-sim-months-value');
    var miniBtn = document.getElementById('gx-mini-sim-btn');
    var miniResult = document.getElementById('gx-mini-sim-result');
    var miniResultText = document.getElementById('gx-mini-sim-result-text');

    if (miniCredit && miniMonths) {
        var formatMiniCurrency = function(v) {
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', maximumFractionDigits: 0 }).format(v);
        };

        miniCredit.addEventListener('input', function() {
            miniCreditVal.textContent = formatMiniCurrency(Number(miniCredit.value));
        });

        miniMonths.addEventListener('input', function() {
            miniMonthsVal.textContent = miniMonths.value + ' meses';
        });

        if (miniBtn) {
            miniBtn.addEventListener('click', function() {
                var credit = Number(miniCredit.value);
                var months = Number(miniMonths.value);
                var adminFee = 0.18;
                var reserveFee = 0.02;
                var consortiumTotal = credit * (1 + adminFee + reserveFee);
                var financingRate = 0.0075;
                var financingMonths = 360;
                var financingFactor = Math.pow(1 + financingRate, financingMonths);
                var financingInstallment = credit * financingRate * financingFactor / (financingFactor - 1);
                var financingTotal = financingInstallment * financingMonths;
                var savings = Math.max(financingTotal - consortiumTotal, 0);

                if (miniResultText) {
                    miniResultText.textContent = 'Você pode economizar até ' + formatMiniCurrency(savings) + ' com consórcio';
                }
                if (miniResult) {
                    miniResult.hidden = false;
                }

                fbqSafe('trackCustom', 'SimulationResult', { value: Math.round(savings), currency: 'BRL' });
                gtagSafe('event', 'simulation_result', { event_category: 'engagement', event_label: 'Consórcio Mini Simulador', value: Math.round(savings), currency: 'BRL' });
            });
        }
    }

    /* ── Priority 5: Quick lead form ── */
    var quickForm = document.getElementById('gx-quick-lead-form');
    var quickStatus = document.getElementById('gx-quick-lead-status');
    var quickPhone = document.querySelector('[data-gx-quick-phone]');

    if (quickPhone) {
        quickPhone.addEventListener('input', function() {
            var d = quickPhone.value.replace(/\D/g, '').substring(0, 11);
            if (d.length <= 2) { quickPhone.value = d.length ? '(' + d : ''; }
            else if (d.length <= 6) { quickPhone.value = '(' + d.substring(0,2) + ') ' + d.substring(2); }
            else {
                var bl = d.length > 10 ? 5 : 4;
                quickPhone.value = '(' + d.substring(0,2) + ') ' + d.substring(2, 2+bl) + (d.length > 2+bl ? '-' + d.substring(2+bl) : '');
            }
        });
    }

    if (quickForm) {
        quickForm.addEventListener('submit', function(event) {
            event.preventDefault();
            if (!quickForm.reportValidity()) return;

            var payload = new FormData(quickForm);
            payload.set('landing_page', window.location.href);
            payload.set('message', 'Lead rápido (hero) — ' + (payload.get('name') || '') + ' — ' + (payload.get('phone') || ''));
            payload.set('observations', 'Formulário rápido do hero');
            payload.set('origin', 'Simulador de Consórcio - Quick Form Hero');
            payload.set('origem', 'Simulador de Consórcio - Quick Form Hero');
            appendUrlParams(payload);

            var btn = quickForm.querySelector('button[type="submit"]');
            if (btn) { btn.disabled = true; btn.textContent = 'Enviando...'; }

            fetch(<?= json_encode(base_url('api/save-simulator-lead')); ?>, {
                method: 'POST', body: payload, credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data.status === 'success') {
                    if (quickStatus) { quickStatus.hidden = false; quickStatus.innerHTML = '<div class="alert alert-success">Recebemos seus dados! Um especialista entrará em contato em breve.</div>'; }
                    if (btn) { btn.textContent = 'Enviado!'; }
                    fbqSafe('track', 'Lead', { content_name: 'Simulador Consórcio', currency: 'BRL', value: 0 });
                    gtagSafe('event', 'generate_lead', { event_category: 'conversion', event_label: 'Simulador Consórcio Quick Form', currency: 'BRL', value: 0 });
                    sessionStorage.setItem('gx_lead_sent', '1');
                } else {
                    throw new Error(data.message || 'Erro ao enviar.');
                }
            }).catch(function(err) {
                if (quickStatus) { quickStatus.hidden = false; quickStatus.innerHTML = '<div class="alert alert-danger">' + (err.message || 'Erro ao enviar.') + '</div>'; }
                if (btn) { btn.disabled = false; btn.textContent = 'Quero meu plano gratuito'; }
            });
        });
    }

    /* ── Add fbq/gtag Lead event to main lead form ── */
    var originalLeadFormSubmitHandler = leadForm.getAttribute('data-gx-fbq-bound');
    if (!originalLeadFormSubmitHandler) {
        leadForm.setAttribute('data-gx-fbq-bound', '1');
        leadForm.addEventListener('submit', function() {
            var creditVal = simulatorForm.elements.credit_value ? Number(simulatorForm.elements.credit_value.value) : 0;
            fbqSafe('track', 'Lead', { content_name: 'Simulador Consórcio', currency: 'BRL', value: creditVal });
            gtagSafe('event', 'generate_lead', { event_category: 'conversion', event_label: 'Simulador Consórcio', currency: 'BRL', value: creditVal });
            sessionStorage.setItem('gx_lead_sent', '1');
        });
    }

    /* ── Priority 11: Exit intent popup ── */
    var exitPopup = document.getElementById('gx-exit-popup');
    var exitClose = document.getElementById('gx-exit-close');
    var exitDismiss = document.getElementById('gx-exit-dismiss');
    var exitForm = document.getElementById('gx-exit-form');
    var exitPhone = document.querySelector('[data-gx-exit-phone]');
    var exitShown = false;

    function showExitPopup() {
        if (exitShown || !exitPopup) return;
        if (sessionStorage.getItem('gx_exit_shown') === '1') return;
        if (sessionStorage.getItem('gx_lead_sent') === '1') return;
        exitShown = true;
        sessionStorage.setItem('gx_exit_shown', '1');
        exitPopup.hidden = false;
    }

    function hideExitPopup() {
        if (exitPopup) exitPopup.hidden = true;
    }

    if (exitClose) exitClose.addEventListener('click', hideExitPopup);
    if (exitDismiss) exitDismiss.addEventListener('click', hideExitPopup);
    if (exitPopup) {
        exitPopup.addEventListener('click', function(e) {
            if (e.target === exitPopup) hideExitPopup();
        });
    }

    if (exitPhone) {
        exitPhone.addEventListener('input', function() {
            var d = exitPhone.value.replace(/\D/g, '').substring(0, 11);
            if (d.length <= 2) { exitPhone.value = d.length ? '(' + d : ''; }
            else if (d.length <= 6) { exitPhone.value = '(' + d.substring(0,2) + ') ' + d.substring(2); }
            else {
                var bl = d.length > 10 ? 5 : 4;
                exitPhone.value = '(' + d.substring(0,2) + ') ' + d.substring(2, 2+bl) + (d.length > 2+bl ? '-' + d.substring(2+bl) : '');
            }
        });
    }

    if (exitForm) {
        exitForm.addEventListener('submit', function(event) {
            event.preventDefault();
            if (!exitForm.reportValidity()) return;

            var payload = new FormData(exitForm);
            payload.set('landing_page', window.location.href);
            payload.set('message', 'Exit intent lead — ' + (payload.get('phone') || ''));
            payload.set('observations', 'Formulário exit intent');
            payload.set('origin', 'Simulador de Consórcio - Exit Intent');
            payload.set('origem', 'Simulador de Consórcio - Exit Intent');
            appendUrlParams(payload);

            var btn = exitForm.querySelector('button[type="submit"]');
            if (btn) { btn.disabled = true; btn.textContent = 'Enviando...'; }

            fetch(<?= json_encode(base_url('api/save-simulator-lead')); ?>, {
                method: 'POST', body: payload, credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data.status === 'success') {
                    fbqSafe('track', 'Lead', { content_name: 'Simulador Consórcio', currency: 'BRL', value: 0 });
                    sessionStorage.setItem('gx_lead_sent', '1');
                    exitForm.innerHTML = '<p style="color:#0c3163;font-weight:600;">Recebemos seu WhatsApp! Entraremos em contato em breve.</p>';
                    setTimeout(hideExitPopup, 2500);
                } else {
                    throw new Error(data.message || 'Erro ao enviar.');
                }
            }).catch(function(err) {
                if (btn) { btn.disabled = false; btn.textContent = 'Receber minha simulação'; }
            });
        });
    }

    /* Desktop: mouse leaving viewport top */
    document.addEventListener('mouseout', function(e) {
        if (e.clientY <= 0) showExitPopup();
    });

    /* Mobile: 60% scroll + 30s on page */
    var mobileExitReady = false;
    setTimeout(function() { mobileExitReady = true; }, 30000);
    window.addEventListener('scroll', function() {
        if (window.innerWidth >= 992 || !mobileExitReady) return;
        var scrollPct = window.scrollY / (document.documentElement.scrollHeight - window.innerHeight);
        if (scrollPct >= 0.6) showExitPopup();
    }, {passive: true});

})();
</script>
