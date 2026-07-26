<?php

namespace App\Listeners;

use App\Domain\Projects\Actions\RecalculateProjectProgress;
use App\Domain\Projects\Actions\UpdateProjectStatus;
use App\Domain\Projects\Models\Project;
use App\Domain\Tasks\Events\TaskCompleted;

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
