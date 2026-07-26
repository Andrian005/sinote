<?php

use App\Domain\Projects\Actions\CreateProject;
use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Goal;
use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new CreateProject;
});

test('creates a project with title', function () {
    $project = $this->action->execute($this->user, ['title' => 'Build app']);

    expect($project)->toBeInstanceOf(Project::class)
        ->and($project->title)->toBe('Build app');

    assertDatabaseHas('projects', ['id' => $project->id]);
});

test('new project has status active and progress 0', function () {
    $project = $this->action->execute($this->user, ['title' => 'Test']);

    expect($project->status)->toBe(ProjectStatus::Active)
        ->and($project->progress)->toBe(0);
});

test('title is trimmed before persisting', function () {
    $project = $this->action->execute($this->user, ['title' => '  padded  ']);
    expect($project->title)->toBe('padded');
});

test('project is associated with the correct user', function () {
    $project = $this->action->execute($this->user, ['title' => 'Mine']);
    expect($project->user_id)->toBe($this->user->id);
});

test('can attach a goal owned by the same user', function () {
    $goal = Goal::factory()->forUser($this->user)->create();

    $project = $this->action->execute($this->user, [
        'title' => 'With goal',
        'goal_id' => $goal->id,
    ]);

    expect($project->goal_id)->toBe($goal->id);
});

test('throws exception if goal_id belongs to another user', function () {
    $otherUser = User::factory()->create();
    $goal = Goal::factory()->forUser($otherUser)->create();

    expect(fn () => $this->action->execute($this->user, [
        'title' => 'Steal goal',
        'goal_id' => $goal->id,
    ]))->toThrow(AuthorizationException::class);
});
