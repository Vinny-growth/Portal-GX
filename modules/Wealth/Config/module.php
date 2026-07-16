<?php

/**
 * Manifesto do módulo Wealth Manager (white-label — Fase 3, retrofit de módulo real).
 *
 * Primeiro módulo REAL da plataforma (o Hello é só prova de contrato). Fase 3c concluída:
 * o módulo é dono das ROTAS públicas + dos CONTROLLERS/MODELS/LIBRARIES, fisicamente
 * residentes em modules/Wealth/ (Modules\Wealth\Controllers|Models|Libraries, via PSR-4
 * em app/Config/Autoload.php). As VIEWS seguem em app/Views/ de propósito: as telas admin
 * usam includes compartilhados (admin/includes/_header|_footer) e as views públicas do
 * wealth são reusadas pelo painel admin — view('wealth/...') resolve contra o namespace
 * APP independente de onde o controller mora. As rotas ADMIN ficam no grupo admin do core
 * (app/Config/Routes.php, gated por enabled('wealth')) por dependerem do prefixo/filtro do
 * grupo; só o handler aponta para Modules\Wealth\Controllers\WealthAdminController.
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
    'admin_nav'       => [
        ['label' => 'Wealth Manager', 'url' => 'wealth'],
    ],
    'permissions'     => [],
    'settings'        => [],
    // Retrofit: as tabelas do wealth já vivem em app/Database/Migrations (já aplicadas
    // em prod). Módulos NOVOS (ex.: Courses) declaram aqui o namespace das suas migrations.
    'migrations_ns'   => null,
    'enabled_default' => true,
];
