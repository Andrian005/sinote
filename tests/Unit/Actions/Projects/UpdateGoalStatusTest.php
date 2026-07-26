<?php

use App\Domain\Projects\Actions\UpdateGoalStatus;
use App\Domain\Projects\Enums\GoalStatus;
use App\Domain\Projects\Exceptions\InvalidGoalTransitionException;
use App\Domain\Projects\Models\Goal;
use App\Domain\Shared\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new UpdateGoalStatus;
});

// Valid transitions
test('active → completed is valid', function () {
    $goal = Goal::factory()->forUser($this->user)->active()->create();
    $updated = $this->action->execute($goal, GoalStatus::Completed);
    expect($updated->status)->toBe(GoalStatus::Completed);
});

test('active → archived is valid', function () {
    $goal = Goal::factory()->forUser($this->user)->active()->create();
    $updated = $this->action->execute($goal, GoalStatus::Archived);
    expect($updated->status)->toBe(GoalStatus::Archived);
});

test('completed → active is valid (reopen)', function () {
    $goal = Goal::factory()->forUser($this->user)->completed()->create();
    $updated = $this->action->execute($goal, GoalStatus::Active);
    expect($updated->status)->toBe(GoalStatus::Active);
});

// Invalid transitions
test('completed → archived is invalid', function () {
    $goal = Goal::factory()->forUser($this->user)->completed()->create();
    expect(fn () => $this->action->execute($goal, GoalStatus::Archived))
        ->toThrow(InvalidGoalTransitionException::class);
});

test('archived is a final state — no transitions out', function () {
    $goal = Goal::factory()->forUser($this->user)->archived()->create();
    foreach ([GoalStatus::Active, GoalStatus::Completed] as $target) {
        expect(fn () => $this->action->execute($goal, $target))
            ->toThrow(InvalidGoalTransitionException::class);
    }
});

test('exception message contains from and to status', function () {
    $goal = Goal::factory()->forUser($this->user)->active()->create();
    expect(fn () => $this->action->execute($goal, GoalStatus::Active))
        ->toThrow(InvalidGoalTransitionException::class, 'active');
});
