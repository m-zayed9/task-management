<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_crud_own_projects(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $create = $this->postJson('/api/projects', [
            'name' => 'My Project',
            'description' => 'Demo project',
            'status' => ProjectStatus::Active->value,
        ]);

        $create->assertCreated()
            ->assertJsonPath('data.name', 'My Project');

        $projectId = $create->json('data.id');

        $this->getJson('/api/projects')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->getJson('/api/projects/'.$projectId)
            ->assertOk()
            ->assertJsonPath('data.id', $projectId);

        $this->putJson('/api/projects/'.$projectId, [
            'name' => 'Updated Project',
            'status' => ProjectStatus::Completed->value,
        ])->assertOk()
            ->assertJsonPath('data.name', 'Updated Project');

        $this->deleteJson('/api/projects/'.$projectId)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSoftDeleted('projects', ['id' => $projectId]);
    }

    public function test_user_cannot_view_another_users_project(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $project = Project::factory()->for($owner)->create();

        Sanctum::actingAs($other);

        $this->getJson('/api/projects/'.$project->id)
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }
}
