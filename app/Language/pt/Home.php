<?php

/**
 * Camada de marketing — strings do HomeController (white-label — Fase 2 i18n) — PORTUGUÊS.
 *
 * O token {brand} é trocado pelo nome da marca via brandLang() (single-source em
 * brand_settings). Strings sem marca usam lang(). Para outro idioma/marca:
 * criar app/Language/<locale>/Home.php e definir brand('locale').
 *
 * Migração incremental (sub-lotes por método do HomeController).
 */
return [
    // Central de simuladores (HomeController::simulatorsHub)
    'sim_hub_title'        => 'Central de simuladores {brand}',
    'sim_hub_description'  => 'Central de simuladores da {brand} para câmbio, trade finance, consórcio, crédito, mercado de capitais e recebíveis, organizada por frente e por ferramenta.',
    'sim_hub_wa'           => 'Olá! Vim pela Central de Simuladores da {brand} e quero falar com um especialista para identificar a solução mais adequada para minha empresa.',
    'sim_hub_stat_tools'   => 'ferramentas disponíveis',
    'sim_hub_stat_fronts'  => 'frentes organizadas',
    'sim_hub_stat_studies' => 'estudos cambiais em destaque',

    // Blog institucional (HomeController::blog + buildEditorialHomeData default)
    'blog_title'            => 'Blog {brand}',
    'blog_description'      => 'Conteúdo técnico sobre câmbio, crédito estruturado, mercado de capitais, consórcios e investimentos.',

    // Simulador de consórcio (HomeController::simuladorConsorcio)
    'consorcio_description' => 'Simule seu consórcio grátis. Compare com financiamento, planeje sua compra e descubra a rota de contemplação mais rápida com IA. 20+ administradoras analisadas pela {brand}.',
    'consorcio_wa'          => 'Olá! Vim pelo simulador de consórcio da {brand} e quero validar a melhor estratégia para o meu caso.',

    // Simulador de seguro de vida resgatável
    'seguro_wa'             => 'Olá! Fiz a simulação do Seguro de Vida Resgatável na {brand} e quero estruturar meu plano.',

    // Mensagens de WhatsApp (HomeController — lote 2). {brand} via brandLang.
    'home_wa'           => 'Olá! Vim pela home institucional da {brand} e quero falar com um especialista para entender a solução mais adequada para a minha empresa.',
    'fx_wa_default'     => 'Olá! Vim pela página de simuladores de câmbio da {brand} e tenho interesse em fazer um estudo para minhas operações de câmbio.',
    'fx_wa_import'      => 'Olá! Vim pela página de simuladores de câmbio da {brand} e quero estudar a exposição das minhas operações de importação.',
    'fx_wa_export'      => 'Olá! Vim pela página de simuladores de câmbio da {brand} e quero estudar a proteção de receita das minhas operações de exportação.',
    'fx_wa_hedge'       => 'Olá! Vim pela página de simuladores de câmbio da {brand} e tenho interesse em estudar uma estratégia de hedge cambial para minha empresa.',
    'fx_wa_funding4131' => 'Olá! Vim pela página de simuladores de câmbio da {brand} e tenho interesse em avaliar uma operação 4131 ou funding internacional.',
    'fx_wa_trade'       => 'Olá! Vim pela página de simuladores de câmbio da {brand} e tenho interesse em avaliar uma estrutura de trade finance para minha operação.',

    // Hub de câmbio (HomeController::simulatorsFxHub) — metadados + schema JSON-LD.
    // serviceType técnico (Câmbio estruturado/Hedge/Trade finance/4131) fica literal no controller.
    'fx_hub_title'                      => 'Simuladores de câmbio para importadores e exportadores',
    'fx_hub_description_fallback'       => 'Simuladores de câmbio, hedge, trade finance e operações 4131 para importadores e exportadores, com leitura consultiva da mesa {brand}.',
    'fx_hub_keywords_extra'             => ', simuladores de câmbio, hedge cambial, trade finance, operação 4131, importação, exportação',
    'fx_hub_schema_webpage_name'        => 'Simuladores de câmbio {brand}',
    'fx_hub_schema_service_name'        => 'Mesa de câmbio e trade finance {brand}',
    'fx_hub_schema_service_description' => 'Boutique financeira especializada em câmbio estruturado para importadores e exportadores. A mesa compara cotações entre mais de 10 instituições financeiras e recomenda a estrutura mais eficiente para cada operação.',
    'fx_hub_faq' => [
        ['q' => 'O que é câmbio estruturado para importadores e exportadores?', 'a' => 'Câmbio estruturado é uma operação que combina contratos de câmbio com instrumentos de proteção (hedge), permitindo que empresas importadoras e exportadoras travem taxas, reduzam exposição cambial e planejem fluxo de caixa com previsibilidade. A {brand} estrutura operações sob medida comparando cotações entre mais de 10 instituições financeiras.'],
        ['q' => 'Como funciona hedge cambial para empresas?', 'a' => 'Hedge cambial é uma estratégia de proteção contra variações na taxa de câmbio. Funciona por meio de contratos a termo (NDF), opções ou swaps que permitem fixar uma taxa futura. A mesa da {brand} avalia exposição real, prazo de liquidação e margem da operação antes de propor a estrutura de proteção mais eficiente.'],
        ['q' => 'Qual a diferença entre ACC, ACE e FINIMP?', 'a' => 'ACC (Adiantamento sobre Contrato de Câmbio) antecipa recursos ao exportador antes do embarque. ACE (Adiantamento sobre Cambiais Entregues) antecipa após o embarque. FINIMP (Financiamento à Importação) financia a compra de mercadorias do exterior. A escolha depende da etapa do fluxo internacional, pressão de caixa e custo comparado.'],
        ['q' => 'O que é uma operação 4131 e quando vale a pena?', 'a' => 'A operação 4131 é um empréstimo internacional que permite captar recursos no exterior com taxas potencialmente mais competitivas que o crédito local. Vale a pena quando o custo all-in (taxa base SOFR + spread offshore + hedge cambial + fees) é inferior ao custo equivalente onshore (CDI + spread local). O simulador da {brand} ajuda a filtrar a viabilidade antes de aprofundar a estrutura.'],
        ['q' => 'O que é uma boutique financeira de câmbio?', 'a' => 'Uma boutique financeira de câmbio é uma empresa independente que não tem produto próprio para distribuir. Isso permite recomendar a instituição e a estrutura mais aderente ao momento do cliente, sem conflito de interesse. A {brand} compara cotações de bancos de câmbio e corretoras para encontrar a operação mais eficiente para cada perfil de empresa.'],
    ],

    // Breadcrumb compartilhado dos playbooks
    'pb_breadcrumb_home'      => 'Início',
    'pb_breadcrumb_playbooks' => 'Playbooks',

    // Playbook Importação Blindada (HomeController::playbookImportacaoBlindada).
    // content_name/category de tracking (Meta) ficam literais no controller (identificadores de analytics).
    'pb_imp_wa'                => 'Olá! Vim pelo playbook Importação Blindada da {brand} e quero falar com a mesa de câmbio para proteger minhas operações em 2026.',
    'pb_imp_description'       => 'Playbook {brand} · Importação Blindada 2026: hedge cambial, antecipação de estoque e Ex-Tarifário para atravessar o "dólar eleitoral" com a margem intacta.',
    'pb_imp_title'            => 'Importação Blindada · Playbook 2026',
    'pb_imp_section'          => 'Câmbio · Importação',
    'pb_imp_keywords'         => 'importação, câmbio, hedge cambial, NDF, FINIMP, dólar eleitoral, trade finance, ex-tarifário, importadores, {brand}',
    'pb_imp_breadcrumb_current' => 'Importação Blindada',
    'pb_imp_chapters' => [
        'Cenário de Risco 2026',
        'Balança Comercial em Números',
        'O Dólar Eleitoral',
        'Análise Setorial',
        'Simulações de Impacto',
        'Hedge Cambial Defensivo',
        'Antecipação Tática de Estoque',
        'Ex-Tarifário & Benefícios Estaduais',
        'Ferramentas {brand}',
        'Checklist de Ação Imediata',
        'Conclusão e Próximos Passos',
    ],
    'pb_imp_faq' => [
        [
            'q' => 'O que é o "dólar eleitoral" e por que ele afeta importadores em 2026?',
            'a' => 'É o padrão recorrente de alta volatilidade cambial em anos de eleição presidencial brasileira. Em 2026, projeções da mesa {brand} indicam pico do USD/BRL entre R$ 5,45 e R$ 5,55 no Q3 (agosto a outubro), pressionando importadores que liquidem operações sem hedge — o custo do dólar contratado em agosto pode ser até 13% maior que o de maio.',
        ],
        [
            'q' => 'Qual a melhor estrutura de hedge cambial para importação?',
            'a' => 'Depende do perfil. NDF (Non-Deliverable Forward) é simples e funciona para 30 a 180 dias. Termo é mais barato quando há fatura/Incoterm definido. Swap migra dívida CDI para USD. Collar protege com piso e teto, mantendo parte do upside. A mesa {brand} compara custo entre 10+ instituições antes de recomendar a estrutura.',
        ],
        [
            'q' => 'Vale a pena antecipar estoque em maio para evitar o pico de Q3?',
            'a' => 'Para importações de bens de capital com lead time longo, sim. Simulações mostram que antecipar 3 meses de estoque a R$ 5,10 (maio) versus R$ 5,50 (agosto) economiza ~R$ 600 mil em uma operação de US$ 500 mil/mês — sem contar economia adicional de frete e prêmio de hedge evitado.',
        ],
        [
            'q' => 'Como funciona o Ex-Tarifário e quanto economiza?',
            'a' => 'Ex-Tarifário é a redução ou eliminação do Imposto de Importação (II) para produtos sem similar nacional, aprovado pela SECEX/MDIC. Em uma máquina de embalagem de US$ 100 mil, com II normal de 14%, o Ex-Tarifário aprovado zera o II e economiza R$ 71.400 a R$ 5,10. Validade de 2 anos, renovável.',
        ],
        [
            'q' => 'Empilhar Ex-Tarifário, benefício estadual e hedge dá quanto de economia?',
            'a' => 'Em uma operação real de US$ 100 mil de máquina de embalagem, o empilhamento de Ex-Tarifário (-14% II), benefício estadual SUDENE/SUDAM (-75% impostos federais e estaduais) e hedge cambial reduz o custo total em até 44,7% — de R$ 637.500 para R$ 352.525.',
        ],
        [
            'q' => 'Quando é a janela ideal para travar câmbio em 2026?',
            'a' => 'Maio e Junho. Em julho a janela começa a fechar conforme o mercado precifica o estresse eleitoral. Q3 (agosto a outubro) é o pico de risco. A recomendação da mesa {brand} para importadores é estruturar hedge defensivo em camadas começando agora, com prazo de 60 a 180 dias.',
        ],
    ],
];
