<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FiscalDocument;
use App\Jobs\SendFiscalDocumentToAGT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class AGTLogsController extends Controller
{
    /**
     * Display AGT integration logs
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all'); // all, submitted, pending, failed
        $startDate = $request->get('start_date', Carbon::now()->subDays(7)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $query = FiscalDocument::with(['order'])
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', 'issued');

        switch ($status) {
            case 'submitted':
                $query->whereNotNull('agt_hash');
                break;
            case 'pending':
                $query->whereNull('agt_hash');
                break;
            case 'failed':
                // Documents older than 2 hours without hash are likely failed
                $query->whereNull('agt_hash')
                    ->where('created_at', '<', Carbon::now()->subHours(2));
                break;
        }

        $documents = $query->orderBy('issue_date', 'desc')->paginate(50);

        // Get statistics
        $stats = $this->getAGTStats($startDate, $endDate);

        return view('admin.agt.logs', compact('documents', 'stats', 'status', 'startDate', 'endDate'));
    }

    /**
     * Show detailed AGT log for a document
     */
    public function show(FiscalDocument $document)
    {
        // Get system logs related to this document
        $logFile = storage_path('logs/laravel.log');
        $logs = [];

        if (file_exists($logFile)) {
            $content = file_get_contents($logFile);
            $pattern = "/\\[.*?\\] .*?AGT.*?document_id.*?{$document->id}.*?$/m";
            
            if (preg_match_all($pattern, $content, $matches)) {
                $logs = array_slice($matches[0], -50); // Last 50 log entries
            }
        }

        // Get related job information from failed_jobs table
        $failedJobs = DB::table('failed_jobs')
            ->where('payload', 'like', "%{$document->id}%")
            ->orderBy('failed_at', 'desc')
            ->get();

        return view('admin.agt.show', compact('document', 'logs', 'failedJobs'));
    }

    /**
     * Retry AGT submission for a document
     */
    public function retry(FiscalDocument $document)
    {
        try {
            if ($document->status !== 'issued') {
                return back()->withErrors([
                    'error' => 'Apenas documentos emitidos podem ser submetidos ao AGT.'
                ]);
            }

            // Clear previous hash to allow resubmission
            $document->agt_hash = null;
            $document->agt_signature = null;
            $document->agt_qrcode = null;
            $document->save();

            // Dispatch job
            SendFiscalDocumentToAGT::dispatch($document);

            Log::info('Admin: Manual AGT retry initiated', [
                'document_id' => $document->id,
                'document_number' => $document->document_number,
            ]);

            return back()->with('success', 'Reenvio para AGT iniciado. Verifique os logs em alguns minutos.');

        } catch (\Exception $e) {
            Log::error('Admin: Failed to retry AGT submission', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'error' => 'Erro ao reenviar: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Bulk retry for multiple documents
     */
    public function bulkRetry(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:fiscal_documents,id',
        ]);

        $documents = FiscalDocument::whereIn('id', $request->document_ids)
            ->where('status', 'issued')
            ->get();

        $retried = 0;
        $failed = 0;

        foreach ($documents as $document) {
            try {
                $document->agt_hash = null;
                $document->agt_signature = null;
                $document->agt_qrcode = null;
                $document->save();

                SendFiscalDocumentToAGT::dispatch($document);
                $retried++;

            } catch (\Exception $e) {
                $failed++;
                Log::error('Admin: Bulk retry failed for document', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Admin: Bulk AGT retry completed', [
            'total' => count($request->document_ids),
            'retried' => $retried,
            'failed' => $failed,
        ]);

        return back()->with('success', "Reenvio iniciado: {$retried} documentos na fila, {$failed} falharam.");
    }

    /**
     * Show AGT connection status
     */
    public function status()
    {
        $config = [
            'api_url' => config('agt.api_url'),
            'use_sandbox' => config('agt.use_sandbox'),
            'certificate_path' => config('agt.certificate_path'),
            'certificate_exists' => file_exists(config('agt.certificate_path')),
            'private_key_path' => config('agt.private_key_path'),
            'private_key_exists' => file_exists(config('agt.private_key_path'))',
        ];

        // Test connection
        $connectionTest = $this->testAGTConnection();

        // Get queue status
        $queueStats = $this->getQueueStats();

        return view('admin.agt.status', compact('config', 'connectionTest', 'queueStats'));
    }

    /**
     * Test AGT connection
     */
    protected function testAGTConnection(): array
    {
        try {
            $agtClient = app(\App\Services\AGT\AGTApiClient::class);
            $response = $agtClient->ping();

            return [
                'success' => $response['success'],
                'message' => $response['success'] ? 'Conexão com AGT estabelecida com sucesso' : 'Falha na conexão com AGT',
                'response' => $response,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao testar conexão: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get queue statistics
     */
    protected function getQueueStats(): array
    {
        return [
            'failed_jobs' => DB::table('failed_jobs')
                ->where('queue', 'agt')
                ->count(),
            'recent_failures' => DB::table('failed_jobs')
                ->where('queue', 'agt')
                ->where('failed_at', '>', Carbon::now()->subDay())
                ->count(),
        ];
    }

    /**
     * Get AGT statistics for a date range
     */
    protected function getAGTStats(string $startDate, string $endDate): array
    {
        $total = FiscalDocument::whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', 'issued')
            ->count();

        $submitted = FiscalDocument::whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', 'issued')
            ->whereNotNull('agt_hash')
            ->count();

        $pending = FiscalDocument::whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', 'issued')
            ->whereNull('agt_hash')
            ->where('created_at', '>', Carbon::now()->subHours(2))
            ->count();

        $failed = FiscalDocument::whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', 'issued')
            ->whereNull('agt_hash')
            ->where('created_at', '<', Carbon::now()->subHours(2))
            ->count();

        return [
            'total' => $total,
            'submitted' => $submitted,
            'pending' => $pending,
            'failed' => $failed,
            'success_rate' => $total > 0 ? round(($submitted / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Clear failed jobs
     */
    public function clearFailedJobs(Request $request)
    {
        try {
            $count = DB::table('failed_jobs')
                ->where('queue', 'agt')
                ->delete();

            Log::info('Admin: Failed AGT jobs cleared', [
                'count' => $count
            ]);

            return back()->with('success', "{$count} jobs falhados foram removidos.");

        } catch (\Exception $e) {
            Log::error('Admin: Failed to clear failed jobs', [
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'error' => 'Erro ao limpar jobs: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export AGT logs to CSV
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $documents = FiscalDocument::whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', 'issued')
            ->orderBy('issue_date')
            ->get();

        $filename = "agt-logs-{$startDate}-{$endDate}.csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($documents) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'Número Documento', 'Tipo', 'Data Emissão', 'Total', 
                'Hash AGT', 'ATCUD', 'Status AGT', 'Data Submissão'
            ]);

            // Rows
            foreach ($documents as $doc) {
                fputcsv($file, [
                    $doc->document_number,
                    $doc->document_type,
                    $doc->issue_date->format('Y-m-d H:i:s'),
                    $doc->total,
                    $doc->agt_hash ? substr($doc->agt_hash, 0, 16) . '...' : 'N/A',
                    $doc->agt_atcud ?? 'N/A',
                    $doc->agt_hash ? 'Submetido' : 'Pendente',
                    $doc->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
