<?php

use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Actions\UpdateTask;
use App\Domain\Tasks\Enums\TaskPriority;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Models\Task;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new UpdateTask;
});

test('can update task title', function () {
    $task = Task::factory()->forUser($this->user)->create(['title' => 'Old title']);

    $updated = $this->action->execute($task, ['title' => 'New title']);

    expect($updated->title)->toBe('New title');
});

test('title is trimmed on update', function () {
    $task = Task::factory()->forUser($this->user)->create();

    $updated = $this->action->execute($task, ['title' => '  trimmed  ']);

    expect($updated->title)->toBe('trimmed');
});

test('can update description', function () {
    $task = Task::factory()->forUser($this->user)->create();

    $updated = $this->action->execute($task, ['description' => 'New description']);

    expect($updated->description)->toBe('New description');
});

test('can update priority', function () {
    $task = Task::factory()->forUser($this->user)->create();

    $updated = $this->action->execute($task, ['priority' => 'high']);

    expect($updated->priority)->toBe(TaskPriority::High);
});

test('status cannot be changed via UpdateTask', function () {
    $task = Task::factory()->forUser($this->user)->todo()->create();

    // Attempt to sneak in a status change — must be silently stripped
    $updated = $this->action->execute($task, [
        'title' => 'Updated',
        'status' => 'done',
    ]);

    expect($updated->status)->toBe(TaskStatus::Todo);
});

test('completed_at cannot be changed via UpdateTask', function () {
    $task = Task::factory()->forUser($this->user)->todo()->create();

    $updated = $this->action->execute($task, [
        'title' => 'Updated',
        'completed_at' => now()->toDateTimeString(),
    ]);

    expect($updated->completed_at)->toBeNull();
});
