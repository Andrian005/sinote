<?php

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\Models\Goal;

class UpdateGoal
{
    /**
     * Update the editable attributes of a Goal.
     *
     * goal_type is immutable after creation — silently stripped (FSD 3.1).
     * status must be changed via UpdateGoalStatus — also stripped.
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
