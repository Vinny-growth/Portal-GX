<?php namespace Modules\Courses\Libraries\Gateways;

use CodeIgniter\HTTP\IncomingRequest;

/**
 * Adaptador MercadoPago (Brasil + México — PIX e métodos locais). Credenciais via env:
 *   COURSES_MP_ACCESS_TOKEN. Sem credenciais → cai no checkout de teste local (fluxo demonstrável).
 */
class MercadoPagoGateway extends AbstractGateway
{
    public function key(): string
    {
        return 'mercadopago';
    }

    public function createCheckout(array $plan, array $customer): array
    {
        $ref = $plan['reference'] ?? $this->newRef();
        $token = $this->cfg('COURSES_MP_ACCESS_TOKEN');
        if (!$token) {
            // sem credenciais: checkout de teste local (confirma e ativa via webhook interno)
            return ['url' => site_url('courses/checkout/confirmar?gw=mercadopago&ref=' . $ref), 'reference' => $ref];
        }
        $pref = $this->httpJson('POST', 'https://api.mercadopago.com/checkout/preferences', [
            'items'              => [[
                'title'      => $plan['title'] ?? 'Assinatura anual',
                'quantity'   => 1,
                'unit_price' => (float) ($plan['amount'] ?? 0),
                'currency_id' => $plan['currency'] ?? 'BRL',
            ]],
            'external_reference' => $ref,
            'metadata'           => [
                'document' => $customer['document'] ?? null,
                'doc_type' => $customer['doc_type'] ?? 'cpf',
                'user_id'  => $customer['user_id'] ?? null,
                'months'   => $plan['months'] ?? 12,
            ],
            'back_urls'          => ['success' => site_url('minha-assinatura'), 'failure' => site_url('minha-assinatura')],
            'notification_url'   => site_url('courses/webhook/pagamento/mercadopago'),
        ], ['Authorization: Bearer ' . $token]);
        return ['url' => $pref['init_point'] ?? site_url('minha-assinatura'), 'reference' => $ref];
    }

    public function parseWebhook(IncomingRequest $request): ?array
    {
        $body = json_decode($request->getBody() ?: '', true) ?: [];
        $token = $this->cfg('COURSES_MP_ACCESS_TOKEN');
        if (!$token) {
            return $this->normalizeTestBody($body, 'mercadopago');
        }
        // notificação real: {type:'payment', data:{id}} → busca o pagamento p/ status/metadata
        $type = $body['type'] ?? $request->getGet('type');
        $payId = $body['data']['id'] ?? $request->getGet('data.id') ?? null;
        if ($type !== 'payment' || !$payId) {
            return null;
        }
        $p = $this->httpJson('GET', 'https://api.mercadopago.com/v1/payments/' . $payId, null, ['Authorization: Bearer ' . $token]);
        if (empty($p['status'])) {
            return null;
        }
        $event = $p['status'] === 'approved' ? 'payment_paid' : ($p['status'] === 'refunded' ? 'payment_refunded' : 'payment_failed');
        $meta = $p['metadata'] ?? [];
        return [
            'event'              => $event,
            'gateway_payment_id' => (string) $payId,
            'reference'          => $p['external_reference'] ?? null,
            'amount'             => (float) ($p['transaction_amount'] ?? 0),
            'currency'           => $p['currency_id'] ?? 'BRL',
            'document'           => $meta['document'] ?? null,
            'doc_type'           => $meta['doc_type'] ?? 'cpf',
            'user_id'            => isset($meta['user_id']) ? (int) $meta['user_id'] : null,
            'event_ref'          => 'mp_' . $payId,
        ];
    }
}
