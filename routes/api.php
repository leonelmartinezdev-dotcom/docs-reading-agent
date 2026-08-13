<?php

use App\DocumentStatus;
use App\Events\DocumentCreated;
use App\Http\Controllers\DocumentChatAgentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProjectChatAgentController;
use App\Http\Controllers\ProjectController;
use App\Jobs\DocumentAnalyzeJob;
use App\Models\Document;
use App\Models\Project;
use App\Services\DocumentAiAnalyzer;
use App\Services\DocumentTextExtractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Shipfastlabs\Parsel;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')
    ->middleware([
        'auth:sanctum',
    ])
    ->name('v1.')
    ->group(function () {
        Route::resource('projects', ProjectController::class)->only(['index', 'show']);
        Route::resource('documents', DocumentController::class)->only(['index', 'show']);
    });


Route::prefix('/chat-agent')
    ->name('chat-agent.')
    ->middleware([
        //'auth:sanctum',
        //'chat-agent'
    ])
    ->group(function () {
        Route::resource('projects', ProjectChatAgentController::class);
        Route::resource('documents', DocumentChatAgentController::class);
    })
;


Route::get('/test', function (Request $request) {


    $document = Document::find(2);

    //DocumentAnalyzeJob::dispatch($document);
    return response()->json($document);


    $documentExtractorService = new DocumentTextExtractor();
    $documentAiAnalyzerService = new DocumentAiAnalyzer();

    $content = $documentExtractorService->extract($document);

    $responseIA = $documentAiAnalyzerService->analyze($document, $content);


    $document->update([
        'approved_at' => $responseIA->approved ? now() : null,
        'analysis_status' => DocumentStatus::Analyzed
    ]);

    $document->analysis()
        ->create([
            'approved' => $responseIA->approved,
            'description' => $responseIA->description,
            'confidence' => $responseIA->confidence,
            'errors' => !empty($responseIA->errors) ? implode(", ", $responseIA->errors) : null,
            'warnings' => !empty($responseIA->warnings) ? implode(", ", $responseIA->warnings) : null
            //'content' => $responseIA->content,
        ]);



    /* $dto = [
        'content' => $content,
        'approved_at' => $responseIA->approved ? now() : null,
        'confidence' => $responseIA->confidence,
        'description' => $responseIA->description,
        'analysis_status' => DocumentStatus::Analyzed
    ]; */

    //$document->update(['content' => $content]);

    return response()->json(['content' => $content, 'response_ia' => $responseIA, /* 'dto' => $dto */]);
});
