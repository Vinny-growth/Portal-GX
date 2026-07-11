<?php

/**
 * Camada de marketing — strings das views wealth/* (Wealth Advisory)
 * (white-label — Fase 2 i18n, lote Wealth) — PORTUGUÊS.
 *
 * Consumido por lang('Wealth.<chave>') e, quando carrega o nome completo da marca,
 * por brandLang('Wealth.<chave>') ({brand} -> brand('display_name')). Menções curtas
 * "GX" e identificadores de tracking (data-wealth-objective/track) ficam literais.
 * Catálogo próprio (isolado). Migração incremental por view.
 */
return [
    // ===================================================================
    // wealth/landing.php
    // ===================================================================

    // config/hero (fallbacks de $landing/$copy['landing'])
    'l_hero_title'  => 'Seu patrimônio precisa de estratégia, não de improviso.',
    'l_hero_sub'    => 'Diagnóstico patrimonial, leitura de liquidez, alocação e próximos passos consultivos para famílias, executivos e empresários.',
    'l_hero_badge'  => 'Wealth Advisory | {brand}',
    'l_cta_primary'   => 'Quero meu diagnóstico',
    'l_cta_secondary' => 'Entender o método',
    'l_form_title'  => 'Receba um diagnóstico consultivo inicial',
    'l_form_desc'   => 'Preencha os dados principais e descreva o contexto. O retorno vem com direcionamento patrimonial e próximos passos possíveis.',
    'l_form_button' => 'Falar com um especialista',

    // faq fallback (array q/a)
    'l_faq' => [
        ['q' => 'Para quem a consultoria é indicada?', 'a' => 'Para famílias, executivos e empresários que querem organizar patrimônio, renda, liquidez e decisões financeiras com visão integrada.'],
        ['q' => 'Preciso transferir a carteira para ter um diagnóstico?', 'a' => 'Não. O diagnóstico inicial parte do seu contexto atual e identifica onde estão travas, desalinhamentos e prioridades.'],
        ['q' => 'O que recebo após o primeiro contato?', 'a' => 'Uma leitura consultiva do caso, hipóteses de ganho de eficiência e indicação objetiva dos próximos movimentos possíveis.'],
        ['q' => 'A análise inclui fluxo de caixa, patrimônio e metas?', 'a' => 'Sim. O foco é conectar patrimônio, liquidez, objetivos e ritmo de construção para evitar decisões isoladas.'],
    ],

    // signalCards
    'l_sig1_t' => 'Liquidez parada', 'l_sig1_x' => 'Caixa excessivo ou desalocado corrói eficiência e reduz capacidade de execução futura.',
    'l_sig2_t' => 'Carteira sem tese', 'l_sig2_x' => 'Ativos acumulados ao longo do tempo, sem uma lógica clara de função, risco e objetivo.',
    'l_sig3_t' => 'Metas concorrentes', 'l_sig3_x' => 'Proteção, renda, crescimento e legado disputam capital sem hierarquia definida.',
    'l_sig4_t' => 'Decisão fragmentada', 'l_sig4_x' => 'Patrimônio pessoal, empresa, dívidas e reservas andam separados e geram ruído de estratégia.',

    // deliverables
    'l_del1_t' => 'Mapa patrimonial e de liquidez', 'l_del1_x' => 'Leitura do que precisa ficar líquido, protegido, produtivo ou reorganizado.',
    'l_del1_c1' => 'Reserva e caixa', 'l_del1_c2' => 'Patrimônio produtivo', 'l_del1_c3' => 'Risco e concentração',
    'l_del2_t' => 'Tese de alocação e prioridades', 'l_del2_x' => 'Hipóteses de melhoria para renda, proteção, crescimento e eficiência do capital.',
    'l_del2_c1' => 'Renda recorrente', 'l_del2_c2' => 'Proteção', 'l_del2_c3' => 'Sequência de execução',
    'l_del3_t' => 'Plano executivo de próximos passos', 'l_del3_x' => 'Um caminho claro do que fazer primeiro, depois e o que merece acompanhamento consultivo.',
    'l_del3_c1' => 'Ações imediatas', 'l_del3_c2' => 'Ganho potencial', 'l_del3_c3' => 'Acompanhamento',

    // nav
    'l_nav_diag' => 'Diagnóstico', 'l_nav_metodo' => 'Método', 'l_nav_entreg' => 'Entregáveis', 'l_nav_faq' => 'FAQ',
    'l_nav_area' => 'Área completa', 'l_nav_entrar' => 'Entrar', 'l_nav_blog' => 'Blog', 'l_nav_menu' => 'Menu',

    // hero proof
    'l_proof1_t' => 'Diagnóstico em 60s', 'l_proof1_s' => 'Uma leitura inicial para entender direção, gap e urgência consultiva.',
    'l_proof2_t' => 'Visão integrada', 'l_proof2_s' => 'Patrimônio, fluxo, liquidez e objetivos tratados como sistema, não como peças soltas.',
    'l_proof3_t' => 'Plano acionável', 'l_proof3_s' => 'Próximos passos objetivos para blindagem, renda, crescimento e legado.',

    // auth note (só autenticado)
    'l_auth_strong'   => 'Área completa já disponível.',
    'l_auth_text'     => 'Você pode seguir com o diagnóstico guiado ou abrir seu panorama em',
    'l_auth_link'     => 'conversa',
    'l_progress_suffix' => '% do mapeamento concluído',
    'l_progress_caption_pre'  => 'de',
    'l_progress_caption_post' => 'etapas preenchidas na área completa.',

    // hero panel
    'l_panel_kicker' => 'Leitura inicial', 'l_panel_title' => 'Patrimônio, renda e liquidez em contexto.', 'l_panel_badge' => 'Consultivo',
    'l_mini1_s' => 'Foco', 'l_mini1_t' => 'Blindar, organizar e acelerar o capital',
    'l_mini2_s' => 'Saída', 'l_mini2_t' => 'Próximo passo claro e priorizado',
    'l_mini3_s' => 'Escopo', 'l_mini3_t' => 'Fluxo, liquidez, alocação e metas',
    'l_mini4_s' => 'Timing', 'l_mini4_t' => 'Diagnóstico rápido e retorno consultivo',
    'l_path1' => '1. Diagnóstico patrimonial e de liquidez',
    'l_path2' => '2. Tese de organização e alocação',
    'l_path3' => '3. Plano executivo com prioridade de ações',
    'l_stat1_s' => 'para medir o gap inicial', 'l_stat2_s' => 'frentes de decisão patrimonial', 'l_stat3_s' => 'visão integrada do capital',

    // strip
    'l_strip_lead' => 'Onde a consultoria atua com mais impacto:',
    'l_strip1' => 'Liquidez e reserva', 'l_strip2' => 'Alocação e renda', 'l_strip3' => 'Proteção patrimonial', 'l_strip4' => 'Prioridade de metas', 'l_strip5' => 'Legado e sucessão',

    // diagnóstico
    'l_diag_label' => 'Diagnóstico Rápido',
    'l_diag_title' => 'Meça o tamanho do gap entre patrimônio atual e padrão de vida desejado.',
    'l_diag_desc'  => 'A conta abaixo não substitui uma consultoria, mas expõe rapidamente se o seu patrimônio está organizado para sustentar objetivos, renda e liquidez com eficiência.',
    'l_obj1' => 'Blindar patrimônio e liquidez', 'l_obj2' => 'Gerar mais renda recorrente', 'l_obj3' => 'Organizar crescimento e alocação', 'l_obj4' => 'Planejar legado e sucessão',
    'l_field_invested' => 'Patrimônio investido hoje',
    'l_field_monthly'  => 'Aporte mensal atual',
    'l_field_cost'     => 'Custo de vida mensal que o patrimônio deveria sustentar',
    'l_chip1' => 'Capital alvo usa referência conservadora de retirada',
    'l_chip2' => 'Projeção considera ritmo constante de aportes',
    'l_chip3' => 'Leitura inicial para priorização consultiva',
    'l_ins_label' => 'Leitura Inicial',
    'l_ins_title' => 'Seu patrimônio deveria produzir clareza, não ruído.',
    'l_kpi1' => 'Capital alvo estimado', 'l_kpi2' => 'Projeção em 10 anos', 'l_kpi3' => 'Cobertura estimada da meta', 'l_kpi4' => 'Gap patrimonial projetado',
    'l_ins_text' => 'Informe os dados principais para estimar o capital-alvo e a distância entre o ritmo atual e o padrão de vida desejado.',
    'l_ins_cta'  => 'Receber leitura consultiva',

    // sinais
    'l_sig_label' => 'Sinais de Ineficiência',
    'l_sig_title' => 'O patrimônio costuma perder eficiência quando a estratégia fica fragmentada.',
    'l_sig_desc'  => 'Esses são os cenários mais comuns em famílias e empresários com patrimônio relevante, mas sem coordenação consultiva contínua.',

    // método
    'l_met_label' => 'Método Consultivo',
    'l_met_title' => 'Como a {brand} estrutura o diagnóstico patrimonial.',
    'l_met_desc'  => 'A ideia não é adicionar mais produto ao patrimônio, e sim construir ordem, tese e priorização.',
    'l_proc1_t' => 'Mapa de patrimônio, liquidez e fluxo', 'l_proc1_x' => 'Entender onde o capital está, que função cada bloco cumpre e quais decisões estão travando eficiência.',
    'l_proc2_t' => 'Tese de organização e alocação', 'l_proc2_x' => 'Definir o que precisa ser protegido, o que deve gerar renda e o que merece assumir risco de crescimento.',
    'l_proc3_t' => 'Plano executivo de próximos passos', 'l_proc3_x' => 'Uma sequência objetiva de ações para ganhar clareza, liquidez, proteção e consistência patrimonial.',

    // entregáveis
    'l_ent_label' => 'Entregáveis',
    'l_ent_title' => 'O que destrava quando o patrimônio passa a obedecer uma estratégia.',
    'l_ent_desc'  => 'O ganho raramente está em um único ativo. Ele aparece quando liquidez, renda, proteção e prioridade passam a conversar.',

    // bloco área completa (só autenticado)
    'l_area_label' => 'Área Completa',
    'l_area_title' => 'Continue a análise detalhada com o mapeamento guiado.',
    'l_area_desc'  => 'Se você já tem acesso, avance pelo diagnóstico estruturado e abra o panorama consolidado do patrimônio.',
    'l_area_cta1'  => 'Continuar diagnóstico', 'l_area_cta2' => 'Ver meu panorama',

    // faq
    'l_faq_label' => 'FAQ',
    'l_faq_title' => 'Perguntas recorrentes antes do primeiro diagnóstico.',
    'l_faq_desc'  => 'O objetivo é reduzir fricção, qualificar o contexto e tornar a conversa seguinte objetiva.',

    // lead
    'l_lead_label' => 'Próximo Passo',
    'l_lead_title' => 'Conecte o diagnóstico rápido a uma leitura consultiva.',
    'l_lead_desc'  => 'Informe o contexto principal. A equipe retorna com uma leitura inicial e possíveis próximos movimentos para organizar o patrimônio.',
    'l_lead_chip1' => 'Diagnóstico inicial rápido', 'l_lead_chip2' => 'Contato consultivo', 'l_lead_chip3' => 'Sem compromisso',
    'l_lead_area'  => 'Abrir área completa',

    // ===================================================================
    // wealth/_lead_form.php (partial). Chaves dos selects (option value) ficam
    // literais (são valores enviados ao backend); só os labels vão p/ lang().
    // ===================================================================
    'lf_title'   => 'Receba um diagnóstico com um especialista',
    'lf_desc'    => 'Preencha os dados principais. A equipe da {brand} retorna com uma leitura consultiva e próximos passos possíveis.',
    'lf_submit'  => 'Quero meu diagnóstico',
    'lf_success_title' => 'Diagnóstico recebido',
    'lf_success_text'  => 'Seu contexto já entrou na fila consultiva. O retorno acontece com próximos passos objetivos, não com mensagem genérica.',
    'lf_goal1' => 'Blindar patrimônio e liquidez', 'lf_goal2' => 'Gerar mais renda recorrente', 'lf_goal3' => 'Organizar crescimento e alocação', 'lf_goal4' => 'Planejar legado e sucessão',
    'lf_patr1' => 'Até R$ 300 mil', 'lf_patr2' => 'De R$ 300 mil a R$ 1 milhão', 'lf_patr3' => 'De R$ 1 milhão a R$ 5 milhões', 'lf_patr4' => 'Acima de R$ 5 milhões',
    'lf_slot1' => 'Manhã em dias úteis', 'lf_slot2' => 'Tarde em dias úteis', 'lf_slot3' => 'Noite', 'lf_slot4' => 'Prefiro retorno por WhatsApp ou e-mail primeiro',
    'lf_field_nome'  => 'Nome', 'lf_field_email' => 'E-mail', 'lf_field_phone' => 'Telefone com DDD',
    'lf_field_patr'  => 'Faixa patrimonial', 'lf_field_goal' => 'Objetivo principal',
    'lf_field_slot'  => 'Melhor formato para o primeiro retorno', 'lf_field_msg' => 'Contexto adicional',
    'lf_select' => 'Selecione', 'lf_opcional' => 'Opcional',
    'lf_msg_ph' => 'Ex.: patrimônio hoje concentrado em caixa, necessidade de renda recorrente, liquidez para empresa ou reorganização de carteira.',
    'lf_consent_pre'  => 'Autorizo contato consultivo da {brand} e concordo com os',
    'lf_consent_link' => 'termos e condições',
    'lf_note' => 'Leitura inicial rápida, com contexto patrimonial e próximos passos possíveis.',
    'lf_success_cta' => 'Explorar conteúdos enquanto aguardamos',

    // ===================================================================
    // wealth/results.php (auth-gated). 'R$'/números ficam literais.
    // ===================================================================
    'res_page_title' => 'Resultado',
    'res_nodata' => 'Ainda não temos dados suficientes para montar seu resultado. Volte à conversa e preencha suas informações.',
    'res_back'   => 'Voltar à Conversa',
    'res_summary'   => 'Resumo Financeiro',
    'res_patr_fin'  => 'Patrimônio financeiro:', 'res_patr_imob' => 'Patrimônio imobiliário:', 'res_passivos' => 'Passivos:', 'res_patr_liq' => 'Patrimônio líquido:',
    'res_cashflow'  => 'Fluxo de Caixa',
    'res_renda'     => 'Renda mensal', 'res_despesas' => 'Despesas mensais (custo de vida)', 'res_poupanca' => 'Potencial de poupança',
    'res_aloc'      => 'Alocação Atual', 'res_no_aloc' => 'Sem alocação financeira informada.',
    'res_evol'      => 'Evolução do Patrimônio e Liberdade Financeira',
    'res_ret_real'  => 'Retorno real anual estimado:', 'res_nw_needed' => 'Patrimônio necessário p/ FI:',
    'res_metas'     => 'Metas', 'res_obj' => 'Objetivo:', 'res_em' => 'em', 'res_meses' => 'meses', 'res_no_meta' => 'Nenhuma meta cadastrada ainda.',
    'res_fi_title'  => 'Rumo à Liberdade Financeira',
    'res_fi_indet'  => 'Com os parâmetros atuais, a liberdade financeira é indeterminada. Um ajuste de poupança e alocação pode acelerar o caminho.',
    'res_fi_done'   => 'Parabéns! Seu patrimônio já sustenta seu custo de vida em termos reais.',
    'res_fi_est_1'  => 'Estimamos', 'res_fi_est_2' => 'anos e', 'res_fi_est_3' => 'meses para seu patrimônio gerar renda passiva suficiente para cobrir seu custo de vida.',
    'res_recom'     => 'Recomendações',
    'res_rec_divida'     => 'Priorize amortização de dívidas com taxa acima da inflação.',
    'res_rec_diversif'   => 'Diversifique a alocação do patrimônio financeiro entre classes.',
    'res_rec_perfil'     => 'Ajuste a alocação ao perfil de risco declarado ({p}).',
    'res_rec_poupanca'   => 'Automatize a poupança mensal para reforçar as metas.',
    'res_rec_disciplina' => 'Mantenha disciplina de aportes e rebalanceamentos periódicos.',
    'res_cta_title' => 'Reduza seu tempo até a liberdade financeira',
    'res_cta_text'  => 'Nossos consultores podem otimizar sua alocação, ajustar aportes e desenhar um plano personalizado focado na sua FI.',
    'res_cta_b1' => 'Estratégias alinhadas ao seu perfil de risco', 'res_cta_b2' => 'Rebalanceamento e disciplina de aportes', 'res_cta_b3' => 'Priorização de metas e proteção do patrimônio',
    'res_cta_btn'    => 'Agendar consultoria gratuita', 'res_cta_senior' => 'Falar com consultor sênior',
    'res_agendar'    => 'Agendar reunião gratuita com consultor', 'res_senior2' => 'Falar com Consultor Sênior',
    'res_pdf'        => 'Baixar Resumo PDF',
    // JS (dicionário L)
    'res_js_aa'    => '% a.a.', 'res_js_anos' => ' anos',
    'res_js_chart_patr' => 'Patrimônio projetado', 'res_js_chart_fi' => 'Necessário p/ FI',
    'res_js_nodata'     => 'Without dados suficientes para projetar; informe renda, despesas e aportes.',

    // ===================================================================
    // wealth/pdf_template.php (Dompdf, auth-gated). Números/R$/% literais.
    // ===================================================================
    'pdf_title'       => 'Resumo',
    'pdf_brand_title' => '{brand} · Resumo Financeiro',
    'pdf_gerado'      => 'Gerado em',
    'pdf_visao'       => 'Visão Geral',
    'pdf_patr_liq'    => 'Patrimônio Líquido', 'pdf_renda' => 'Renda Mensal', 'pdf_despesas' => 'Despesas Mensais', 'pdf_poupanca' => 'Poupança Mensal',
    'pdf_cashflow'    => 'Fluxo de Caixa',
    'pdf_th_item'     => 'Item', 'pdf_th_valor' => 'Valor',
    'pdf_renda2'      => 'Renda mensal', 'pdf_despesas2' => 'Despesas mensais', 'pdf_poupanca2' => 'Poupança potencial',
    'pdf_fi'          => 'Independência Financeira',
    'pdf_ret'         => 'Retorno real estimado', 'pdf_aa' => '% a.a.',
    'pdf_nw_needed'   => 'Patrimônio necessário (estim.)',
    'pdf_tempo'       => 'Tempo estimado até FI',
    'pdf_indet'       => 'Indeterminado', 'pdf_atingido' => 'Já atingido',
    'pdf_anos_e'      => 'anos e', 'pdf_meses' => 'meses',
    'pdf_nota'        => 'Nota:', 'pdf_nota_text' => 'projeções em termos reais (descontada a inflação), considerando aportes constantes e retorno esperado.',
    'pdf_patr_aloc'   => 'Patrimônio e Alocação',
    'pdf_th_comp'     => 'Componente',
    'pdf_ativos'      => 'Ativos financeiros', 'pdf_imoveis' => 'Imóveis', 'pdf_passivos' => 'Passivos', 'pdf_patr_liq2' => 'Patrimônio líquido',
    'pdf_aloc_aprox'  => 'Alocação aproximada (por classe):',
    'pdf_disclaimer'  => 'Este material tem caráter informativo e não constitui recomendação de investimento. Projeções são estimativas sujeitas a variações de mercado.',
    'pdf_foot'        => '© {brand} · Documento gerado automaticamente',
];
