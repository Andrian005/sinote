<?php

namespace App\Domain\Notification\Enums;

enum ReminderType: string
{
    case Deadline = 'deadline';
    case HabitSchedule = 'habit_schedule';
    case ReviewRitual = 'review_ritual';

    public function label(): string
    {
        return match ($this) {
            self::Deadline => 'Deadline',
            self::HabitSchedule => 'Jadwal Habit',
            self::ReviewRitual => 'Ritual Review',
        };
    }
}
