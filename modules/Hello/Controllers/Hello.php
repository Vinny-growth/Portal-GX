<?php namespace Modules\Hello\Controllers;

use CodeIgniter\Controller;

/**
 * Controller de prova do módulo Hello. Retorna texto simples (sem view) de propósito,
 * para o teste do toggle ser 100% determinístico (200 quando ligado, 404 quando desligado).
 */
class Hello extends Controller
{
    public function index()
    {
        return 'Hello module OK — habilitado via ModuleRegistry.';
    }
}
