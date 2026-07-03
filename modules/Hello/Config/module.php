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
    'enabled_default' => false,
];
