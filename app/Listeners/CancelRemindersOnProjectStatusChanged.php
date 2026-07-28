<?php

namespace App\Listeners;

use App\Domain\Notification\Actions\CancelDeadlineReminder;
use App\Domain\Projects\Events\ProjectStatusChanged;

class CancelRemindersOnProjectStatusChanged
{
    public function handle(ProjectStatusChanged $event): void
    {
        if ($event->project->due_date === null) {
            return;
        }

        (new CancelDeadlineReminder)->execute($event->project);
    }
}
