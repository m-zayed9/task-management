<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    /**
     * @param  array{status?: string, priority?: string, title?: string}  $filters
     */
    public function paginateForProject(Project $project, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findForUser(User $user, int $taskId): ?Task;

    public function create(array $data): Task;

    public function update(Task $task, array $data): Task;

    public function delete(Task $task): bool;

    /**
     * @return Collection<int, Task>
     */
    public function getUnnotifiedOverdueTasks(): Collection;
}
