<?php

use App\Domain\Notification\Enums\ReminderStatus;
use App\Domain\Notification\Models\Reminder;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;
use App\Jobs\SendDeadlineReminder;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('marks reminder as sent and sets sent_at', function () {
    $task = Task::factory()->forUser($this->user)->create(['due_date' => now()->addDay()->toDateString()]);
    $reminder = Reminder::factory()->forUser($this->user)->forTask($task)->scheduled()->create();

    (new SendDeadlineReminder($reminder->id))->handle();

    assertDatabaseHas('reminders', [
        'id' => $reminder->id,
        'status' => ReminderStatus::Sent->value,
    ]);

    expect($reminder->fresh()->sent_at)->not->toBeNull();
});

test('skips if reminder is already sent', function () {
    $task = Task::factory()->forUser($this->user)->create(['due_date' => now()->addDay()->toDateString()]);
    $reminder = Reminder::factory()->forUser($this->user)->forTask($task)->sent()->create();

    (new SendDeadlineReminder($reminder->id))->handle();

    assertDatabaseHas('reminders', [
        'id' => $reminder->id,
        'status' => ReminderStatus::Sent->value,
    ]);
});

test('skips if reminder is cancelled', function () {
    $task = Task::factory()->forUser($this->user)->create(['due_date' => now()->addDay()->toDateString()]);
    $reminder = Reminder::factory()->forUser($this->user)->forTask($task)->cancelled()->create();

    (new SendDeadlineReminder($reminder->id))->handle();

    assertDatabaseHas('reminders', [
        'id' => $reminder->id,
        'status' => ReminderStatus::Cancelled->value,
    ]);
});

test('skips if reminder is skipped', function () {
    $task = Task::factory()->forUser($this->user)->create(['due_date' => now()->addDay()->toDateString()]);
    $reminder = Reminder::factory()->forUser($this->user)->forTask($task)->skipped()->create();

    (new SendDeadlineReminder($reminder->id))->handle();

    assertDatabaseHas('reminders', [
        'id' => $reminder->id,
        'status' => ReminderStatus::Skipped->value,
    ]);
});

test('does nothing if reminder id does not exist', function () {
    expect(fn () => (new SendDeadlineReminder('non-existent-id'))->handle())->not->toThrow(Throwable::class);
});
