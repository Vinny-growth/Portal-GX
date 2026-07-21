<?php

/**
 * Rotas do módulo Simuladores (white-label — Fase 3).
 *
 * Carregadas no topo do app/Config/Routes.php via ModuleRegistry::enabledRouteFiles()
 * — antes do catch-all (:any) da CMS. Guard MOD_ROUTES_SIMULATORS evita registro duplo.
 * Só registram se o módulo `simulators` estiver habilitado (enabled_default=true → GX on).
 *
 * Os 301 de slugs legados/quebrados (LEGACY_SIMULATOR_REDIRECTS) precisam ser rotas GET
 * (não addRedirect): getRoutes() casa o verbo GET antes do wildcard `*`, senão o
 * catch-all (:any) -> HomeController::any interceptaria e devolveria 404.
 */
if (defined('MOD_ROUTES_SIMULATORS')) {
    return;
}
define('MOD_ROUTES_SIMULATORS', 1);

if (service('moduleRegistry')->enabled('simulators')) {
    $routes->get('simuladores', '\App\Controllers\HomeController::simulatorsHub');
    $routes->get('simuladores/cambio', '\App\Controllers\HomeController::simulatorsFxHub');
    $routes->get('simuladores-cambio', '\App\Controllers\HomeController::simulatorsFxLegacyRedirect');
    $routes->get('simulador-de-risco-cambial', '\App\Controllers\HomeController::simulatorsFxLegacyRedirect');
    $routes->get('fx-loan', '\App\Controllers\HomeController::simulatorsFxLegacyRedirect');
    $routes->get('simulador-aurum', '\App\Controllers\HomeController::simuladorAurum');
    $routes->get('simulador-seguro-resgatavel', '\App\Controllers\HomeController::simuladorSeguroResgatavel');
    $routes->get('playbook/importacao-blindada', '\App\Controllers\HomeController::playbookImportacaoBlindada');
    $routes->get('playbook/exportacao-premium', '\App\Controllers\HomeController::playbookExportacaoPremium');
    $routes->post('api/save-simulator-lead', '\App\Controllers\ApiController::saveSimulatorLead');
    $routes->match(['POST', 'OPTIONS'], 'api/quotation/preview', '\App\Controllers\ApiController::quotationPreview');
    $routes->match(['POST', 'OPTIONS'], 'api/quotation/unlock', '\App\Controllers\ApiController::quotationUnlock');

    foreach (array_keys(\App\Controllers\HomeController::LEGACY_SIMULATOR_REDIRECTS) as $legacySlug) {
        $routes->get($legacySlug, '\App\Controllers\HomeController::legacyRedirect');
    }
}
