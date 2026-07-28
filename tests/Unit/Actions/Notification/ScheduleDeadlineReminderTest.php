<?php

use App\Domain\Notification\Actions\ScheduleDeadlineReminder;
use App\Domain\Notification\Enums\ReminderStatus;
use App\Domain\Notification\Enums\ReminderType;
use App\Domain\Notification\Models\Reminder;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new ScheduleDeadlineReminder;
});

test('creates two reminders (H-1 and H) when due_date is in the future', function () {
    $task = Task::factory()->forUser($this->user)->create([
        'due_date' => now()->addDays(3)->toDateString(),
    ]);

    $this->action->execute($task);

    assertDatabaseCount('reminders', 2);
});

test('H-1 reminder is scheduled at 08:00 one day before due_date', function () {
    $due = now()->addDays(3)->toDateString();
    $task = Task::factory()->forUser($this->user)->create(['due_date' => $due]);

    $this->action->execute($task);

    $h1Date = now()->addDays(2)->toDateString();
    assertDatabaseHas('reminders', [
        'remindable_id' => $task->id,
        'remindable_type' => Task::class,
        'reminder_type' => ReminderType::Deadline->value,
        'status' => ReminderStatus::Scheduled->value,
    ]);

    $h1 = Reminder::where('remindable_id', $task->id)->get()
        ->first(fn ($r) => $r->scheduled_at->toDateString() === $h1Date);

    expect($h1)->not->toBeNull()
        ->and($h1->scheduled_at->format('H:i'))->toBe('08:00');
});

test('H reminder is scheduled at 08:00 on due_date', function () {
    $due = now()->addDays(3)->toDateString();
    $task = Task::factory()->forUser($this->user)->create(['due_date' => $due]);

    $this->action->execute($task);

    $hReminder = Reminder::where('remindable_id', $task->id)->get()
        ->first(fn ($r) => $r->scheduled_at->toDateString() === $due);

    expect($hReminder)->not->toBeNull()
        ->and($hReminder->scheduled_at->format('H:i'))->toBe('08:00');
});

test('skips H-1 when due_date is today — only creates H reminder', function () {
    $task = Task::factory()->forUser($this->user)->create([
        'due_date' => now()->toDateString(),
    ]);

    $this->action->execute($task);

    assertDatabaseCount('reminders', 1);

    $reminder = Reminder::first();
    expect($reminder->scheduled_at->toDateString())->toBe(now()->toDateString());
});

test('does nothing when due_date is null', function () {
    $task = Task::factory()->forUser($this->user)->create(['due_date' => null]);

    $this->action->execute($task);

    assertDatabaseCount('reminders', 0);
});

test('is idempotent — does not duplicate reminders on repeated calls', function () {
    $task = Task::factory()->forUser($this->user)->create([
        'due_date' => now()->addDays(3)->toDateString(),
    ]);

    $this->action->execute($task);
    $this->action->execute($task);
    $this->action->execute($task);

    assertDatabaseCount('reminders', 2);
});

test('reminders are associated with the correct user', function () {
    $task = Task::factory()->forUser($this->user)->create([
        'due_date' => now()->addDays(2)->toDateString(),
    ]);

    $this->action->execute($task);

    Reminder::all()->each(fn ($r) => expect($r->user_id)->toBe($this->user->id));
});
