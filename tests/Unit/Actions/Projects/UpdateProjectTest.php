<?php

use App\Domain\Projects\Actions\UpdateProject;
use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new UpdateProject;
});

test('can update project title', function () {
    $project = Project::factory()->forUser($this->user)->create(['title' => 'Old']);
    $updated = $this->action->execute($project, ['title' => 'New title']);
    expect($updated->title)->toBe('New title');
});

test('title is trimmed on update', function () {
    $project = Project::factory()->forUser($this->user)->create();
    $updated = $this->action->execute($project, ['title' => '  trimmed  ']);
    expect($updated->title)->toBe('trimmed');
});

test('status cannot be changed via UpdateProject (silently stripped)', function () {
    $project = Project::factory()->forUser($this->user)->active()->create();
    $updated = $this->action->execute($project, ['title' => 'Same', 'status' => 'archived']);
    expect($updated->status)->toBe(ProjectStatus::Active);
});

test('progress cannot be changed via UpdateProject (silently stripped)', function () {
    $project = Project::factory()->forUser($this->user)->withProgress(0)->create();
    $updated = $this->action->execute($project, ['title' => 'Same', 'progress' => 100]);
    expect($updated->progress)->toBe(0);
});

test('can update description', function () {
    $project = Project::factory()->forUser($this->user)->create();
    $updated = $this->action->execute($project, ['description' => 'New desc']);
    expect($updated->description)->toBe('New desc');
});
