<?php

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Exceptions\InvalidProjectTransitionException;
use App\Domain\Projects\Models\Project;

class UpdateProjectStatus
{
    /**
     * Transition a Project to a new status, enforcing the state machine.
     *
     * State machine (FSD 3.2 / ProjectStatus::allowedTransitions()):
     *   active    → completed, archived
     *   completed → active  (reopen)
     *   archived  → (final)
     *
     * @throws InvalidProjectTransitionException
     */
    public function execute(Project $project, ProjectStatus $newStatus): Project
    {
        if (! in_array($newStatus, $project->status->allowedTransitions(), strict: true)) {
            throw new InvalidProjectTransitionException($project->status, $newStatus);
        }

        $project->update(['status' => $newStatus]);

        return $project->fresh();
    }
}
