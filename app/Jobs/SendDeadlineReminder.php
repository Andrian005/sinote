<?php

namespace App\Jobs;

use App\Domain\Notification\Models\Reminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Sends a single Reminder by marking it as 'sent'.
 *
 * Receives Reminder by ID (not model) to avoid stale data if the
 * Reminder was cancelled between dispatch and execution.
 *
 * In-app channel only for MVP — external push/email is a Future Enhancement.
 */
class SendDeadlineReminder implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly string $reminderId) {}

    public function handle(): void
    {
        $reminder = Reminder::find($this->reminderId);

        if ($reminder === null) {
            return;
        }

        // Skip if already in a final state (sent, cancelled, or skipped)
        if ($reminder->status->isFinal()) {
            return;
        }

        $reminder->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
