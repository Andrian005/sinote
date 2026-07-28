<?php

namespace Database\Factories\Domain\Notification;

use App\Domain\Notification\Enums\ReminderStatus;
use App\Domain\Notification\Enums\ReminderType;
use App\Domain\Notification\Models\Reminder;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reminder>
 */
class ReminderFactory extends Factory
{
    protected $model = Reminder::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'remindable_id' => Task::factory(),
            'remindable_type' => Task::class,
            'reminder_type' => ReminderType::Deadline,
            'scheduled_at' => now()->addDay(),
            'status' => ReminderStatus::Scheduled,
            'sent_at' => null,
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn () => ['user_id' => $user->id]);
    }

    public function forTask(Task $task): static
    {
        return $this->state(fn () => [
            'remindable_id' => $task->id,
            'remindable_type' => Task::class,
            'user_id' => $task->user_id,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => ReminderStatus::Scheduled,
            'sent_at' => null,
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn () => [
            'status' => ReminderStatus::Sent,
            'sent_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => ReminderStatus::Cancelled,
            'sent_at' => null,
        ]);
    }

    public function skipped(): static
    {
        return $this->state(fn () => [
            'status' => ReminderStatus::Skipped,
            'sent_at' => null,
        ]);
    }

    public function dueToday(): static
    {
        return $this->state(fn () => ['scheduled_at' => now()]);
    }

    public function dueTomorrow(): static
    {
        return $this->state(fn () => ['scheduled_at' => now()->addDay()]);
    }
}
