<?php

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\Enums\GoalStatus;
use App\Domain\Projects\Models\Goal;

class ArchiveGoal
{
    public function __construct(private readonly UpdateGoalStatus $updateStatus) {}

    public function execute(Goal $goal): Goal
    {
        return $this->updateStatus->execute($goal, GoalStatus::Archived);
    }
}
