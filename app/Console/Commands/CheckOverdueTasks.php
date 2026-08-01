<?php

namespace App\Console\Commands;

use App\Jobs\NotifyOverdueTask;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Console\Command;

class CheckOverdueTasks extends Command
{
    protected $signature = 'tasks:check-overdue';

    protected $description = 'Dispatch notifications for overdue tasks that have not been notified yet';

    public function handle(TaskRepositoryInterface $tasks): int
    {
        $overdueTasks = $tasks->getUnnotifiedOverdueTasks();

        foreach ($overdueTasks as $task) {
            NotifyOverdueTask::dispatch($task->id);
        }

        $this->info('Dispatched '.$overdueTasks->count().' overdue task notification(s).');

        return self::SUCCESS;
    }
}
