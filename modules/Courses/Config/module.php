<?php

/**
 * Manifesto do módulo Courses — LMS gamificado (visual Netflix) + jornada + (futuro) comunidade.
 * Fase 4 do white-label. Nasce JÁ no contrato de módulo validado na Fase 3 (é a "forcing
 * function" da arquitetura). Diferente do Wealth/Simulators (retrofit), o Courses é
 * GREENFIELD e 100% autocontido: controllers/models/views/migrations/rotas TODOS vivem
 * em modules/Courses/ sob o namespace Modules\Courses\* (PSR-4 em app/Config/Autoload.php).
 *
 * enabled_default = FALSE → a GX Brasil (produção ao vivo) NÃO é afetada: rotas/menus não
 * registram enquanto o flag estiver desligado. O módulo é o produto do install de EDUCAÇÃO
 * (install #3); liga-se lá via linha na tabela `modules`. Isto mantém a produção intocada
 * durante o desenvolvimento (verificação é feita por flip temporário do flag).
 */
return [
    'key'             => 'courses',
    'name'            => 'Cursos & Comunidade',
    'version'         => '0.1.0-4a',
    'requires'        => [],
    // Link no menu público (área do aluno). Só aparece se o módulo estiver ligado.
    'menu'            => [
        ['label' => 'Cursos', 'url' => 'cursos'],
    ],
    // Item(ns) no sidebar do admin. Só aparece se o módulo estiver ligado.
    'admin_nav'       => [
        ['label' => 'Cursos', 'url' => 'admin/cursos'],
    ],
    'permissions'     => [],
    'settings'        => [],
    // Migrations do módulo vivem em modules/Courses/Database/Migrations sob este namespace.
    // Rodar com: php spark migrate -n "Modules\Courses"
    'migrations_ns'   => 'Modules\\Courses\\Database\\Migrations',
    'enabled_default' => false,
];
