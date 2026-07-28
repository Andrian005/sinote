<?php

use App\Domain\Notification\Enums\ReminderStatus;
use App\Domain\Notification\Models\Reminder;
use App\Domain\Projects\Actions\UpdateProjectStatus;
use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Events\ProjectStatusChanged;
use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Models\User;
use App\Listeners\CancelRemindersOnProjectStatusChanged;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('cancels scheduled reminders when project with due_date is completed', function () {
    $project = Project::factory()->forUser($this->user)->active()->create([
        'due_date' => now()->addDays(2)->toDateString(),
    ]);

    Reminder::factory()->forUser($this->user)->count(2)->create([
        'remindable_id' => $project->id,
        'remindable_type' => Project::class,
        'status' => ReminderStatus::Scheduled,
    ]);

    $listener = new CancelRemindersOnProjectStatusChanged;
    $listener->handle(new ProjectStatusChanged($project, ProjectStatus::Completed));

    expect(Reminder::where('status', ReminderStatus::Cancelled)->count())->toBe(2);
});

test('cancels scheduled reminders when project is archived', function () {
    $project = Project::factory()->forUser($this->user)->active()->create([
        'due_date' => now()->addDays(2)->toDateString(),
    ]);

    Reminder::factory()->forUser($this->user)->count(1)->create([
        'remindable_id' => $project->id,
        'remindable_type' => Project::class,
        'status' => ReminderStatus::Scheduled,
    ]);

    $listener = new CancelRemindersOnProjectStatusChanged;
    $listener->handle(new ProjectStatusChanged($project, ProjectStatus::Archived));

    expect(Reminder::where('status', ReminderStatus::Cancelled)->count())->toBe(1);
});

test('does nothing when project has no due_date', function () {
    $project = Project::factory()->forUser($this->user)->active()->create(['due_date' => null]);

    Reminder::factory()->forUser($this->user)->count(1)->create([
        'remindable_id' => $project->id,
        'remindable_type' => Project::class,
        'status' => ReminderStatus::Scheduled,
    ]);

    $listener = new CancelRemindersOnProjectStatusChanged;
    $listener->handle(new ProjectStatusChanged($project, ProjectStatus::Completed));

    expect(Reminder::where('status', ReminderStatus::Scheduled)->count())->toBe(1);
});

test('listener is triggered via UpdateProjectStatus when project is completed', function () {
    $project = Project::factory()->forUser($this->user)->active()->create([
        'due_date' => now()->addDays(2)->toDateString(),
    ]);

    Reminder::factory()->forUser($this->user)->count(2)->create([
        'remindable_id' => $project->id,
        'remindable_type' => Project::class,
        'status' => ReminderStatus::Scheduled,
    ]);

    (new UpdateProjectStatus)->execute($project, ProjectStatus::Completed);

    expect(Reminder::where('status', ReminderStatus::Cancelled)->count())->toBe(2);
});
