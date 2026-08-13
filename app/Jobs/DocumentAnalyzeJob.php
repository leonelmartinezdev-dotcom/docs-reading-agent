<?php

namespace App\Jobs;

use App\DocumentStatus;
use App\Models\Document;
use App\Models\User;
use App\Services\DocumentAiAnalyzer;
use App\Services\DocumentTextExtractor;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DocumentAnalyzeJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    protected Document $document;
    protected User $user;

    public function __construct(Document $document, User $user)
    {
        $this->document = $document;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $documentExtractorService = new DocumentTextExtractor();
            $documentAiAnalyzerService = new DocumentAiAnalyzer();

            $content = $documentExtractorService->extract($this->document);

            $responseIA = $documentAiAnalyzerService->analyze($this->document, $content);


            $this->document->update([
                'status' => $responseIA->approved ? DocumentStatus::Approved : DocumentStatus::Rejected,
                'status_changed_at' => now(),
                'status_changed_by' => null
            ]);

            $this->document->analysis()
                ->create([
                    'approved' => $responseIA->approved,
                    'description' => $responseIA->description,
                    'confidence' => $responseIA->confidence,
                    'errors' => !empty($responseIA->errors) ? implode(", ", $responseIA->errors) : null,
                    'warnings' => !empty($responseIA->warnings) ? implode(", ", $responseIA->warnings) : null
                ]);


            Notification::make()
                ->success()
                ->title('Analisis completado para el documento ' . ($this->document->title ?? $this->document->original_name))
                ->body('Resultado: ' . ($responseIA->approved ? 'Aprobado' : 'Rechazado') . ' ')
                ->sendToDatabase($this->user);
        } catch (\Throwable $th) {
            Notification::make()
                ->danger()
                ->title('Error al analizar el documento ' . ($this->document->title ?? $this->document->original_name))
                ->body('Error: ' . $th->getMessage())
                ->sendToDatabase($this->user);
        }
    }
}
