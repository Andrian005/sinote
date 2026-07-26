<?php

use App\Domain\Projects\Actions\CreateGoal;
use App\Domain\Projects\Enums\GoalStatus;
use App\Domain\Projects\Enums\GoalType;
use App\Domain\Projects\Models\Goal;
use App\Domain\Shared\Models\User;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new CreateGoal;
});

test('creates an ongoing goal with title', function () {
    $goal = $this->action->execute($this->user, [
        'title' => 'Hidup lebih sehat',
        'goal_type' => 'ongoing',
    ]);

    expect($goal)->toBeInstanceOf(Goal::class)
        ->and($goal->title)->toBe('Hidup lebih sehat')
        ->and($goal->goal_type)->toBe(GoalType::Ongoing);

    assertDatabaseHas('goals', ['id' => $goal->id]);
});

test('new goal always has status active', function () {
    $goal = $this->action->execute($this->user, [
        'title' => 'Test',
        'goal_type' => 'ongoing',
    ]);

    expect($goal->status)->toBe(GoalStatus::Active);
});

test('creates a time_bound goal with target_date', function () {
    $goal = $this->action->execute($this->user, [
        'title' => 'Lari 5K',
        'goal_type' => 'time_bound',
        'target_date' => now()->addMonths(3)->toDateString(),
    ]);

    expect($goal->goal_type)->toBe(GoalType::TimeBound)
        ->and($goal->target_date)->not->toBeNull();
});

test('throws exception if time_bound goal has no target_date', function () {
    expect(fn () => $this->action->execute($this->user, [
        'title' => 'No date',
        'goal_type' => 'time_bound',
    ]))->toThrow(InvalidArgumentException::class, 'target_date');
});

test('title is trimmed before persisting', function () {
    $goal = $this->action->execute($this->user, [
        'title' => '  padded  ',
        'goal_type' => 'ongoing',
    ]);

    expect($goal->title)->toBe('padded');
});

test('goal is associated with the correct user', function () {
    $goal = $this->action->execute($this->user, [
        'title' => 'My goal',
        'goal_type' => 'ongoing',
    ]);

    expect($goal->user_id)->toBe($this->user->id);
});
