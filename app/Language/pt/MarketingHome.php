<?php

/**
 * Camada de marketing — DEFAULTS de config da home institucional + hub de
 * simuladores (App\Libraries\MarketingHomeDefaults; white-label — Fase 2 i18n,
 * Lote 5c) — PORTUGUÊS.
 *
 * Consumido por lang('MarketingHome.<chave>') e, quando a string carrega o nome
 * da marca, por brandLang('MarketingHome.<chave>') ({brand} -> brand('display_name')).
 * URLs/âncoras, flags (enabled/show_*), cores (accent), paths e labels curtos de
 * simulador (FX/4131/CAP...) ficam literais no library. Para outro idioma/marca:
 * criar app/Language/<locale>/MarketingHome.php.
 *
 * Prefixos:
 *   cfg_   = getHomeConfigDefaults (home institucional /)
 *   vert_  = getBusinessVerticals (cards de vertical)
 *   sim_   = getSimulatorCatalog (definições de simulador)
 *   topic_ = getSimulatorTopics (frentes do hub /simuladores)
 */
return [
    // ===================================================================
    // 5c-1 — getHomeConfigDefaults (home institucional)
    // ===================================================================

    // nav
    'cfg_nav_cta' => 'Começar agora',
    'cfg_nav_ql1' => 'Soluções',
    'cfg_nav_ql2' => 'Simuladores',
    'cfg_nav_ql3' => 'Clipping',
    'cfg_nav_ql4' => 'Insights',
    'cfg_nav_ql5' => 'Especialista',

    // hero stats (labels)
    'cfg_stat_simulators' => 'simuladores disponíveis',
    'cfg_stat_verticals'  => 'frentes de atuação',
    'cfg_stat_insights'   => 'análises recentes',

    // hero
    'cfg_hero_badge'         => 'Estruturação financeira com profundidade técnica',
    'cfg_hero_title'         => 'Soluções sofisticadas em capital, proteção e patrimônio.',
    'cfg_hero_subtitle'      => 'A {brand} estrutura crédito, câmbio, consórcios, seguros e investimentos para empresas, empresários e famílias empresárias que precisam decidir com clareza, previsibilidade e visão de longo prazo.',
    'cfg_hero_primary_cta'   => 'Falar com especialista',
    'cfg_hero_secondary_cta' => 'Explorar simuladores',
    'cfg_hero_proof1_title'  => 'Capital e risco',
    'cfg_hero_proof1_text'   => 'Crédito, câmbio, seguros e patrimônio lidos em conjunto.',
    'cfg_hero_proof2_title'  => 'Ferramentas práticas',
    'cfg_hero_proof2_text'   => 'Simuladores para antecipar custo, prazo e estrutura.',
    'cfg_hero_proof3_title'  => 'Relação consultiva',
    'cfg_hero_proof3_text'   => 'Direcionamento para a vertical mais aderente ao caso.',

    // trust strip
    'cfg_strip_lead' => 'Atuação integrada',
    'cfg_strip1'     => 'Crédito Estruturado',
    'cfg_strip2'     => 'Câmbio & Trade Finance',
    'cfg_strip3'     => 'Consórcios',
    'cfg_strip4'     => 'Seguros Corporativos',
    'cfg_strip5'     => 'Wealth Management',

    // seção verticais (cabeçalho)
    'cfg_vert_label' => 'Verticais de negócio',
    'cfg_vert_title' => 'Cinco frentes especializadas. Um critério integrado.',
    'cfg_vert_desc'  => 'Da liquidez de curto prazo ao patrimônio de longo prazo, cada frente opera com profundidade técnica e visão de conjunto.',

    // processo
    'cfg_proc_label' => 'Como atuamos',
    'cfg_proc_title' => 'Da leitura do problema à estrutura executada.',
    'cfg_proc_desc'  => 'Combinamos análise financeira, modelagem comparativa e acompanhamento executivo para reduzir ruído e ganhar velocidade na decisão.',
    'cfg_proc1_title' => 'Diagnóstico',
    'cfg_proc1_desc'  => 'Mapeamos caixa, exposição cambial, custo de capital e objetivo patrimonial antes de qualquer recomendação.',
    'cfg_proc2_title' => 'Estruturação',
    'cfg_proc2_desc'  => 'Comparamos alternativas, modelamos cenários e montamos a estrutura mais aderente ao momento da empresa.',
    'cfg_proc3_title' => 'Execução',
    'cfg_proc3_desc'  => 'Acompanhamos a implementação para transformar a análise em decisão executada com critério e rastreabilidade.',

    // seção simuladores (cabeçalho)
    'cfg_sim_label' => 'Simuladores',
    'cfg_sim_title' => 'Ferramentas para antecipar custo, risco e estrutura.',
    'cfg_sim_desc'  => 'Use os simuladores para testar cenários antes da conversa comercial e chegue com a demanda mais madura.',
    'cfg_sim_cta'   => 'Ver catálogo completo',

    // clippings
    'cfg_clip_label'    => 'Clipping de notícias',
    'cfg_clip_title'    => '{brand} em grandes portais.',
    'cfg_clip_desc'     => 'Acompanhe a presença da {brand} nos principais veículos de economia, finanças e mercado.',
    'cfg_clip_item_cta' => 'Ler matéria',

    // parceiros
    'cfg_part_label' => 'Parceiros de qualidade',
    'cfg_part_title' => 'Instituições que fortalecem a atuação da {brand}.',
    'cfg_part_desc'  => 'A {brand} opera com instituições de referência para garantir execução, governança e acesso a soluções de mercado.',

    // blog
    'cfg_blog_label'        => 'Conteúdo técnico',
    'cfg_blog_title'        => 'Análises para acompanhar funding, câmbio, patrimônio e mercado.',
    'cfg_blog_desc'         => 'Publicamos leituras práticas para quem decide com responsabilidade financeira e precisa de contexto para agir.',
    'cfg_blog_featured_cta' => 'Ler análise',
    'cfg_blog_cta'          => 'Ver todos os artigos',

    // cta final
    'cfg_cta_label'     => 'Atendimento consultivo',
    'cfg_cta_title'     => 'Leve a demanda financeira para a frente certa desde o primeiro contato.',
    'cfg_cta_desc'      => 'Se a pauta envolver funding, hedge, recebíveis, consórcio, seguros ou patrimônio, centralize a conversa com a {brand}.',
    'cfg_cta_primary'   => 'Falar com o time',
    'cfg_cta_secondary' => 'Abrir simuladores',

    // lead / formulário
    'cfg_lead_label'       => 'Fale com a {brand}',
    'cfg_lead_title'       => 'Converse com um especialista.',
    'cfg_lead_desc'        => 'Compartilhe o contexto da empresa, da operação ou do objetivo patrimonial e direcionamos a conversa para a vertical mais aderente.',
    'cfg_lead_form_heading' => 'Envie sua demanda para o time {brand}',
    'cfg_lead_form_desc'    => 'Informe a estrutura, operação ou objetivo patrimonial. O retorno parte da vertical mais aderente.',
    'cfg_lead_form_button'  => 'Solicitar contato',
    'cfg_lead_msg_ph'       => 'Ex.: preciso revisar hedge cambial, custo de capital, recebíveis, consórcio, seguros ou carteira de investimentos.',
    'cfg_lead_sim_chip'     => 'Ver simuladores',
    'cfg_lead_blog_chip'    => 'Explorar blog',

    // ===================================================================
    // 5c-2 — getBusinessVerticals (cards de vertical). accent/URLs literais.
    // ===================================================================
    'vert_c1_title'  => 'Crédito Estruturado',
    'vert_c1_eyebrow' => 'Funding e capital de giro',
    'vert_c1_desc'   => 'Estruture capital, alongue prazos e compare linhas com mais clareza antes de negociar.',
    'vert_c1_link'   => 'Ver frente de crédito',
    'vert_c2_title'  => 'Câmbio e Trade Finance',
    'vert_c2_eyebrow' => 'Proteção cambial e execução',
    'vert_c2_desc'   => 'Combine hedge, fluxo internacional e leitura de exposição cambial em uma única frente.',
    'vert_c2_link'   => 'Explorar câmbio',
    'vert_c3_title'  => 'Consórcios Estruturados',
    'vert_c3_eyebrow' => 'Planejamento e alavancagem',
    'vert_c3_desc'   => 'Avalie fluxo de pagamento, contemplação e custo total antes de escolher a estrutura ideal.',
    'vert_c3_link'   => 'Abrir simulador',
    'vert_c4_title'  => 'Seguros',
    'vert_c4_eyebrow' => 'Proteção patrimonial e operacional',
    'vert_c4_desc'   => 'Desenhe coberturas aderentes ao risco real da empresa e traga a conversa para a mesa financeira.',
    'vert_c4_link'   => 'Falar com especialista',
    'vert_c5_title'  => 'Consultoria de Investimentos',
    'vert_c5_eyebrow' => 'Patrimônio, liquidez e estratégia',
    'vert_c5_desc'   => 'Conecte tesouraria, objetivos patrimoniais e alocação com uma leitura mais executável do patrimônio.',
    'vert_c5_link'   => 'Conhecer wealth',
];
