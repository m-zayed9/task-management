<?php

namespace App\Services;

use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
    ) {}

    /**
     * @param  array{status?: string, priority?: string, title?: string}  $filters
     */
    public function listForProject(Project $project, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->tasks->paginateForProject($project, $filters, $perPage);
    }

    public function findForUser(User $user, int $taskId): Task
    {
        $task = $this->tasks->findForUser($user, $taskId);

        abort_if($task === null, 404, 'Task not found.');

        return $task;
    }

    /**
     * @param  array{title: string, description?: string|null, priority?: string, status?: string, due_date?: string|null, attachments?: array<int, UploadedFile>|null}  $data
     */
    public function create(Project $project, array $data): Task
    {
        return DB::transaction(function () use ($project, $data) {
            $attachments = $data['attachments'] ?? [];
            unset($data['attachments']);

            $task = $this->tasks->create([
                ...$data,
                'project_id' => $project->id,
            ]);

            $this->attachMedia($task, $attachments);

            $task = $task->fresh();

            event(new TaskCreated($task));

            return $task;
        });
    }

    /**
     * @param  array{title?: string, description?: string|null, priority?: string, status?: string, due_date?: string|null, attachments?: array<int, UploadedFile>|null}  $data
     */
    public function update(Task $task, array $data): Task
    {
        return DB::transaction(function () use ($task, $data) {
            $attachments = $data['attachments'] ?? null;
            unset($data['attachments']);

            if ($data !== []) {
                $task = $this->tasks->update($task, $data);
            }

            if (is_array($attachments)) {
                $this->attachMedia($task, $attachments);
            }

            $task = $task->fresh();

            event(new TaskUpdated($task));

            return $task;
        });
    }

    public function delete(Task $task): void
    {
        $taskId = $task->id;
        $projectId = $task->project_id;

        $this->tasks->delete($task);

        event(new TaskDeleted($taskId, $projectId));
    }

    /**
     * @param  array<int, UploadedFile>  $attachments
     */
    private function attachMedia(Task $task, array $attachments): void
    {
        foreach ($attachments as $attachment) {
            if ($attachment instanceof UploadedFile) {
                $task->addMedia($attachment)->toMediaCollection('attachments');
            }
        }
    }
}
