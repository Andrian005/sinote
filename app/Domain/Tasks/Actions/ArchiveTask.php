<?php

namespace App\Domain\Tasks\Actions;

use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Models\Task;

class ArchiveTask
{
    public function __construct(private readonly UpdateTaskStatus $updateStatus) {}

    public function execute(Task $task): Task
    {
        return $this->updateStatus->execute($task, TaskStatus::Archived);
    }
}
