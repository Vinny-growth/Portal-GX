<?php

/**
 * Manifesto do módulo Simuladores (white-label — Fase 3, retrofit de módulo real).
 *
 * Dono das ROTAS públicas dos simuladores (hub, câmbio/fx, aurum, seguro, playbooks
 * de câmbio e as APIs de lead/quotation), além dos 301 de slugs legados. Controllers
 * (App\Controllers\HomeController / ApiController) e views seguem em app/ por ora —
 * realocação física é sub-fase posterior.
 *
 * NÃO cobre (por ora): as páginas de simulador servidas pelo CMS (consórcio,
 * aurum-custo-de-capital, mercado-de-capitais, bndes, custo-de-antecipação) — são
 * CONTEÚDO no catch-all (:any), não rotas explícitas; nem o dashboard admin.
 *
 * enabled_default = true → GX Brasil mantém os simuladores LIGADOS (behavior-preserving).
 */
return [
    'key'             => 'simulators',
    'name'            => 'Simuladores',
    'version'         => '1.0.0',
    'requires'        => [],
    'menu'            => [
        ['label' => 'Simuladores', 'url' => 'simuladores'],
    ],
    'admin_nav'       => [
        ['label' => 'Simuladores (CMS)', 'url' => 'marketing/simulators-cms'],
    ],
    'permissions'     => [],
    'settings'        => [],
    // Retrofit: sem migrations próprias (páginas servidas pelo CMS + leads em app/).
    'migrations_ns'   => null,
    'enabled_default' => true,
];
