<?php

namespace App\Services\AGT;

use App\Models\FiscalDocument;
use Illuminate\Support\Facades\Log;
use Exception;

class AGTSignatureService
{
    protected $privateKeyPath;
    protected $publicKeyPath;
    protected $hashAlgorithm;

    public function __construct()
    {
        $this->privateKeyPath = storage_path('agt/certificates/private.key');
        $this->publicKeyPath = storage_path('agt/certificates/public.pem');
        $this->hashAlgorithm = config('agt.hash_algorithm', 'sha256');
    }

    /**
     * Generate hash for fiscal document
     * Format: DocumentType|Serie/SequentialNumber|IssueDate|Total|PreviousHash
     */
    public function generateDocumentHash(FiscalDocument $document): string
    {
        $dataToHash = $this->prepareDataForHash($document);
        
        $hash = hash($this->hashAlgorithm, $dataToHash);
        
        Log::info('AGT: Document hash generated', [
            'document_id' => $document->id,
            'document_number' => $document->document_number,
            'hash' => substr($hash, 0, 16) . '...',
            'data_length' => strlen($dataToHash)
        ]);

        return $hash;
    }

    /**
     * Prepare document data for hash generation
     */
    protected function prepareDataForHash(FiscalDocument $document): string
    {
        // Format according to AGT specifications
        $parts = [
            $document->document_type,
            $document->document_number,
            $document->issue_date->format('Y-m-d'),
            number_format($document->total, 2, '.', ''),
            $document->previous_hash ?? ''
        ];

        return implode('|', $parts);
    }

    /**
     * Generate hash chain - link current document with previous
     */
    public function generateHashChain(FiscalDocument $document): string
    {
        $previousDocument = FiscalDocument::where('document_type', $document->document_type)
            ->where('serie', $document->serie)
            ->where('year', $document->year)
            ->where('id', '<', $document->id)
            ->where('status', 'issued')
            ->orderBy('id', 'desc')
            ->first();

        if ($previousDocument && $previousDocument->agt_hash) {
            return $previousDocument->agt_hash;
        }

        // First document in the chain
        return hash($this->hashAlgorithm, '0');
    }

    /**
     * Sign document data with private key
     */
    public function signDocument(string $data): ?string
    {
        if (!file_exists($this->privateKeyPath)) {
            Log::warning('AGT: Private key not found', [
                'path' => $this->privateKeyPath
            ]);
            return null;
        }

        try {
            $privateKey = openssl_pkey_get_private(
                file_get_contents($this->privateKeyPath),
                config('agt.private_key_password', '')
            );

            if (!$privateKey) {
                throw new Exception('Failed to load private key: ' . openssl_error_string());
            }

            $signature = '';
            $success = openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);

            openssl_free_key($privateKey);

            if (!$success) {
                throw new Exception('Failed to sign data: ' . openssl_error_string());
            }

            $base64Signature = base64_encode($signature);

            Log::info('AGT: Document signed successfully', [
                'signature_length' => strlen($signature),
                'base64_length' => strlen($base64Signature)
            ]);

            return $base64Signature;

        } catch (Exception $e) {
            Log::error('AGT: Signature failed', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Verify document signature
     */
    public function verifySignature(string $data, string $signature): bool
    {
        if (!file_exists($this->publicKeyPath)) {
            Log::warning('AGT: Public key not found', [
                'path' => $this->publicKeyPath
            ]);
            return false;
        }

        try {
            $publicKey = openssl_pkey_get_public(
                file_get_contents($this->publicKeyPath)
            );

            if (!$publicKey) {
                throw new Exception('Failed to load public key: ' . openssl_error_string());
            }

            $binarySignature = base64_decode($signature);
            $result = openssl_verify($data, $binarySignature, $publicKey, OPENSSL_ALGO_SHA256);

            openssl_free_key($publicKey);

            if ($result === 1) {
                Log::info('AGT: Signature verified successfully');
                return true;
            } elseif ($result === 0) {
                Log::warning('AGT: Invalid signature');
                return false;
            } else {
                throw new Exception('Verification error: ' . openssl_error_string());
            }

        } catch (Exception $e) {
            Log::error('AGT: Signature verification failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate ATCUD (Código Único do Documento)
     * Format: ATCUD:[SoftwareCertificate]-[SequentialNumber]
     */
    public function generateATCUD(FiscalDocument $document): string
    {
        $softwareCert = config('agt.software_certificate', '0');
        $sequential = $document->document_number ? 
            str_replace(['/', ' ', '-'], '', $document->document_number) : 
            '0';

        return "ATCUD:{$softwareCert}-{$sequential}";
    }

    /**
     * Validate hash chain integrity
     */
    public function validateHashChain(FiscalDocument $document): bool
    {
        if (!$document->previous_hash) {
            // First document, no previous hash to validate
            return true;
        }

        $previousDocument = FiscalDocument::where('document_type', $document->document_type)
            ->where('serie', $document->serie)
            ->where('year', $document->year)
            ->where('id', '<', $document->id)
            ->where('status', 'issued')
            ->orderBy('id', 'desc')
            ->first();

        if (!$previousDocument) {
            Log::warning('AGT: Previous document not found for hash chain validation', [
                'document_id' => $document->id,
                'expected_previous_hash' => $document->previous_hash
            ]);
            return false;
        }

        $isValid = $previousDocument->agt_hash === $document->previous_hash;

        if (!$isValid) {
            Log::error('AGT: Hash chain integrity broken', [
                'document_id' => $document->id,
                'previous_document_id' => $previousDocument->id,
                'expected_hash' => $document->previous_hash,
                'actual_hash' => $previousDocument->agt_hash
            ]);
        }

        return $isValid;
    }

    /**
     * Get signature service status
     */
    public function getStatus(): array
    {
        return [
            'private_key_exists' => file_exists($this->privateKeyPath),
            'public_key_exists' => file_exists($this->publicKeyPath),
            'hash_algorithm' => $this->hashAlgorithm,
            'openssl_available' => function_exists('openssl_sign'),
            'private_key_path' => $this->privateKeyPath,
            'public_key_path' => $this->publicKeyPath,
        ];
    }
}
