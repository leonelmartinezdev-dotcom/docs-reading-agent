<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectListResource;
use App\Http\Resources\ProjectShowResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class ProjectController extends Controller
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

        $baseQuery = QueryBuilder::for(Project::class)
            ->allowedFields('id', 'name', 'description', 'start_at', 'end_at')
            ->allowedFilters('id', 'name', 'description', 'start_at', 'end_at')
            ->allowedSorts('id', 'name', 'description', 'start_at', 'end_at')
            ->allowedIncludes('documents')
            ->jsonPaginate()
            ->appends(request()->query());

        return ProjectListResource::collection($baseQuery);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return new ProjectShowResource($project);
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
