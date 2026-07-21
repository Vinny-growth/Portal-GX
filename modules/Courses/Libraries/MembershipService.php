<?php namespace Modules\Courses\Libraries;

use Modules\Courses\Models\MembershipModel;
use Modules\Courses\Models\PaymentModel;

/**
 * Ciclo de vida do membership único (§7.3 do plano). Fontes: paid (assinatura anual via
 * gateway) e client_comp (cortesia enquanto cliente GX). Identidade = documento nacional.
 *
 * ACESSO (isActive): client_active OU dentro do período pago (now < paid_until) OU dentro
 * da carência (now < access_until). REGRA DE CORTE ao cancelar cliente:
 *  - now < paid_until  → honra a compra: acesso até paid_until.
 *  - já fora do pago   → 30 dias de carência a partir do cancelamento.
 *  - fim do prazo sem comp/renovação → expira (expireSweep, sem carência extra).
 */
class MembershipService
{
    protected MembershipModel $memberships;
    protected PaymentModel $payments;
    protected int $graceDays = 30;
    protected int $defaultMonths = 12;

    public function __construct()
    {
        $this->memberships = new MembershipModel();
        $this->payments    = new PaymentModel();
    }

    /** Computa acesso a partir dos campos do membership (pura; testável). */
    public static function isActive(?array $m, ?string $nowStr = null): bool
    {
        if (empty($m)) {
            return false;
        }
        $now = strtotime($nowStr ?? date('Y-m-d H:i:s'));
        if ((int) ($m['client_active'] ?? 0) === 1) {
            return true;
        }
        if (!empty($m['paid_until']) && $now < strtotime($m['paid_until'])) {
            return true;
        }
        if (!empty($m['access_until']) && $now < strtotime($m['access_until'])) {
            return true;
        }
        return false;
    }

    public function hasActiveForUser(int $userId): bool
    {
        return self::isActive($this->memberships->getForUser($userId));
    }

    public function hasActiveForDocument(string $document): bool
    {
        return self::isActive($this->memberships->getByDocument($document));
    }

    /** Assinatura anual paga (ativa/renova). Renovação empilha sobre o tempo restante. */
    public function activatePaid(string $document, string $docType, ?int $userId, string $gateway, ?string $gatewayPaymentId, float $amount, string $currency, ?int $months = null): array
    {
        $months = $months ?: $this->defaultMonths;
        $now = date('Y-m-d H:i:s');
        $m = $this->upsertByDocument($document, $docType, $userId);

        // renovação empilha: base = paid_until futuro, senão agora
        $base = (!empty($m['paid_until']) && strtotime($m['paid_until']) > time()) ? $m['paid_until'] : $now;
        $paidUntil = date('Y-m-d H:i:s', strtotime($base . ' +' . $months . ' months'));

        $this->memberships->update((int) $m['id'], [
            'source'       => 'paid',
            'status'       => 'active',
            'paid_until'   => $paidUntil,
            'access_until' => $paidUntil,
            'canceled_at'  => null,
            'started_at'   => $m['started_at'] ?: $now,
            'user_id'      => $userId ?: $m['user_id'],
            'updated_at'   => $now,
        ]);

        $this->payments->insert([
            'membership_id'      => (int) $m['id'],
            'user_id'            => $userId ?: $m['user_id'],
            'document'           => $document,
            'gateway'            => $gateway,
            'gateway_payment_id' => $gatewayPaymentId,
            'amount'             => $amount,
            'currency'           => $currency,
            'status'             => 'paid',
            'period_start'       => $now,
            'period_end'         => $paidUntil,
            'created_at'         => $now,
            'updated_at'         => $now,
        ]);

        return $this->memberships->find((int) $m['id']);
    }

    /** Cortesia enquanto cliente GX ativo (webhook client_activated). */
    public function grantClientComp(string $document, string $docType, ?int $userId = null): array
    {
        $now = date('Y-m-d H:i:s');
        $m = $this->upsertByDocument($document, $docType, $userId);
        $this->memberships->update((int) $m['id'], [
            'source'        => empty($m['paid_until']) ? 'client_comp' : $m['source'],
            'client_active' => 1,
            'status'        => 'active',
            'access_until'  => null, // enquanto client_active, acesso é aberto
            'canceled_at'   => null,
            'started_at'    => $m['started_at'] ?: $now,
            'user_id'       => $userId ?: $m['user_id'],
            'updated_at'    => $now,
        ]);
        return $this->memberships->find((int) $m['id']);
    }

    /** Cancelamento do vínculo de cliente (webhook client_canceled) — aplica a regra de corte. */
    public function cancelClient(string $document): ?array
    {
        $m = $this->memberships->getByDocument($document);
        if (empty($m)) {
            return null;
        }
        $now = date('Y-m-d H:i:s');
        $patch = ['client_active' => 0, 'canceled_at' => $now, 'updated_at' => $now];

        if (!empty($m['paid_until']) && time() < strtotime($m['paid_until'])) {
            // dentro dos 12 meses pagos → honra a compra até o fim do período
            $patch['access_until'] = $m['paid_until'];
            $patch['status'] = 'active';
        } else {
            // fora do período pago (só comp / nunca pagou) → 30 dias de carência
            $patch['access_until'] = date('Y-m-d H:i:s', strtotime($now . ' +' . $this->graceDays . ' days'));
            $patch['status'] = 'grace';
        }
        $this->memberships->update((int) $m['id'], $patch);
        return $this->memberships->find((int) $m['id']);
    }

    /** Estorno/chargeback: revoga o período pago imediatamente (comp de cliente, se houver, permanece). */
    public function refundPaid(string $document, string $gateway, ?string $gatewayPaymentId = null): ?array
    {
        $m = $this->memberships->getByDocument($document);
        if (empty($m)) {
            return null;
        }
        $now = date('Y-m-d H:i:s');
        if ($gatewayPaymentId) {
            $pay = $this->payments->getByGatewayId($gateway, $gatewayPaymentId);
            if (!empty($pay)) {
                $this->payments->update((int) $pay['id'], ['status' => 'refunded', 'updated_at' => $now]);
            }
        }
        $isClient = (int) ($m['client_active'] ?? 0) === 1;
        $this->memberships->update((int) $m['id'], [
            'paid_until'   => null,
            'access_until' => null,
            'source'       => $isClient ? 'client_comp' : $m['source'],
            'status'       => $isClient ? 'active' : 'expired',
            'canceled_at'  => $now,
            'updated_at'   => $now,
        ]);
        log_message('warning', 'Courses: estorno processado — período pago revogado (documento mascarado ***' . substr($document, -4) . ').');
        return $this->memberships->find((int) $m['id']);
    }

    /** Concessão manual pelo admin (cortesia avulsa por N meses). */
    public function grantManual(string $document, string $docType, ?int $userId, int $months = 12): array
    {
        $now = date('Y-m-d H:i:s');
        $m = $this->upsertByDocument($document, $docType, $userId);
        $until = date('Y-m-d H:i:s', strtotime($now . ' +' . $months . ' months'));
        $this->memberships->update((int) $m['id'], [
            'source'       => 'manual',
            'status'       => 'active',
            'access_until' => $until,
            'canceled_at'  => null,
            'started_at'   => $m['started_at'] ?: $now,
            'user_id'      => $userId ?: $m['user_id'],
            'updated_at'   => $now,
        ]);
        return $this->memberships->find((int) $m['id']);
    }

    /** Reconciliação: expira memberships fora de qualquer janela de acesso. Retorna nº expirados. */
    public function expireSweep(): int
    {
        $rows = $this->memberships->where('status !=', 'expired')->findAll();
        $n = 0;
        foreach ($rows as $m) {
            if (!self::isActive($m)) {
                $this->memberships->update((int) $m['id'], ['status' => 'expired', 'updated_at' => date('Y-m-d H:i:s')]);
                $n++;
            }
        }
        return $n;
    }

    private function upsertByDocument(string $document, string $docType, ?int $userId): array
    {
        $m = $this->memberships->getByDocument($document);
        if (!empty($m)) {
            return $m;
        }
        $now = date('Y-m-d H:i:s');
        $id = $this->memberships->insert([
            'user_id'       => $userId,
            'document'      => $document,
            'doc_type'      => $docType ?: 'cpf',
            'source'        => 'paid',
            'client_active' => 0,
            'status'        => 'active',
            'started_at'    => $now,
            'created_at'    => $now,
            'updated_at'    => $now,
        ], true);
        return $this->memberships->find($id);
    }
}
