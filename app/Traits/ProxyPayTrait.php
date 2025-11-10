<?php

namespace App\Traits;

use App\Services\ProxyPayService;
use App\Models\ProxypayReference;
use Illuminate\Support\Facades\Log;

/**
 * ProxyPayTrait - Helper para Controllers
 *
 * Facilita o uso do ProxyPay em qualquer controller
 *
 * @version 1.0.0
 */
trait ProxyPayTrait
{
    /**
     * Criar referência ProxyPay EMIS
     *
     * @param string|int $orderId ID do pedido/transação
     * @param float $amount Valor em Kwanzas
     * @param array $customFields Campos personalizados (opcional)
     * @param int $validityHours Horas de validade (padrão: 2)
     * @return array ['success' => bool, 'reference_id' => string, 'entity' => string, 'error' => string|null]
     */
    protected function createProxyPayReference($orderId, $amount, $customFields = [], $validityHours = null)
    {
        try {
            $proxyPay = new ProxyPayService();

            // Gerar ID único
            $referenceId = ProxyPayService::generateReferenceId();

            // Calcular data de expiração
            $validityHours = $validityHours ?? config('proxypay.validity_hours', 2);
            $endDateTime = now()->addHours($validityHours)->toIso8601String();

            // Criar referência via API
            $result = $proxyPay->createReference(
                $referenceId,
                $amount,
                $endDateTime,
                array_merge($customFields, [
                    'order_id' => (string) $orderId
                ])
            );

            if (!$result['success']) {
                return [
                    'success' => false,
                    'error' => $result['error']
                ];
            }

            // Salvar na base de dados
            $reference = ProxypayReference::create([
                'reference_id' => $referenceId,
                'entity' => $proxyPay->getEntity(),
                'reference' => (string) $referenceId,
                'amount' => $amount,
                'end_datetime' => $endDateTime,
                'status' => 'pending',
                'order_id' => $orderId,
                'custom_fields' => $customFields
            ]);

            Log::info('ProxyPay: Reference created and saved', [
                'reference_id' => $referenceId,
                'order_id' => $orderId,
                'amount' => $amount
            ]);

            return [
                'success' => true,
                'reference_id' => $referenceId,
                'entity' => $proxyPay->getEntity(),
                'reference' => $referenceId,
                'amount' => $amount,
                'end_datetime' => $endDateTime,
                'model' => $reference,
                'error' => null
            ];

        } catch (\Exception $e) {
            Log::error('ProxyPay: Error creating reference', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao criar referência de pagamento: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verificar status de pagamento
     *
     * @param string|int $referenceId ID da referência
     * @return array ['success' => bool, 'status' => string, 'paid' => bool]
     */
    protected function checkProxyPayStatus($referenceId)
    {
        try {
            $reference = ProxypayReference::where('reference_id', $referenceId)->first();

            if (!$reference) {
                return [
                    'success' => false,
                    'status' => 'not_found',
                    'paid' => false
                ];
            }

            // Se já está pago, retornar
            if ($reference->isPaid()) {
                return [
                    'success' => true,
                    'status' => 'paid',
                    'paid' => true,
                    'reference' => $reference
                ];
            }

            // Verificar se expirou
            if ($reference->isExpired()) {
                $reference->markAsExpired();
                return [
                    'success' => true,
                    'status' => 'expired',
                    'paid' => false,
                    'reference' => $reference
                ];
            }

            // Consultar API ProxyPay
            $proxyPay = new ProxyPayService();
            $result = $proxyPay->checkPaymentStatus($referenceId);

            if ($result['success'] && $result['status'] === 'paid') {
                $reference->markAsPaid($result['data']);

                return [
                    'success' => true,
                    'status' => 'paid',
                    'paid' => true,
                    'reference' => $reference
                ];
            }

            return [
                'success' => true,
                'status' => $reference->status,
                'paid' => false,
                'reference' => $reference
            ];

        } catch (\Exception $e) {
            Log::error('ProxyPay: Error checking status', [
                'reference_id' => $referenceId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'paid' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Processar webhook do ProxyPay
     *
     * @param array $webhookData Dados do webhook
     * @return array ['success' => bool, 'reference' => ProxypayReference|null]
     */
    protected function processProxyPayWebhook($webhookData)
    {
        try {
            $referenceId = $webhookData['reference_id'] ?? $webhookData['id'] ?? null;

            if (!$referenceId) {
                Log::warning('ProxyPay: Webhook without reference_id', $webhookData);
                return ['success' => false, 'error' => 'No reference_id'];
            }

            $reference = ProxypayReference::where('reference_id', $referenceId)->first();

            if (!$reference) {
                Log::warning('ProxyPay: Webhook for unknown reference', [
                    'reference_id' => $referenceId
                ]);
                return ['success' => false, 'error' => 'Reference not found'];
            }

            if ($reference->isPaid()) {
                Log::info('ProxyPay: Webhook for already paid reference', [
                    'reference_id' => $referenceId
                ]);
                return ['success' => true, 'reference' => $reference, 'already_paid' => true];
            }

            // Marcar como pago
            $reference->markAsPaid($webhookData);

            Log::info('ProxyPay: Webhook processed successfully', [
                'reference_id' => $referenceId,
                'order_id' => $reference->order_id
            ]);

            return [
                'success' => true,
                'reference' => $reference,
                'newly_paid' => true
            ];

        } catch (\Exception $e) {
            Log::error('ProxyPay: Webhook processing error', [
                'error' => $e->getMessage(),
                'data' => $webhookData
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Cancelar/expirar referência
     *
     * @param string|int $referenceId
     * @return bool
     */
    protected function expireProxyPayReference($referenceId)
    {
        try {
            $reference = ProxypayReference::where('reference_id', $referenceId)->first();

            if ($reference && $reference->isPending()) {
                $reference->markAsExpired();
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('ProxyPay: Error expiring reference', [
                'reference_id' => $referenceId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
