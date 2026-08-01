<?php

namespace App\Http\Controllers\Api;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\ListTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService,
    ) {}

    public function index(ListTaskRequest $request, Project $project): JsonResponse
    {
        $this->authorize('viewAny', [Task::class, $project]);

        $filters = $request->only(['status', 'priority', 'title']);
        $tasks = $this->taskService->listForProject(
            $project,
            $filters,
            (int) $request->integer('per_page', 15)
        );

        return ApiResponse::success(
            TaskResource::collection($tasks),
            'Tasks retrieved successfully.'
        );
    }

    public function store(StoreTaskRequest $request, Project $project): JsonResponse
    {
        $this->authorize('create', [Task::class, $project]);

        $task = $this->taskService->create($project, $request->validated());

        return ApiResponse::created(
            new TaskResource($task),
            'Task created successfully.'
        );
    }

    public function show(Request $request, Task $task): JsonResponse
    {
        $task->loadMissing('project');
        $this->authorize('view', $task);

        return ApiResponse::success(
            new TaskResource($task),
            'Task retrieved successfully.'
        );
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->loadMissing('project');
        $this->authorize('update', $task);

        $task = $this->taskService->update($task, $request->validated());

        return ApiResponse::success(
            new TaskResource($task),
            'Task updated successfully.'
        );
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->loadMissing('project');
        $this->authorize('delete', $task);

        $this->taskService->delete($task);

        return ApiResponse::noContent('Task deleted successfully.');
    }
}
