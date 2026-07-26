<?php

namespace App\Domain\Tasks\Actions;

use App\Domain\Tasks\Models\Task;

class UpdateTask
{
    /**
     * Update the editable attributes of a Task.
     *
     * Status must NOT be changed via this action — use UpdateTaskStatus instead.
     * The 'status' key is explicitly stripped from $data to enforce this invariant.
     *
     * @param  array{title?: string, description?: string|null, priority?: string|null, due_date?: string|null, project_id?: string|null}  $data
     */
    public function execute(Task $task, array $data): Task
    {
        // Strip status — it must only change through UpdateTaskStatus.
        unset($data['status'], $data['completed_at']);

        if (isset($data['title'])) {
            $data['title'] = trim($data['title']);
        }

        $task->update($data);

        return $task->fresh();
    }
}
