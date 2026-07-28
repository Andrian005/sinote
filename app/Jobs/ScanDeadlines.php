<?php

namespace App\Jobs;

use App\Domain\Notification\Enums\ReminderType;
use App\Domain\Notification\Models\NotificationPreference;
use App\Domain\Notification\Models\Reminder;
use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Scheduled daily job (07:00) that scans for Tasks and Projects with
 * deadlines falling today (H) or tomorrow (H-1), then dispatches
 * a SendDeadlineReminder job per pending Reminder.
 *
 * Scanner + Sender pattern: failures in individual Sender jobs do not
 * affect the rest of the batch (LARAVEL_RULES.md — Notification & Queue).
 */
class ScanDeadlines implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        // Collect user IDs with deadline_reminder_enabled = true
        $enabledUserIds = NotificationPreference::where('deadline_reminder_enabled', true)
            ->pluck('user_id');

        if ($enabledUserIds->isEmpty()) {
            return;
        }

        // Pending reminders for active Tasks due today or tomorrow
        $taskReminders = Reminder::where('reminder_type', ReminderType::Deadline)
            ->where('status', 'scheduled')
            ->whereIn('user_id', $enabledUserIds)
            ->where('remindable_type', Task::class)
            ->whereIn('scheduled_at', [
                now()->toDateString().' 08:00:00',
            ])
            ->whereHas('remindable', function ($q) use ($today, $tomorrow) {
                $q->whereIn('status', [TaskStatus::Todo->value, TaskStatus::InProgress->value])
                    ->whereIn('due_date', [$today, $tomorrow]);
            })
            ->get();

        // Pending reminders for active Projects due today or tomorrow
        $projectReminders = Reminder::where('reminder_type', ReminderType::Deadline)
            ->where('status', 'scheduled')
            ->whereIn('user_id', $enabledUserIds)
            ->where('remindable_type', Project::class)
            ->whereHas('remindable', function ($q) use ($today, $tomorrow) {
                $q->where('status', ProjectStatus::Active->value)
                    ->whereIn('due_date', [$today, $tomorrow]);
            })
            ->get();

        $taskReminders->merge($projectReminders)->each(function (Reminder $reminder) {
            SendDeadlineReminder::dispatch($reminder->id);
        });
    }
}
