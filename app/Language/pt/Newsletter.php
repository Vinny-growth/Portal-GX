<?php

/**
 * Camada de marketing — strings das views newsletter/* (captura pública)
 * (white-label — Fase 2 i18n, lote Newsletter) — PORTUGUÊS.
 *
 * Consumido por lang('Newsletter.<chave>') / brandLang(...) ({brand} ->
 * brand('display_name')). Menções curtas "GX"/"GXC" (watermark) ficam literais.
 * Catálogo próprio. Migração incremental por view.
 */
return [
    // compartilhada (_layout title fallback + _status eyebrow default)
    'nl_brand_title' => 'Newsletter {brand}',

    // <title> da landing /newsletter (landing.php) — keyword em vez do headline conceitual
    'landing_seo_title' => 'Newsletter de Economia e Mercado para Executivos',

    // ===================================================================
    // newsletter/_layout.php
    // ===================================================================
    'lay_desc'      => 'Briefings curtos e acionáveis sobre câmbio, crédito, economia e consórcio.',
    'lay_nav_inicio' => 'Início', 'lay_nav_wealth' => 'Wealth Manager', 'lay_nav_sim' => 'Simuladores', 'lay_nav_nl' => 'Newsletter',
    'lay_copyright' => '{brand} · Inteligência Financeira',

    // ===================================================================
    // newsletter/thank_you.php (via _status_partial + _layout)
    // ===================================================================
    'ty_title'    => 'Bem-vindo à {brand}',
    'ty_glyph'    => '[ 01 / 01 ] CONCLUÍDO',
    'ty_eyebrow'  => 'Inscrição confirmada',
    'ty_headline' => 'Bem-vindo à inteligência GX.',
    'ty_message'  => 'Sua primeira edição já está a caminho. Em até alguns minutos você receberá um email de boas-vindas com o material exclusivo da frente que você escolheu.',
    'ty_cta'      => 'Conhecer o Wealth Manager',
    'ty_cta2'     => 'Voltar ao início',
    'ty_meta1_l'  => 'Frequência', 'ty_meta1_v' => '3× / dia',
    'ty_meta2_l'  => 'Cancelar', 'ty_meta2_v' => '1 clique',
    'ty_meta3_l'  => 'Inbox típico', 'ty_meta3_v' => '90s leitura',

    // ===================================================================
    // newsletter/confirmed.php ('Voltar ao início'=ty_cta2, 'Conhecer...'=ty_cta)
    // ===================================================================
    'cf_eyebrow_ativa' => 'Inscrição ativa',
    'cf_ok_glyph'    => '[ 02 / 02 ] CONFIRMADO', 'cf_ok_headline' => 'Pronto. Seu material está chegando.',
    'cf_ok_message'  => 'Sua inscrição foi confirmada com sucesso. Em alguns minutos você recebe o email de boas-vindas com o material exclusivo da frente que escolheu.',
    'cf_already_glyph'   => '[ STATUS ] JÁ CONFIRMADO', 'cf_already_headline' => 'Você já está com a gente.',
    'cf_already_message' => 'Sua inscrição já estava confirmada. Você continua recebendo a inteligência GX no inbox normalmente.',
    'cf_err_glyph'   => '[ ERRO ] TOKEN INVÁLIDO', 'cf_err_eyebrow' => 'Não foi possível confirmar', 'cf_err_headline' => 'Link inválido ou expirado.',
    'cf_err_message' => 'Este link não foi reconhecido. Pode ser que já tenha expirado ou tenha sido usado. Tente se inscrever novamente para receber um novo link.',
    'cf_err_cta'     => 'Inscrever-me novamente',

    // ===================================================================
    // newsletter/pending_confirmation.php ('Voltar ao início'=ty_cta2)
    // ===================================================================
    'pc_glyph'    => '[ 01 / 02 ] AGUARDANDO CONFIRMAÇÃO',
    'pc_eyebrow'  => 'Falta um passo',
    'pc_headline' => 'Verifique seu email.',
    'pc_message'  => 'Enviamos um link de confirmação para {email}. Clique no botão dentro do email para ativar sua inscrição e receber o material exclusivo. Não esqueça de checar a caixa de spam.',
    'pc_meta1_l'  => 'Tempo médio', 'pc_meta1_v' => '< 2 min',
    'pc_meta2_l'  => 'Validade do link', 'pc_meta2_v' => '24h',
    'pc_title'    => 'Confirme seu email — {brand}',

    // ===================================================================
    // newsletter/landing.php ($settings->* são DB, ficam. GXC/GX/emails literais.)
    // ===================================================================
    'nll_hero_alt' => 'Inteligência financeira {brand}',
    'nll_stat1_l' => 'Frequência', 'nll_stat1_s' => 'por dia',
    'nll_stat2_l' => 'Tempo de leitura', 'nll_stat2_s' => 'por edição',
    'nll_stat3_l' => 'Cancelar', 'nll_stat3_s' => 'clique',
    'nll_form_eyebrow' => 'Comece agora',
    'nll_form_title'   => 'Inscreva-se gratuitamente',
    'nll_form_email'   => 'Email corporativo',
    'nll_form_frentes' => 'Quais frentes você acompanha',
    'nll_form_cta'     => 'Inscrever-me',
    'nll_proof'        => ' executivos recebem a newsletter da {brand}',

    'nll_val_eyebrow' => 'O que muda para você',
    'nll_val_title'   => 'Decisão informada antes do mercado reagir.',
    'nll_val1_t' => 'Câmbio em tempo real', 'nll_val1_x' => 'Movimentações de dólar e juros explicadas em 90 segundos — com a leitura GX por trás dos números.',
    'nll_val2_t' => 'Crédito acionável', 'nll_val2_x' => 'Linhas Pronampe, ProCred 360, BNDES e debêntures — quando entrar, quanto pagar, como estruturar.',
    'nll_val3_t' => 'Economia decifrada', 'nll_val3_x' => 'Boletim Focus, IPCA, decisões do Copom e contas externas — traduzidos para a planilha do seu CFO.',

    'nll_mag_eyebrow' => 'Bônus de inscrição',
    'nll_mag_title'   => 'Materiais exclusivos no seu inbox.',
    'nll_mag_lead'    => 'Ao se inscrever em uma das frentes acima, você recebe o material correspondente direto no email — gratuito, sem follow-up agressivo.',

    'nll_edit_eyebrow' => 'Como produzimos',
    'nll_edit_title'   => 'Curadoria editorial assistida por IA.',
    'nll_edit_p1' => "A newsletter da {brand} combina <strong>jornalismo financeiro tradicional</strong>\n                    com <strong>inteligência artificial proprietária</strong>. Toda edição passa por três\n                    filtros antes de chegar ao seu inbox — seleção temática, redação editorial e revisão humana.",
    'nll_edit_p2' => "Cobrimos quatro frentes: <strong>câmbio</strong> (dólar, hedge, importação/exportação),\n                    <strong>crédito empresarial</strong> (Pronampe, ProCred 360, linhas BNDES, debêntures),\n                    <strong>economia brasileira</strong> (Selic, IPCA, Focus, balança comercial) e\n                    <strong>consórcio</strong> (matemática, comparação com financiamento, blindagem patrimonial).",
    'nll_edit_p3' => "Nossa proposta é tirar a barreira entre o noticiário macro e a planilha do seu CFO:\n                    cada edição traz números do dia, contexto e — quando aplicável — uma ação prática\n                    que você pode levar para a reunião da semana.",
    'nll_meth_eyebrow' => 'Metodologia em 4 etapas',
    'nll_meth1_t' => 'Captura de sinais', 'nll_meth1_x' => 'Monitoramento de RSS, comunicados oficiais (BCB, IBGE, Fazenda, BNDES), Google Trends financeiros, e feed institucional dos principais agentes do mercado.',
    'nll_meth2_t' => 'Filtro de relevância', 'nll_meth2_x' => 'Classificador treinado com a linha editorial da {brand} descarta ruído (especulação, clickbait, eventos sem impacto direto) e mantém só o que importa para tesouraria, crédito empresarial e câmbio.',
    'nll_meth3_t' => 'Redação editorial', 'nll_meth3_x' => 'Cada edição é redigida por modelo de linguagem instruído com o tom GX (executivo, direto, factual) e auditado contra fonte primária — sem invenção de números nem extrapolação.',
    'nll_meth4_t' => 'Revisão humana', 'nll_meth4_x' => 'Antes do disparo, um analista da GX valida números, contexto e tom. Erro de dado é vetor de credibilidade — preferimos atrasar uma edição a deixar passar.',

    'nll_ed_eyebrow'   => 'Últimas edições publicadas',
    'nll_ed_title'     => 'O que saiu da redação esta semana.',
    'nll_ed_lead'      => 'Uma amostra real do que você recebe ao se inscrever — assuntos, gancho e tom editorial.',
    'nll_ed_editorial' => 'Editorial',

    'nll_faq_eyebrow' => 'Perguntas frequentes',
    'nll_faq_title'   => 'Tudo que você precisa saber.',
    'nll_faq1_q' => 'O que eu vou receber na newsletter {brand}?',
    'nll_faq1_a' => 'Briefings curtos (90 segundos de leitura) sobre câmbio, crédito empresarial, economia brasileira e consórcio. Cada edição traz os destaques mais relevantes do dia para tesoureiros, CFOs e empresários que tomam decisões financeiras — selecionados por IA editorial e validados pelo nosso time.',
    'nll_faq2_q' => 'Com que frequência os emails são enviados?',
    'nll_faq2_a' => 'Até 3 envios por dia, distribuídos entre manhã, almoço e fim de tarde. Você escolhe quais frentes editoriais quer acompanhar (Câmbio, Crédito, Economia, Consórcio) e só recebe o conteúdo correspondente. Sem floods.',
    'nll_faq3_q' => 'A inscrição é gratuita?',
    'nll_faq3_a' => 'Sim, totalmente gratuita. Além disso, ao se inscrever em uma das frentes, você recebe um material exclusivo (PDF) com análises práticas — como guias de hedge cambial, comparativos de linhas de crédito BNDES/Pronampe, ou matemática completa de consórcio vs financiamento.',
    'nll_faq4_q' => 'Como posso cancelar a inscrição?',
    'nll_faq4_a' => 'Todo email da {brand} traz um link de cancelamento ao final. Um clique e você é removido imediatamente da lista — sem perguntas, sem retenção forçada, sem tentativas de "última chance".',
    'nll_faq5_q' => 'Quem produz o conteúdo?',
    'nll_faq5_a' => 'A curadoria é feita pelo time editorial da {brand}, com leitura crítica de profissionais de mercado em câmbio, crédito corporativo e investimentos. A redação de cada edição é assistida por IA proprietária treinada com a linha editorial da casa, sempre revisada antes do envio.',
    'nll_faq6_q' => 'Vocês vão me enviar propaganda ou compartilhar meu email?',
    'nll_faq6_a' => 'Não. Não vendemos, alugamos nem compartilhamos seu email com terceiros. Você recebe apenas conteúdo editorial e, ocasionalmente, comunicações sobre serviços diretamente relacionados — sempre com opção clara de opt-out.',

    'nll_close_title' => 'Comece a receber a inteligência GX hoje.',
    'nll_close_btn'   => 'Inscrever-me agora',

    // JSON-LD
    'nll_ld_about' => 'Newsletter financeira {brand}',

    // ===================================================================
    // common/_newsletter_cta_inline.php (CTA inline nos posts; aria/default
    // eyebrow reusam nl_brand_title). Categorias 6/7/8/11 = BR.
    // ===================================================================
    'cta_cambio_eyebrow'  => 'Inteligência cambial',
    'cta_cambio_headline' => 'Receba o radar do dólar todo dia.',
    'cta_cambio_subhead'  => 'Briefings de 90 segundos sobre câmbio, hedge e movimentos do BCB — direto no seu inbox.',
    'cta_cambio_cta'      => 'Inscrever na frente Câmbio',
    'cta_radar_eyebrow'   => 'Radar Econômico',
    'cta_radar_headline'  => 'O Focus decifrado antes do mercado abrir.',
    'cta_radar_subhead'   => 'Selic, IPCA, Copom e balança comercial — traduzidos para a planilha do seu CFO.',
    'cta_radar_cta'       => 'Inscrever no Radar Econômico',
    'cta_credito_eyebrow'  => 'Crédito empresarial',
    'cta_credito_headline' => 'Pronampe, BNDES e debêntures sem ruído.',
    'cta_credito_subhead'  => 'Linhas de crédito empresarial explicadas com matemática e prazo — sem letra miúda.',
    'cta_credito_cta'      => 'Inscrever na frente Crédito',
    'cta_gx_eyebrow'  => 'Inteligência GX',
    'cta_gx_headline' => 'Quer mais conteúdo como este no seu inbox?',
    'cta_gx_subhead'  => 'Câmbio, crédito, economia e consórcio explicados em 90 segundos por edição.',
    'cta_gx_cta'      => 'Inscrever na newsletter',
    'cta_def_headline' => 'Inteligência financeira que chega antes do mercado reagir.',
    'cta_def_subhead'  => 'Briefings curtos sobre câmbio, crédito, economia e consórcio — 3 edições por dia, 90 segundos cada.',
    'cta_def_cta'      => 'Conhecer a newsletter',
    'cta_smallprint'   => 'Gratuita. Cancele com 1 clique. Sem spam.',

    // ===================================================================
    // e-mails (email/email_newsletter_*). Conteúdo é variável (controller);
    // aqui só as poucas strings fixas. brand line reusa lay_copyright.
    // ===================================================================
    'em_welcome_material' => 'Seu material exclusivo',
    'em_welcome_first'    => 'A primeira edição da nossa newsletter chega em breve à sua caixa.',
    'em_welcome_footer'   => 'Você está recebendo este email porque se inscreveu na newsletter da {brand}.',
    'em_confirm_fallback' => 'Se o botão não funcionar, copie e cole este link no navegador:',
    'em_confirm_ignore'   => 'Se você não solicitou esta inscrição, ignore este email — nenhuma ação adicional é necessária.',
    'em_ai_readmore'      => 'Leia mais',
];
