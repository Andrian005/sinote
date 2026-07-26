<?php

use App\Domain\Projects\Actions\UpdateGoal;
use App\Domain\Projects\Enums\GoalType;
use App\Domain\Projects\Models\Goal;
use App\Domain\Shared\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new UpdateGoal;
});

test('can update goal title', function () {
    $goal = Goal::factory()->forUser($this->user)->create(['title' => 'Old']);

    $updated = $this->action->execute($goal, ['title' => 'New title']);

    expect($updated->title)->toBe('New title');
});

test('title is trimmed on update', function () {
    $goal = Goal::factory()->forUser($this->user)->create();

    $updated = $this->action->execute($goal, ['title' => '  trimmed  ']);

    expect($updated->title)->toBe('trimmed');
});

test('goal_type cannot be changed via UpdateGoal (silently stripped)', function () {
    $goal = Goal::factory()
        ->forUser($this->user)
        ->ongoing()
        ->create();

    $updated = $this->action->execute($goal, [
        'title' => 'Still ongoing',
        'goal_type' => 'time_bound',
    ]);

    expect($updated->goal_type)->toBe(GoalType::Ongoing);
});

test('status cannot be changed via UpdateGoal (silently stripped)', function () {
    $goal = Goal::factory()->forUser($this->user)->active()->create();

    $updated = $this->action->execute($goal, [
        'title' => 'Same',
        'status' => 'completed',
    ]);

    expect($updated->status->value)->toBe('active');
});

test('can update description', function () {
    $goal = Goal::factory()->forUser($this->user)->create();

    $updated = $this->action->execute($goal, ['description' => 'New desc']);

    expect($updated->description)->toBe('New desc');
});
