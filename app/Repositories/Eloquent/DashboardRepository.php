<?php

namespace App\Repositories\Eloquent;

use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\DashboardRepositoryInterface;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getStatsForUser(User $user): array
    {
        $projectIds = Project::query()
            ->where('user_id', $user->id)
            ->pluck('id');

        $tasksQuery = Task::query()->whereIn('project_id', $projectIds);

        return [
            'total_projects' => $projectIds->count(),
            'active_projects' => Project::query()
                ->where('user_id', $user->id)
                ->where('status', ProjectStatus::Active)
                ->count(),
            'total_tasks' => (clone $tasksQuery)->count(),
            'completed_tasks' => (clone $tasksQuery)->where('status', TaskStatus::Done)->count(),
            'pending_tasks' => (clone $tasksQuery)
                ->whereIn('status', [TaskStatus::Todo, TaskStatus::InProgress])
                ->count(),
            'overdue_tasks' => (clone $tasksQuery)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', now()->toDateString())
                ->where('status', '!=', TaskStatus::Done)
                ->count(),
        ];
    }
}
