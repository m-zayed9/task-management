<?php

namespace App\Repositories\Eloquent;

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function paginateForProject(Project $project, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Task::query()
            ->where('project_id', $project->id)
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['priority'] ?? null, fn ($query, $priority) => $query->where('priority', $priority))
            ->when($filters['title'] ?? null, fn ($query, $title) => $query->where('title', 'like', '%'.$title.'%'))
            ->latest()
            ->paginate($perPage);
    }

    public function findForUser(User $user, int $taskId): ?Task
    {
        return Task::query()
            ->whereKey($taskId)
            ->whereHas('project', fn ($query) => $query->where('user_id', $user->id))
            ->with('project')
            ->first();
    }

    public function create(array $data): Task
    {
        return Task::query()->create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->refresh();
    }

    public function delete(Task $task): bool
    {
        return (bool) $task->delete();
    }

    public function getUnnotifiedOverdueTasks(): Collection
    {
        return Task::query()
            ->with(['project.user'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->where('status', '!=', TaskStatus::Done->value)
            ->whereNull('overdue_notified_at')
            ->get();
    }
}
