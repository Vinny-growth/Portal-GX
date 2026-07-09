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
    'srs_hero_headline'  => 'Você não paga seguro. Você <em>constrói patrimônio.</em>',
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
    'srs_lock_sub'          => 'Ganhe o seu <strong>Relatório Patrimonial 360</strong> — o raio-X completo da sua vida financeira, com plano de proteção, reserva e sucessão. Um especialista GX prepara e te entrega pelo WhatsApp.',
    'srs_desbloquear'       => 'Quero meu Relatório 360',
    'srs_legend_red'        => 'O que você paga (aporte acumulado)',
    'srs_legend_green'      => 'O que você junta (reserva acumulada)',
    'srs_kpi_protecao'      => 'Proteção contratada',
    'srs_kpi_protecao_sub'  => 'blindagem da sua família por toda a vida',
    'srs_kpi_quitacao'      => 'Plano quitado em',
    'srs_kpi_quitacao_sub'  => 'pagamento finito, sem mensalidade vitalícia',
    'srs_kpi_breakeven'     => 'Ponto de virada',
    'srs_kpi_breakeven_sub' => 'reserva ultrapassa o total pago',
    'srs_celebrate_eyebrow' => 'Relatório Patrimonial 360 conquistado',
    'srs_wa_btn'            => 'Falar com meu especialista e receber',
    'srs_disclaimer'        => 'Simulação educativa. Os valores são <strong>projetados</strong> a partir de IPCA estimado (5,5% a.a.) e dos fatores de resgate da apólice; <strong>não constituem garantia</strong> de rentabilidade nem proposta de contratação. Condições, carências e coberturas seguem o regulamento do produto e a regulação da SUSEP. O prêmio inclui IOF de 0,38%.',

    // modal de lead
    'srs_modal_close'    => 'Fechar',
    'srs_modal_title'    => 'Seu Relatório Patrimonial 360',
    'srs_modal_sub'      => 'Preencha seus dados e um especialista da GX vai preparar e te enviar, pelo WhatsApp, o seu Relatório Patrimonial 360 completo — o raio-X da sua vida financeira, com proteção, reserva e sucessão desenhados para o seu objetivo.',
    'srs_lbl_nome'       => 'Nome',
    'srs_lbl_email'      => 'E-mail',
    'srs_lbl_whatsapp'   => 'WhatsApp / Telefone',
    'srs_consent'        => 'Autorizo o contato de um especialista pelo WhatsApp/telefone para receber meu relatório e tirar dúvidas.',
];
