<?php

namespace App\Domain\Tasks\Actions;

use App\Domain\Tasks\Models\Task;

class UpdateTask
{
    /**
     * Update editable attributes of a Task.
     * Status changes must go through UpdateTaskStatus — stripped here to enforce the invariant.
     */
    public function execute(Task $task, array $data): Task
    {
        unset($data['status'], $data['completed_at']);

        if (isset($data['title'])) {
            $data['title'] = trim($data['title']);
        }

        $task->update($data);

        return $task->fresh();
    }
}
