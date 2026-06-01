<?php

/**
 * Deferred Tasks Helper
 *
 * Agenda callbacks para execução APÓS a resposta HTTP ser enviada ao usuário.
 * Usa fastcgi_finish_request() em PHP-FPM ou register_shutdown_function() como fallback.
 *
 * Uso:
 *   deferAfterResponse(function () {
 *       // chamada cURL pesada, envio de email, etc.
 *   });
 */

if (!function_exists('deferAfterResponse')) {
    /**
     * @var callable[] Lista interna de callbacks pendentes
     */
    $_gx_deferred_queue = [];
    $_gx_deferred_registered = false;

    /**
     * Agenda um callback para rodar depois que a resposta HTTP for enviada.
     *
     * - Em PHP-FPM: usa fastcgi_finish_request() para liberar o usuário imediatamente.
     * - Fallback: usa register_shutdown_function() (executa após o script, mas antes de fechar o processo).
     *
     * @param callable $callback Função a ser executada após a resposta
     */
    function deferAfterResponse(callable $callback)
    {
        global $_gx_deferred_queue, $_gx_deferred_registered;

        $_gx_deferred_queue[] = $callback;

        if (!$_gx_deferred_registered) {
            $_gx_deferred_registered = true;
            register_shutdown_function('_gx_flush_deferred');
        }
    }

    /**
     * Executa todos os callbacks pendentes após finalizar a resposta HTTP.
     * @internal Não chamar diretamente.
     */
    function _gx_flush_deferred()
    {
        global $_gx_deferred_queue;

        // Envia a resposta HTTP ao client antes de processar os callbacks
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        // Garantir tempo suficiente para processar (máx 60s além do limite original)
        $currentLimit = (int) ini_get('max_execution_time');
        if ($currentLimit > 0 && $currentLimit < 120) {
            set_time_limit($currentLimit + 60);
        }

        // Ignorar abort do client (já desconectado)
        ignore_user_abort(true);

        foreach ($_gx_deferred_queue as $callback) {
            try {
                $callback();
            } catch (\Throwable $e) {
                log_message('error', 'Deferred task failed: ' . $e->getMessage());
            }
        }

        $_gx_deferred_queue = [];
    }
}
