<?php

/**
 * Manifesto do módulo Hello (white-label — Fase 0).
 * Módulo de PROVA que valida o contrato de módulo (rota/menu ligam/desligam por flag).
 * enabled_default = false → nasce desligado; só liga se a tabela `modules` disser enabled=1.
 */
return [
    'key'             => 'hello',
    'name'            => 'Hello',
    'version'         => '1.0.0',
    'requires'        => [],
    'menu'            => [
        ['label' => 'Hello', 'url' => '_hello'],
    ],
    'admin_nav'       => [],
    'permissions'     => [],
    'settings'        => [],
    // Convenção do manifesto: módulos NOVOS declaram aqui o namespace das suas
    // migrations (ex.: 'Modules\Courses'); retrofit/prova = null (sem migrations próprias).
    'migrations_ns'   => null,
    'enabled_default' => false,
];
