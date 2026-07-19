<?php namespace Modules\Courses\Libraries\Gateways;

/**
 * Escolhe o gateway do install por config (env COURSES_PAYMENT_GATEWAY = mercadopago|stripe;
 * default mercadopago p/ BR/MX). getByKey() resolve o adaptador pelo segmento da URL do webhook.
 */
class GatewayFactory
{
    public static function default(): PaymentGatewayInterface
    {
        return self::getByKey(getenv('COURSES_PAYMENT_GATEWAY') ?: 'mercadopago');
    }

    public static function getByKey(string $key): PaymentGatewayInterface
    {
        switch (strtolower($key)) {
            case 'stripe':
                return new StripeGateway();
            case 'mercadopago':
            default:
                return new MercadoPagoGateway();
        }
    }

    /** Preço/moeda do plano anual (por install, via env; default coerente com a marca). */
    public static function plan(): array
    {
        return [
            'title'    => getenv('COURSES_PLAN_TITLE') ?: 'Assinatura Anual · Acesso completo',
            'amount'   => (float) (getenv('COURSES_PLAN_AMOUNT') ?: 349.00),
            'currency' => getenv('COURSES_PLAN_CURRENCY') ?: (function_exists('brandCurrency') ? brandCurrency() : 'BRL'),
            'months'   => (int) (getenv('COURSES_PLAN_MONTHS') ?: 12),
        ];
    }
}
