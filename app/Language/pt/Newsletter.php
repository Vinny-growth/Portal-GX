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
];
