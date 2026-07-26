<?php

namespace App\Policies;

use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Models\Task;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /** Editing done or archived tasks is not supported. */
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->user_id
            && ! in_array($task->status, [TaskStatus::Done, TaskStatus::Archived]);
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Ownership check only — valid transition is enforced by UpdateTaskStatus Action.
     */
    public function updateStatus(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /** Archiving a done task is allowed; archiving an already-archived task is not. */
    public function archive(User $user, Task $task): bool
    {
        return $user->id === $task->user_id
            && $task->status !== TaskStatus::Archived;
    }
}
