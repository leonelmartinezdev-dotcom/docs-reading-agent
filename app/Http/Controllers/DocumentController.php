<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocumentListResource;
use App\Http\Resources\DocumentShowResource;
use App\Models\Document;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('all')) {
            config(['json-api-paginate.default_size' => 10000]);
            config(['json-api-paginate.max_results' => 10000]);
        }

        $baseQuery = QueryBuilder::for(Document::class)
            ->allowedFields(
                'id',
                'url',
                'title',
                'extension',
                'size',
                'original_name',
                'requires_analysis',
                'status',
                'status_changed_by',
                'status_changed_at',
                'description',
                'document_type_id',
                'documentable_type',
                'documentable_id',
            )
            ->allowedFilters(
                AllowedFilter::exact('id'),
                'url',
                'title',
                'extension',
                'size',
                'original_name',
                'requires_analysis',
                'status'
            )
            ->allowedSorts(
                'url',
                'title',
                'extension',
                'size',
                'original_name',
                'requires_analysis',
                'status'
            )
            ->allowedIncludes('analysis', 'latestAnalysis', 'type', 'statusChangedBy', 'owner')
            ->jsonPaginate()
            ->appends(request()->query());

        return DocumentListResource::collection($baseQuery);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        return new DocumentShowResource($document);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
