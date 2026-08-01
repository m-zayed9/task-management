<?php

namespace App\Events;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Task $task)
    {
        $this->task->loadMissing('project');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('projects.'.$this->task->project_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'task.created';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'project_id' => $this->task->project_id,
            'task' => (new TaskResource($this->task))->resolve(),
        ];
    }
}
