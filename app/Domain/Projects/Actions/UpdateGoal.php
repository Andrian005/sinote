<?php

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\Models\Goal;

class UpdateGoal
{
    /**
     * Update editable attributes of a Goal.
     * goal_type is immutable after creation — stripped here to enforce the invariant.
     * Status changes must go through UpdateGoalStatus.
     */
    public function execute(Goal $goal, array $data): Goal
    {
        unset($data['goal_type'], $data['status']);

        if (isset($data['title'])) {
            $data['title'] = trim($data['title']);
        }

        $goal->update($data);

        return $goal->fresh();
    }
}
