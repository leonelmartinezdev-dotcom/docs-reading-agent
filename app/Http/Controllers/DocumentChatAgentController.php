<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentChatAgentController extends Controller
{
    public function index()
    {
        $documents = Document::select(
            'id',
            'url',
            'title',
            'extension',
            'original_name',
            'requires_analysis',
            'status',
            'document_type_id',
        )
            ->withCount('analysis')->get();
        return response()->json($documents);
    }


    public function show(int $id)
    {
        $document = Document::whereId($id)
            ->select(
                'id',
                'url',
                'title',
                'extension',
                'original_name',
                'requires_analysis',
                'status',
                'document_type_id',
                'status_changed_by',
                'status_changed_at',
            )
            ->with([
                'type:id,label',
                'statusChangedBy:id,name',
                'latestAnalysis',
            ])
            ->first();
        return response()->json($document);
    }
}
