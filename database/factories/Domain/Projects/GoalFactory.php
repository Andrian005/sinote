<?php

namespace Database\Factories\Domain\Projects;

use App\Domain\Projects\Enums\GoalStatus;
use App\Domain\Projects\Enums\GoalType;
use App\Domain\Projects\Models\Goal;
use App\Domain\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Goal>
 */
class GoalFactory extends Factory
{
    protected $model = Goal::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'description' => null,
            'goal_type' => GoalType::Ongoing,
            'status' => GoalStatus::Active,
            'target_date' => null,
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn () => ['user_id' => $user->id]);
    }

    /** TimeBound goal with a future target_date. */
    public function timeBound(?string $targetDate = null): static
    {
        return $this->state(fn () => [
            'goal_type' => GoalType::TimeBound,
            'target_date' => $targetDate ?? now()->addMonths(3)->toDateString(),
        ]);
    }

    public function ongoing(): static
    {
        return $this->state(fn () => [
            'goal_type' => GoalType::Ongoing,
            'target_date' => null,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => GoalStatus::Active]);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => GoalStatus::Completed]);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['status' => GoalStatus::Archived]);
    }

    public function withTitle(string $title): static
    {
        return $this->state(fn () => ['title' => $title]);
    }

    /** Overdue: time_bound + target_date in the past + active. */
    public function overdue(): static
    {
        return $this->state(fn () => [
            'goal_type' => GoalType::TimeBound,
            'target_date' => now()->subDays(7)->toDateString(),
            'status' => GoalStatus::Active,
        ]);
    }
}
