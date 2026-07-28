<?php

use App\Domain\Notification\Actions\CancelDeadlineReminder;
use App\Domain\Notification\Enums\ReminderStatus;
use App\Domain\Notification\Models\Reminder;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;

use function Pest\Laravel\assertDatabaseCount;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new CancelDeadlineReminder;
});

test('cancels all scheduled reminders for the entity', function () {
    $task = Task::factory()->forUser($this->user)->create([
        'due_date' => now()->addDays(3)->toDateString(),
    ]);

    Reminder::factory()->forUser($this->user)->forTask($task)->scheduled()->count(2)->create();

    $this->action->execute($task);

    expect(Reminder::where('status', ReminderStatus::Cancelled)->count())->toBe(2);
});

test('does not affect reminders in a final state', function () {
    $task = Task::factory()->forUser($this->user)->create([
        'due_date' => now()->addDays(3)->toDateString(),
    ]);

    Reminder::factory()->forUser($this->user)->forTask($task)->sent()->create();
    Reminder::factory()->forUser($this->user)->forTask($task)->skipped()->create();
    Reminder::factory()->forUser($this->user)->forTask($task)->scheduled()->create();

    $this->action->execute($task);

    expect(Reminder::where('status', ReminderStatus::Sent)->count())->toBe(1)
        ->and(Reminder::where('status', ReminderStatus::Skipped)->count())->toBe(1)
        ->and(Reminder::where('status', ReminderStatus::Cancelled)->count())->toBe(1);
});

test('is idempotent — safe to call when no scheduled reminders exist', function () {
    $task = Task::factory()->forUser($this->user)->create([
        'due_date' => now()->addDays(3)->toDateString(),
    ]);

    $this->action->execute($task);

    assertDatabaseCount('reminders', 0);
});

test('does not cancel reminders belonging to other entities', function () {
    $task1 = Task::factory()->forUser($this->user)->create(['due_date' => now()->addDays(2)->toDateString()]);
    $task2 = Task::factory()->forUser($this->user)->create(['due_date' => now()->addDays(2)->toDateString()]);

    Reminder::factory()->forUser($this->user)->forTask($task1)->scheduled()->create();
    Reminder::factory()->forUser($this->user)->forTask($task2)->scheduled()->create();

    $this->action->execute($task1);

    expect(Reminder::where('remindable_id', $task2->id)->first()->status)
        ->toBe(ReminderStatus::Scheduled);
});
