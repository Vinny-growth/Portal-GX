<?php namespace Modules\Courses\Libraries\Gateways;

/** Utilidades comuns aos adaptadores de pagamento (config por env + HTTP JSON + normalização de teste). */
abstract class AbstractGateway implements PaymentGatewayInterface
{
    protected function cfg(string $key, ?string $default = null): ?string
    {
        $v = getenv($key);
        return ($v !== false && $v !== '') ? $v : $default;
    }

    protected function newRef(): string
    {
        return 'gxc_' . bin2hex(random_bytes(6));
    }

    /** POST/GET JSON simples via cURL. Retorna array decodificado (ou [] em falha). */
    protected function httpJson(string $method, string $url, ?array $body = null, array $headers = []): array
    {
        $ch = curl_init($url);
        $headers[] = 'Content-Type: application/json';
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 20,
        ]);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
        $resp = curl_exec($ch);
        curl_close($ch);
        return is_string($resp) ? (json_decode($resp, true) ?: []) : [];
    }

    /**
     * Normaliza um corpo de teste (usado quando não há credenciais do gateway): o endpoint de
     * confirmação local posta um JSON já no formato interno. Mantém o fluxo demonstrável sem API real.
     */
    protected function normalizeTestBody(array $body, string $gateway): ?array
    {
        if (empty($body['gateway_payment_id']) && empty($body['reference'])) {
            return null;
        }
        $ref = (string) ($body['reference'] ?? $body['gateway_payment_id']);
        return [
            'event'              => $body['event'] ?? 'payment_paid',
            'gateway_payment_id' => (string) ($body['gateway_payment_id'] ?? $ref),
            'reference'          => $ref,
            'amount'             => (float) ($body['amount'] ?? 0),
            'currency'           => (string) ($body['currency'] ?? 'BRL'),
            'document'           => $body['document'] ?? null,
            'doc_type'           => $body['doc_type'] ?? 'cpf',
            'user_id'            => isset($body['user_id']) ? (int) $body['user_id'] : null,
            'event_ref'          => $gateway . '_' . $ref,
        ];
    }
}
