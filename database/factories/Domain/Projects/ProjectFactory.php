<?php

namespace Database\Factories\Domain\Projects;

use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Goal;
use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'goal_id' => null,
            'title' => $this->faker->sentence(4),
            'description' => null,
            'status' => ProjectStatus::Active,
            'progress' => 0,
            'due_date' => null,
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn () => ['user_id' => $user->id]);
    }

    public function forGoal(Goal $goal): static
    {
        return $this->state(fn () => ['goal_id' => $goal->id]);
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => ProjectStatus::Active]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => ProjectStatus::Completed,
            'progress' => 100,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['status' => ProjectStatus::Archived]);
    }

    public function withTitle(string $title): static
    {
        return $this->state(fn () => ['title' => $title]);
    }

    public function withProgress(int $progress): static
    {
        return $this->state(fn () => ['progress' => $progress]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'due_date' => now()->subDay()->toDateString(),
            'status' => ProjectStatus::Active,
        ]);
    }
}
