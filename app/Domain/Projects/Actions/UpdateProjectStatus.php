<?php

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Events\ProjectStatusChanged;
use App\Domain\Projects\Exceptions\InvalidProjectTransitionException;
use App\Domain\Projects\Models\Project;

class UpdateProjectStatus
{
    /**
     * Transition a Project to a new status, enforcing the state machine.
     *
     * @throws InvalidProjectTransitionException
     */
    public function execute(Project $project, ProjectStatus $newStatus): Project
    {
        if (! in_array($newStatus, $project->status->allowedTransitions(), strict: true)) {
            throw new InvalidProjectTransitionException($project->status, $newStatus);
        }

        $project->update(['status' => $newStatus]);

        if (in_array($newStatus, [ProjectStatus::Completed, ProjectStatus::Archived], strict: true)) {
            ProjectStatusChanged::dispatch($project->fresh(), $newStatus);
        }

        return $project->fresh();
    }
}
