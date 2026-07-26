<?php

use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Actions\UpdateTaskStatus;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Events\TaskCompleted;
use App\Domain\Tasks\Exceptions\InvalidTaskTransitionException;
use App\Domain\Tasks\Models\Task;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new UpdateTaskStatus;
});

// ---------------------------------------------------------------------------
// Valid transitions
// ---------------------------------------------------------------------------

test('todo → in_progress is valid', function () {
    $task = Task::factory()->forUser($this->user)->todo()->create();

    $updated = $this->action->execute($task, TaskStatus::InProgress);

    expect($updated->status)->toBe(TaskStatus::InProgress);
});

test('todo → archived is valid', function () {
    $task = Task::factory()->forUser($this->user)->todo()->create();

    $updated = $this->action->execute($task, TaskStatus::Archived);

    expect($updated->status)->toBe(TaskStatus::Archived);
});

test('in_progress → done is valid', function () {
    $task = Task::factory()->forUser($this->user)->inProgress()->create();

    $updated = $this->action->execute($task, TaskStatus::Done);

    expect($updated->status)->toBe(TaskStatus::Done);
});

test('in_progress → todo is valid (step back)', function () {
    $task = Task::factory()->forUser($this->user)->inProgress()->create();

    $updated = $this->action->execute($task, TaskStatus::Todo);

    expect($updated->status)->toBe(TaskStatus::Todo);
});

test('in_progress → archived is valid', function () {
    $task = Task::factory()->forUser($this->user)->inProgress()->create();

    $updated = $this->action->execute($task, TaskStatus::Archived);

    expect($updated->status)->toBe(TaskStatus::Archived);
});

test('done → todo is valid (reopen)', function () {
    $task = Task::factory()->forUser($this->user)->done()->create();

    $updated = $this->action->execute($task, TaskStatus::Todo);

    expect($updated->status)->toBe(TaskStatus::Todo);
});

// ---------------------------------------------------------------------------
// completed_at side effects
// ---------------------------------------------------------------------------

test('completed_at is set when transitioning to done', function () {
    $task = Task::factory()->forUser($this->user)->inProgress()->create();

    $updated = $this->action->execute($task, TaskStatus::Done);

    expect($updated->completed_at)->not->toBeNull();
    assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'done']);
});

test('completed_at is cleared when reopening from done', function () {
    $task = Task::factory()->forUser($this->user)->done()->create();

    $updated = $this->action->execute($task, TaskStatus::Todo);

    expect($updated->completed_at)->toBeNull();
});

// ---------------------------------------------------------------------------
// TaskCompleted event
// ---------------------------------------------------------------------------

test('TaskCompleted event is dispatched when transitioning to done', function () {
    Event::fake([TaskCompleted::class]);

    $task = Task::factory()->forUser($this->user)->inProgress()->create();

    $this->action->execute($task, TaskStatus::Done);

    Event::assertDispatched(TaskCompleted::class, function ($event) use ($task) {
        return $event->task->id === $task->id;
    });
});

test('TaskCompleted event is NOT dispatched for other transitions', function () {
    Event::fake([TaskCompleted::class]);

    $task = Task::factory()->forUser($this->user)->todo()->create();

    $this->action->execute($task, TaskStatus::InProgress);

    Event::assertNotDispatched(TaskCompleted::class);
});

// ---------------------------------------------------------------------------
// Invalid transitions — throws exception
// ---------------------------------------------------------------------------

test('todo → done is invalid', function () {
    $task = Task::factory()->forUser($this->user)->todo()->create();

    expect(fn () => $this->action->execute($task, TaskStatus::Done))
        ->toThrow(InvalidTaskTransitionException::class);
});

test('done → in_progress is invalid', function () {
    $task = Task::factory()->forUser($this->user)->done()->create();

    expect(fn () => $this->action->execute($task, TaskStatus::InProgress))
        ->toThrow(InvalidTaskTransitionException::class);
});

test('done → archived is invalid', function () {
    $task = Task::factory()->forUser($this->user)->done()->create();

    expect(fn () => $this->action->execute($task, TaskStatus::Archived))
        ->toThrow(InvalidTaskTransitionException::class);
});

test('archived is a final state — no transitions out', function () {
    $task = Task::factory()->forUser($this->user)->archived()->create();

    foreach ([TaskStatus::Todo, TaskStatus::InProgress, TaskStatus::Done] as $target) {
        expect(fn () => $this->action->execute($task, $target))
            ->toThrow(InvalidTaskTransitionException::class);
    }
});

test('exception message contains from and to status values', function () {
    $task = Task::factory()->forUser($this->user)->todo()->create();

    expect(fn () => $this->action->execute($task, TaskStatus::Done))
        ->toThrow(InvalidTaskTransitionException::class, 'todo')
        ->toThrow(InvalidTaskTransitionException::class, 'done');
});
