<?php

namespace App\Livewire\Notification;

use App\Domain\Notification\Models\Reminder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class ReminderList extends Component
{
    /**
     * When limit > 0 the component runs in widget mode (dashboard).
     * When limit = 0 all pending reminders are returned (full view).
     */
    public int $limit = 0;

    /**
     * Reminders that are scheduled and whose delivery time has arrived (scheduled_at <= now).
     *
     * Ordered by scheduled_at ASC so the most urgent items appear first.
     * The remindable relation is eager-loaded to avoid N+1 queries.
     */
    public function getRemindersProperty(): Collection
    {
        return Reminder::where('user_id', auth()->id())
            ->pendingDelivery()
            ->with('remindable')
            ->orderBy('scheduled_at')
            ->when($this->limit > 0, fn ($query) => $query->limit($this->limit))
            ->get();
    }

    #[On('reminder-updated')]
    public function refreshReminders(): void
    {
        unset($this->reminders);
    }

    public function render()
    {
        return view('livewire.notification.reminder-list', [
            'reminders' => $this->reminders,
        ]);
    }
}
