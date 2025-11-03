<?php

namespace App\Jobs;

use App\Models\FiscalDocument;
use App\Services\AGT\AGTIntegrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class SendFiscalDocumentToAGT implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $document;
    public $tries = 3; // Number of retry attempts
    public $backoff = [60, 300, 900]; // Retry delays in seconds (1min, 5min, 15min)
    public $timeout = 120; // Job timeout in seconds

    /**
     * Create a new job instance
     */
    public function __construct(FiscalDocument $document)
    {
        $this->document = $document;
        $this->onQueue('agt'); // Use dedicated queue for AGT jobs
    }

    /**
     * Execute the job
     */
    public function handle(AGTIntegrationService $agtService)
    {
        try {
            Log::info('AGT Job: Starting document submission', [
                'document_id' => $this->document->id,
                'document_number' => $this->document->document_number,
                'attempt' => $this->attempts()
            ]);

            // Submit document to AGT
            $result = $agtService->submitDocument($this->document);

            if ($result['success']) {
                Log::info('AGT Job: Document submitted successfully', [
                    'document_id' => $this->document->id,
                    'document_number' => $this->document->document_number,
                    'attempts' => $this->attempts()
                ]);

                // Fire event for successful submission
                event(new \App\Events\FiscalDocumentSentToAGT($this->document, $result['data']));

            } else {
                throw new Exception($result['error'] ?? 'Submission failed');
            }

        } catch (Exception $e) {
            Log::error('AGT Job: Submission failed', [
                'document_id' => $this->document->id,
                'document_number' => $this->document->document_number,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage()
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure
     */
    public function failed(Exception $exception)
    {
        Log::error('AGT Job: All retry attempts exhausted', [
            'document_id' => $this->document->id,
            'document_number' => $this->document->document_number,
            'error' => $exception->getMessage(),
            'total_attempts' => $this->tries
        ]);

        // Fire event for failed submission
        event(new \App\Events\FiscalDocumentAGTFailed($this->document, $exception->getMessage()));

        // Optionally, you could mark the document with a flag
        // $this->document->update(['agt_submission_failed' => true]);
    }

    /**
     * Get the tags that should be assigned to the job
     */
    public function tags()
    {
        return [
            'agt',
            'fiscal-document:' . $this->document->id,
            'type:' . $this->document->document_type
        ];
    }
}
