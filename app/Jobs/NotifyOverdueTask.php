<?php

namespace App\Jobs;

use App\Models\Task;
use App\Notifications\TaskOverdueNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyOverdueTask implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $taskId) {}

    public function handle(): void
    {
        $task = Task::query()
            ->with(['project.user'])
            ->whereKey($this->taskId)
            ->whereNull('overdue_notified_at')
            ->first();

        if ($task === null || ! $task->isOverdue() || $task->project?->user === null) {
            return;
        }

        $task->project->user->notify(new TaskOverdueNotification($task));

        $task->forceFill([
            'overdue_notified_at' => now(),
        ])->save();
    }
}
