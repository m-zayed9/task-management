<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $sampleImage = $this->ensureSampleImage();

        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => 'password',
        ]);

        $user->addMedia($sampleImage)
            ->preservingOriginal()
            ->toMediaCollection('avatar');

        $active = Project::factory()->active()->create([
            'user_id' => $user->id,
            'name' => 'Website Redesign',
            'description' => 'Revamp marketing site and dashboard UI.',
        ]);

        $completed = Project::factory()->completed()->create([
            'user_id' => $user->id,
            'name' => 'Mobile App Launch',
            'description' => 'Ship the first public release.',
        ]);

        $archived = Project::factory()->archived()->create([
            'user_id' => $user->id,
            'name' => 'Legacy Migration',
            'description' => 'Archived after cutover.',
        ]);

        foreach ([$active, $completed, $archived] as $project) {
            $project->addMedia($sampleImage)
                ->preservingOriginal()
                ->toMediaCollection('cover');
        }

        $todo = Task::factory()->create([
            'project_id' => $active->id,
            'title' => 'Draft homepage wireframes',
            'priority' => TaskPriority::High,
            'status' => TaskStatus::Todo,
            'due_date' => now()->addDays(5)->toDateString(),
        ]);

        $inProgress = Task::factory()->create([
            'project_id' => $active->id,
            'title' => 'Implement design system tokens',
            'priority' => TaskPriority::Medium,
            'status' => TaskStatus::InProgress,
            'due_date' => now()->addDays(2)->toDateString(),
        ]);

        Task::factory()->done()->create([
            'project_id' => $active->id,
            'title' => 'Stakeholder kickoff',
            'priority' => TaskPriority::Low,
            'due_date' => now()->subDays(2)->toDateString(),
        ]);

        $overdue = Task::factory()->overdue()->create([
            'project_id' => $active->id,
            'title' => 'Finalize content inventory',
            'priority' => TaskPriority::High,
        ]);

        Task::factory()->count(3)->create([
            'project_id' => $completed->id,
            'status' => TaskStatus::Done,
            'priority' => TaskPriority::Medium,
        ]);

        Task::factory()->create([
            'project_id' => $archived->id,
            'title' => 'Archive cleanup notes',
            'status' => TaskStatus::Done,
            'priority' => TaskPriority::Low,
        ]);

        foreach ([$todo, $inProgress, $overdue] as $task) {
            $task->addMedia($sampleImage)
                ->preservingOriginal()
                ->toMediaCollection('attachments');
        }

        Project::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => ProjectStatus::Active,
        ])->each(function (Project $project) {
            Task::factory()->count(2)->create([
                'project_id' => $project->id,
            ]);
        });
    }

    private function ensureSampleImage(): string
    {
        $directory = database_path('seeders/media');
        File::ensureDirectoryExists($directory);

        $path = $directory.'/sample.png';

        if (! File::exists($path)) {
            $image = imagecreatetruecolor(200, 200);
            $background = imagecolorallocate($image, 37, 99, 235);
            $accent = imagecolorallocate($image, 255, 255, 255);
            imagefilledrectangle($image, 0, 0, 200, 200, $background);
            imagefilledellipse($image, 100, 100, 120, 120, $accent);
            imagepng($image, $path);
            imagedestroy($image);
        }

        return $path;
    }
}
