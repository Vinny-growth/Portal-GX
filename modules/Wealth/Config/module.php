<?php

/**
 * Manifesto do módulo Wealth Manager (white-label — Fase 3, retrofit de módulo real).
 *
 * Primeiro módulo REAL da plataforma (o Hello é só prova de contrato). Nesta fase o
 * módulo é dono das ROTAS públicas do wealth (gating por feature flag); os controllers
 * (App\Controllers\WealthManagerController / WealthAdminController), models e views
 * seguem em app/ por enquanto — a realocação física é uma sub-fase posterior.
 *
 * enabled_default = true → a GX Brasil mantém o wealth LIGADO sem precisar de linha na
 * tabela `modules` (behavior-preserving). Um install que não queira wealth desliga o
 * flag e as rotas somem (ver Config/Routes.php).
 */
return [
    'key'             => 'wealth',
    'name'            => 'Wealth Manager',
    'version'         => '1.0.0',
    'requires'        => [],
    'menu'            => [
        ['label' => 'Wealth Manager', 'url' => 'wealth'],
    ],
    'admin_nav'       => [],
    'permissions'     => [],
    'settings'        => [],
    'enabled_default' => true,
];
