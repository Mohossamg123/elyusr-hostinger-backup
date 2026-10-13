<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Services\ActivityLogService;

class ProjectController extends Controller
{
    public function index()
    {
        return ProjectResource::collection(Project::latest()->paginate(10));
    }

    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->validated());

        // Activity Log
        app(ActivityLogService::class)->created(
            $project,
            'إنشاء مشروع جديد: ' . ($project->name ?? 'Project'),
            [
                'attributes' => $request->validated(),
            ]
        );

        return new ProjectResource($project);
    }

    public function show(Project $project)
    {
        return new ProjectResource($project);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $oldData = $project->toArray();

        $project->update($request->validated());

        // Activity Log
        app(ActivityLogService::class)->updated(
            $project,
            'تعديل المشروع: ' . ($project->name ?? 'Project'),
            [
                'old' => $oldData,
                'new' => $project->fresh()->toArray(),
            ]
        );

        return new ProjectResource($project);
    }

    public function destroy(Project $project)
    {
        $projectName = $project->name ?? 'Project';
        $projectId = $project->id;

        // Activity Log
        app(ActivityLogService::class)->deleted(
            $project,
            'حذف المشروع: ' . $projectName,
            [
                'deleted_project_id' => $projectId,
                'deleted_project_name' => $projectName,
            ]
        );

        $project->delete();

        return response()->json([
            'message'=>'Project deleted successfully'
        ]);
    }
}