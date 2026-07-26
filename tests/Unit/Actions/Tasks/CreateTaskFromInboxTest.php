<?php

use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Actions\CreateTask;
use App\Domain\Tasks\Actions\CreateTaskFromInbox;
use App\Domain\Tasks\Enums\TaskStatus;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new CreateTaskFromInbox(new CreateTask);
});

test('creates a task from inbox item content', function () {
    $item = InboxItem::factory()->forUser($this->user)
        ->withContent('Fix the login bug')
        ->create();

    $task = $this->action->execute($this->user, $item);

    expect($task->title)->toBe('Fix the login bug')
        ->and($task->user_id)->toBe($this->user->id);
});

test('new task from inbox has status todo', function () {
    $item = InboxItem::factory()->forUser($this->user)->create();

    $task = $this->action->execute($this->user, $item);

    expect($task->status)->toBe(TaskStatus::Todo);
});

test('content ≤255 chars becomes title with no description', function () {
    $content = str_repeat('a', 255);
    $item = InboxItem::factory()->forUser($this->user)->withContent($content)->create();

    $task = $this->action->execute($this->user, $item);

    expect($task->title)->toBe($content)
        ->and($task->description)->toBeNull();
});

test('content >255 chars: title is first 255 chars, description is full content', function () {
    $content = str_repeat('x', 300);
    $item = InboxItem::factory()->forUser($this->user)->withContent($content)->create();

    $task = $this->action->execute($this->user, $item);

    expect($task->title)->toBe(str_repeat('x', 255))
        ->and($task->description)->toBe($content);
});
