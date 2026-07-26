<?php

namespace App\Listeners;

use App\Domain\Projects\Actions\RecalculateProjectProgress;
use App\Domain\Projects\Actions\UpdateProjectStatus;
use App\Domain\Projects\Models\Project;
use App\Domain\Tasks\Events\TaskCompleted;

/**
 * Listener for TaskCompleted event.
 * Recalculates the progress of the Project the completed Task belongs to.
 * If progress reaches 100%, the Project is automatically marked as completed.
 */
class UpdateProjectProgress
{
    public function handle(TaskCompleted $event): void
    {
        if ($event->task->project_id === null) {
            return;
        }

        $project = Project::find($event->task->project_id);

        if ($project === null) {
            return;
        }

        (new RecalculateProjectProgress(new UpdateProjectStatus))->execute($project);
    }
}
