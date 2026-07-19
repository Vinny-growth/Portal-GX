<?php namespace Modules\Courses\Libraries\Gateways;

use CodeIgniter\HTTP\IncomingRequest;

/**
 * Contrato do sub-módulo de pagamento (§7.3). Um install escolhe UM gateway por config
 * (BR/MX → MercadoPago; demais → Stripe). O fluxo é único e independente do gateway:
 * checkout do plano → webhook do gateway → NORMALIZA para um evento interno padrão →
 * MembershipService ativa/renova o membership.
 */
interface PaymentGatewayInterface
{
    /** Identificador estável do gateway (mercadopago|stripe|manual). */
    public function key(): string;

    /**
     * Cria a sessão/preferência de checkout e retorna para onde redirecionar o aluno.
     * @param array $plan     ['title','amount','currency','months','reference']
     * @param array $customer ['document','doc_type','user_id','email','name']
     * @return array ['url' => string, 'reference' => string]  (url = destino do redirect)
     */
    public function createCheckout(array $plan, array $customer): array;

    /**
     * Normaliza um webhook do gateway para o evento interno padrão, ou null se irrelevante/inválido.
     * @return array|null [
     *   'event' => 'payment_paid'|'payment_failed'|'payment_refunded',
     *   'gateway_payment_id' => string, 'reference' => ?string,
     *   'amount' => float, 'currency' => string,
     *   'document' => ?string, 'doc_type' => ?string, 'user_id' => ?int,
     *   'event_ref' => string  // id único do evento p/ dedup
     * ]
     */
    public function parseWebhook(IncomingRequest $request): ?array;
}
