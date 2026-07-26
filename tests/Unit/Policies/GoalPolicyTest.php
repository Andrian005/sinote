<?php

use App\Domain\Projects\Models\Goal;
use App\Domain\Shared\Models\User;
use App\Policies\GoalPolicy;

beforeEach(function () {
    $this->policy = new GoalPolicy;
    $this->user = User::factory()->create();
    $this->other = User::factory()->create();
});

test('viewAny always returns true', function () {
    expect($this->policy->viewAny($this->user))->toBeTrue();
});

test('create always returns true', function () {
    expect($this->policy->create($this->user))->toBeTrue();
});

test('user can view their own goal', function () {
    $goal = Goal::factory()->forUser($this->user)->create();
    expect($this->policy->view($this->user, $goal))->toBeTrue();
});

test('user cannot view another users goal', function () {
    $goal = Goal::factory()->forUser($this->other)->create();
    expect($this->policy->view($this->user, $goal))->toBeFalse();
});

test('user can update their own active goal', function () {
    $goal = Goal::factory()->forUser($this->user)->active()->create();
    expect($this->policy->update($this->user, $goal))->toBeTrue();
});

test('user cannot update a completed goal', function () {
    $goal = Goal::factory()->forUser($this->user)->completed()->create();
    expect($this->policy->update($this->user, $goal))->toBeFalse();
});

test('user cannot update an archived goal', function () {
    $goal = Goal::factory()->forUser($this->user)->archived()->create();
    expect($this->policy->update($this->user, $goal))->toBeFalse();
});

test('user cannot update another users goal', function () {
    $goal = Goal::factory()->forUser($this->other)->active()->create();
    expect($this->policy->update($this->user, $goal))->toBeFalse();
});

test('user can delete their own goal', function () {
    $goal = Goal::factory()->forUser($this->user)->create();
    expect($this->policy->delete($this->user, $goal))->toBeTrue();
});

test('user cannot delete another users goal', function () {
    $goal = Goal::factory()->forUser($this->other)->create();
    expect($this->policy->delete($this->user, $goal))->toBeFalse();
});

test('user can complete their own active goal', function () {
    $goal = Goal::factory()->forUser($this->user)->active()->create();
    expect($this->policy->complete($this->user, $goal))->toBeTrue();
});

test('user cannot complete a non-active goal', function () {
    $completed = Goal::factory()->forUser($this->user)->completed()->create();
    $archived = Goal::factory()->forUser($this->user)->archived()->create();
    expect($this->policy->complete($this->user, $completed))->toBeFalse();
    expect($this->policy->complete($this->user, $archived))->toBeFalse();
});

test('user can archive their own active goal', function () {
    $goal = Goal::factory()->forUser($this->user)->active()->create();
    expect($this->policy->archive($this->user, $goal))->toBeTrue();
});

test('user cannot archive an already archived goal', function () {
    $goal = Goal::factory()->forUser($this->user)->archived()->create();
    expect($this->policy->archive($this->user, $goal))->toBeFalse();
});

test('user cannot archive another users goal', function () {
    $goal = Goal::factory()->forUser($this->other)->active()->create();
    expect($this->policy->archive($this->user, $goal))->toBeFalse();
});
