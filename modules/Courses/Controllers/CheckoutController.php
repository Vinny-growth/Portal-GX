<?php

namespace Modules\Courses\Controllers;

use App\Controllers\BaseController;
use Modules\Courses\Models\MembershipModel;
use Modules\Courses\Models\PaymentModel;
use Modules\Courses\Libraries\MembershipService;
use Modules\Courses\Libraries\Gateways\GatewayFactory;

/**
 * Checkout + área "minha assinatura" (Fase 4b). Fluxo único independente do gateway:
 * iniciar → redirect ao checkout do gateway → webhook ativa o membership. Sem credenciais
 * de gateway, o checkout cai num "confirmar" local (test mode) que simula o pagamento aprovado.
 */
class CheckoutController extends BaseController
{
    private function userId(): int
    {
        return (int) (user()->id ?? 0);
    }

    public function assinatura()
    {
        $userId = $this->userId();
        $mModel = new MembershipModel();
        $membership = $mModel->getForUser($userId);
        return view('courses/subscription', [
            'pageTitle'  => 'Minha assinatura',
            'membership' => $membership,
            'isActive'   => MembershipService::isActive($membership),
            'plan'       => GatewayFactory::plan(),
            'gateway'    => GatewayFactory::default()->key(),
            'payments'   => (new PaymentModel())->forUser($userId),
            'totalXp'    => null,
        ]);
    }

    public function iniciar()
    {
        $userId = $this->userId();
        $document = preg_replace('/\D+/', '', (string) $this->request->getPost('document'));
        $docType = (string) ($this->request->getPost('doc_type') ?: 'cpf');
        if (strlen($document) < 8) {
            return redirect()->to(site_url('minha-assinatura'))->with('error', 'Informe um documento (CPF/CURP) válido.');
        }
        $u = user();
        $plan = GatewayFactory::plan();
        $plan['reference'] = 'gxc_' . bin2hex(random_bytes(6));
        $gateway = GatewayFactory::default();
        $checkout = $gateway->createCheckout($plan, [
            'document' => $document, 'doc_type' => $docType, 'user_id' => $userId,
            'email' => $u->email ?? null, 'name' => $u->username ?? null,
        ]);

        // contexto pendente p/ o retorno de teste (quando não há credenciais reais)
        session()->set('courses_checkout', [
            'reference' => $checkout['reference'], 'document' => $document, 'doc_type' => $docType,
            'user_id' => $userId, 'gateway' => $gateway->key(),
            'amount' => $plan['amount'], 'currency' => $plan['currency'], 'months' => $plan['months'],
        ]);
        return redirect()->to($checkout['url']);
    }

    /**
     * Auto-serviço: vincula o documento do usuário logado a um membership existente.
     * Fecha o gap do fluxo client_comp — o webhook do CRM cria membership por documento
     * com user_id NULL (o CRM não conhece o login do LMS), e o gate de acesso resolve
     * por user_id; sem este vínculo o cliente GX nunca vê a cortesia.
     */
    public function vincular()
    {
        $userId = $this->userId();
        $document = preg_replace('/\D+/', '', (string) $this->request->getPost('document'));
        if (strlen($document) < 8) {
            return redirect()->to(site_url('minha-assinatura'))->with('error', 'Informe um documento (CPF/CURP) válido.');
        }
        $mModel = new MembershipModel();
        $m = $mModel->getByDocument($document);
        if (empty($m)) {
            return redirect()->to(site_url('minha-assinatura'))->with('error', 'Nenhuma assinatura encontrada para este documento.');
        }
        if (!empty($m['user_id']) && (int) $m['user_id'] !== $userId) {
            log_message('warning', 'Courses: user ' . $userId . ' tentou vincular documento já associado a outra conta (***' . substr($document, -4) . ').');
            return redirect()->to(site_url('minha-assinatura'))->with('error', 'Este documento já está vinculado a outra conta. Fale com o suporte.');
        }
        $mModel->linkUser($document, $userId);
        log_message('info', 'Courses: membership do documento ***' . substr($document, -4) . ' vinculado ao user ' . $userId . ' (auto-serviço).');
        return redirect()->to(site_url('minha-assinatura'))->with('success', 'Documento vinculado! Seu acesso foi atualizado.');
    }

    /** Retorno de teste (sem credenciais de gateway): simula pagamento aprovado e ativa. */
    public function confirmar()
    {
        $pending = session('courses_checkout');
        $ref = (string) $this->request->getGet('ref');
        if (empty($pending) || ($pending['reference'] ?? '') !== $ref) {
            return redirect()->to(site_url('minha-assinatura'))->with('error', 'Sessão de checkout expirada.');
        }
        $gw = $pending['gateway'];
        $hasCreds = ($gw === 'stripe' && getenv('COURSES_STRIPE_SECRET'))
                 || ($gw === 'mercadopago' && getenv('COURSES_MP_ACCESS_TOKEN'));
        if ($hasCreds) {
            // gateway real ativa via webhook — nada a fazer aqui
            return redirect()->to(site_url('minha-assinatura'))->with('success', 'Pagamento em processamento. O acesso é liberado assim que confirmado.');
        }
        // test mode: ativa direto
        (new MembershipService())->activatePaid(
            $pending['document'], $pending['doc_type'], (int) $pending['user_id'], $gw,
            'test_' . $ref, (float) $pending['amount'], (string) $pending['currency'], (int) $pending['months']
        );
        session()->remove('courses_checkout');
        return redirect()->to(site_url('minha-assinatura'))->with('success', 'Assinatura ativada! Acesso completo liberado.');
    }
}
