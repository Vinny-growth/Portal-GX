<?php

/**
 * Rotas do módulo Hello.
 * Carregadas de duas formas possíveis: (1) explicitamente no topo do Routes.php principal
 * via ModuleRegistry::enabledRouteFiles() — para ter prioridade sobre o catch-all (:any)
 * da CMS; (2) auto-descoberta do CI4 (namespace registrado). O guard abaixo evita registrar
 * a rota duas vezes — a primeira carga vence.
 */
if (defined('MOD_ROUTES_HELLO')) {
    return;
}
define('MOD_ROUTES_HELLO', 1);

if (service('moduleRegistry')->enabled('hello')) {
    $routes->get('_hello', '\Modules\Hello\Controllers\Hello::index');
}
