<?php

namespace App\Events;

use App\Models\FiscalDocument;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FiscalDocumentAGTFailed
{
    use Dispatchable, SerializesModels;

    public $document;
    public $error;

    public function __construct(FiscalDocument $document, string $error)
    {
        $this->document = $document;
        $this->error = $error;
    }
}
