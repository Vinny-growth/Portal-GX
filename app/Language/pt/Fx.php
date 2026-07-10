<?php

/**
 * Camada de marketing — strings da view marketing/simulators_fx_hub.php
 * (hub de simuladores de câmbio; white-label — Fase 2 i18n, Lote 4c) — PORTUGUÊS.
 *
 * Consumido por lang('Fx.<chave>') e, quando a string carrega o nome completo da
 * marca, por brandLang('Fx.<chave>') ({brand} -> brand('display_name')). Menções
 * curtas "GX" e termos técnicos fixos (FX Desk, ACC, 4131) ficam literais.
 * Identificadores de CRM/analytics no JS ficam literais. Catálogo próprio (isolado).
 *
 * Migração incremental por sub-lote (4c-N).
 */
return [
    // ===================================================================
    // 4c-1 — topo de marketing (nav, hero, strip, autoridade)
    // ===================================================================

    // navegação
    'nav_mesa'         => 'A Mesa',
    'nav_indicadores'  => 'Indicadores',
    'nav_ferramentas'  => 'Ferramentas',
    'nav_laboratorio'  => 'Laboratório',
    'nav_especialista' => 'Especialista',
    'nav_blog'         => 'Blog',
    'nav_menu'         => 'Menu',

    // hero (fallbacks de $hero/$technology)
    'hero_cta'           => 'Receber leitura da mesa',
    'hero_badge'         => 'Mesa de câmbio com tecnologia e leitura consultiva',
    'hero_title'         => 'Simuladores de câmbio para decidir com mais clareza antes de fechar a operação.',
    'hero_subtitle'      => 'Use cenários para importação, exportação, hedge, trade finance e operações 4131. Depois, a {brand} leva a operação para múltiplas instituições financeiras e apresenta a alternativa mais aderente ao seu momento.',
    'hero_wa'            => 'Chamar no WhatsApp',
    'hero_secondary_cta' => 'Abrir simuladores',
    'tech_label'         => 'Mesa {brand}',
    'tech_stat1_label'   => 'instituições financeiras monitoradas',
    'tech_stat2_label'   => 'anos de experiência no mercado financeiro',
    'tech_stat3_label'   => 'visão sobre câmbio, hedge, trade finance e funding',

    // strip de fluxo
    'strip_lead' => 'Fluxo recomendado',
    'strip_1'    => 'Ajuste as premissas econômicas da operação',
    'strip_2'    => 'Teste impacto em custo, receita, hedge ou funding',
    'strip_3'    => 'Envie o cenário para a mesa',
    'strip_4'    => 'Receba uma leitura comparativa entre múltiplas instituições',

    // autoridade (quem-somos)
    'auth_label' => 'A mesa por trás dos simuladores',
    'auth_title' => 'Câmbio estruturado com leitura consultiva, não com produto de prateleira.',
    'auth_desc'  => 'A {brand} é uma boutique financeira fundada por <strong>Vinicius Teixeira</strong>, com mais de 16 anos de experiência em câmbio, crédito e mercado de capitais. O modelo é diferente: em vez de empurrar cotação, a mesa lê a operação, compara entre mais de 10 instituições financeiras e recomenda a estrutura mais eficiente para cada caso.',
    'auth_card1_label' => 'Câmbio Estruturado',
    'auth_card1_title' => 'Operações desenhadas sob medida para importadores e exportadores.',
    'auth_card1_desc'  => 'Contratos a termo, NDF, swaps e proteções combinadas para travar custo, proteger margem e dar previsibilidade ao fluxo de caixa. A cotação final vem de uma leitura comparativa entre múltiplas instituições.',
    'auth_card2_label' => 'Hedge e Proteção Cambial',
    'auth_card2_title' => 'Proteção como decisão de margem, não como produto genérico.',
    'auth_card2_desc'  => 'Hedge desenhado conforme exposição real, prazo de liquidação, repasse ao preço final e capacidade de absorver volatilidade. A mesa avalia se proteger faz sentido antes de propor a estrutura.',
    'auth_card3_label' => 'Trade Finance e Funding',
    'auth_card3_title' => 'ACC, FINIMP, 4131 e outras estruturas lidas no mesmo raciocínio.',
    'auth_card3_desc'  => 'A escolha entre financiar embarque, antecipar recebível, alongar caixa ou captar offshore depende da etapa do fluxo, da pressão de caixa e do custo real comparado. A mesa roteia para a melhor alternativa.',
    'boutique_note'    => '<strong>Modelo boutique:</strong> a {brand} não é banco e não tem produto próprio para distribuir. Isso permite recomendar a instituição e a estrutura mais aderente ao momento do cliente, sem conflito de interesse. Os simuladores abaixo são o primeiro passo para dimensionar a operação antes de levá-la à mesa.',

    // ===================================================================
    // 4c-2 — indicadores + seção de ferramentas + arrays (indicatorCards/toolCards)
    // ===================================================================

    // indicadores (tickers USD/BRL/SELIC... ficam literais)
    'ind_ref_label'   => 'Indicadores de referência',
    'ind_title'       => 'Os cenários partem de uma base econômica acompanhada de forma recorrente.',
    'ind_desc_label'  => 'Competência atual:',
    'ind_desc_2'      => 'SELIC, inflação, SOFR, spread e outras premissas servem como ponto de partida para a leitura inicial da operação.',
    'ind_hint_usdbrl' => 'referência base para os cenários',
    'ind_hint_spread' => 'default inicial da mesa',
    'ind_hint_selic'  => 'piso local para custo de capital',
    'ind_hint_cdi'    => 'benchmark onshore usado nos comparativos',
    'ind_hint_ipca'   => 'pressão macro e custo real',
    'ind_hint_sofr'   => 'base offshore para 4131',

    // seção ferramentas
    'tools_label' => 'Ferramentas de decisão',
    'tools_title' => 'Cinco frentes para entender preço, margem, proteção e funding antes do fechamento.',
    'tools_desc'  => "As simulações ajudam a amadurecer a conversa com a mesa. A contratação final continua dependendo da leitura consultiva,\n                    da documentação e das condições efetivas de mercado.",
    'tool_pill'   => 'Simulação orientada',

    // toolCards (marcas IMP/EXP/HDG/4131/TF ficam literais)
    'tc_import_eyebrow' => 'Importadores',
    'tc_import_title'   => 'Pressão de custo na importação',
    'tc_import_desc'    => 'Compare o custo em BRL hoje com um cenário de estresse cambial e entenda se a margem comporta operar sem proteção.',
    'tc_import_b1'      => 'Custo base em reais da fatura.',
    'tc_import_b2'      => 'Impacto de um movimento adverso no câmbio.',
    'tc_import_b3'      => 'Leitura sobre necessidade de hedge.',
    'tc_import_btn'     => 'Simular importação',
    'tc_export_eyebrow' => 'Exportadores',
    'tc_export_title'   => 'Proteção de receita em BRL',
    'tc_export_desc'    => 'Projete o efeito de uma queda do câmbio na receita e avalie se faz sentido travar um piso antes da liquidação.',
    'tc_export_b1'      => 'Receita líquida estimada em BRL.',
    'tc_export_b2'      => 'Gap entre cenário protegido e cenário de queda.',
    'tc_export_b3'      => 'Cobertura de custos ou caixa mínimo.',
    'tc_export_btn'     => 'Simular exportação',
    'tc_hedge_eyebrow'  => 'Decisão de hedge',
    'tc_hedge_title'    => 'Hedge x exposição aberta',
    'tc_hedge_desc'     => 'Meça o custo indicativo da proteção contra a perda potencial de operar aberto e compare com a margem disponível.',
    'tc_hedge_b1'       => 'Break-even do hedge.',
    'tc_hedge_b2'       => 'Perda potencial sem proteção.',
    'tc_hedge_b3'       => 'Pressão sobre a margem da operação.',
    'tc_hedge_btn'      => 'Diagnosticar hedge',
    'tc_funding_eyebrow' => 'Funding internacional',
    'tc_funding_title'  => 'Viabilidade de uma operação 4131',
    'tc_funding_desc'   => 'Compare custo local e offshore, hedge e fees para saber se vale aprofundar a estrutura com a mesa.',
    'tc_funding_b1'     => 'Custo anual indicativo onshore x offshore.',
    'tc_funding_b2'     => 'Economia potencial em BRL no prazo da operação.',
    'tc_funding_b3'     => 'Filtro inicial para ticket, prazo e proteção.',
    'tc_funding_btn'    => 'Avaliar 4131',
    'tc_trade_eyebrow'  => 'Trade finance',
    'tc_trade_title'    => 'Roteador de estruturas de trade finance',
    'tc_trade_desc'     => 'Descubra se o caso pende mais para ACC, ACE, FINIMP, supplier credit ou carta de crédito antes da conversa com o time.',
    'tc_trade_b1'       => 'Leitura por etapa do fluxo internacional.',
    'tc_trade_b2'       => 'Estruturas sugeridas conforme prazo e objetivo.',
    'tc_trade_b3'       => 'Prioridade para caixa, garantia ou alongamento.',
    'tc_trade_btn'      => 'Mapear estrutura',

    // ===================================================================
    // 4c-3 — laboratório de cenário (5 forms). Heads reusam tc_*_eyebrow.
    // ===================================================================
    'lab_label'        => 'Laboratório de cenário',
    'lab_title'        => 'Modele a operação antes de pedir cotação ou estruturação.',
    'lab_desc'         => 'Cada resultado é indicativo. O fechamento final depende da leitura de documentação, fluxo, instituição, prazo, garantia e momento de mercado.',
    'lab_tablist_aria' => 'Escolha a ferramenta',

    // labels compartilhadas
    'lab_usdbrl_atual' => 'USD/BRL atual',
    'lab_spread_pct'   => 'Spread comercial (%)',
    'lab_sim'          => 'Sim',
    'lab_nao'          => 'Não',

    // form: importação
    'lab_import_q'      => 'Quanto do seu custo em BRL fica exposto se o câmbio andar contra?',
    'lab_import_amount' => 'Valor da fatura (USD)',
    'lab_import_stress' => 'USD/BRL estressado',
    'lab_iof'           => 'IOF (%)',
    'lab_import_days'   => 'Dias até fechamento',
    'lab_import_sale'   => 'Receita projetada em BRL (opcional)',

    // form: exportação
    'lab_export_q'        => 'Qual receita em BRL você perde se o câmbio cair antes da liquidação?',
    'lab_export_amount'   => 'Recebível em moeda (USD)',
    'lab_export_downside' => 'USD/BRL em queda',
    'lab_export_floor'    => 'Câmbio piso desejado',
    'lab_export_days'     => 'Dias até liquidação',
    'lab_export_cost'     => 'Custo ou caixa mínimo em BRL (opcional)',

    // form: hedge
    'lab_hedge_q'        => 'Quanto custa proteger versus o tamanho da perda potencial operando aberto?',
    'lab_hedge_exposure' => 'Exposição em USD',
    'lab_hedge_move'     => 'Movimento adverso (%)',
    'lab_hedge_margin'   => 'Margem da operação (%)',
    'lab_hedge_cost'     => 'Custo mensal do hedge (%)',
    'lab_hedge_months'   => 'Meses até liquidação',

    // form: 4131
    'lab_4131_head'      => 'Operação 4131',
    'lab_4131_q'         => 'Faz sentido aprofundar um funding offshore em vez de ficar 100% onshore?',
    'lab_4131_principal' => 'Principal em USD',
    'lab_4131_tenor'     => 'Prazo (meses)',
    'lab_4131_sofr'      => 'SOFR base (%)',
    'lab_4131_offspread' => 'Spread offshore (%)',
    'lab_4131_local'     => 'Base local CDI/SELIC (%)',
    'lab_4131_onspread'  => 'Spread onshore (%)',
    'lab_4131_hedge'     => 'Hedge mensal (%)',
    'lab_4131_fee'       => 'Fees de estrutura (%)',
    'lab_4131_natural'   => 'Receita natural em moeda forte (%)',

    // form: trade finance
    'lab_trade_q'          => 'Qual estrutura faz mais sentido para o ponto do fluxo internacional em que sua empresa está?',
    'lab_trade_profile'    => 'Perfil',
    'lab_trade_stage'      => 'Objetivo principal',
    'lab_trade_ticket'     => 'Ticket em USD',
    'lab_trade_tenor'      => 'Prazo (dias)',
    'lab_trade_collateral' => 'Tem colateral/garantia?',
    'lab_trade_natural'    => 'Tem hedge natural?',
    'lab_opt_importer'  => 'Importador',
    'lab_opt_exporter'  => 'Exportador',
    'lab_opt_both'      => 'Importador e exportador',
    'lab_opt_pre'       => 'Financiar antes do embarque',
    'lab_opt_post'      => 'Antecipar depois do embarque',
    'lab_opt_pay'       => 'Pagar fornecedor e alongar caixa',
    'lab_opt_guarantee' => 'Dar mais segurança para a contraparte',
    'lab_opt_term'      => 'Alongar prazo da operação',
];
