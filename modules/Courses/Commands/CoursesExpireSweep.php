<?php

namespace Modules\Courses\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Modules\Courses\Libraries\MembershipService;

/**
 * Reconciliação diária dos memberships: marca status='expired' nos que estão fora de
 * qualquer janela de acesso (o acesso em si é computado ao vivo por isActive(); este
 * sweep só mantém a coluna `status` fiel p/ a tela admin e relatórios).
 *
 * Cron sugerido (instalações com o módulo courses ligado):
 *   0 4 * * * cd /caminho/da/instalacao && php spark courses:expire-sweep
 */
class CoursesExpireSweep extends BaseCommand
{
    protected $group       = 'Courses';
    protected $name        = 'courses:expire-sweep';
    protected $description = 'Marca como expirados os memberships fora de qualquer janela de acesso.';

    public function run(array $params)
    {
        if (!service('moduleRegistry')->enabled('courses')) {
            CLI::write('Módulo courses desligado — nada a fazer.');
            return;
        }
        $n = (new MembershipService())->expireSweep();
        CLI::write('courses:expire-sweep — ' . $n . ' membership(s) marcados como expirados.', 'green');
    }
}
