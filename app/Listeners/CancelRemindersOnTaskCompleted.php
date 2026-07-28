<?php

namespace App\Listeners;

use App\Domain\Notification\Actions\CancelDeadlineReminder;
use App\Domain\Tasks\Events\TaskCompleted;

class CancelRemindersOnTaskCompleted
{
    public function handle(TaskCompleted $event): void
    {
        if ($event->task->due_date === null) {
            return;
        }

        (new CancelDeadlineReminder)->execute($event->task);
    }
}
