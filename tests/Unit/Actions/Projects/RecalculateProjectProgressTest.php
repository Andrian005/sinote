<?php

use App\Domain\Projects\Actions\RecalculateProjectProgress;
use App\Domain\Projects\Actions\UpdateProjectStatus;
use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new RecalculateProjectProgress(new UpdateProjectStatus);
});

test('progress is 0 when no tasks exist', function () {
    $project = Project::factory()->forUser($this->user)->active()->create();

    $result = $this->action->execute($project);

    // No tasks — progress unchanged (stays 0)
    expect($result->progress)->toBe(0)
        ->and($result->status)->toBe(ProjectStatus::Active);
});

test('progress is 0 when all tasks are todo', function () {
    $project = Project::factory()->forUser($this->user)->active()->create();
    Task::factory()->count(3)->forUser($this->user)->todo()->create(['project_id' => $project->id]);

    $result = $this->action->execute($project);

    expect($result->progress)->toBe(0);
});

test('progress is 50 when half the tasks are done', function () {
    $project = Project::factory()->forUser($this->user)->active()->create();
    Task::factory()->count(2)->forUser($this->user)->done()->create(['project_id' => $project->id]);
    Task::factory()->count(2)->forUser($this->user)->todo()->create(['project_id' => $project->id]);

    $result = $this->action->execute($project);

    expect($result->progress)->toBe(50);
});

test('progress is 100 when all tasks are done', function () {
    $project = Project::factory()->forUser($this->user)->active()->create();
    Task::factory()->count(3)->forUser($this->user)->done()->create(['project_id' => $project->id]);

    $result = $this->action->execute($project);

    expect($result->progress)->toBe(100);
});

test('project auto-completes when progress reaches 100', function () {
    $project = Project::factory()->forUser($this->user)->active()->create();
    Task::factory()->count(3)->forUser($this->user)->done()->create(['project_id' => $project->id]);

    $result = $this->action->execute($project);

    expect($result->status)->toBe(ProjectStatus::Completed);
});

test('archived tasks are excluded from progress calculation', function () {
    $project = Project::factory()->forUser($this->user)->active()->create();
    Task::factory()->count(2)->forUser($this->user)->done()->create(['project_id' => $project->id]);
    // Archived task should NOT count in denominator
    Task::factory()->forUser($this->user)->archived()->create(['project_id' => $project->id]);

    $result = $this->action->execute($project);

    // 2 done / 2 total (archived excluded) = 100%
    expect($result->progress)->toBe(100);
});

test('already completed project is not re-completed', function () {
    $project = Project::factory()->forUser($this->user)->completed()->create();
    Task::factory()->count(2)->forUser($this->user)->done()->create(['project_id' => $project->id]);

    // Should not throw — just recalculates progress
    $result = $this->action->execute($project);

    expect($result->progress)->toBe(100);
});
