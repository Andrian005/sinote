<?php

namespace App\Domain\Tasks\Actions;

use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Events\TaskCompleted;
use App\Domain\Tasks\Exceptions\InvalidTaskTransitionException;
use App\Domain\Tasks\Models\Task;

class UpdateTaskStatus
{
    /**
     * Transition a Task to a new status, enforcing the state machine.
     *
     * State machine (FSD 2.2 / TaskStatus::allowedTransitions()):
     *   todo        → in_progress, archived
     *   in_progress → todo, done, archived
     *   done        → todo  (reopen)
     *   archived    → (final — no transitions)
     *
     * Side effects:
     *   → done:     set completed_at = now(), dispatch TaskCompleted event
     *   → not done: clear completed_at
     *
     * @throws InvalidTaskTransitionException
     */
    public function execute(Task $task, TaskStatus $newStatus): Task
    {
        $currentStatus = $task->status;

        if (! in_array($newStatus, $currentStatus->allowedTransitions(), strict: true)) {
            throw new InvalidTaskTransitionException($currentStatus, $newStatus);
        }

        $completedAt = $newStatus === TaskStatus::Done ? now() : null;

        $task->update([
            'status' => $newStatus,
            'completed_at' => $completedAt,
        ]);

        if ($newStatus === TaskStatus::Done) {
            TaskCompleted::dispatch($task->fresh());
        }

        return $task->fresh();
    }
}
