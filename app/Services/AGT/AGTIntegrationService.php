<?php

namespace App\Services\AGT;

use App\Models\FiscalDocument;
use Illuminate\Support\Facades\Log;
use Exception;

class AGTIntegrationService
{
    protected $apiClient;
    protected $signatureService;

    public function __construct(
        AGTApiClient $apiClient,
        AGTSignatureService $signatureService
    ) {
        $this->apiClient = $apiClient;
        $this->signatureService = $signatureService;
    }

    /**
     * Submit fiscal document to AGT
     */
    public function submitDocument(FiscalDocument $document): array
    {
        try {
            // Generate hash chain
            $previousHash = $this->signatureService->generateHashChain($document);
            $document->previous_hash = $previousHash;

            // Generate document hash
            $hash = $this->signatureService->generateDocumentHash($document);
            $document->agt_hash = $hash;

            // Generate ATCUD
            $atcud = $this->signatureService->generateATCUD($document);
            $document->agt_atcud = $atcud;

            // Prepare document data for AGT
            $payload = $this->prepareDocumentPayload($document);

            // Sign the payload
            $signature = $this->signatureService->signDocument(json_encode($payload));
            if ($signature) {
                $document->agt_signature = $signature;
                $payload['signature'] = $signature;
            }

            // Submit to AGT API
            $response = $this->apiClient->post('/documents/submit', $payload);

            if ($response['success']) {
                Log::info('AGT: Document submitted successfully', [
                    'document_id' => $document->id,
                    'document_number' => $document->document_number,
                    'response' => $response['data']
                ]);

                // Update document with AGT response
                if (isset($response['data']['qrcode'])) {
                    $document->agt_qrcode = $response['data']['qrcode'];
                }

                $document->save();

                return [
                    'success' => true,
                    'message' => 'Document submitted to AGT successfully',
                    'data' => $response['data'],
                    'document' => $document
                ];
            } else {
                Log::error('AGT: Document submission failed', [
                    'document_id' => $document->id,
                    'error' => $response['error'] ?? 'Unknown error',
                    'status_code' => $response['status_code']
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to submit document to AGT',
                    'error' => $response['error'] ?? 'Unknown error',
                    'status_code' => $response['status_code']
                ];
            }

        } catch (Exception $e) {
            Log::error('AGT: Exception during document submission', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred during AGT submission',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Prepare document payload for AGT API
     */
    protected function prepareDocumentPayload(FiscalDocument $document): array
    {
        return [
            'documentType' => $document->document_type,
            'documentNumber' => $document->document_number,
            'serie' => $document->serie,
            'year' => $document->year,
            'issueDate' => $document->issue_date->format('Y-m-d'),
            'dueDate' => $document->due_date ? $document->due_date->format('Y-m-d') : null,
            
            // Company info
            'company' => [
                'nif' => config('agt.company_nif'),
                'name' => config('agt.company_name'),
                'address' => config('agt.company_address'),
                'phone' => config('agt.company_phone'),
                'email' => config('agt.company_email'),
            ],
            
            // Customer info
            'customer' => [
                'name' => $document->customer_name,
                'nif' => $document->customer_nif,
                'email' => $document->customer_email,
                'phone' => $document->customer_phone,
                'address' => $document->customer_address,
            ],
            
            // Financial data
            'amounts' => [
                'subtotal' => $document->subtotal,
                'discount' => $document->discount,
                'tax' => $document->tax,
                'total' => $document->total,
            ],
            
            // Items
            'items' => $document->items->map(function ($item) {
                return [
                    'productCode' => $item->product_code,
                    'productName' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unitPrice' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                    'taxRate' => $item->tax_rate,
                    'taxAmount' => $item->tax_amount,
                    'total' => $item->total,
                ];
            })->toArray(),
            
            // AGT specific fields
            'hash' => $document->agt_hash,
            'previousHash' => $document->previous_hash,
            'atcud' => $document->agt_atcud,
            'softwareVersion' => config('agt.software_version'),
            'softwareCertificate' => config('agt.software_certificate'),
        ];
    }

    /**
     * Check document status in AGT
     */
    public function checkDocumentStatus(FiscalDocument $document): array
    {
        try {
            $response = $this->apiClient->get("/documents/{$document->id}/status");

            if ($response['success']) {
                return [
                    'success' => true,
                    'status' => $response['data']['status'] ?? 'unknown',
                    'data' => $response['data']
                ];
            }

            return [
                'success' => false,
                'error' => $response['error'] ?? 'Failed to check status'
            ];

        } catch (Exception $e) {
            Log::error('AGT: Failed to check document status', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Cancel document in AGT
     */
    public function cancelDocument(FiscalDocument $document, string $reason): array
    {
        try {
            $payload = [
                'documentId' => $document->id,
                'documentNumber' => $document->document_number,
                'reason' => $reason,
                'cancelledAt' => now()->toIso8601String(),
            ];

            $response = $this->apiClient->post("/documents/{$document->id}/cancel", $payload);

            if ($response['success']) {
                Log::info('AGT: Document cancelled successfully', [
                    'document_id' => $document->id,
                    'document_number' => $document->document_number
                ]);

                return [
                    'success' => true,
                    'message' => 'Document cancelled in AGT',
                    'data' => $response['data']
                ];
            }

            return [
                'success' => false,
                'error' => $response['error'] ?? 'Failed to cancel document'
            ];

        } catch (Exception $e) {
            Log::error('AGT: Failed to cancel document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Test connection to AGT API
     */
    public function testConnection(): array
    {
        try {
            $ping = $this->apiClient->ping();
            $configStatus = $this->apiClient->getConfigStatus();
            $signatureStatus = $this->signatureService->getStatus();

            return [
                'success' => $ping,
                'api_reachable' => $ping,
                'config' => $configStatus,
                'signature' => $signatureStatus,
                'message' => $ping ? 'Connection successful' : 'Connection failed'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
