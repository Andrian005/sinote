<?php

namespace App\Domain\Tasks\Events;

use App\Domain\Tasks\Models\Task;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a Task transitions to 'done'.
 * Listened to by UpdateProjectProgress (triggers RecalculateProjectProgress).
 */
class TaskCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Task $task) {}
}
