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
     * Side effects:
     *   → done:     sets completed_at, dispatches TaskCompleted event.
     *   → not done: clears completed_at.
     *
     * @throws InvalidTaskTransitionException
     */
    public function execute(Task $task, TaskStatus $newStatus): Task
    {
        if (! in_array($newStatus, $task->status->allowedTransitions(), strict: true)) {
            throw new InvalidTaskTransitionException($task->status, $newStatus);
        }

        $task->update([
            'status' => $newStatus,
            'completed_at' => $newStatus === TaskStatus::Done ? now() : null,
        ]);

        if ($newStatus === TaskStatus::Done) {
            TaskCompleted::dispatch($task->fresh());
        }

        return $task->fresh();
    }
}
