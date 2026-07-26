<?php

namespace App\Domain\Tasks\Events;

use App\Domain\Tasks\Models\Task;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a Task transitions to 'done' status.
 *
 * Listeners:
 *   - UpdateProjectProgress (stub in EPIC-003, full implementation in EPIC-004)
 *
 * Not broadcast (no realtime for MVP).
 */
class TaskCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Task $task) {}
}
