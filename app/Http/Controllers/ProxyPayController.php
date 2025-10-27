<?php

namespace App\Http\Controllers;

use App\Models\ProxypayReference;
use App\Services\ProxyPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * ProxyPayController
 * 
 * Controller para gestão de pagamentos ProxyPay (Multicaixa EMIS)
 * 
 * Funcionalidades:
 * - Exibir página de referência EMIS
 * - Polling de status de pagamento
 * - Receber webhook do ProxyPay
 */
class ProxyPayController extends Controller
{
    protected $proxyPayService;

    public function __construct()
    {
        $this->proxyPayService = new ProxyPayService();
    }

    /**
     * Exibir página da referência EMIS
     * 
     * @param string $reference_id
     * @return \Illuminate\View\View
     */
    public function show($reference_id)
    {
        $reference = ProxypayReference::where('reference_id', $reference_id)->firstOrFail();

        // Verificar se já expirou
        if ($reference->isExpired() && $reference->status === 'pending') {
            $reference->markAsExpired();
        }

        return view('proxypay.reference', compact('reference'));
    }

    /**
     * Verificar status do pagamento (polling)
     * 
     * Endpoint chamado pelo JavaScript a cada 10 segundos
     * 
     * @param string $reference_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function check($reference_id)
    {
        try {
            $reference = ProxypayReference::where('reference_id', $reference_id)->firstOrFail();

            // Se já foi pago, retornar URL de sucesso
            if ($reference->isPaid()) {
                return response()->json([
                    'status' => 'paid',
                    'redirect_url' => url('/orders/' . $reference->order_id)
                ]);
            }

            // Se expirou, retornar status expirado
            if ($reference->isExpired()) {
                if ($reference->status === 'pending') {
                    $reference->markAsExpired();
                }
                
                return response()->json([
                    'status' => 'expired',
                    'message' => 'A referência expirou. Por favor, crie um novo pedido.'
                ]);
            }

            // Verificar status na API do ProxyPay
            $statusCheck = $this->proxyPayService->checkPaymentStatus($reference_id);

            if ($statusCheck['success'] && $statusCheck['status'] === 'paid') {
                // Marcar como pago
                $reference->markAsPaid($statusCheck['data'] ?? []);

                // Processar o pedido
                $this->processOrder($reference->order_id);

                return response()->json([
                    'status' => 'paid',
                    'redirect_url' => url('/orders/' . $reference->order_id)
                ]);
            }

            // Ainda pendente
            return response()->json([
                'status' => 'pending',
                'time_remaining' => $reference->getTimeRemaining()
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao verificar status ProxyPay: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao verificar pagamento'
            ], 500);
        }
    }

    /**
     * Receber webhook do ProxyPay
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function webhook(Request $request)
    {
        try {
            // Log do webhook para debug
            Log::info('ProxyPay Webhook recebido:', $request->all());

            $referenceId = $request->input('reference_id');

            if (!$referenceId) {
                Log::warning('Webhook sem reference_id');
                return response()->json(['error' => 'reference_id ausente'], 400);
            }

            $reference = ProxypayReference::where('reference_id', $referenceId)->first();

            if (!$reference) {
                Log::warning('Referência não encontrada: ' . $referenceId);
                return response()->json(['error' => 'Referência não encontrada'], 404);
            }

            // Se já foi processado, retornar sucesso
            if ($reference->isPaid()) {
                return response()->json(['success' => true, 'message' => 'Já processado']);
            }

            // Marcar como pago
            $reference->markAsPaid([
                'payment_id' => $request->input('payment_id'),
                'webhook_data' => $request->all()
            ]);

            // Processar o pedido
            $this->processOrder($reference->order_id);

            Log::info('Pagamento confirmado via webhook: ' . $referenceId);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook ProxyPay: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }

    /**
     * Processar pedido após pagamento confirmado
     * 
     * @param string $orderId
     * @return void
     */
    protected function processOrder($orderId)
    {
        try {
            // TODO: Implementar lógica específica do projeto
            // Exemplo:
            // $order = Order::find($orderId);
            // $order->update(['payment_status' => 'paid']);
            // $order->processOrder();
            
            Log::info('Pedido processado: ' . $orderId);
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar pedido ' . $orderId . ': ' . $e->getMessage());
        }
    }
}
