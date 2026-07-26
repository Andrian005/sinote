<?php

use App\Domain\Projects\Actions\UpdateProjectStatus;
use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Exceptions\InvalidProjectTransitionException;
use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new UpdateProjectStatus;
});

test('active → completed is valid', function () {
    $project = Project::factory()->forUser($this->user)->active()->create();
    $updated = $this->action->execute($project, ProjectStatus::Completed);
    expect($updated->status)->toBe(ProjectStatus::Completed);
});

test('active → archived is valid', function () {
    $project = Project::factory()->forUser($this->user)->active()->create();
    $updated = $this->action->execute($project, ProjectStatus::Archived);
    expect($updated->status)->toBe(ProjectStatus::Archived);
});

test('completed → active is valid (reopen)', function () {
    $project = Project::factory()->forUser($this->user)->completed()->create();
    $updated = $this->action->execute($project, ProjectStatus::Active);
    expect($updated->status)->toBe(ProjectStatus::Active);
});

test('completed → archived is invalid', function () {
    $project = Project::factory()->forUser($this->user)->completed()->create();
    expect(fn () => $this->action->execute($project, ProjectStatus::Archived))
        ->toThrow(InvalidProjectTransitionException::class);
});

test('archived is a final state — no transitions out', function () {
    $project = Project::factory()->forUser($this->user)->archived()->create();
    foreach ([ProjectStatus::Active, ProjectStatus::Completed] as $target) {
        expect(fn () => $this->action->execute($project, $target))
            ->toThrow(InvalidProjectTransitionException::class);
    }
});

test('exception message contains from and to status', function () {
    $project = Project::factory()->forUser($this->user)->archived()->create();
    expect(fn () => $this->action->execute($project, ProjectStatus::Active))
        ->toThrow(InvalidProjectTransitionException::class, 'archived');
});
