<?php

namespace App\Services\Fiscal;

use App\Models\FiscalSequence;
use Exception;

class SequenceGeneratorService
{
    /**
     * Get next sequential number for a document type
     * Thread-safe implementation using database locking
     */
    public function getNextNumber(string $documentType, string $serie = "A", ?int $year = null): int
    {
        $year = $year ?? date("Y");

        // Validar tipo de documento
        $this->validateDocumentType($documentType);

        return FiscalSequence::getNextNumber($documentType, $serie, $year);
    }

    /**
     * Format complete document number
     */
    public function formatDocumentNumber(string $documentType, string $serie, int $year, int $number): string
    {
        return FiscalSequence::formatDocumentNumber($documentType, $serie, $year, $number);
    }

    /**
     * Get current number without incrementing
     */
    public function getCurrentNumber(string $documentType, string $serie = "A", ?int $year = null): int
    {
        $year = $year ?? date("Y");
        return FiscalSequence::getCurrentNumber($documentType, $serie, $year);
    }

    /**
     * Reset sequence for new year
     */
    public function resetForNewYear(string $documentType, string $serie = "A"): FiscalSequence
    {
        $this->validateDocumentType($documentType);
        return FiscalSequence::resetForNewYear($documentType, $serie);
    }

    /**
     * Initialize all sequences for a new year
     */
    public function initializeAllSequencesForYear(int $year, string $serie = "A"): array
    {
        $documentTypes = ["FT", "FR", "FS", "NC", "ND", "RC", "FP", "GR"];
        $created = [];

        foreach ($documentTypes as $type) {
            $sequence = FiscalSequence::firstOrCreate(
                [
                    "document_type" => $type,
                    "serie" => $serie,
                    "year" => $year,
                ],
                [
                    "current_number" => 0,
                ]
            );

            $created[$type] = $sequence;
        }

        return $created;
    }

    /**
     * Check for gaps in sequence
     */
    public function hasGaps(string $documentType, string $serie = "A", ?int $year = null): bool
    {
        $year = $year ?? date("Y");
        return FiscalSequence::hasGaps($documentType, $serie, $year);
    }

    /**
     * Get summary of all sequences
     */
    public function getSummary(): array
    {
        return FiscalSequence::getSummary()->toArray();
    }

    /**
     * Get sequences for specific year
     */
    public function getSequencesForYear(int $year): array
    {
        return FiscalSequence::where("year", $year)
            ->orderBy("document_type")
            ->orderBy("serie")
            ->get()
            ->map(function ($sequence) {
                return [
                    "document_type" => $sequence->document_type,
                    "serie" => $sequence->serie,
                    "year" => $sequence->year,
                    "current_number" => $sequence->current_number,
                    "last_document" => $this->formatDocumentNumber(
                        $sequence->document_type,
                        $sequence->serie,
                        $sequence->year,
                        $sequence->current_number
                    ),
                ];
            })
            ->toArray();
    }

    /**
     * Validate document type
     */
    protected function validateDocumentType(string $documentType): void
    {
        $validTypes = ["FT", "FR", "FS", "NC", "ND", "RC", "FP", "GR"];

        if (!in_array($documentType, $validTypes)) {
            throw new Exception("Tipo de documento inválido: {$documentType}. Tipos válidos: " . implode(", ", $validTypes));
        }
    }

    /**
     * Get document type name
     */
    public function getDocumentTypeName(string $documentType): string
    {
        $types = [
            "FT" => "Fatura",
            "FR" => "Fatura Recibo",
            "FS" => "Fatura Simplificada",
            "NC" => "Nota de Crédito",
            "ND" => "Nota de Débito",
            "RC" => "Recibo",
            "FP" => "Fatura Proforma",
            "GR" => "Guia de Remessa",
        ];

        return $types[$documentType] ?? $documentType;
    }

    /**
     * Verify sequence integrity
     */
    public function verifyIntegrity(string $documentType, string $serie = "A", ?int $year = null): array
    {
        $year = $year ?? date("Y");

        $sequence = FiscalSequence::where("document_type", $documentType)
            ->where("serie", $serie)
            ->where("year", $year)
            ->first();

        if (!$sequence) {
            return [
                "exists" => false,
                "has_gaps" => false,
                "current_number" => 0,
                "document_count" => 0,
                "message" => "Sequência não inicializada",
            ];
        }

        $documentCount = \App\Models\FiscalDocument::where("document_type", $documentType)
            ->where("serie", $serie)
            ->where("year", $year)
            ->count();

        $hasGaps = $this->hasGaps($documentType, $serie, $year);

        return [
            "exists" => true,
            "has_gaps" => $hasGaps,
            "current_number" => $sequence->current_number,
            "document_count" => $documentCount,
            "expected_count" => $sequence->current_number,
            "message" => $hasGaps ? "⚠️ Foram detectadas lacunas na sequência" : "✅ Sequência íntegra",
        ];
    }
}
