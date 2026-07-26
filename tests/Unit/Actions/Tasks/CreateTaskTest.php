<?php

use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Actions\CreateTask;
use App\Domain\Tasks\Enums\TaskPriority;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Models\Task;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new CreateTask;
});

test('creates a task with the given title', function () {
    $task = $this->action->execute($this->user, ['title' => 'Buy groceries']);

    expect($task)->toBeInstanceOf(Task::class)
        ->and($task->title)->toBe('Buy groceries');

    assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Buy groceries']);
});

test('new task always has status todo', function () {
    $task = $this->action->execute($this->user, ['title' => 'Something']);

    expect($task->status)->toBe(TaskStatus::Todo);
});

test('new task is associated with the correct user', function () {
    $task = $this->action->execute($this->user, ['title' => 'My task']);

    expect($task->user_id)->toBe($this->user->id);
});

test('title is trimmed before persisting', function () {
    $task = $this->action->execute($this->user, ['title' => '  padded  ']);

    expect($task->title)->toBe('padded');
    assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'padded']);
});

test('completed_at is null on a newly created task', function () {
    $task = $this->action->execute($this->user, ['title' => 'Fresh task']);

    expect($task->completed_at)->toBeNull();
});

test('defaults to medium priority when not specified', function () {
    $task = $this->action->execute($this->user, ['title' => 'No priority']);

    expect($task->priority)->toBe(TaskPriority::Medium);
});

test('respects explicit priority', function () {
    $task = $this->action->execute($this->user, [
        'title' => 'Urgent task',
        'priority' => 'high',
    ]);

    expect($task->priority)->toBe(TaskPriority::High);
});

test('description is persisted when provided', function () {
    $task = $this->action->execute($this->user, [
        'title' => 'With desc',
        'description' => 'Detailed notes here',
    ]);

    expect($task->description)->toBe('Detailed notes here');
});

test('project_id stays null when not provided', function () {
    $task = $this->action->execute($this->user, ['title' => 'Standalone']);

    expect($task->project_id)->toBeNull();
});
