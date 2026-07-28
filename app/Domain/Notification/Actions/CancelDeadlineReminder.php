<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Enums\ReminderStatus;
use App\Domain\Notification\Enums\ReminderType;
use App\Domain\Notification\Models\Reminder;
use Illuminate\Database\Eloquent\Model;

class CancelDeadlineReminder
{
    /**
     * Cancel all pending deadline reminders for a Task or Project.
     *
     * Idempotent — safe to call when no scheduled reminders exist.
     */
    public function execute(Model $remindable): void
    {
        Reminder::where('remindable_type', $remindable::class)
            ->where('remindable_id', $remindable->getKey())
            ->where('reminder_type', ReminderType::Deadline)
            ->where('status', ReminderStatus::Scheduled)
            ->update(['status' => ReminderStatus::Cancelled]);
    }
}
