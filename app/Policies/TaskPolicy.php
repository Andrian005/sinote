<?php

namespace App\Policies;

use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Models\Task;

class TaskPolicy
{
    /** Any authenticated user can list tasks (filtered by user_id in query). */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** User can only view their own tasks. */
    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /** Any authenticated user can create a task. */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * User can update a task only if they own it and it is not yet
     * done or archived (FSD 2.1 — editing a completed/archived task
     * is not supported in MVP).
     */
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->user_id
            && ! in_array($task->status, [TaskStatus::Done, TaskStatus::Archived]);
    }

    /** User can delete their own task regardless of status. */
    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * User can update the status of their own task.
     * The state machine guard (valid transition check) lives in
     * UpdateTaskStatus Action — this policy only checks ownership.
     */
    public function updateStatus(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * User can archive their own task as long as it is not already archived.
     * Archiving a done task is allowed (FSD 2.2 state machine).
     */
    public function archive(User $user, Task $task): bool
    {
        return $user->id === $task->user_id
            && $task->status !== TaskStatus::Archived;
    }
}
