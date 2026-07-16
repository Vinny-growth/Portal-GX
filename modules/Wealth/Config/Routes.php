<?php

/**
 * Rotas do módulo Wealth Manager (white-label — Fase 3).
 *
 * Carregadas no topo do app/Config/Routes.php via ModuleRegistry::enabledRouteFiles()
 * — antes do catch-all (:any) da CMS. O guard MOD_ROUTES_WEALTH evita registro duplo
 * caso o CI4 também auto-descubra este arquivo pelo namespace PSR-4.
 *
 * Só registram se o módulo `wealth` estiver habilitado (enabled_default=true → GX on).
 * Os handlers apontam para os controllers do próprio módulo (Modules\Wealth\Controllers),
 * fisicamente residentes em modules/Wealth/ (Fase 3c — realocação física concluída).
 */
if (defined('MOD_ROUTES_WEALTH')) {
    return;
}
define('MOD_ROUTES_WEALTH', 1);

if (service('moduleRegistry')->enabled('wealth')) {
    $routes->get('wealth', '\Modules\Wealth\Controllers\WealthManagerController::index');
    $routes->get('wealth/conversa', '\Modules\Wealth\Controllers\WealthManagerController::conversa', ['filter' => 'auth']);
    $routes->post('wealth/lead', '\Modules\Wealth\Controllers\WealthManagerController::leadCapture');
    $routes->post('WealthManager/sendMessage', '\Modules\Wealth\Controllers\WealthManagerController::sendMessage', ['filter' => 'auth']);
    $routes->post('WealthManager/acceptConsent', '\Modules\Wealth\Controllers\WealthManagerController::acceptConsent', ['filter' => 'auth']);
    $routes->post('WealthManager/saveProfileBasic', '\Modules\Wealth\Controllers\WealthManagerController::saveProfileBasic', ['filter' => 'auth']);
    $routes->post('WealthManager/saveIncomeForm', '\Modules\Wealth\Controllers\WealthManagerController::saveIncomeForm', ['filter' => 'auth']);
    $routes->post('WealthManager/saveExpenseForm', '\Modules\Wealth\Controllers\WealthManagerController::saveExpenseForm', ['filter' => 'auth']);
    $routes->post('WealthManager/saveDependentsForm', '\Modules\Wealth\Controllers\WealthManagerController::saveDependentsForm', ['filter' => 'auth']);
    $routes->post('WealthManager/saveAllocationForm', '\Modules\Wealth\Controllers\WealthManagerController::saveAllocationForm', ['filter' => 'auth']);
    $routes->post('WealthManager/saveRealEstateForm', '\Modules\Wealth\Controllers\WealthManagerController::saveRealEstateForm', ['filter' => 'auth']);
    $routes->post('WealthManager/saveLiabilitiesForm', '\Modules\Wealth\Controllers\WealthManagerController::saveLiabilitiesForm', ['filter' => 'auth']);
    $routes->post('WealthManager/saveGoalsForm', '\Modules\Wealth\Controllers\WealthManagerController::saveGoalsForm', ['filter' => 'auth']);
    $routes->get('wealth/resultado', '\Modules\Wealth\Controllers\WealthManagerController::resultado', ['filter' => 'auth']);
    $routes->get('wealth/resultado/pdf', '\Modules\Wealth\Controllers\WealthManagerController::resumoPdf', ['filter' => 'auth']);
    $routes->get('wealth/agendar', '\Modules\Wealth\Controllers\WealthManagerController::agendar');
    $routes->post('wealth/agendar', '\Modules\Wealth\Controllers\WealthManagerController::agendarPost');
    $routes->post('WealthManager/trackEvent', '\Modules\Wealth\Controllers\WealthManagerController::trackEvent');
}
