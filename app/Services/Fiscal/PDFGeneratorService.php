<?php

namespace App\Services\Fiscal;

use App\Models\FiscalDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PDFGeneratorService
{
    protected $qrCodeGenerator;

    public function __construct(QRCodeGeneratorService $qrCodeGenerator)
    {
        $this->qrCodeGenerator = $qrCodeGenerator;
    }

    /**
     * Generate PDF for fiscal document
     */
    public function generate(FiscalDocument $document, bool $download = false)
    {
        $document->load(["items", "order", "user"]);

        // Generate QR Code if AGT hash exists
        $qrCodeBase64 = null;
        if ($document->agt_hash || $document->document_number) {
            $qrCodeBase64 = $this->qrCodeGenerator->generateForDocument($document);
        }

        // Select template based on document type
        $template = $this->getTemplateForDocumentType($document->document_type);

        // Generate PDF
        $pdf = Pdf::loadView($template, [
            "document" => $document,
            "qrCode" => $qrCodeBase64,
        ])
            ->setPaper("a4", "portrait")
            ->setOption("isHtml5ParserEnabled", true)
            ->setOption("isRemoteEnabled", true);

        // Save to storage
        $filename = $this->generateFilename($document);
        $path = "fiscal_documents/" . $document->year . "/" . $document->document_type;
        $fullPath = $path . "/" . $filename;

        // Ensure directory exists
        Storage::disk("public")->makeDirectory($path);

        // Save PDF
        Storage::disk("public")->put($fullPath, $pdf->output());

        // Return PDF or download
        if ($download) {
            return $pdf->download($filename);
        }

        return [
            "pdf" => $pdf,
            "path" => $fullPath,
            "url" => Storage::disk("public")->url($fullPath),
        ];
    }

    /**
     * Stream PDF (inline view)
     */
    public function stream(FiscalDocument $document)
    {
        $document->load(["items", "order", "user"]);

        $qrCodeBase64 = null;
        if ($document->agt_hash || $document->document_number) {
            $qrCodeBase64 = $this->qrCodeGenerator->generateForDocument($document);
        }

        $template = $this->getTemplateForDocumentType($document->document_type);

        $pdf = Pdf::loadView($template, [
            "document" => $document,
            "qrCode" => $qrCodeBase64,
        ])
            ->setPaper("a4", "portrait");

        $filename = $this->generateFilename($document);

        return $pdf->stream($filename);
    }

    /**
     * Download PDF
     */
    public function download(FiscalDocument $document)
    {
        return $this->generate($document, true);
    }

    /**
     * Get template for document type
     */
    protected function getTemplateForDocumentType(string $documentType): string
    {
        $templates = [
            "FR" => "fiscal.pdf.fatura-recibo",
            "FT" => "fiscal.pdf.fatura",
            "FS" => "fiscal.pdf.fatura-simplificada",
            "NC" => "fiscal.pdf.nota-credito",
            "ND" => "fiscal.pdf.nota-debito",
            "RC" => "fiscal.pdf.recibo",
            "FP" => "fiscal.pdf.fatura-proforma",
            "GR" => "fiscal.pdf.guia-remessa",
        ];

        return $templates[$documentType] ?? "fiscal.pdf.base";
    }

    /**
     * Generate filename
     */
    protected function generateFilename(FiscalDocument $document): string
    {
        $documentNumber = str_replace([" ", "/"], ["_", "-"], $document->document_number);
        return $documentNumber . ".pdf";
    }

    /**
     * Generate watermark for cancelled documents
     */
    public function addWatermark($pdf, string $text = "ANULADO")
    {
        // This will be implemented when we add watermark support
        return $pdf;
    }
}
