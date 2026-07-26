<?php

namespace App\Listeners;

use App\Domain\Tasks\Events\TaskCompleted;
use Illuminate\Support\Facades\Log;

/**
 * Stub listener for TaskCompleted event.
 *
 * Full implementation (recalculate Project progress from Task completion ratio)
 * will be added in EPIC-004 when the Projects module is built.
 * Registered now to ensure the event architecture is wired up correctly.
 */
class UpdateProjectProgress
{
    public function handle(TaskCompleted $event): void
    {
        // EPIC-003 stub: log the event so it is observable during development.
        // Replace with actual progress recalculation in EPIC-004.
        if ($event->task->project_id === null) {
            return;
        }

        Log::debug('UpdateProjectProgress: Task completed — recalculation pending EPIC-004.', [
            'task_id' => $event->task->id,
            'project_id' => $event->task->project_id,
        ]);
    }
}
