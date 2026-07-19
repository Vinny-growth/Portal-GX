<?php

namespace Modules\Courses\Controllers;

use App\Controllers\BaseController;
use Modules\Courses\Libraries\Gateways\GatewayFactory;
use Modules\Courses\Libraries\MembershipService;

/**
 * Webhooks do módulo Courses (Fase 4b). Rotas públicas, CSRF-exempt (ver Config\Security).
 * Sempre respondem 200 (padrão de webhook). Idempotentes: dedup por event_ref.
 *   - pagamento/<gateway>: gateway normaliza → ativa/renova/estorna membership.
 *   - crm: eventos de cliente da GX (concede comp ao virar cliente; aplica corte ao cancelar).
 */
class WebhookController extends BaseController
{
    public function paymentWebhook($gateway = '')
    {
        $db = \Config\Database::connect();
        $adapter = GatewayFactory::getByKey((string) $gateway);
        $event = $adapter->parseWebhook($this->request);

        if (empty($event)) {
            return $this->response->setStatusCode(200)->setBody('ignored');
        }

        // log + dedup (unique gateway+gateway_ref)
        $ref = $event['event_ref'] ?? ($adapter->key() . '_' . ($event['gateway_payment_id'] ?? uniqid()));
        $dup = $db->table('payment_events')->where('gateway', $adapter->key())->where('gateway_ref', $ref)->countAllResults();
        if ($dup > 0) {
            return $this->response->setStatusCode(200)->setBody('duplicate');
        }
        $db->table('payment_events')->insert([
            'gateway'      => $adapter->key(),
            'event_type'   => $event['event'],
            'gateway_ref'  => $ref,
            'payload_json' => json_encode($event),
            'processed'    => 0,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        if ($event['event'] === 'payment_paid' && !empty($event['document'])) {
            $plan = GatewayFactory::plan();
            (new MembershipService())->activatePaid(
                (string) $event['document'],
                (string) ($event['doc_type'] ?? 'cpf'),
                $event['user_id'] ?? null,
                $adapter->key(),
                (string) ($event['gateway_payment_id'] ?? ''),
                (float) ($event['amount'] ?? 0),
                (string) ($event['currency'] ?? 'BRL'),
                (int) $plan['months']
            );
            $db->table('payment_events')->where('gateway', $adapter->key())->where('gateway_ref', $ref)->update(['processed' => 1]);
        }

        return $this->response->setStatusCode(200)->setBody('ok');
    }

    public function crmWebhook()
    {
        // autenticação por segredo compartilhado (env COURSES_CRM_WEBHOOK_SECRET)
        $secret = getenv('COURSES_CRM_WEBHOOK_SECRET');
        $sent = $this->request->getHeaderLine('X-GX-Signature') ?: (string) $this->request->getGet('secret');
        if ($secret && !hash_equals($secret, $sent)) {
            return $this->response->setStatusCode(401)->setBody('unauthorized');
        }

        $body = json_decode($this->request->getBody() ?: '', true) ?: [];
        $document = (string) ($body['document'] ?? '');
        $eventType = (string) ($body['event_type'] ?? '');
        $eventRef = (string) ($body['event_ref'] ?? ($eventType . '_' . $document . '_' . date('Ymd')));
        if ($document === '' || !in_array($eventType, ['client_activated', 'client_canceled'], true)) {
            return $this->response->setStatusCode(200)->setBody('ignored');
        }

        $db = \Config\Database::connect();
        $dup = $db->table('crm_client_events')->where('event_ref', $eventRef)->countAllResults();
        if ($dup > 0) {
            return $this->response->setStatusCode(200)->setBody('duplicate');
        }
        $db->table('crm_client_events')->insert([
            'document'     => $document,
            'doc_type'     => $body['doc_type'] ?? 'cpf',
            'event_type'   => $eventType,
            'event_ref'    => $eventRef,
            'payload_json' => json_encode($body),
            'processed'    => 1,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        $svc = new MembershipService();
        if ($eventType === 'client_activated') {
            $svc->grantClientComp($document, (string) ($body['doc_type'] ?? 'cpf'), isset($body['user_id']) ? (int) $body['user_id'] : null);
        } else {
            $svc->cancelClient($document);
        }
        return $this->response->setStatusCode(200)->setBody('ok');
    }
}
