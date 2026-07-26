<?php

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;
use App\Domain\Tasks\Enums\TaskStatus;

class RecalculateProjectProgress
{
    public function __construct(private readonly UpdateProjectStatus $updateStatus) {}

    /**
     * Recalculate progress for a Project based on its Task completion ratio.
     *
     * Formula (FSD 3.2):
     *   progress = (count tasks WHERE status = 'done')
     *            / (count tasks WHERE status IN 'todo','in_progress','done')
     *            * 100  — rounded to nearest integer
     *
     * Tasks with status 'archived' are excluded from both numerator and denominator.
     *
     * Auto-complete rule: if progress = 100 AND total > 0 AND project is active,
     * the Project status is automatically set to 'completed' (FSD 3.2).
     * No auto-complete if there are no tasks (progress stays 0).
     *
     * @return Project The updated Project instance.
     */
    public function execute(Project $project): Project
    {
        $total = $project->tasks()
            ->whereIn('status', [
                TaskStatus::Todo,
                TaskStatus::InProgress,
                TaskStatus::Done,
            ])
            ->count();

        if ($total === 0) {
            // No countable tasks — progress stays at current value (do not regress).
            return $project;
        }

        $done = $project->tasks()
            ->where('status', TaskStatus::Done)
            ->count();

        $progress = (int) round(($done / $total) * 100);

        $project->update(['progress' => $progress]);

        // Auto-complete when all tasks are done.
        if ($progress === 100 && $project->status === ProjectStatus::Active) {
            $this->updateStatus->execute($project->fresh(), ProjectStatus::Completed);
        }

        return $project->fresh();
    }
}
