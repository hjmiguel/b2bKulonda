<?php

namespace App\Services\Fiscal;

use App\Models\FiscalDocument;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QRCodeGeneratorService
{
    /**
     * Generate QR Code for fiscal document
     * Returns base64 encoded PNG
     */
    public function generateForDocument(FiscalDocument $document): string
    {
        $data = $this->prepareDataForQRCode($document);

        // Generate QR Code as base64
        $qrCode = QrCode::format("png")
            ->size(200)
            ->errorCorrection("H")
            ->generate($data);

        return "data:image/png;base64," . base64_encode($qrCode);
    }

    /**
     * Save QR Code to storage
     */
    public function saveQRCode(FiscalDocument $document): string
    {
        $data = $this->prepareDataForQRCode($document);

        $qrCode = QrCode::format("png")
            ->size(300)
            ->errorCorrection("H")
            ->generate($data);

        $filename = $this->generateFilename($document);
        $path = "fiscal_qrcodes/" . $document->year . "/" . $document->document_type;
        $fullPath = $path . "/" . $filename;

        // Ensure directory exists
        Storage::disk("public")->makeDirectory($path);

        // Save QR Code
        Storage::disk("public")->put($fullPath, $qrCode);

        return $fullPath;
    }

    /**
     * Prepare data for QR Code according to AGT standards
     * Format: Document_Number|NIF|Date|Total|Hash
     */
    protected function prepareDataForQRCode(FiscalDocument $document): string
    {
        // AGT QR Code format (simplified for now)
        // Real implementation should follow AGT specifications
        $data = implode("|", [
            $document->document_number,
            $document->customer_nif ?? "999999999",
            $document->issue_date->format("Y-m-d"),
            number_format($document->total, 2, ".", ""),
            $document->agt_hash ?? $this->generateTemporaryHash($document),
        ]);

        // For production, this should include:
        // - AGT Digital Signature
        // - Previous document hash
        // - Company NIF
        // - Document status

        return $data;
    }

    /**
     * Generate temporary hash for testing (before AGT integration)
     */
    protected function generateTemporaryHash(FiscalDocument $document): string
    {
        $dataToHash = implode("", [
            $document->document_number,
            $document->customer_nif,
            $document->total,
            $document->issue_date->format("YmdHis"),
        ]);

        return strtoupper(substr(hash("sha256", $dataToHash), 0, 32));
    }

    /**
     * Generate filename for QR Code
     */
    protected function generateFilename(FiscalDocument $document): string
    {
        $documentNumber = str_replace([" ", "/"], ["_", "-"], $document->document_number);
        return "QR_" . $documentNumber . ".png";
    }

    /**
     * Verify QR Code data
     */
    public function verifyQRCode(string $data): array
    {
        $parts = explode("|", $data);

        if (count($parts) < 5) {
            return [
                "valid" => false,
                "message" => "QR Code inválido",
            ];
        }

        return [
            "valid" => true,
            "document_number" => $parts[0],
            "customer_nif" => $parts[1],
            "issue_date" => $parts[2],
            "total" => $parts[3],
            "hash" => $parts[4],
        ];
    }

    /**
     * Generate QR Code for AGT validation
     * This will be used when AGT integration is complete
     */
    public function generateAGTQRCode(FiscalDocument $document): string
    {
        // TODO: Implement full AGT QR Code generation
        // Should include:
        // 1. Company NIF
        // 2. Document number
        // 3. Issue date
        // 4. Total amount
        // 5. AGT hash
        // 6. Digital signature
        // 7. Previous document hash

        return $this->generateForDocument($document);
    }
}
