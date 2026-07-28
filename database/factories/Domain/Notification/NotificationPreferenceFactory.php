<?php

namespace Database\Factories\Domain\Notification;

use App\Domain\Notification\Models\NotificationPreference;
use App\Domain\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NotificationPreference>
 */
class NotificationPreferenceFactory extends Factory
{
    protected $model = NotificationPreference::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'deadline_reminder_enabled' => true,
            'habit_reminder_enabled' => true,
            'habit_reminder_time' => '20:00',
            'review_ritual_enabled' => true,
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn () => ['user_id' => $user->id]);
    }

    public function withDeadlineDisabled(): static
    {
        return $this->state(fn () => ['deadline_reminder_enabled' => false]);
    }

    public function withHabitDisabled(): static
    {
        return $this->state(fn () => ['habit_reminder_enabled' => false]);
    }

    public function withReviewDisabled(): static
    {
        return $this->state(fn () => ['review_ritual_enabled' => false]);
    }
}
