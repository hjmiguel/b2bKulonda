<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FiscalSequenceSeeder extends Seeder
{
    /**
     * Seed fiscal sequences for all document types
     */
    public function run()
    {
        $currentYear = date('Y');
        $documentTypes = ['FR', 'FT', 'FS', 'NC', 'ND', 'RC', 'FP', 'GR'];
        $series = ['A', 'B']; // Série A (principal) e B (backup)

        foreach ($documentTypes as $type) {
            foreach ($series as $serie) {
                DB::table('fiscal_sequences')->updateOrInsert(
                    [
                        'document_type' => $type,
                        'serie' => $serie,
                        'year' => $currentYear,
                    ],
                    [
                        'current_number' => 0,
                        'last_used_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        echo "✓ Sequências fiscais inicializadas com sucesso!\n";
        echo "Total: " . (count($documentTypes) * count($series)) . " sequências criadas.\n";
    }
}
