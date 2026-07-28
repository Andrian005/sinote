<?php

use App\Domain\Notification\Enums\ReminderStatus;
use App\Domain\Notification\Models\Reminder;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Actions\UpdateTaskStatus;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Events\TaskCompleted;
use App\Domain\Tasks\Models\Task;
use App\Listeners\CancelRemindersOnTaskCompleted;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('cancels scheduled reminders when task with due_date is completed', function () {
    $task = Task::factory()->forUser($this->user)->inProgress()->create([
        'due_date' => now()->addDays(2)->toDateString(),
    ]);

    Reminder::factory()->forUser($this->user)->forTask($task)->scheduled()->count(2)->create();

    $listener = new CancelRemindersOnTaskCompleted;
    $listener->handle(new TaskCompleted($task));

    expect(Reminder::where('status', ReminderStatus::Cancelled)->count())->toBe(2);
});

test('does nothing when task has no due_date', function () {
    $task = Task::factory()->forUser($this->user)->inProgress()->create(['due_date' => null]);

    Reminder::factory()->forUser($this->user)->forTask($task)->scheduled()->create();

    $listener = new CancelRemindersOnTaskCompleted;
    $listener->handle(new TaskCompleted($task));

    expect(Reminder::where('status', ReminderStatus::Scheduled)->count())->toBe(1);
});

test('listener is triggered via UpdateTaskStatus when task transitions to done', function () {
    $task = Task::factory()->forUser($this->user)->inProgress()->create([
        'due_date' => now()->addDays(2)->toDateString(),
    ]);

    Reminder::factory()->forUser($this->user)->forTask($task)->scheduled()->count(2)->create();

    (new UpdateTaskStatus)->execute($task, TaskStatus::Done);

    expect(Reminder::where('status', ReminderStatus::Cancelled)->count())->toBe(2);
});
