<?php

namespace App\Listeners;

use App\Events\FiscalDocumentIssued;
use App\Jobs\SendFiscalDocumentToAGT;
use Illuminate\Support\Facades\Log;

class SyncDocumentWithAGT
{
    /**
     * Handle the event
     */
    public function handle(FiscalDocumentIssued $event)
    {
        $document = $event->document;

        Log::info('Listener: Queueing document for AGT submission', [
            'document_id' => $document->id,
            'document_number' => $document->document_number
        ]);

        // Dispatch job to send document to AGT
        // Job will be processed asynchronously with retry logic
        SendFiscalDocumentToAGT::dispatch($document);
    }
}
