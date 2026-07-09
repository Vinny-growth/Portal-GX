<?php

/**
 * Camada de marketing — strings da view simulators/consorcio.php
 * (white-label — Fase 2 i18n, Lote 4) — PORTUGUÊS.
 *
 * Catálogo SEPARADO de Simuladores.php de propósito: o seguro está sob refatoração
 * concorrente (redesign do gate) e mexer no catálogo compartilhado arriscaria varrer
 * WIP alheio para um commit meu. Consumido por lang('Consorcio.<chave>') e, quando a
 * string carrega o nome completo da marca, por brandLang('Consorcio.<chave>') — o
 * token literal {brand} é trocado por brand('display_name'). Menções curtas "GX"
 * seguem literais. Reconciliável com Simuladores.php depois que o seguro estabilizar.
 *
 * Migração incremental por sub-lote (4b-N).
 */
return [
    // ===================================================================
    // 4b-1 — topo de marketing (nav, hero, quick-lead, strip, autoridade)
    // ===================================================================

    // navegação
    'nav_mesa'        => 'A Mesa',
    'nav_estrategias' => 'Estratégias',
    'nav_simulador'   => 'Simulador',
    'nav_tecnologia'  => 'Tecnologia',
    'nav_especialista' => 'Especialista',
    'nav_blog'        => 'Blog',
    'nav_cta'         => 'Receber meu plano',
    'nav_menu'        => 'Menu',

    // prova do hero
    'hp1_title' => '20+ administradoras',
    'hp1_text'  => 'Você não fica preso a uma prateleira. Comparamos as melhores opções do mercado.',
    'hp2_title' => '1.000+ grupos analisados',
    'hp2_text'  => 'IA cruza prazo, taxa e assembleia para achar o grupo certo para o seu perfil.',
    'hp3_title' => 'Resultado em minutos',
    'hp3_text'  => 'Diagnóstico imediato com números reais para decidir com segurança.',

    // hero
    'hero_badge'       => 'Exclusivo {brand} &mdash; simulador com inteligência artificial',
    'hero_title'       => 'Consórcio ou financiamento? Simule e descubra.',
    'hero_copy'        => 'A {brand} cruza 20+ administradoras com IA para encontrar a rota de contemplação mais rápida para o seu caso.',
    'hero_cta'         => 'Simular agora &mdash; grátis',
    'hero_reassurance' => 'Gratuito &bull; Sem compromisso &bull; Resultado em minutos',

    // mini-simulador do hero
    'mini_title'         => 'Simulação rápida',
    'mini_credit_label'  => 'Quanto você quer de carta de crédito?',
    'mini_months_label'  => 'Em quanto tempo quer ser contemplado?',
    'mini_btn'           => 'Ver minha economia estimada &rarr;',
    'mini_result_suffix' => 'em relação ao financiamento tradicional.',
    'mini_result_link'   => 'Quer o plano detalhado? &rarr;',

    // quick-lead
    'quick_h2'      => 'Prefere que um especialista simule para você?',
    'quick_p'       => 'Deixe seu WhatsApp e receba um plano personalizado em até 24h. Sem compromisso.',
    'quick_ph_name' => 'Seu nome',
    'quick_btn'     => 'Quero meu plano gratuito',
    'quick_trust'   => 'Seus dados estão seguros. Sem spam.',

    // strip de passos
    'strip_lead' => '4 passos',
    'strip_1'    => 'Escolha seu objetivo',
    'strip_2'    => 'Ajuste os números',
    'strip_3'    => 'Veja o resultado ao vivo',
    'strip_4'    => 'Receba o plano do especialista',

    // autoridade (quem-somos)
    'auth_label' => 'Quem está por trás do simulador',
    'auth_title' => 'Consórcio com estratégia de contemplação, não com parcela genérica.',
    'auth_desc'  => 'A {brand} é uma boutique financeira fundada por <strong>Vinicius Teixeira</strong>, com mais de 16 anos de atuação em mercado financeiro. A frente de consórcio nasceu da mesma lógica que a mesa de câmbio e crédito: comparar dezenas de opções, filtrar com tecnologia e recomendar a estrutura mais eficiente para cada perfil.',
    'auth_card1_label' => 'Comparativo estruturado',
    'auth_card1_title' => 'Consórcio x financiamento com números reais, não com propaganda.',
    'auth_card1_desc'  => 'A decisão entre consórcio e financiamento depende de entrada disponível, custo total, prazo e capacidade de lance. O simulador coloca os dois lado a lado para que a conta fale por si.',
    'auth_card2_label' => 'Planejamento de contemplação',
    'auth_card2_title' => 'Rota para contemplar no prazo certo, com o grupo certo.',
    'auth_card2_desc'  => 'A IA cruza mais de 20 administradoras e 1.000+ grupos para encontrar a combinação de taxa, assembleia e estratégia de lance mais aderente ao seu fluxo de caixa e objetivo.',
    'auth_card3_label' => 'Tese de investimento',
    'auth_card3_title' => 'Consórcio como veículo de retorno, não só de compra.',
    'auth_card3_desc'  => 'Para quem quer contemplar e revender com margem, o simulador projeta ROI líquido, custo de carregamento e break-even. O especialista valida a tese antes de você entrar no grupo.',
    'boutique_note'    => '<strong>Modelo independente:</strong> a {brand} não é administradora de consórcio e não tem cota própria para vender. Isso permite recomendar o grupo, a administradora e a estratégia de lance mais eficiente para o seu caso, sem viés comercial. Os simuladores abaixo são o primeiro passo para dimensionar a operação antes de falar com o especialista.',

    // ===================================================================
    // 4b-2 — depoimentos (fallbacks) + estratégias + cards de cenário + header do simulador
    // ===================================================================

    // depoimentos (fallbacks do config + aria)
    'testi_label' => 'Quem já simulou',
    'testi_title' => 'Quem já simulou, aprovou',
    'testi_stars' => '5 estrelas',

    // seção estratégias
    'estr_label' => 'Escolha sua jornada',
    'estr_title' => 'Qual é o seu objetivo com o consórcio? Comece pelo cenário certo.',
    'estr_desc'  => "Cada objetivo pede uma conta diferente. Escolha o cenário que mais se parece com o seu momento\n                    e veja os números que realmente importam para a sua decisão.",

    // cards de cenário ($scenarioCards) — pílula + compare/planned/investor
    'sc_gratis'           => 'Simulação gratuita',
    'sc_compare_eyebrow'  => 'Mais popular',
    'sc_compare_title'    => 'Consórcio x financiamento',
    'sc_compare_desc'     => 'Já tem entrada? Veja em números se o consórcio reduz a parcela mensal e o custo total comparado ao financiamento.',
    'sc_compare_b1'       => 'Descubra a diferença real de parcela entre consórcio e financiamento.',
    'sc_compare_b2'       => 'Veja quanto sua entrada acelera a contemplação como lance.',
    'sc_compare_b3'       => 'Saiba exatamente quanto você economiza no desembolso total.',
    'sc_compare_btn'      => 'Comparar agora',
    'sc_planned_eyebrow'  => 'Compra inteligente',
    'sc_planned_title'    => 'Comprar imóvel de forma planejada',
    'sc_planned_desc'     => 'Quer comprar sem apertar o caixa? Veja se seu ritmo mensal sustenta a contemplação no prazo que você precisa.',
    'sc_planned_b1'       => 'Saiba quanto de reserva você terá para dar lance no mês certo.',
    'sc_planned_b2'       => 'Veja se parcela + reserva cabem no seu orçamento confortável.',
    'sc_planned_b3'       => 'Descubra se sobra ou aperta antes de se comprometer.',
    'sc_planned_btn'      => 'Planejar minha compra',
    'sc_investor_eyebrow' => 'Alta rentabilidade',
    'sc_investor_title'   => 'Investir para revender após contemplação',
    'sc_investor_desc'    => 'Use o consórcio como alavanca patrimonial. Veja se a margem de revenda compensa o capital investido.',
    'sc_investor_b1'      => 'Calcule o capital total que você precisa mobilizar até a venda.',
    'sc_investor_b2'      => 'Veja a margem líquida real depois de todos os custos de saída.',
    'sc_investor_b3'      => 'Descubra o ROI da operação sobre o seu dinheiro travado.',
    'sc_investor_btn'     => 'Calcular retorno',

    // seção simulador ao vivo
    'sim_label'          => 'Simulador ao vivo',
    'sim_title'          => 'Ajuste os números do seu caso e veja o resultado mudar em tempo real.',
    'sim_desc'           => "Mexa nos campos abaixo e acompanhe o impacto na parcela, no lance e no custo total. Quando estiver satisfeito,\n                    envie a simulação e receba o plano de contemplação do especialista.",
    'sim_switcher_aria'  => 'Escolha a jornada do simulador',
    'sim_infobar_strong' => 'Os resultados atualizam ao vivo.',
    'sim_infobar_span'   => 'Mude qualquer campo e veja o impacto na parcela, no custo total e na viabilidade da contemplação.',

    // ===================================================================
    // 4b-3 — formulário do simulador (4 fieldsets). Sufixo '%' fica literal.
    // ===================================================================
    'f_meses' => 'meses',
    'f_mes'   => 'mês',
    'f_aa'    => '% a.a.',

    // fieldset: dados da cota
    'f_cota_head'  => 'Dados da cota',
    'f_cota_desc'  => 'Informe o valor do bem e as condições do grupo. Esses campos valem para qualquer cenário.',
    'f_credit'     => 'Valor da carta de crédito',
    'f_credit_h'   => 'Use o valor do imóvel ou do ativo que pretende adquirir.',
    'f_term'       => 'Prazo do grupo',
    'f_term_h'     => 'Prazo total de pagamento da cota.',
    'f_admin'      => 'Taxa administrativa',
    'f_admin_h'    => 'Percentual total de taxa administrativa considerado na estrutura.',
    'f_reserve'    => 'Fundo de reserva',
    'f_reserve_h'  => 'Percentual adicional para reserva e composição do custo.',
    'f_target'     => 'Meta de contemplação',
    'f_target_h'   => 'Janela que você deseja mirar para a contemplação.',

    // fieldset: compare com o financiamento
    'f_cmp_head'   => 'Compare com o financiamento',
    'f_cmp_desc'   => 'Informe quanto você tem de entrada e as condições do financiamento para ver a diferença real.',
    'f_entry'      => 'Recurso disponível para entrada/lance',
    'f_entry_h'    => 'Valor que você já teria para usar no início da operação.',
    'f_frate'      => 'Taxa anual do financiamento',
    'f_frate_h'    => 'Taxa nominal usada para o comparativo da linha de crédito tradicional.',
    'f_fterm'      => 'Prazo do financiamento',
    'f_fterm_h'    => 'Prazo total do contrato de financiamento usado como referência.',
    'f_fcost'      => 'Custos iniciais do financiamento',
    'f_fcost_h'    => 'Inclua tarifas, seguros embutidos ou custos acessórios de contratação.',

    // fieldset: planeje sua compra
    'f_pln_head'    => 'Planeje sua compra',
    'f_pln_desc'    => 'Informe quanto consegue guardar por mês e veja se dá para contemplar no prazo que você precisa.',
    'f_avail'       => 'Reserva disponível hoje',
    'f_avail_h'     => 'Valor já disponível para compor a estratégia de lance.',
    'f_mreserve'    => 'Reserva mensal para lance',
    'f_mreserve_h'  => 'Quanto você consegue acumular por mês além da parcela.',
    'f_mbudget'     => 'Orçamento mensal confortável',
    'f_mbudget_h'   => 'Teto que você deseja respeitar somando parcela e formação de reserva.',
    'f_correction'  => 'Correção anual esperada do ativo',
    'f_correction_h' => 'Serve para ajustar o valor de referência do imóvel até a contemplação.',

    // fieldset: calcule o retorno da revenda
    'f_inv_head'     => 'Calcule o retorno da revenda',
    'f_inv_desc'     => 'Informe o capital disponível e a margem esperada para ver se a operação vale o investimento.',
    'f_bidcash'      => 'Caixa para lance próprio',
    'f_bidcash_h'    => 'Capital que você pretende usar para acelerar a contemplação.',
    'f_margin'       => 'Margem esperada na revenda',
    'f_margin_h'     => 'Margem bruta estimada entre aquisição e revenda.',
    'f_salecost'     => 'Custos de saída e transação',
    'f_salecost_h'   => 'Inclua corretagem, impostos, documentação e atritos da operação.',
    'f_holdmonths'   => 'Meses até a revenda após contemplação',
    'f_holdmonths_h' => 'Horizonte entre contemplação, aquisição e venda do ativo.',
    'f_holdcost'     => 'Custo mensal de carregamento',
    'f_holdcost_h'   => 'Condomínio, manutenção, carência de aluguel ou outro custo de carregamento.',
];
