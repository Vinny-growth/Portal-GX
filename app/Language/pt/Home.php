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

    // Home institucional (HomeController::index + buildHomeDescription + buildHomeMarketingSchema)
    'home_meta_title'           => 'Câmbio estruturado, crédito e consultoria para empresas',
    'home_stat_simulators'      => 'simuladores disponíveis',
    'home_stat_verticals'       => 'frentes de atuação',
    'home_stat_insights'        => 'análises recentes',
    'home_description_fallback' => '{brand} oferece soluções em câmbio estruturado, crédito, proteção patrimonial, seguros, wealth e consultoria estratégica para empresas e famílias.',
    'home_faq' => [
        ['q' => 'O que é câmbio estruturado?', 'a' => 'Câmbio estruturado é uma operação financeira que combina contratos de câmbio com instrumentos de proteção (hedge), permitindo que empresas importadoras e exportadoras travem taxas, reduzam exposição cambial e planejem fluxo de caixa com previsibilidade. A {brand} estrutura essas operações sob medida para cada perfil de empresa.'],
        ['q' => 'Como funciona hedge cambial?', 'a' => 'Hedge cambial é uma estratégia de proteção contra variações na taxa de câmbio. Funciona por meio de contratos a termo (NDF), opções de câmbio ou swaps que permitem fixar uma taxa futura. Empresas que importam ou exportam usam hedge para eliminar o risco de oscilação do dólar sobre suas margens operacionais.'],
        ['q' => 'Quais serviços a {brand} oferece para empresas?', 'a' => 'A {brand} oferece câmbio estruturado, hedge cambial, trade finance, crédito corporativo, operações 4131 (funding internacional), consultoria em mercado de capitais e wealth advisory para patrimônio de famílias e empresários.'],
        ['q' => 'O que é uma operação 4131?', 'a' => 'A operação 4131 é um empréstimo internacional regulado pela Resolução 4131 do Banco Central, que permite a captação de recursos no exterior com taxas potencialmente mais competitivas. É indicada para empresas com exposição em moeda estrangeira ou que buscam diversificação de fontes de financiamento.'],
        ['q' => 'Como funciona a consultoria de wealth advisory da {brand}?', 'a' => 'O wealth advisory da {brand} oferece diagnóstico patrimonial, leitura de liquidez, tese de alocação e plano executivo de próximos passos para famílias, executivos e empresários. O processo começa com um mapeamento do patrimônio e fluxo de caixa, seguido de recomendações consultivas integradas.'],
    ],

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

    // Playbook Exportação Premium (HomeController::playbookExportacaoPremium).
    'pb_exp_wa'                => 'Olá! Vim pelo playbook Exportação Premium da {brand} e quero falar com a mesa de câmbio para travar receita em moeda forte em 2026.',
    'pb_exp_description'       => 'Playbook {brand} · Exportação Premium 2026: hedge de venda em camadas, ACC/ACE, Drawback e conta offshore para capturar o pico do dólar eleitoral em receita extra.',
    'pb_exp_title'            => 'Exportação Premium · Playbook 2026',
    'pb_exp_section'          => 'Câmbio · Exportação',
    'pb_exp_keywords'         => 'exportação, câmbio, hedge cambial, NDF venda, ACC, ACE, dólar eleitoral, trade finance, drawback, conta offshore, exportadores, {brand}',
    'pb_exp_breadcrumb_current' => 'Exportação Premium',
    'pb_exp_chapters' => [
        'Cenário de Oportunidade 2026',
        'Balança Comercial em Números',
        'O Pico do Dólar Eleitoral',
        'Setores Exportadores em Foco',
        'Simulações de Receita',
        'Hedge de Venda em Camadas',
        'ACC, ACE & Antecipação',
        'Drawback & Conta Offshore',
        'Ferramentas {brand}',
        'Checklist de Ação Imediata',
        'Conclusão e Próximos Passos',
    ],
    'pb_exp_faq' => [
        [
            'q' => 'Por que o "dólar eleitoral" é uma oportunidade para exportadores brasileiros em 2026?',
            'a' => 'Em anos eleitorais, o USD/BRL costuma atingir picos de estresse no Q3 — projeções da mesa {brand} apontam R$ 5,45 a R$ 5,55 entre agosto e outubro. Para exportadores que estruturam hedge de venda em camadas antes do pico, isso vira receita extra de até 13% por embarque versus liquidar no spot atual de R$ 4,91.',
        ],
        [
            'q' => 'Qual a diferença entre NDF Venda, Termo de Venda e ACC para exportadores?',
            'a' => 'NDF Venda fixa a taxa de venda futura sem entrega física da moeda — bom para 30 a 180 dias. Termo de Venda é com entrega da moeda na liquidação, exige DUE ou invoice. ACC (Adiantamento sobre Contrato de Câmbio) antecipa até 100% do valor em USD antes do embarque, com taxa SOFR + spread e hedge embutido. Cada um cobre uma necessidade diferente do pipeline.',
        ],
        [
            'q' => 'O que é hedge em camadas e por que é melhor que tentar acertar o pico do dólar?',
            'a' => 'Hedge em camadas é dividir o pipeline em 30/30/40% e travar cada parte em momentos diferentes da curva — primeira em junho, segunda no primeiro repique de julho/agosto, terceira no pico Q3. Captura ~85% do prêmio máximo sem o risco de perder a janela tentando cravar o topo exato.',
        ],
        [
            'q' => 'O que é Drawback e quanto economiza em uma operação de exportação?',
            'a' => 'Drawback é o regime aduaneiro que suspende II, IPI, PIS, COFINS e ICMS sobre insumos importados que serão usados na fabricação de produto exportado. Em insumos de US$ 200 mil a R$ 5,10, com tributos normais de ~30%, a economia direta chega a R$ 306 mil por lote.',
        ],
        [
            'q' => 'Vale a pena manter parte da receita em conta offshore (Bradesco Miami, BTG Cayman)?',
            'a' => 'Para volumes médios e altos, sim. Manter parte dos USD em conta offshore reduz dependência da liquidação no spot do dia, permite repatriar quando o câmbio for favorável e funciona como reserva estratégica em cenários fiscais adversos. Contas mais usadas: NCC no Brasil, Bradesco Miami / BB Americas para volume médio, BTG Cayman / Itaú BBA Nassau para reserva sofisticada.',
        ],
        [
            'q' => 'Empilhar hedge, ACC e Drawback eleva a receita em quanto?',
            'a' => 'Em uma operação real de US$ 1 milhão, o empilhamento de NDF Venda no pico Q3 (R$ 5,30 médio), Drawback sobre insumos (US$ 200k) e 30% mantido em conta offshore com timing favorável eleva a receita de R$ 4,91 mi (liquidando no spot Q2) para R$ 5,89 mi — ganho líquido de +R$ 981 mil ou +20%.',
        ],
    ],
];
