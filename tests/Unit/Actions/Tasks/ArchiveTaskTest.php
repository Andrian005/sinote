<?php

use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Actions\ArchiveTask;
use App\Domain\Tasks\Actions\UpdateTaskStatus;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Exceptions\InvalidTaskTransitionException;
use App\Domain\Tasks\Models\Task;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new ArchiveTask(new UpdateTaskStatus);
});

test('can archive a todo task', function () {
    $task = Task::factory()->forUser($this->user)->todo()->create();

    $archived = $this->action->execute($task);

    expect($archived->status)->toBe(TaskStatus::Archived);
});

test('can archive an in_progress task', function () {
    $task = Task::factory()->forUser($this->user)->inProgress()->create();

    $archived = $this->action->execute($task);

    expect($archived->status)->toBe(TaskStatus::Archived);
});

test('throws exception when archiving an already archived task', function () {
    $task = Task::factory()->forUser($this->user)->archived()->create();

    expect(fn () => $this->action->execute($task))
        ->toThrow(InvalidTaskTransitionException::class);
});

test('cannot archive a done task (done → archived is invalid per state machine)', function () {
    $task = Task::factory()->forUser($this->user)->done()->create();

    expect(fn () => $this->action->execute($task))
        ->toThrow(InvalidTaskTransitionException::class);
});
