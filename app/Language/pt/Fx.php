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

    // ===================================================================
    // 4c-4 — seção mesa-gx + card do especialista/lead + sticky bar
    // ===================================================================

    // mesa-gx (label reusa tech_label; índices 01-05 e ícones literais)
    'mesa_title'   => 'Tecnologia para comparar. Experiência para fechar a operação certa.',
    'mesa_desc'    => 'A mesa usa agente de IA para cruzar cotação, spread, prazo, documentação e aderência operacional entre múltiplas instituições. O foco não é a menor taxa isolada. É a estrutura mais eficiente para a necessidade real do cliente.',
    'mesa_c1_label' => 'Cotação inteligente',
    'mesa_c1_title' => 'Mais de 10 instituições na mesma mesa de comparação.',
    'mesa_c1_desc'  => 'Bancos de câmbio e corretoras entram na leitura para buscar a operação mais eficiente para o contexto do cliente.',
    'mesa_c2_label' => 'Proteção',
    'mesa_c2_title' => 'Hedge como decisão de margem, não como produto de prateleira.',
    'mesa_c2_desc'  => 'A proteção é desenhada conforme exposição, prazo, repasse e capacidade de absorver volatilidade.',
    'mesa_c3_label' => 'Trade finance',
    'mesa_c3_title' => 'ACC, ACE, FINIMP e outras estruturas lidas no mesmo raciocínio.',
    'mesa_c3_desc'  => 'A escolha depende de etapa do fluxo, pressão de caixa, risco de contraparte e timing operacional.',
    'mesa_c4_label' => 'Funding',
    'mesa_c4_title' => 'Operações 4131 entram quando a equação de custo, prazo e proteção fecha.',
    'mesa_c4_desc'  => 'O simulador filtra viabilidade. A mesa valida premissas, hedge, documentação e governança da operação.',
    'mesa_c5_label' => 'Experiência',
    'mesa_c5_title' => 'Time com mais de 16 anos de experiência atendendo operações de diferentes portes.',
    'mesa_c5_desc'  => 'Da demanda recorrente de comércio exterior a estruturas sofisticadas de funding, a leitura parte da operação real.',

    // card do especialista / lead
    'lead_label' => 'Leve o cenário para a mesa',
    'lead_title' => 'Receba uma leitura consultiva da sua operação de câmbio.',
    'lead_desc'  => 'Envie o seu cenário e nossa mesa avalia cotação, estrutura de hedge, trade finance e melhor janela de execução para o seu fluxo.',
    'lead_live_eyebrow' => 'Ferramenta ativa',
    'lead_live_title'   => 'Simulador de importação',
    'lead_live_label'   => 'impacto indicativo em cenário de estresse',
    'lead_live_copy'    => 'Preencha os campos para gerar uma leitura executiva da exposição.',
    'lead_cred_1'  => 'Mais de 10 instituições financeiras na cotação comparativa',
    'lead_cred_2'  => '16+ anos de experiência no mercado financeiro e operações internacionais',
    'lead_cred_3'  => 'Retorno em até 1 dia útil após o envio do cenário',
    'lead_chip_wa'   => 'WhatsApp',
    'lead_chip_blog' => 'Ver análises técnicas',
    'lead_form_title' => 'Fale com a mesa de câmbio {brand}',
    'lead_form_desc'  => 'Compartilhe os dados da operação. O retorno considera preço, prazo, risco, funding e documentação.',
    'lead_nome'    => 'Nome',
    'lead_email'   => 'E-mail',
    'lead_empresa' => 'Empresa',
    'lead_opcional' => 'Opcional',
    'lead_complemento'     => 'Complemento',
    'lead_complemento_opt' => '(opcional)',
    'lead_msg_ph'    => 'Ex.: vencimento da fatura, exposição mensal, se já tentou hedge antes.',
    'lead_terms_pre'  => 'Li e concordo com os',
    'lead_terms_link' => 'termos e condições',
    'lead_button'    => 'Solicitar leitura da mesa',
    'lead_note'      => 'Retorno em até 1 dia útil. Priorizamos operações com fechamento nos próximos 30 dias.',

    // sticky bar
    'sticky_text' => 'Cenário pronto. Fale com um especialista.',
    'sticky_cta'  => 'Agendar reunião',

    // ===================================================================
    // 4c-5 — microcopy do JS (dicionário L + fill; {x}=placeholder runtime).
    // Só USER-FACING; leadSummary/CRM/analytics ficam literais no JS.
    // ===================================================================
    'js_dias' => ' dias',

    // renderResult + WhatsApp helper
    'js_r_list_title' => 'Leitura inicial da mesa',
    'js_r_cta_p'      => 'Esse cenário merece uma leitura da mesa.',
    'js_r_cta_btn'    => 'Agendar conversa com especialista',
    'js_wa_tool_fallback' => 'sua conversa com a mesa',
    'js_wa_copy_tool'     => 'Mensagem pronta para {tool}.',
    'js_wa_copy_default'  => 'Mensagem pronta para iniciar a conversa com a mesa de câmbio.',

    // computeImport
    'js_im_ins1' => 'Sem proteção, o custo sairia de {b} para {s} se o USD/BRL fosse a {r}.',
    'js_im_ins2' => 'O impacto indicativo da exposição aberta é de {v} antes de negociar eventual repasse com fornecedor ou cliente.',
    'js_im_ins3' => 'Com receita projetada de {v}, a margem iria de {a} para {b}.',
    'js_im_ins3_alt' => 'Se você informar a receita projetada em BRL, a mesa também consegue ler compressão de margem no lote.',
    'js_im_ins_hedge' => 'Leitura inicial: vale comparar NDF, termo ou trava parcial para reduzir a pressão de caixa do fechamento.',
    'js_im_ins_ok'    => 'Leitura inicial: a exposição parece administrável, mas ainda vale testar cotação e proteção parcial com a mesa.',
    'js_im_badge_hedge' => 'Hedge recomendado',
    'js_im_badge_ok'    => 'Monitorar exposição',
    'js_im_title_hedge' => 'A margem tende a sofrer se o câmbio andar contra.',
    'js_im_title_ok'    => 'A exposição parece controlável, mas merece leitura de mesa.',
    'js_im_desc' => 'O cálculo compara o custo em BRL no cenário atual com um cenário cambial mais pressionado.',
    'js_im_m1' => 'Custo base em BRL', 'js_im_m2' => 'Custo em estresse', 'js_im_m3' => 'Impacto da exposição', 'js_im_m4' => 'Prazo até fechamento',
    'js_im_copy_hedge' => 'O cenário sugere discutir hedge ou trava parcial antes do fechamento.',
    'js_im_copy_ok'    => 'A exposição parece menos agressiva, mas a mesa pode buscar execução mais eficiente.',

    // computeExport
    'js_ex_ins1' => 'No cenário atual, a liquidação indicativa seria de {v} líquidos de spread.',
    'js_ex_ins2' => 'Se o câmbio cair para {r}, a receita cairia para {v}.',
    'js_ex_ins3' => 'Com piso em {r}, a operação protegeria aproximadamente {v} versus o cenário de queda.',
    'js_ex_ins_min_no'  => 'O cenário de queda não cobre o caixa mínimo informado de {v}.',
    'js_ex_ins_min_yes' => 'Mesmo no cenário de queda, a receita ainda cobre o caixa mínimo informado de {v}.',
    'js_ex_ins_min_alt' => 'Se você informar o caixa mínimo em BRL, a mesa consegue avaliar melhor o piso operacional necessário.',
    'js_ex_ins_hedge' => 'Leitura inicial: faz sentido discutir trava de piso, ACC/ACE ou execução escalonada para proteger a conversão.',
    'js_ex_ins_ok'    => 'Leitura inicial: o câmbio ainda parece acomodar a operação, mas vale comparar alternativas de proteção com a mesa.',
    'js_ex_badge_hedge' => 'Piso recomendado', 'js_ex_badge_ok' => 'Receita monitorada',
    'js_ex_title_hedge' => 'A queda do câmbio pode comprimir a receita em BRL.',
    'js_ex_title_ok'    => 'A receita parece mais equilibrada, mas ainda pede disciplina de execução.',
    'js_ex_desc' => 'O cálculo compara receita líquida atual, cenário de queda e cenário protegido.',
    'js_ex_m1' => 'Receita hoje', 'js_ex_m2' => 'Receita em queda', 'js_ex_m3' => 'Receita protegida', 'js_ex_m4' => 'Diferença protegida',
    'js_ex_livelabel' => 'proteção indicativa de receita versus cenário de queda',
    'js_ex_copy_hedge' => 'A operação tende a ganhar previsibilidade se você travar um piso de conversão.',
    'js_ex_copy_ok'    => 'A exposição parece menos crítica, mas a mesa pode calibrar a melhor janela de venda.',

    // computeHedge
    'js_hd_ins1' => 'Operando aberto, uma variação adversa de {p} consumiria aproximadamente {v}.',
    'js_hd_ins2' => 'O custo indicativo do hedge para {m} meses seria de {v}.',
    'js_hd_ins3' => 'O break-even da proteção fica próximo de {p} de movimento cambial no período.',
    'js_hd_ins4' => 'A perda potencial consome cerca de {p} da margem estimada da operação.',
    'js_hd_ins_hedge' => 'Leitura inicial: a assimetria favorece discutir hedge total ou parcial com a mesa.',
    'js_hd_ins_ok'    => 'Leitura inicial: a proteção pode ser seletiva, mas ainda vale comparar custos e estruturas antes de decidir operar aberto.',
    'js_hd_badge_hedge' => 'Assimetria pró-hedge', 'js_hd_badge_ok' => 'Proteção seletiva',
    'js_hd_title_hedge' => 'O custo da proteção parece menor que o risco de ficar aberto.',
    'js_hd_title_ok'    => 'A decisão pode admitir proteção parcial, mas não deve ignorar o risco.',
    'js_hd_desc' => 'O simulador compara perda potencial, custo indicativo do hedge e espaço de margem da operação.',
    'js_hd_m1' => 'Exposição em BRL', 'js_hd_m2' => 'Perda potencial', 'js_hd_m3' => 'Custo do hedge', 'js_hd_m4' => 'Risco / custo',
    'js_hd_livelabel' => 'risco potencial em relação ao custo da proteção',
    'js_hd_copy_hedge' => 'O quadro sugere que vale aprofundar a proteção antes da execução.',
    'js_hd_copy_ok'    => 'A decisão pode admitir hedge parcial ou tático, mas com leitura de margem.',

    // computeFunding4131
    'js_fd_ins1' => 'O custo anual indicativo onshore fica em {p} considerando base local e spread adicional.',
    'js_fd_ins2' => 'O custo anual indicativo offshore fica em {p} somando SOFR, spread, hedge ajustado e fees.',
    'js_fd_ins3_off' => 'A diferença favorece o offshore em cerca de {p} ao ano, ou {v} no prazo analisado.',
    'js_fd_ins3_on'  => 'A diferença favorece o onshore em cerca de {p} ao ano no prazo analisado.',
    'js_fd_ins4_yes' => 'A presença de hedge natural reduz parte do custo de proteção considerado no comparativo.',
    'js_fd_ins4_no'  => 'Sem hedge natural, o custo de proteção pesa integralmente na conta offshore.',
    'js_fd_ins_score'   => 'Leitura inicial: a tese de 4131 parece merecer aprofundamento com a mesa e validação jurídica/operacional.',
    'js_fd_ins_noscore' => 'Leitura inicial: a operação pode até ser viável, mas ainda não mostra folga suficiente para priorizar 4131 sem análise mais fina.',
    'js_fd_badge_yes' => '4131 faz sentido analisar', 'js_fd_badge_no' => 'Comparar com funding local',
    'js_fd_title_yes' => 'O offshore parece competitivo para esta operação.',
    'js_fd_title_no'  => 'A tese ainda precisa provar eficiência frente ao funding local.',
    'js_fd_desc' => 'O simulador compara custo anual indicativo local e internacional já considerando proteção e fees.',
    'js_fd_m1' => 'Custo onshore', 'js_fd_m2' => 'Custo offshore', 'js_fd_m3' => 'Diferença anual', 'js_fd_m4' => 'Economia indicativa',
    'js_fd_livelabel' => 'diferença anual indicativa entre funding local e offshore',
    'js_fd_copy_yes' => 'Os números sugerem que vale aprofundar a estrutura 4131 com hedge e documentação.',
    'js_fd_copy_no'  => 'A conta ainda pede mais cuidado. Talvez o funding local ou outra estrutura continue mais eficiente.',

    // computeTrade
    'js_tr_struct_acc'      => 'ACC para financiar antes do embarque e aliviar caixa da produção.',
    'js_tr_struct_ace'      => 'ACE ou desconto internacional para antecipar recursos depois do embarque.',
    'js_tr_struct_finimp'   => 'FINIMP ou supplier credit para alongar o pagamento ao fornecedor.',
    'js_tr_struct_lc'       => 'Carta de crédito ou garantia bancária para reforçar a segurança da contraparte.',
    'js_tr_struct_supplier' => 'Supplier credit, financiamento estruturado ou 4131 para ganhar prazo com critério.',
    'js_tr_struct_custom'   => 'A operação pede desenho sob medida entre câmbio, funding e garantia.',
    'js_tr_struct_big'      => 'Pelo ticket e prazo, vale comparar uma estrutura mais elaborada com funding internacional.',
    'js_tr_struct_nocol'    => 'Sem colateral claro, a mesa precisa priorizar instituições e produtos mais aderentes ao risco da operação.',
    'js_tr_struct_nohedge'  => 'Sem hedge natural, a proteção cambial precisa entrar cedo na conversa para evitar que o funding vire nova exposição.',
    'js_tr_profile_importer' => 'importador', 'js_tr_profile_exporter' => 'exportador', 'js_tr_profile_both' => 'importador e exportador',
    'js_tr_ins_profile' => 'Perfil da operação: {v}.',
    'js_tr_stage_pre' => 'financiar antes do embarque', 'js_tr_stage_post' => 'antecipar depois do embarque', 'js_tr_stage_pay' => 'pagar fornecedor e alongar caixa', 'js_tr_stage_guarantee' => 'dar segurança para a contraparte', 'js_tr_stage_term' => 'ganhar prazo para a operação', 'js_tr_stage_default' => 'avaliar estrutura',
    'js_tr_ins_stage' => 'Objetivo principal: {v}.',
    'js_tr_ins_prazo' => 'Prazo analisado: {d} dias para um ticket de aproximadamente USD {t}.',
    'js_tr_badge' => 'Estrutura prioritária',
    'js_tr_desc'  => 'O roteador abaixo não substitui análise de crédito e documentação, mas ajuda a separar a conversa certa desde o início.',
    'js_tr_m1' => 'Ticket', 'js_tr_m2' => 'Prazo', 'js_tr_m3' => 'Colateral', 'js_tr_m4' => 'Hedge natural',
    'js_tr_opcoes' => ' opções',
    'js_tr_livelabel' => 'estruturas sugeridas para aprofundar com a mesa',
    'js_tr_copy' => 'O objetivo aqui é chegar na instituição certa com uma tese mais madura de execução.',

    // status
    'js_enviando'      => 'Enviando...',
    'js_status_loading' => 'Enviando cenário para a mesa...',
    'js_send_fail'     => 'Falha ao enviar a simulação.',
    'js_send_err'      => 'Não foi possível enviar o cenário agora.',
    'js_success'       => 'Recebemos o seu cenário. A mesa de câmbio da {brand} vai avaliar o melhor caminho para a sua operação.',

    // ===================================================================
    // LOTE 5b — defaults de config da página (MarketingSimulatorsDefaults).
    // Camada INDEPENDENTE dos fallbacks da view acima (mesmo valor em alguns
    // casos, mas seed de config != fallback). URLs de âncora, valores de stat
    // e tickers numéricos ficam literais no library.
    // ===================================================================
    'def_hero_badge'         => 'Mesa de câmbio com tecnologia e leitura consultiva',
    'def_hero_title'         => 'Simuladores de câmbio para importar, exportar, proteger margem e estruturar funding com mais clareza.',
    'def_hero_subtitle'      => 'Use cenários para importação, exportação, hedge, trade finance e operações 4131. Depois, a {brand} compara sua operação em mais de 10 instituições financeiras e apresenta a alternativa mais aderente ao seu momento.',
    'def_hero_primary_cta'   => 'Agendar com especialista',
    'def_hero_secondary_cta' => 'Abrir simuladores',
    'def_hero_proof' => [
        ['text' => 'Mais de 10 instituições financeiras entre bancos de câmbio e corretoras no processo de cotação.'],
        ['text' => 'Mais de 16 anos de experiência do time no mercado financeiro e em operações internacionais.'],
        ['text' => 'Leitura integrada de preço, prazo, proteção, funding e execução operacional.'],
    ],
    'def_tech_label'       => 'Mesa {brand}',
    'def_tech_title'       => 'Tecnologia para comparar. Experiência para fechar a operação certa.',
    'def_tech_desc'        => 'A mesa usa agente de IA para cruzar cotação, spread, prazo, documentação e aderência operacional entre múltiplas instituições. O foco não é a menor taxa isolada. É a estrutura mais eficiente para a necessidade real do cliente.',
    'def_tech_stat1_label' => 'instituições financeiras monitoradas',
    'def_tech_stat2_label' => 'anos de experiência no mercado financeiro',
    'def_tech_stat3_label' => 'visão sobre câmbio, hedge, trade finance e funding',
    'def_tech_signals' => [
        ['text' => 'Cotação comparativa entre bancos de câmbio e corretoras, com leitura de custo total e não só de taxa nominal.'],
        ['text' => 'Estruturação para importadores e exportadores com foco em margem, previsibilidade de caixa e timing operacional.'],
        ['text' => 'Diagnóstico consultivo para hedge, ACC, ACE, FINIMP, supplier credit e alternativas 4131.'],
    ],
    'def_ind_ref_label' => 'Indicadores de referência',
    'def_ind_note'      => 'Os resultados usam referências de mercado atualizadas periodicamente. O fechamento final depende da leitura da mesa, das instituições consultadas e das condições da operação.',
    'def_lead_label'     => 'Leve o cenário para a mesa',
    'def_lead_title'     => 'Agende uma conversa com a mesa de câmbio {brand}.',
    'def_lead_desc'      => 'Preencha os dados abaixo e nossa mesa entra em contato para agendar a conversa. O retorno considera cotação, hedge, trade finance e melhor janela de execução.',
    'def_lead_form_title' => 'Agende sua reunião com a mesa',
    'def_lead_form_desc'  => 'Preencha os dados para reservar sua conversa. Retorno em até 1 dia útil.',
    'def_lead_button'    => 'Quero falar com um especialista',
    'def_lead_success'   => 'Recebemos o seu cenário. A mesa de câmbio da {brand} vai entrar em contato para agendar a conversa.',
];
