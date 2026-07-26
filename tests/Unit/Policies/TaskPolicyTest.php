<?php

use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;
use App\Policies\TaskPolicy;

beforeEach(function () {
    $this->policy = new TaskPolicy;
    $this->user = User::factory()->create();
    $this->other = User::factory()->create();
});

// viewAny
test('viewAny always returns true', function () {
    expect($this->policy->viewAny($this->user))->toBeTrue();
});

// view
test('user can view their own task', function () {
    $task = Task::factory()->forUser($this->user)->create();
    expect($this->policy->view($this->user, $task))->toBeTrue();
});

test('user cannot view another users task', function () {
    $task = Task::factory()->forUser($this->other)->create();
    expect($this->policy->view($this->user, $task))->toBeFalse();
});

// create
test('create always returns true', function () {
    expect($this->policy->create($this->user))->toBeTrue();
});

// update
test('user can update their own todo task', function () {
    $task = Task::factory()->forUser($this->user)->todo()->create();
    expect($this->policy->update($this->user, $task))->toBeTrue();
});

test('user can update their own in_progress task', function () {
    $task = Task::factory()->forUser($this->user)->inProgress()->create();
    expect($this->policy->update($this->user, $task))->toBeTrue();
});

test('user cannot update a done task', function () {
    $task = Task::factory()->forUser($this->user)->done()->create();
    expect($this->policy->update($this->user, $task))->toBeFalse();
});

test('user cannot update an archived task', function () {
    $task = Task::factory()->forUser($this->user)->archived()->create();
    expect($this->policy->update($this->user, $task))->toBeFalse();
});

test('user cannot update another users task', function () {
    $task = Task::factory()->forUser($this->other)->todo()->create();
    expect($this->policy->update($this->user, $task))->toBeFalse();
});

// delete
test('user can delete their own task', function () {
    $task = Task::factory()->forUser($this->user)->create();
    expect($this->policy->delete($this->user, $task))->toBeTrue();
});

test('user cannot delete another users task', function () {
    $task = Task::factory()->forUser($this->other)->create();
    expect($this->policy->delete($this->user, $task))->toBeFalse();
});

// updateStatus
test('user can update status of their own task', function () {
    $task = Task::factory()->forUser($this->user)->create();
    expect($this->policy->updateStatus($this->user, $task))->toBeTrue();
});

test('user cannot update status of another users task', function () {
    $task = Task::factory()->forUser($this->other)->create();
    expect($this->policy->updateStatus($this->user, $task))->toBeFalse();
});

// archive
test('user can archive their own active task', function () {
    $task = Task::factory()->forUser($this->user)->todo()->create();
    expect($this->policy->archive($this->user, $task))->toBeTrue();
});

test('user cannot archive an already archived task', function () {
    $task = Task::factory()->forUser($this->user)->archived()->create();
    expect($this->policy->archive($this->user, $task))->toBeFalse();
});

test('user cannot archive another users task', function () {
    $task = Task::factory()->forUser($this->other)->todo()->create();
    expect($this->policy->archive($this->user, $task))->toBeFalse();
});
