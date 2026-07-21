<?php namespace Modules\Courses\Libraries\Gateways;

use CodeIgniter\HTTP\IncomingRequest;

/**
 * Adaptador MercadoPago (Brasil + México — PIX e métodos locais). Credenciais via env:
 *   COURSES_MP_ACCESS_TOKEN, COURSES_MP_WEBHOOK_SECRET (assinatura secreta do painel de
 *   webhooks do MP — valida o header x-signature). Sem credenciais → checkout de teste local.
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
            // sem credenciais não há pagamento real: o fluxo de teste ativa via
            // CheckoutController::confirmar() (sessão autenticada), nunca por este
            // endpoint público — aceitar POSTs aqui permitiria forjar membership
            log_message('warning', 'Courses/MP: webhook ignorado — gateway sem credenciais (modo teste).');
            return null;
        }
        // notificação real: {type:'payment', data:{id}} → busca o pagamento p/ status/metadata
        $type = $body['type'] ?? $request->getGet('type');
        $payId = $body['data']['id'] ?? $request->getGet('data.id') ?? null;
        if ($type !== 'payment' || !$payId) {
            return null;
        }
        // origem: valida o x-signature quando o segredo estiver configurado (defesa em profundidade
        // além da re-consulta na API, que já garante que o payment_id existe e é da conta)
        $whSecret = $this->cfg('COURSES_MP_WEBHOOK_SECRET');
        if ($whSecret && !$this->verifySignature((string) $payId, $request->getHeaderLine('x-signature'), $request->getHeaderLine('x-request-id'), $whSecret)) {
            log_message('error', 'Courses/MP: webhook rejeitado — x-signature ausente/inválida.');
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

    /** x-signature do MP: "ts=<ts>,v1=<hmac>" com HMAC-SHA256 de "id:<data.id>;request-id:<x-request-id>;ts:<ts>;". */
    private function verifySignature(string $dataId, string $header, string $requestId, string $secret): bool
    {
        if ($header === '') {
            return false;
        }
        $ts = '';
        $v1 = '';
        foreach (explode(',', $header) as $part) {
            [$k, $v] = array_pad(explode('=', trim($part), 2), 2, '');
            if ($k === 'ts') {
                $ts = $v;
            } elseif ($k === 'v1') {
                $v1 = $v;
            }
        }
        if ($ts === '' || $v1 === '') {
            return false;
        }
        $manifest = 'id:' . strtolower($dataId) . ';request-id:' . $requestId . ';ts:' . $ts . ';';
        return hash_equals(hash_hmac('sha256', $manifest, $secret), $v1);
    }
}
