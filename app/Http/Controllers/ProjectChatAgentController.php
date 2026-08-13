<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectChatAgentController extends Controller
{
    public function index()
    {
        $projects = Project::withCount('documents', 'approvedDocuments', 'rejectedDocuments')->get();
        return response()->json($projects);
    }

    public function show(int $id)
    {
        $project = Project::whereId($id)
            ->select('id', 'name', 'description', 'start_at', 'end_at')
            ->with('documents:id,documentable_type,documentable_id,title,extension,original_name,status',)
            ->first();
        return response()->json($project);
    }
}
