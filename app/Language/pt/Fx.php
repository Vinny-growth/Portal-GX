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
];
