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
];
