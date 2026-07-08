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
];
