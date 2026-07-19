<?php

/**
 * Rotas do módulo Courses (white-label — Fase 4a: LMS + jornada).
 *
 * Carregadas no TOPO do app/Config/Routes.php via ModuleRegistry::enabledRouteFiles()
 * — antes do catch-all (:any) da CMS. Guard MOD_ROUTES_COURSES evita registro duplo.
 * $routes e $customRoutes estão em escopo (definidos no topo do core Routes.php antes do
 * loop de módulos), então o módulo define TANTO as rotas públicas QUANTO o próprio grupo
 * admin (prefixo/filtro iguais aos do core) — módulo 100% autocontido.
 *
 * Só registram se o módulo `courses` estiver habilitado (enabled_default=false → GX off).
 */
if (defined('MOD_ROUTES_COURSES')) {
    return;
}
define('MOD_ROUTES_COURSES', 1);

if (service('moduleRegistry')->enabled('courses')) {

    // ── Área do aluno (público/auth) ─────────────────────────────────────────
    $routes->get('cursos', '\Modules\Courses\Controllers\StudentController::catalog');
    $routes->get('meus-cursos', '\Modules\Courses\Controllers\StudentController::myCourses', ['filter' => 'auth']);
    $routes->get('curso/(:segment)', '\Modules\Courses\Controllers\StudentController::course/$1');
    $routes->get('curso/(:segment)/aula/(:segment)', '\Modules\Courses\Controllers\StudentController::lesson/$1/$2', ['filter' => 'auth']);
    $routes->post('curso/aula/progresso', '\Modules\Courses\Controllers\StudentController::saveProgress', ['filter' => 'auth']);
    $routes->post('curso/aula/completar', '\Modules\Courses\Controllers\StudentController::completeLesson', ['filter' => 'auth']);
    $routes->get('certificado/(:segment)', '\Modules\Courses\Controllers\StudentController::certificate/$1');

    // ── Fase 4b: assinatura/checkout (aluno) + webhooks (públicos, CSRF-exempt) ──────
    $routes->get('minha-assinatura', '\Modules\Courses\Controllers\CheckoutController::assinatura', ['filter' => 'auth']);
    $routes->post('assinatura/iniciar', '\Modules\Courses\Controllers\CheckoutController::iniciar', ['filter' => 'auth']);
    $routes->get('courses/checkout/confirmar', '\Modules\Courses\Controllers\CheckoutController::confirmar', ['filter' => 'auth']);
    // webhooks: sem auth; CSRF isento via Config\Security::$csrfExcludeURIs ('courses/webhook/.*')
    $routes->post('courses/webhook/pagamento/(:segment)', '\Modules\Courses\Controllers\WebhookController::paymentWebhook/$1');
    $routes->post('courses/webhook/crm', '\Modules\Courses\Controllers\WebhookController::crmWebhook');

    // ── Admin (course builder) — mesmo grupo/filtro do painel do core ─────────
    $routes->group($customRoutes->admin, ['filter' => 'auth'], function ($routes) {
        $routes->get('cursos', '\Modules\Courses\Controllers\CourseAdminController::index');
        $routes->get('cursos/novo', '\Modules\Courses\Controllers\CourseAdminController::create');
        $routes->post('cursos/salvar', '\Modules\Courses\Controllers\CourseAdminController::store');
        $routes->get('cursos/(:num)/editar', '\Modules\Courses\Controllers\CourseAdminController::edit/$1');
        $routes->post('cursos/(:num)/editar', '\Modules\Courses\Controllers\CourseAdminController::update/$1');
        $routes->post('cursos/(:num)/publicar', '\Modules\Courses\Controllers\CourseAdminController::togglePublish/$1');
        $routes->post('cursos/(:num)/excluir', '\Modules\Courses\Controllers\CourseAdminController::delete/$1');
        // seções + aulas (via AJAX do builder)
        $routes->post('cursos/secao/salvar', '\Modules\Courses\Controllers\CourseAdminController::saveSection');
        $routes->post('cursos/secao/(:num)/excluir', '\Modules\Courses\Controllers\CourseAdminController::deleteSection/$1');
        $routes->post('cursos/aula/salvar', '\Modules\Courses\Controllers\CourseAdminController::saveLesson');
        $routes->post('cursos/aula/(:num)/excluir', '\Modules\Courses\Controllers\CourseAdminController::deleteLesson/$1');
        // níveis de acesso (grant manual) + concessão a alunos
        $routes->get('cursos/niveis', '\Modules\Courses\Controllers\CourseAdminController::accessLevels');
        $routes->post('cursos/niveis/salvar', '\Modules\Courses\Controllers\CourseAdminController::saveAccessLevel');
        $routes->post('cursos/acesso/conceder', '\Modules\Courses\Controllers\CourseAdminController::grantAccess');
        // Fase 4b: gestão de assinaturas/memberships
        $routes->get('cursos/assinaturas', '\Modules\Courses\Controllers\CourseAdminController::memberships');
        $routes->post('cursos/assinaturas/conceder', '\Modules\Courses\Controllers\CourseAdminController::grantMembership');
    });
}
