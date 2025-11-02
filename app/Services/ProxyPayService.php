<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * ProxyPay EMIS Service - Serviço Reutilizável
 *
 * Integração completa com ProxyPay para pagamentos Multicaixa EMIS em Angola
 *
 * @version 1.0.0
 * @author Desenvolvido para projetos angolanos
 * @license MIT
 */
class ProxyPayService
{
    protected $apiKey;
    protected $baseUrl;
    protected $environment;
    protected $entity;

    public function __construct()
    {
        $this->environment = config('proxypay.environment', env('PROXYPAY_ENVIRONMENT', 'sandbox'));

        $this->apiKey = $this->environment === 'production'
            ? config('proxypay.production_api_key', env('PROXYPAY_PRODUCTION_API_KEY'))
            : config('proxypay.sandbox_api_key', env('PROXYPAY_SANDBOX_API_KEY'));

        $this->baseUrl = $this->environment === 'production'
            ? 'https://api.proxypay.co.ao'
            : 'https://api.sandbox.proxypay.co.ao';

        $this->entity = config('proxypay.entity', env('PROXYPAY_ENTITY'));
    }

    /**
     * Criar referência de pagamento EMIS
     *
     * @param string|int $referenceId ID único da referência (9 dígitos)
     * @param float $amount Valor em Kwanzas (AOA)
     * @param string $endDateTime Data/hora de expiração (ISO 8601)
     * @param array $customFields Campos personalizados
     * @return array ['success' => bool, 'data' => array|null, 'error' => string|null]
     */
    public function createReference($referenceId, $amount, $endDateTime, $customFields = [])
    {
        try {
            Log::info('ProxyPay: Creating reference', [
                'reference_id' => $referenceId,
                'amount' => $amount,
                'environment' => $this->environment
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/vnd.proxypay.v2+json',
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/references", [
                'amount' => (string) $amount,
                'end_datetime' => $endDateTime,
                'custom_fields' => array_merge([
                    'reference_id' => (string) $referenceId,
                ], $customFields)
            ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('ProxyPay: Reference created successfully', [
                    'reference_id' => $referenceId,
                    'api_response' => $data
                ]);

                return [
                    'success' => true,
                    'data' => $data,
                    'error' => null
                ];
            } else {
                $error = $response->json()['message'] ?? 'Failed to create reference';

                Log::error('ProxyPay: Reference creation failed', [
                    'reference_id' => $referenceId,
                    'status' => $response->status(),
                    'error' => $error,
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'data' => null,
                    'error' => $error
                ];
            }
        } catch (\Exception $e) {
            Log::error('ProxyPay: API Exception', [
                'reference_id' => $referenceId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obter detalhes de uma referência
     *
     * @param string|int $referenceId ID da referência
     * @return array ['success' => bool, 'data' => array|null, 'error' => string|null]
     */
    public function getReference($referenceId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/vnd.proxypay.v2+json',
            ])->get("{$this->baseUrl}/references/{$referenceId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'error' => null
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'error' => $response->json()['message'] ?? 'Failed to get reference'
                ];
            }
        } catch (\Exception $e) {
            Log::error('ProxyPay: Get reference exception', [
                'reference_id' => $referenceId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar status de pagamento de uma referência
     *
     * @param string|int $referenceId ID da referência
     * @return array ['success' => bool, 'status' => string, 'data' => array|null]
     */
    public function checkPaymentStatus($referenceId)
    {
        $result = $this->getReference($referenceId);

        if ($result['success'] && isset($result['data']['status'])) {
            return [
                'success' => true,
                'status' => $result['data']['status'], // 'pending' ou 'paid'
                'data' => $result['data']
            ];
        }

        return [
            'success' => false,
            'status' => 'unknown',
            'data' => null
        ];
    }

    /**
     * Gerar ID de referência único (9 dígitos)
     *
     * @return int
     */
    public static function generateReferenceId()
    {
        return (int) substr((string) (time() * 1000 + rand(100, 999)), -9);
    }

    /**
     * Validar webhook data
     *
     * @param array $data Dados do webhook
     * @return bool
     */
    public function validateWebhook($data)
    {
        // Adicionar validação de assinatura se ProxyPay fornecer
        return isset($data['reference_id']) || isset($data['id']);
    }

    /**
     * Obter entidade configurada
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Obter ambiente atual
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Verificar se está em sandbox
     *
     * @return bool
     */
    public function isSandbox()
    {
        return $this->environment === 'sandbox';
    }

    /**
     * Verificar se está em produção
     *
     * @return bool
     */
    public function isProduction()
    {
        return $this->environment === 'production';
    }

    /**
     * Obter API Key configurada
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Obter Base URL da API
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
}
