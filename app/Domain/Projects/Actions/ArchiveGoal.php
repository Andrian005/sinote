<?php

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\Enums\GoalStatus;
use App\Domain\Projects\Models\Goal;

class ArchiveGoal
{
    public function __construct(private readonly UpdateGoalStatus $updateStatus) {}

    /**
     * Archive a Goal.
     * Throws InvalidGoalTransitionException if already archived (via UpdateGoalStatus).
     */
    public function execute(Goal $goal): Goal
    {
        return $this->updateStatus->execute($goal, GoalStatus::Archived);
    }
}
