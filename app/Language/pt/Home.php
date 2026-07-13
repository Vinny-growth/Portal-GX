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
];
