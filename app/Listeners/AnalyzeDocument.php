<?php

namespace App\Listeners;

use App\Events\DocumentCreated;
use App\Jobs\DocumentAnalyzeJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AnalyzeDocument
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DocumentCreated $event): void
    {
        DocumentAnalyzeJob::dispatchIf($event->document->requires_analysis, $event->document, $event->user);
    }
}
