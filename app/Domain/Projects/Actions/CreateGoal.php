<?php

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\Enums\GoalStatus;
use App\Domain\Projects\Enums\GoalType;
use App\Domain\Projects\Models\Goal;
use App\Domain\Shared\Models\User;
use InvalidArgumentException;

class CreateGoal
{
    /**
     * Create a new Goal. goal_type is immutable after creation.
     * target_date is required when goal_type is 'time_bound'.
     *
     * @throws InvalidArgumentException if goal_type=time_bound and target_date is missing.
     */
    public function execute(User $user, array $data): Goal
    {
        $goalType = GoalType::from($data['goal_type']);

        if ($goalType === GoalType::TimeBound && empty($data['target_date'])) {
            throw new InvalidArgumentException('target_date is required for time_bound goals.');
        }

        return Goal::create([
            'user_id' => $user->id,
            'title' => trim($data['title']),
            'description' => $data['description'] ?? null,
            'goal_type' => $goalType,
            'status' => GoalStatus::Active,
            'target_date' => $data['target_date'] ?? null,
        ]);
    }
}
