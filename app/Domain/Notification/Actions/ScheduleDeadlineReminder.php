<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Enums\ReminderStatus;
use App\Domain\Notification\Enums\ReminderType;
use App\Domain\Notification\Models\Reminder;
use Illuminate\Database\Eloquent\Model;

class ScheduleDeadlineReminder
{
    /**
     * Schedule H-1 and H deadline reminders for a Task or Project.
     *
     * Idempotent: skips creating a reminder for a date that already has
     * a scheduled reminder for the same entity.
     *
     * H-1 is skipped entirely when due_date is today — the window has passed.
     */
    public function execute(Model $remindable): void
    {
        $dueDate = $remindable->due_date;

        if ($dueDate === null) {
            return;
        }

        $today = now()->startOfDay();
        $due = $dueDate->copy()->startOfDay();

        $slots = [];

        // H-1: only schedule when due_date is in the future
        if ($due->gt($today)) {
            $slots[] = $due->copy()->subDay()->setTime(8, 0);
        }

        // H: always schedule
        $slots[] = $due->copy()->setTime(8, 0);

        foreach ($slots as $scheduledAt) {
            $alreadyExists = Reminder::where('remindable_type', $remindable::class)
                ->where('remindable_id', $remindable->getKey())
                ->where('reminder_type', ReminderType::Deadline)
                ->where('status', ReminderStatus::Scheduled)
                ->whereDate('scheduled_at', $scheduledAt->toDateString())
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            Reminder::create([
                'user_id' => $remindable->user_id,
                'remindable_id' => $remindable->getKey(),
                'remindable_type' => $remindable::class,
                'reminder_type' => ReminderType::Deadline,
                'scheduled_at' => $scheduledAt,
                'status' => ReminderStatus::Scheduled,
            ]);
        }
    }
}
