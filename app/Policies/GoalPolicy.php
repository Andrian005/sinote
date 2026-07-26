<?php

namespace App\Policies;

use App\Domain\Projects\Enums\GoalStatus;
use App\Domain\Projects\Models\Goal;
use App\Domain\Shared\Models\User;

class GoalPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Goal $goal): bool
    {
        return $user->id === $goal->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /** Only active goals can be edited (description/title/target_date). */
    public function update(User $user, Goal $goal): bool
    {
        return $user->id === $goal->user_id
            && $goal->status === GoalStatus::Active;
    }

    public function delete(User $user, Goal $goal): bool
    {
        return $user->id === $goal->user_id;
    }

    /** Complete is only valid when goal is active (state machine guard in Action). */
    public function complete(User $user, Goal $goal): bool
    {
        return $user->id === $goal->user_id
            && $goal->status === GoalStatus::Active;
    }

    public function archive(User $user, Goal $goal): bool
    {
        return $user->id === $goal->user_id
            && $goal->status !== GoalStatus::Archived;
    }
}
