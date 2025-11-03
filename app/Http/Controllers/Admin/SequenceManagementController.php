<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FiscalSequence;
use App\Models\FiscalDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SequenceManagementController extends Controller
{
    /**
     * Display all sequences
     */
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $type = $request->get('type');

        $query = FiscalSequence::where('year', $year);

        if ($type) {
            $query->where('document_type', $type);
        }

        $sequences = $query->orderBy('document_type')
            ->orderBy('serie')
            ->get();

        // Get actual document counts for verification
        foreach ($sequences as $sequence) {
            $sequence->actual_count = FiscalDocument::where('document_type', $sequence->document_type)
                ->where('serie', $sequence->serie)
                ->where('year', $sequence->year)
                ->count();
        }

        $documentTypes = ['FR', 'FT', 'FS', 'NC', 'ND', 'RC', 'FP', 'GR'];
        
        return view('admin.sequences.index', compact('sequences', 'year', 'type', 'documentTypes'));
    }

    /**
     * Show form to create new sequence
     */
    public function create()
    {
        $documentTypes = ['FR', 'FT', 'FS', 'NC', 'ND', 'RC', 'FP', 'GR'];
        $years = range(date('Y') - 1, date('Y') + 1);
        
        return view('admin.sequences.create', compact('documentTypes', 'years'));
    }

    /**
     * Store a new sequence
     */
    public function store(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string|in:FR,FT,FS,NC,ND,RC,FP,GR',
            'serie' => 'required|string|max:5',
            'year' => 'required|integer|min:2020|max:2100',
            'starting_number' => 'required|integer|min:0',
        ]);

        // Check if sequence already exists
        $exists = FiscalSequence::where('document_type', $request->document_type)
            ->where('serie', $request->serie)
            ->where('year', $request->year)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'sequence' => 'Sequência já existe para este tipo, série e ano.'
            ])->withInput();
        }

        try {
            FiscalSequence::create([
                'document_type' => $request->document_type,
                'serie' => strtoupper($request->serie),
                'year' => $request->year,
                'current_number' => $request->starting_number,
                'last_used_at' => null,
            ]);

            Log::info('Admin: New sequence created', [
                'type' => $request->document_type,
                'serie' => $request->serie,
                'year' => $request->year,
            ]);

            return redirect()->route('admin.sequences.index', ['year' => $request->year])
                ->with('success', 'Sequência criada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Admin: Failed to create sequence', [
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'error' => 'Erro ao criar sequência: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Show sequence details
     */
    public function show(FiscalSequence $sequence)
    {
        // Get recent documents using this sequence
        $recentDocuments = FiscalDocument::where('document_type', $sequence->document_type)
            ->where('serie', $sequence->serie)
            ->where('year', $sequence->year)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Get statistics
        $stats = [
            'total_documents' => FiscalDocument::where('document_type', $sequence->document_type)
                ->where('serie', $sequence->serie)
                ->where('year', $sequence->year)
                ->count(),
            'issued' => FiscalDocument::where('document_type', $sequence->document_type)
                ->where('serie', $sequence->serie)
                ->where('year', $sequence->year)
                ->where('status', 'issued')
                ->count(),
            'cancelled' => FiscalDocument::where('document_type', $sequence->document_type)
                ->where('serie', $sequence->serie)
                ->where('year', $sequence->year)
                ->where('status', 'cancelled')
                ->count(),
        ];

        return view('admin.sequences.show', compact('sequence', 'recentDocuments', 'stats'));
    }

    /**
     * Reset sequence number (with caution)
     */
    public function reset(Request $request, FiscalSequence $sequence)
    {
        $request->validate([
            'new_number' => 'required|integer|min:0',
            'confirmation' => 'required|string|in:RESET',
        ]);

        $oldNumber = $sequence->current_number;

        try {
            DB::beginTransaction();

            $sequence->current_number = $request->new_number;
            $sequence->save();

            Log::warning('Admin: Sequence number reset', [
                'sequence_id' => $sequence->id,
                'type' => $sequence->document_type,
                'serie' => $sequence->serie,
                'year' => $sequence->year,
                'old_number' => $oldNumber,
                'new_number' => $request->new_number,
                'admin_user' => auth()->user()->id ?? 'unknown',
            ]);

            DB::commit();

            return redirect()->route('admin.sequences.show', $sequence)
                ->with('success', "Sequência reiniciada de {$oldNumber} para {$request->new_number}");

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Admin: Failed to reset sequence', [
                'sequence_id' => $sequence->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'error' => 'Erro ao reiniciar sequência: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Initialize sequences for a new year
     */
    public function initializeYear(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2100',
        ]);

        $year = $request->year;
        $documentTypes = ['FR', 'FT', 'FS', 'NC', 'ND', 'RC', 'FP', 'GR'];
        $series = ['A', 'B'];

        $created = 0;
        $skipped = 0;

        try {
            DB::beginTransaction();

            foreach ($documentTypes as $type) {
                foreach ($series as $serie) {
                    $exists = FiscalSequence::where('document_type', $type)
                        ->where('serie', $serie)
                        ->where('year', $year)
                        ->exists();

                    if (!$exists) {
                        FiscalSequence::create([
                            'document_type' => $type,
                            'serie' => $serie,
                            'year' => $year,
                            'current_number' => 0,
                        ]);
                        $created++;
                    } else {
                        $skipped++;
                    }
                }
            }

            DB::commit();

            Log::info('Admin: Year sequences initialized', [
                'year' => $year,
                'created' => $created,
                'skipped' => $skipped,
            ]);

            return redirect()->route('admin.sequences.index', ['year' => $year])
                ->with('success', "Ano {$year} inicializado: {$created} sequências criadas, {$skipped} já existiam.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Admin: Failed to initialize year sequences', [
                'year' => $year,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'error' => 'Erro ao inicializar ano: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Verify sequence integrity
     */
    public function verify(FiscalSequence $sequence)
    {
        $documents = FiscalDocument::where('document_type', $sequence->document_type)
            ->where('serie', $sequence->serie)
            ->where('year', $sequence->year)
            ->orderBy('sequential_number')
            ->get();

        $issues = [];
        $lastNumber = 0;

        foreach ($documents as $doc) {
            // Check for gaps
            if ($doc->sequential_number != $lastNumber + 1 && $lastNumber != 0) {
                $issues[] = [
                    'type' => 'gap',
                    'message' => "Falha na sequência: {$lastNumber} -> {$doc->sequential_number}",
                    'document_id' => $doc->id,
                ];
            }

            // Check hash chain
            if ($doc->previous_hash && $lastDoc = $documents->where('sequential_number', $lastNumber)->first()) {
                if ($doc->previous_hash !== $lastDoc->agt_hash) {
                    $issues[] = [
                        'type' => 'hash_chain',
                        'message' => "Cadeia de hash quebrada no documento {$doc->document_number}",
                        'document_id' => $doc->id,
                    ];
                }
            }

            $lastNumber = $doc->sequential_number;
        }

        return view('admin.sequences.verify', compact('sequence', 'documents', 'issues'));
    }
}
