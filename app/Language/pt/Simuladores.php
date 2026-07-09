<?php

/**
 * Camada de marketing — strings das VIEWS de simulador (white-label — Fase 2 i18n) — PORTUGUÊS.
 *
 * Consumido por lang('Simuladores.<chave>') e, quando a string carrega o nome da
 * marca, por brandLang('Simuladores.<chave>') — o token literal {brand} é trocado
 * por brand('display_name'). Menções curtas "GX" seguem literais (tokenizar mudaria
 * os bytes). Para outro idioma/marca: criar app/Language/<locale>/Simuladores.php.
 *
 * Migração incremental por arquivo/sub-lote (Lote 4). Prefixos:
 *   srs_ = simulators/seguro_resgatavel.php
 *   cons_ = simulators/consorcio.php
 *   fx_  = marketing/simulators_fx_hub.php
 */
return [
    // ===================================================================
    // Seguro de Vida Resgatável — simulators/seguro_resgatavel.php
    // ===================================================================

    // topbar
    'srs_back' => 'Todos os simuladores',

    // hero
    'srs_hero_eyebrow'   => 'Seguro de Vida Resgatável · Whole Life',
    'srs_hero_headline'  => 'Seguro de Vida Resgatável: você não paga seguro, <em>constrói patrimônio.</em>',
    'srs_hero_sub'       => 'Um plano quitado em 10 anos, corrigido todo ano, que forma uma reserva resgatável enquanto blinda sua família hoje. Veja o ponto em que a reserva ultrapassa tudo o que você pagou — e a proteção passa a sair de graça.',
    'srs_chip_pagamento' => 'Pagamento finito',
    'srs_chip_correcao'  => 'Correção anual (IPCA)',
    'srs_reserva_resgatavel' => 'Reserva resgatável',
    'srs_chip_protecoes' => 'Proteções em vida',

    // formulário — diagnóstico
    'srs_form_title'    => 'Diagnóstico · 2 minutos',
    'srs_form_intro'    => 'Responda como num planejamento financeiro. A partir das suas respostas calculamos a proteção ideal e projetamos a reserva que você constrói.',
    'srs_sec1'          => '1 · Sobre você',
    'srs_lbl_idade'     => 'Sua idade',
    'srs_lbl_sexo'      => 'Sexo',
    'srs_masculino'     => 'Masculino',
    'srs_feminino'      => 'Feminino',
    'srs_sec2'          => '2 · Quem depende de você',
    'srs_lbl_dependentes' => 'Pessoas que dependem da sua renda',
    'srs_dep_0'         => 'Ninguém — só eu',
    'srs_dep_1'         => '1 pessoa',
    'srs_dep_2'         => '2 pessoas',
    'srs_dep_3'         => '3 pessoas',
    'srs_4mais'         => '4 ou mais',
    'srs_lbl_filhos'    => 'Filhos em formação',
    'srs_filho_nenhum'  => 'Nenhum',
    'srs_sec3'          => '3 · Renda e patrimônio',
    'srs_lbl_renda'     => 'Renda mensal da família (R$)',
    'srs_ph_renda'      => 'Ex.: 30.000',
    'srs_hint_renda'    => 'Padrão de vida que sua renda sustenta.',
    'srs_lbl_uf'        => 'Estado (define o ITCMD)',
    'srs_hint_uf'       => 'Alíquota usada na sucessão.',
    'srs_lbl_patr_imob' => 'Patrimônio imobiliário (R$)',
    'srs_ph_patr_imob'  => 'Ex.: 1.500.000',
    'srs_hint_patr_imob' => 'Imóveis: moradia, locação, terrenos.',
    'srs_lbl_patr_fin'  => 'Patrimônio financeiro (R$)',
    'srs_ph_patr_fin'   => 'Ex.: 500.000',
    'srs_hint_patr_fin' => 'Investimentos, empresas, aplicações.',
    'srs_lbl_dividas'   => 'Dívidas / financiamentos (R$)',
    'srs_ph_dividas'    => 'Ex.: 200.000',
    'srs_hint_dividas'  => 'Saldo que a família precisaria quitar num imprevisto.',
    'srs_sec4'          => '4 · Seu objetivo',
    'srs_lbl_objetivo'  => 'O que mais importa para você?',
    'srs_obj_protecao'  => 'Proteger a renda da minha família',
    'srs_obj_sucessao'  => 'Planejar sucessão com liquidez',
    'srs_obj_quitar'    => 'Garantir a quitação de dívidas num imprevisto',
    'srs_obj_aposentadoria' => 'Complementar a aposentadoria',
    'srs_lbl_estrategia' => 'Em quanto tempo quer quitar o plano?',
    'srs_aria_estrategia' => 'Estratégia',
    'srs_10anos'        => '10 anos',
    'srs_20anos'        => '20 anos',

    // caixa de recomendação
    'srs_reco_label'           => 'Sua proteção recomendada',
    'srs_reco_insight_default' => 'Preencha os campos acima para calcularmos.',
    'srs_capital_segurado'     => 'Capital segurado',
    'srs_reco_edit'            => '(ajuste se quiser)',
    'srs_dg_toggle'            => 'Incluir proteção contra <strong>Doenças Graves</strong> <small style="opacity:.65">(capital até R$ 1 mi)</small>',
    'srs_calc_btn'             => 'Ver meu diagnóstico e projeção',

    // resultado
    'srs_result_title'      => 'Patrimônio x Aporte — projeção até os 100',
    'srs_placeholder'       => 'Preencha seu perfil e clique em “Desenhar minha projeção”.',
    'srs_lock_title'        => 'Sua reserva vira lucro a partir de um certo ponto.',
    'srs_lock_sub'          => 'Agende uma <strong>reunião gratuita</strong> com um especialista GX e ganhe o seu <strong>Relatório Patrimonial 360</strong> — o raio-X completo da sua vida financeira (proteção, reserva e sucessão). Um relatório que normalmente custa <strong>R$ 1.297</strong>, seu de graça na reunião.',
    'srs_desbloquear'       => 'Quero agendar minha reunião',
    // âncora de valor da oferta (relatório entregue de graça na reunião)
    'srs_anchor_label'      => 'Relatório Patrimonial 360',
    'srs_anchor_from'       => 'De R$ 1.297',
    'srs_anchor_now'        => 'por R$ 0 na reunião',
    'srs_legend_red'        => 'O que você paga (aporte acumulado)',
    'srs_legend_green'      => 'O que você junta (reserva acumulada)',
    'srs_kpi_protecao'      => 'Proteção contratada',
    'srs_kpi_protecao_sub'  => 'blindagem da sua família por toda a vida',
    'srs_kpi_quitacao'      => 'Plano quitado em',
    'srs_kpi_quitacao_sub'  => 'pagamento finito, sem mensalidade vitalícia',
    'srs_kpi_breakeven'     => 'Ponto de virada',
    'srs_kpi_breakeven_sub' => 'reserva ultrapassa o total pago',
    'srs_celebrate_eyebrow' => 'Reunião gratuita garantida',
    'srs_wa_btn'            => 'Agendar minha reunião no WhatsApp',
    'srs_disclaimer'        => 'Simulação educativa. Os valores são <strong>projetados</strong> a partir de IPCA estimado (5,5% a.a.) e dos fatores de resgate da apólice; <strong>não constituem garantia</strong> de rentabilidade nem proposta de contratação. Condições, carências e coberturas seguem o regulamento do produto e a regulação da SUSEP. O prêmio inclui IOF de 0,38%.',

    // ===================================================================
    // Conteúdo educacional (SEO/GEO) — seção abaixo do simulador
    // ===================================================================
    'srs_learn_eyebrow'   => 'Entenda o produto',
    'srs_learn_def_title' => 'O que é seguro de vida resgatável?',
    'srs_learn_def_p1'    => 'O <strong>seguro de vida resgatável</strong> — também chamado de <em>whole life</em> ou seguro de vida inteira com pagamento finito — é a modalidade em que você paga o prêmio por um período determinado (normalmente 10 ou 20 anos) e, ao mesmo tempo, forma uma <strong>reserva resgatável</strong> (o valor de resgate, ou <em>cash value</em>). Diferente do seguro de vida tradicional, a fundo perdido, parte do que você aporta se acumula e pode ser resgatada em vida.',
    'srs_learn_def_p2'    => 'Essa reserva é corrigida ao longo do tempo e cresce conforme os fatores de resgate da apólice. Em determinado momento — o <strong>ponto de virada (break-even)</strong> — a reserva acumulada ultrapassa tudo o que você já pagou, e a proteção passa a se sustentar pela própria reserva. Tudo dentro das regras da <strong>SUSEP</strong>, órgão que regula os seguros no Brasil.',
    'srs_learn_how_title' => 'Como funciona, passo a passo',
    'srs_learn_how_1'     => 'Você define um capital segurado adequado ao seu objetivo: proteger a renda da família, planejar a sucessão, garantir a quitação de dívidas ou complementar a aposentadoria.',
    'srs_learn_how_2'     => 'Paga o prêmio por um prazo finito — 10 ou 20 anos — e depois a apólice fica quitada, sem mensalidade vitalícia.',
    'srs_learn_how_3'     => 'A cada ano o plano é corrigido (no simulador, por um IPCA estimado de 5,5% a.a.), acompanhando a inflação.',
    'srs_learn_how_4'     => 'Enquanto isso, forma-se a reserva resgatável, que pode ser resgatada em vida conforme as condições da apólice.',
    'srs_learn_how_5'     => 'No ponto de break-even, a reserva ultrapassa o total aportado — e a cobertura segue protegendo sua família por toda a vida.',
    'srs_learn_cmp_title' => 'Resgatável x Temporário x Vitalício',
    'srs_learn_cmp_intro' => 'Como o seguro de vida resgatável se compara às outras modalidades mais comuns:',
    'srs_cmp_head'        => ['Característica', 'Resgatável (Whole Life)', 'Temporário (Term)', 'Vitalício sem resgate'],
    'srs_cmp_rows'        => [
        ['Prazo de pagamento', 'Finito (10 ou 20 anos)', 'Enquanto durar a cobertura', 'Geralmente vitalício'],
        ['Forma reserva resgatável', 'Sim, o valor de resgate', 'Não', 'Não'],
        ['Resgate em vida', 'Sim, conforme a apólice', 'Não', 'Não'],
        ['Break-even (reserva supera o pago)', 'Sim, ao longo do plano', 'Não se aplica', 'Não se aplica'],
        ['Duração da cobertura', 'Vitalícia', 'Prazo determinado', 'Vitalícia'],
        ['Faz mais sentido para', 'Proteção + patrimônio + sucessão', 'Proteção temporária de menor custo', 'Proteção vitalícia sem acúmulo'],
    ],
    'srs_learn_related_title'  => 'Continue explorando',
    'srs_learn_related_blog'   => 'Artigos sobre seguro de vida e planejamento',
    'srs_learn_related_hub'    => 'Todos os simuladores da GX',
    'srs_learn_related_wealth' => 'Consultoria de patrimônio (Wealth Advisory)',

    // FAQ — o MESMO array alimenta o HTML (_faq_section) e o schema FAQPage
    'srs_faq_eyebrow' => 'FAQ',
    'srs_faq_title'   => 'Perguntas frequentes sobre seguro de vida resgatável',
    'srs_faq' => [
        ['q' => 'O que é um seguro de vida resgatável?', 'a' => 'É uma modalidade de seguro de vida (whole life, ou vida inteira) em que você paga o prêmio por um prazo finito e forma uma reserva resgatável, chamada de valor de resgate. Diferente do seguro tradicional a fundo perdido, parte do que você aporta se acumula e pode ser resgatada em vida, enquanto a proteção continua vitalícia.'],
        ['q' => 'Qual a diferença entre seguro de vida resgatável e o tradicional?', 'a' => 'No seguro tradicional (temporário, a fundo perdido) você paga o prêmio e não recupera nada se não usar a cobertura. No resgatável, além da proteção, você forma uma reserva que cresce ao longo do tempo e pode ser resgatada em vida, conforme as condições da apólice.'],
        ['q' => 'Como funciona o resgate?', 'a' => 'A reserva (valor de resgate) se acumula conforme os fatores de resgate da apólice e pode ser resgatada em vida segundo as regras do contrato. Nos primeiros anos o valor de resgate costuma ser baixo e cresce com o tempo, até superar o total pago no ponto de break-even.'],
        ['q' => 'Seguro de vida resgatável vale a pena?', 'a' => 'Faz mais sentido para quem busca proteção vitalícia somada à construção de patrimônio e liquidez para a sucessão, com pagamento finito. Para uma proteção apenas temporária e de menor custo, o seguro por prazo determinado pode ser mais indicado. O ideal é dimensionar pelo seu objetivo, que é o que o simulador faz.'],
        ['q' => 'O que é o ponto de break-even?', 'a' => 'É o momento em que a reserva acumulada ultrapassa tudo o que você já pagou de prêmio. A partir daí, na prática, a proteção passa a se sustentar pela própria reserva formada.'],
        ['q' => 'Quanto custa um seguro de vida resgatável?', 'a' => 'O prêmio depende da sua idade, do capital segurado, das coberturas escolhidas e do prazo de pagamento (10 ou 20 anos). Por isso não existe um valor único: use o simulador para dimensionar a proteção ideal e fale com um especialista para saber o valor exato.'],
        ['q' => 'Seguro de vida resgatável serve para planejamento sucessório?', 'a' => 'Sim. O capital do seguro entrega liquidez imediata e fora do inventário, ajudando a família a arcar com os custos de sucessão (ITCMD e inventário) sem precisar vender bens nem resgatar investimentos no pior momento.'],
        ['q' => 'O seguro de vida resgatável é regulado?', 'a' => 'Sim. Os seguros de vida no Brasil são regulados pela SUSEP (Superintendência de Seguros Privados). Condições, carências e coberturas seguem o regulamento do produto. Os valores exibidos no simulador são projeções educativas e não constituem garantia de rentabilidade.'],
    ],

    // modal de lead
    'srs_modal_close'    => 'Fechar',
    'srs_modal_title'    => 'Agende e ganhe seu Relatório 360',
    'srs_modal_sub'      => 'Preencha seus dados para um especialista da GX agendar sua <strong>reunião gratuita</strong>. Nela você recebe o Relatório Patrimonial 360 completo — que normalmente custa <strong>R$ 1.297</strong> — de graça e sem compromisso.',
    'srs_lbl_nome'       => 'Nome',
    'srs_lbl_email'      => 'E-mail',
    'srs_lbl_whatsapp'   => 'WhatsApp / Telefone',
    'srs_consent'        => 'Autorizo o contato de um especialista pelo WhatsApp/telefone para receber meu relatório e tirar dúvidas.',

    // ---- microcopy do JS (injetada via dicionário L; {x} = placeholder preenchido no runtime) ----
    // status / validação
    'js_err_idade'     => 'Informe uma idade entre 14 e 65.',
    'js_err_capital'   => 'Informe o capital de vida inteira.',
    'js_calculando'    => 'Calculando...',
    'js_err_calcular'  => 'Não foi possível calcular agora.',
    'js_err_refaca'    => 'Refaça a projeção antes de continuar.',
    'js_err_consent'   => 'Marque a autorização de contato para receber seu relatório.',
    'js_enviando'      => 'Enviando...',
    'js_err_registrar' => 'Não foi possível registrar agora.',
    // gráfico
    'js_chart_aporte'  => 'Aporte acumulado',
    'js_chart_reserva' => 'Reserva acumulada',
    'js_chart_idade'   => 'Idade',
    // caixa de proteção recomendada
    'js_reco_default'  => 'Preencha os dados acima para estimarmos a proteção ideal.',
    'js_reco_sugestao' => 'Sugerimos <strong>R$ {v}</strong> — {r}',
    'js_rat_sucessao'  => 'cobre as custas de sucessão ({itcmd} de ITCMD em {uf} + {inv} de inventário/cartório = {suc}) sobre o patrimônio de R$ {patr}, para a família receber os bens sem precisar vendê-los.',
    'js_rat_quitar'    => 'quita R$ {div} de dívidas e cobre R$ {suc} de custas de sucessão ({rate}) sobre o patrimônio, sem comprometer o legado.',
    'js_rat_aposent'   => 'complementa R$ {v}/mês (sua renda menos o teto do INSS de R$ 8.475,55) por 10 anos.',
    'js_rat_protecao'  => 'equivale a 5 anos (60 meses) da renda da família, o tempo para se reorganizar financeiramente após um imprevisto.',
    // KPIs do teaser / lock
    'js_kpi_idade'        => 'idade {n}',
    'js_kpi_ano'          => 'no ano {n} da apólice',
    'js_kpi_ano_fallback' => 'reserva ultrapassa o total pago',
    'js_kpi_anos'         => '{n} anos',
    'js_kpi_ate'          => 'até os {n}',
    'js_kpi_vitalicia'    => 'vitalícia',
    'js_lock_breakeven'   => 'Aos {n} anos sua reserva ultrapassa tudo que você pagou.',
    // conquista + handoff WhatsApp
    'js_parabens_nome' => 'Parabéns, {nome}! ',
    'js_parabens'      => 'Parabéns! ',
    'js_celebrate'     => 'Você garantiu uma reunião gratuita com um especialista GX — e nela vai receber, de graça, o Relatório Patrimonial 360 completo (que normalmente custa R$ 1.297). Fale agora no WhatsApp para escolher o melhor horário. 👇',
    'js_obj_protecao'  => 'proteger a renda da minha família',
    'js_obj_sucessao'  => 'planejar a sucessão com liquidez',
    'js_obj_quitar'    => 'garantir a quitação de dívidas num imprevisto',
    'js_obj_aposent'   => 'complementar a aposentadoria',
    'js_obj_fallback'  => 'proteger meu patrimônio',
    'js_wa_saud'       => 'Sou {nome}. ',
    'js_wa_msg'        => 'Olá! {saud}Fiz meu diagnóstico no simulador de seguro de vida resgatável da GX e quero agendar minha reunião gratuita para receber meu Relatório Patrimonial 360 (avaliado em R$ 1.297). Meu objetivo é {obj}.',
];
