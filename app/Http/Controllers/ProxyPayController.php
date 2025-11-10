<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProxypayReference;
use App\Traits\ProxyPayTrait;
use Illuminate\Support\Facades\Log;

/**
 * ProxyPayController - Gerenciar Pagamentos ProxyPay EMIS
 *
 * Implementação do ProxyPay v1.0.1 com polling
 */
class ProxyPayController extends Controller
{
    use ProxyPayTrait;

    /**
     * Mostrar página de pagamento EMIS com polling
     *
     * @param string|int $referenceId
     * @return \Illuminate\View\View
     */
    public function show($referenceId)
    {
        $reference = ProxypayReference::where('reference_id', $referenceId)->firstOrFail();

        // Verificar se já foi pago
        if ($reference->isPaid()) {
            return redirect()->route('payment.success', ['reference' => $referenceId])
                           ->with('success', 'Pagamento confirmado!');
        }

        // Verificar se expirou
        if ($reference->isExpired()) {
            return redirect()->route('payment.expired', ['reference' => $referenceId])
                           ->with('error', 'Referência expirada');
        }

        return view('proxypay.reference', [
            'reference' => $reference,
            'entity' => $reference->entity,
            'referenceNumber' => $reference->reference,
            'amount' => $reference->amount,
            'timeRemaining' => $reference->getTimeRemaining()
        ]);
    }

    /**
     * Verificar status de pagamento (API para polling)
     *
     * @param string|int $referenceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPayment($referenceId)
    {
        try {
            $result = $this->checkProxyPayStatus($referenceId);

            return response()->json([
                'success' => $result['success'],
                'status' => $result['status'],
                'paid' => $result['paid'] ?? false,
                'redirect_url' => $result['paid'] ?? false
                    ? route('payment.success', ['reference' => $referenceId])
                    : null
            ]);

        } catch (\Exception $e) {
            Log::error('ProxyPay: Check payment error', [
                'reference_id' => $referenceId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'status' => 'error',
                'paid' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook do ProxyPay (recebe notificações de pagamento)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function webhook(Request $request)
    {
        try {
            Log::info('ProxyPay: Webhook received', $request->all());

            $result = $this->processProxyPayWebhook($request->all());

            if ($result['success']) {
                Log::info('ProxyPay: Webhook processed successfully', [
                    'reference_id' => $result['reference']->reference_id ?? null
                ]);

                // Aqui você pode adicionar lógica adicional
                // Por exemplo: atualizar status do pedido, enviar email, etc.

                return response()->json([
                    'success' => true,
                    'message' => 'Webhook processed'
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Unknown error'
            ], 400);

        } catch (\Exception $e) {
            Log::error('ProxyPay: Webhook exception', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Página de sucesso (exemplo - adaptar ao seu projeto)
     */
    public function success($referenceId)
    {
        $reference = ProxypayReference::where('reference_id', $referenceId)->firstOrFail();

        return view('proxypay.success', [
            'reference' => $reference
        ]);
    }

    /**
     * Página de expiração (exemplo - adaptar ao seu projeto)
     */
    public function expired($referenceId)
    {
        $reference = ProxypayReference::where('reference_id', $referenceId)->firstOrFail();

        return view('proxypay.expired', [
            'reference' => $reference
        ]);
    }
}
