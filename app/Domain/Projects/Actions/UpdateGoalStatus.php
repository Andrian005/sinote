<?php

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\Enums\GoalStatus;
use App\Domain\Projects\Exceptions\InvalidGoalTransitionException;
use App\Domain\Projects\Models\Goal;

class UpdateGoalStatus
{
    /**
     * Transition a Goal to a new status, enforcing the state machine.
     *
     * State machine (FSD 3.1 / GoalStatus::allowedTransitions()):
     *   active    → completed, archived
     *   completed → active  (reopen)
     *   archived  → (final)
     *
     * @throws InvalidGoalTransitionException
     */
    public function execute(Goal $goal, GoalStatus $newStatus): Goal
    {
        if (! in_array($newStatus, $goal->status->allowedTransitions(), strict: true)) {
            throw new InvalidGoalTransitionException($goal->status, $newStatus);
        }

        $goal->update(['status' => $newStatus]);

        return $goal->fresh();
    }
}
