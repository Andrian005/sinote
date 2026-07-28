<?php

namespace App\Policies;

use App\Domain\Notification\Models\Reminder;
use App\Domain\Shared\Models\User;

class ReminderPolicy
{
    public function view(User $user, Reminder $reminder): bool
    {
        return $user->id === $reminder->user_id;
    }
}
