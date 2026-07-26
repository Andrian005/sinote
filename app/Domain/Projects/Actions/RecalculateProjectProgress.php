<?php

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;
use App\Domain\Tasks\Enums\TaskStatus;

class RecalculateProjectProgress
{
    public function __construct(private readonly UpdateProjectStatus $updateStatus) {}

    /**
     * Recalculate a Project's progress based on Task completion ratio.
     *
     * Formula: done_tasks / (todo + in_progress + done) * 100, rounded.
     * Archived tasks are excluded from both numerator and denominator.
     *
     * Auto-complete rule: progress = 100 AND total > 0 AND project is active
     * → status is automatically set to 'completed'.
     */
    public function execute(Project $project): Project
    {
        $total = $project->tasks()
            ->whereIn('status', [TaskStatus::Todo, TaskStatus::InProgress, TaskStatus::Done])
            ->count();

        if ($total === 0) {
            return $project;
        }

        $done = $project->tasks()
            ->where('status', TaskStatus::Done)
            ->count();

        $progress = (int) round(($done / $total) * 100);

        $project->update(['progress' => $progress]);

        if ($progress === 100 && $project->status === ProjectStatus::Active) {
            $this->updateStatus->execute($project->fresh(), ProjectStatus::Completed);
        }

        return $project->fresh();
    }
}
