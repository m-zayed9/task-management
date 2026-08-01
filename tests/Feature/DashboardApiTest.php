<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_aggregated_stats(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create([
            'status' => ProjectStatus::Active,
        ]);

        Task::factory()->create([
            'project_id' => $project->id,
            'status' => TaskStatus::Done,
        ]);

        Task::factory()->create([
            'project_id' => $project->id,
            'status' => TaskStatus::Todo,
            'due_date' => now()->subDay()->toDateString(),
        ]);

        Task::factory()->create([
            'project_id' => $project->id,
            'status' => TaskStatus::InProgress,
            'due_date' => now()->addDay()->toDateString(),
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total_projects', 1)
            ->assertJsonPath('data.active_projects', 1)
            ->assertJsonPath('data.total_tasks', 3)
            ->assertJsonPath('data.completed_tasks', 1)
            ->assertJsonPath('data.pending_tasks', 2)
            ->assertJsonPath('data.overdue_tasks', 1);
    }
}
