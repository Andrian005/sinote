<?php

namespace App\Domain\Shared\Observers;

use App\Domain\Notification\Models\NotificationPreference;
use App\Domain\Shared\Models\User;

class UserObserver
{
    /** Auto-create default NotificationPreference when a new User is registered. */
    public function created(User $user): void
    {
        NotificationPreference::create([
            'user_id' => $user->id,
            'deadline_reminder_enabled' => true,
            'habit_reminder_enabled' => true,
            'habit_reminder_time' => '20:00',
            'review_ritual_enabled' => true,
        ]);
    }
}
