<?php

namespace Database\Factories\Domain\Tasks;

use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Enums\TaskPriority;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'project_id' => null,
            'title' => $this->faker->sentence(4),
            'description' => null,
            'status' => TaskStatus::Todo,
            'priority' => TaskPriority::Medium,
            'due_date' => null,
            'completed_at' => null,
        ];
    }

    /** Associate with a specific user. */
    public function forUser(User $user): static
    {
        return $this->state(fn () => ['user_id' => $user->id]);
    }

    /**
     * Associate with a project (project_id only — no FK constraint yet).
     * Accepts a string ID directly to avoid Project model dependency.
     */
    public function withProjectId(string $projectId): static
    {
        return $this->state(fn () => ['project_id' => $projectId]);
    }

    /** Explicit todo state (default, but useful for test clarity). */
    public function todo(): static
    {
        return $this->state(fn () => [
            'status' => TaskStatus::Todo,
            'completed_at' => null,
        ]);
    }

    /** Set status to in_progress. */
    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => TaskStatus::InProgress,
            'completed_at' => null,
        ]);
    }

    /** Set status to done with completed_at timestamp. */
    public function done(): static
    {
        return $this->state(fn () => [
            'status' => TaskStatus::Done,
            'completed_at' => now(),
        ]);
    }

    /** Set status to archived (final state). */
    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => TaskStatus::Archived,
            'completed_at' => null,
        ]);
    }

    /** Override title with a specific string. */
    public function withTitle(string $title): static
    {
        return $this->state(fn () => ['title' => $title]);
    }

    /** Set due_date to yesterday — simulates an overdue task. */
    public function overdue(): static
    {
        return $this->state(fn () => ['due_date' => now()->subDay()->toDateString()]);
    }

    /** Set priority to high. */
    public function highPriority(): static
    {
        return $this->state(fn () => ['priority' => TaskPriority::High]);
    }

    /** Set priority to low. */
    public function lowPriority(): static
    {
        return $this->state(fn () => ['priority' => TaskPriority::Low]);
    }

    /** Add a description. */
    public function withDescription(string $description): static
    {
        return $this->state(fn () => ['description' => $description]);
    }
}
