<?php

namespace App\Services;

use App\DocumentStatus;
use App\Events\DocumentCreated;
use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    /**
     * Create a new class instance.
     */

    public function __construct() {}

    public static function store(array $data, Model $morph)
    {

        $disk = Storage::disk('public');
        $file = $data['document'];

        $originalName = $data['original_name'];
        $size = $disk->size($file);
        $mimeType = $disk->mimeType($file);

        $body = [
            'url' => $file,
            'extension' => $mimeType,
            'size' => $size,
            'original_name' => $originalName,
            'title' => $data['title'],
            'document_type_id' => $data['document_type_id'],
            'requires_analysis' => $data['requires_analysis'],
            'status' => $data['requires_analysis'] ? DocumentStatus::Pending : DocumentStatus::Approved,
            'documentable_type' => $morph::class ?? null,
            'documentable_id' => $morph->id ?? null
        ];

        return Document::create($body);
    }


    public static function edit(Document $document, array $data)
    {

        $hasFileChanged = $document->url !== $data['document'];
        $hasRequiresAnalysisChanged = $document->requires_analysis !== $data['requires_analysis'];
        $hasTypeChanged = $document->document_type_id !== $data['document_type_id'];
        $hasStatusChanged = $document->status !== $data['status'];

        $requireAnlaysis = (($hasFileChanged || $hasTypeChanged || $hasRequiresAnalysisChanged) && $data['requires_analysis']);

        if ($hasFileChanged) {
            $disk = Storage::disk('public');
            $data['url'] = $data['document'];
            $data['size'] = $disk->size($data['document']);
            $data['extension'] = $disk->mimeType($data['document']);
        }

        if ($hasStatusChanged) {
            $data['status_changed_by'] = auth()->user()->id;
            $data['status_changed_at'] = now();
        }

        if ($requireAnlaysis) {
            $data['status'] = DocumentStatus::Pending;
            $data['status_changed_by'] = null;
            $data['status_changed_at'] = null;
        }


        $document->update($data);

        if ($requireAnlaysis) DocumentCreated::dispatch($document, auth()->user());

        return $document->refresh();
    }

    public static function forceAnalysis(Document $document)
    {
        $document->update([
            'status' => DocumentStatus::Pending,
            'status_changed_by' => null,
            'status_changed_at' => null,
        ]);

        DocumentCreated::dispatch($document, auth()->user());

        return true;
    }
}
