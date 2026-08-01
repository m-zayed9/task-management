<?php

namespace Tests\Feature;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_manage_tasks_and_filter_them(): void
    {
        Event::fake([TaskCreated::class, TaskUpdated::class, TaskDeleted::class]);

        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();
        Sanctum::actingAs($user);

        Task::factory()->create([
            'project_id' => $project->id,
            'title' => 'Write docs',
            'status' => TaskStatus::Todo,
            'priority' => TaskPriority::Low,
        ]);

        Task::factory()->create([
            'project_id' => $project->id,
            'title' => 'Ship API',
            'status' => TaskStatus::InProgress,
            'priority' => TaskPriority::High,
        ]);

        $create = $this->postJson('/api/projects/'.$project->id.'/tasks', [
            'title' => 'Review PR',
            'priority' => TaskPriority::Medium->value,
            'status' => TaskStatus::Todo->value,
            'due_date' => now()->addDay()->toDateString(),
        ]);

        $create->assertCreated()
            ->assertJsonPath('data.title', 'Review PR');

        Event::assertDispatched(TaskCreated::class);

        $taskId = $create->json('data.id');

        $this->getJson('/api/projects/'.$project->id.'/tasks?status=todo&priority=medium&title=Review')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Review PR');

        $this->putJson('/api/tasks/'.$taskId, [
            'status' => TaskStatus::Done->value,
        ])->assertOk()
            ->assertJsonPath('data.status', TaskStatus::Done->value);

        Event::assertDispatched(TaskUpdated::class);

        $this->deleteJson('/api/tasks/'.$taskId)
            ->assertOk()
            ->assertJsonPath('success', true);

        Event::assertDispatched(TaskDeleted::class);
    }
}
