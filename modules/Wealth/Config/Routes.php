<?php

/**
 * Rotas do módulo Wealth Manager (white-label — Fase 3).
 *
 * Carregadas no topo do app/Config/Routes.php via ModuleRegistry::enabledRouteFiles()
 * — antes do catch-all (:any) da CMS. O guard MOD_ROUTES_WEALTH evita registro duplo
 * caso o CI4 também auto-descubra este arquivo pelo namespace PSR-4.
 *
 * Só registram se o módulo `wealth` estiver habilitado (enabled_default=true → GX on).
 * Os handlers apontam para os controllers ainda em App\Controllers (realocação física
 * é sub-fase posterior).
 */
if (defined('MOD_ROUTES_WEALTH')) {
    return;
}
define('MOD_ROUTES_WEALTH', 1);

if (service('moduleRegistry')->enabled('wealth')) {
    $routes->get('wealth', '\App\Controllers\WealthManagerController::index');
    $routes->get('wealth/conversa', '\App\Controllers\WealthManagerController::conversa', ['filter' => 'auth']);
    $routes->post('wealth/lead', '\App\Controllers\WealthManagerController::leadCapture');
    $routes->post('WealthManager/sendMessage', '\App\Controllers\WealthManagerController::sendMessage', ['filter' => 'auth']);
    $routes->post('WealthManager/acceptConsent', '\App\Controllers\WealthManagerController::acceptConsent', ['filter' => 'auth']);
    $routes->post('WealthManager/saveProfileBasic', '\App\Controllers\WealthManagerController::saveProfileBasic', ['filter' => 'auth']);
    $routes->post('WealthManager/saveIncomeForm', '\App\Controllers\WealthManagerController::saveIncomeForm', ['filter' => 'auth']);
    $routes->post('WealthManager/saveExpenseForm', '\App\Controllers\WealthManagerController::saveExpenseForm', ['filter' => 'auth']);
    $routes->post('WealthManager/saveDependentsForm', '\App\Controllers\WealthManagerController::saveDependentsForm', ['filter' => 'auth']);
    $routes->post('WealthManager/saveAllocationForm', '\App\Controllers\WealthManagerController::saveAllocationForm', ['filter' => 'auth']);
    $routes->post('WealthManager/saveRealEstateForm', '\App\Controllers\WealthManagerController::saveRealEstateForm', ['filter' => 'auth']);
    $routes->post('WealthManager/saveLiabilitiesForm', '\App\Controllers\WealthManagerController::saveLiabilitiesForm', ['filter' => 'auth']);
    $routes->post('WealthManager/saveGoalsForm', '\App\Controllers\WealthManagerController::saveGoalsForm', ['filter' => 'auth']);
    $routes->get('wealth/resultado', '\App\Controllers\WealthManagerController::resultado', ['filter' => 'auth']);
    $routes->get('wealth/resultado/pdf', '\App\Controllers\WealthManagerController::resumoPdf', ['filter' => 'auth']);
    $routes->get('wealth/agendar', '\App\Controllers\WealthManagerController::agendar');
    $routes->post('wealth/agendar', '\App\Controllers\WealthManagerController::agendarPost');
    $routes->post('WealthManager/trackEvent', '\App\Controllers\WealthManagerController::trackEvent');
}
