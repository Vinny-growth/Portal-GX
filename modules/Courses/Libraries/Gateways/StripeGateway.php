<?php namespace Modules\Courses\Libraries\Gateways;

use CodeIgniter\HTTP\IncomingRequest;

/**
 * Adaptador Stripe (fallback global — qualquer país). Credenciais via env:
 *   COURSES_STRIPE_SECRET, COURSES_STRIPE_WEBHOOK_SECRET (verificação de assinatura).
 * Sem credenciais → checkout de teste local (fluxo demonstrável).
 */
class StripeGateway extends AbstractGateway
{
    public function key(): string
    {
        return 'stripe';
    }

    public function createCheckout(array $plan, array $customer): array
    {
        $ref = $plan['reference'] ?? $this->newRef();
        $secret = $this->cfg('COURSES_STRIPE_SECRET');
        if (!$secret) {
            return ['url' => site_url('courses/checkout/confirmar?gw=stripe&ref=' . $ref), 'reference' => $ref];
        }
        // Stripe Checkout Session (form-encoded). Valor em centavos.
        $amountCents = (int) round(((float) ($plan['amount'] ?? 0)) * 100);
        $fields = http_build_query([
            'mode'                 => 'payment',
            'success_url'          => site_url('minha-assinatura'),
            'cancel_url'           => site_url('minha-assinatura'),
            'client_reference_id'  => $ref,
            'line_items[0][price_data][currency]'     => strtolower($plan['currency'] ?? 'brl'),
            'line_items[0][price_data][product_data][name]' => $plan['title'] ?? 'Assinatura anual',
            'line_items[0][price_data][unit_amount]'  => $amountCents,
            'line_items[0][quantity]'                 => 1,
            'metadata[document]'   => $customer['document'] ?? '',
            'metadata[doc_type]'   => $customer['doc_type'] ?? 'cpf',
            'metadata[user_id]'    => (string) ($customer['user_id'] ?? ''),
            'metadata[months]'     => (string) ($plan['months'] ?? 12),
        ]);
        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $secret], CURLOPT_TIMEOUT => 20,
        ]);
        $resp = json_decode((string) curl_exec($ch), true) ?: [];
        curl_close($ch);
        return ['url' => $resp['url'] ?? site_url('minha-assinatura'), 'reference' => $ref];
    }

    public function parseWebhook(IncomingRequest $request): ?array
    {
        $raw = $request->getBody() ?: '';
        $body = json_decode($raw, true) ?: [];
        if (!$this->cfg('COURSES_STRIPE_SECRET')) {
            // sem credenciais não há pagamento real: o fluxo de teste ativa via
            // CheckoutController::confirmar() (sessão autenticada), nunca por este
            // endpoint público — aceitar POSTs aqui permitiria forjar membership
            log_message('warning', 'Courses/Stripe: webhook ignorado — gateway sem credenciais (modo teste).');
            return null;
        }
        // modo real: a rota é pública/CSRF-exempt, então só a assinatura prova a origem
        if (!$this->verifySignature($raw, $request->getHeaderLine('Stripe-Signature'), $this->cfg('COURSES_STRIPE_WEBHOOK_SECRET'))) {
            log_message('error', 'Courses/Stripe: webhook rejeitado — Stripe-Signature ausente/inválida (COURSES_STRIPE_WEBHOOK_SECRET é obrigatório em modo real).');
            return null;
        }
        // evento real: checkout.session.completed / charge.refunded
        $type = $body['type'] ?? '';
        $obj = $body['data']['object'] ?? [];
        $map = [
            'checkout.session.completed' => 'payment_paid',
            'charge.refunded'            => 'payment_refunded',
            'payment_intent.payment_failed' => 'payment_failed',
        ];
        if (!isset($map[$type])) {
            return null;
        }
        $meta = $obj['metadata'] ?? [];
        return [
            'event'              => $map[$type],
            'gateway_payment_id' => (string) ($obj['id'] ?? ($body['id'] ?? '')),
            'reference'          => $obj['client_reference_id'] ?? null,
            'amount'             => isset($obj['amount_total']) ? ((int) $obj['amount_total']) / 100 : 0,
            'currency'           => strtoupper($obj['currency'] ?? 'BRL'),
            'document'           => $meta['document'] ?? null,
            'doc_type'           => $meta['doc_type'] ?? 'cpf',
            'user_id'            => isset($meta['user_id']) && $meta['user_id'] !== '' ? (int) $meta['user_id'] : null,
            'event_ref'          => 'stripe_' . ($body['id'] ?? ($obj['id'] ?? '')),
        ];
    }

    /** Assinatura do webhook: header "t=<ts>,v1=<hmac>", HMAC-SHA256 de "<ts>.<payload>", tolerância 5 min. */
    private function verifySignature(string $payload, string $header, ?string $secret): bool
    {
        if (empty($secret) || $header === '') {
            return false;
        }
        $ts = 0;
        $sigs = [];
        foreach (explode(',', $header) as $part) {
            [$k, $v] = array_pad(explode('=', trim($part), 2), 2, '');
            if ($k === 't') {
                $ts = (int) $v;
            } elseif ($k === 'v1') {
                $sigs[] = $v;
            }
        }
        if ($ts <= 0 || empty($sigs) || abs(time() - $ts) > 300) {
            return false;
        }
        $expected = hash_hmac('sha256', $ts . '.' . $payload, $secret);
        foreach ($sigs as $sig) {
            if (hash_equals($expected, $sig)) {
                return true;
            }
        }
        return false;
    }
}
