<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Emite o header HSTS (Strict-Transport-Security) nas respostas HTTPS.
 *
 * O nginx (aaPanel) já força http->https e TEM o add_header HSTS no vhost, mas
 * ele não estava sendo emitido em nenhuma resposta (gotcha de herança de
 * add_header na cadeia de includes). Emitir pela app é confiável e versionado:
 * o browser registra a política a partir de qualquer resposta HTTPS e passa a
 * pular o redirect http->https em visitas repetidas (~630ms no mobile).
 *
 * Sem includeSubDomains/preload de propósito (evita comprometer subdomínios e
 * a lista de preload); espelha o max-age já configurado no nginx.
 */
class Hsts implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if ($request instanceof IncomingRequest && $request->isSecure()) {
            $response->setHeader('Strict-Transport-Security', 'max-age=31536000');
        }
    }
}
