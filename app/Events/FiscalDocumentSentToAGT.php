<?php

namespace App\Events;

use App\Models\FiscalDocument;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FiscalDocumentSentToAGT
{
    use Dispatchable, SerializesModels;

    public $document;
    public $agtResponse;

    public function __construct(FiscalDocument $document, $agtResponse)
    {
        $this->document = $document;
        $this->agtResponse = $agtResponse;
    }
}
