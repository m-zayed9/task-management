<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'priority' => fake()->randomElement(TaskPriority::cases()),
            'status' => fake()->randomElement(TaskStatus::cases()),
            'due_date' => fake()->optional()->dateTimeBetween('-1 week', '+3 weeks'),
        ];
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'status' => TaskStatus::Todo,
            'due_date' => now()->subDays(3)->toDateString(),
            'overdue_notified_at' => null,
        ]);
    }

    public function done(): static
    {
        return $this->state(fn () => [
            'status' => TaskStatus::Done,
        ]);
    }
}
