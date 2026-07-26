<?php

use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Models\User;
use App\Policies\ProjectPolicy;

beforeEach(function () {
    $this->policy = new ProjectPolicy;
    $this->user = User::factory()->create();
    $this->other = User::factory()->create();
});

test('viewAny always returns true', function () {
    expect($this->policy->viewAny($this->user))->toBeTrue();
});

test('create always returns true', function () {
    expect($this->policy->create($this->user))->toBeTrue();
});

test('user can view their own project', function () {
    $project = Project::factory()->forUser($this->user)->create();
    expect($this->policy->view($this->user, $project))->toBeTrue();
});

test('user cannot view another users project', function () {
    $project = Project::factory()->forUser($this->other)->create();
    expect($this->policy->view($this->user, $project))->toBeFalse();
});

test('user can update their own active project', function () {
    $project = Project::factory()->forUser($this->user)->active()->create();
    expect($this->policy->update($this->user, $project))->toBeTrue();
});

test('user can update their own completed project', function () {
    $project = Project::factory()->forUser($this->user)->completed()->create();
    expect($this->policy->update($this->user, $project))->toBeTrue();
});

test('user cannot update an archived project', function () {
    $project = Project::factory()->forUser($this->user)->archived()->create();
    expect($this->policy->update($this->user, $project))->toBeFalse();
});

test('user cannot update another users project', function () {
    $project = Project::factory()->forUser($this->other)->active()->create();
    expect($this->policy->update($this->user, $project))->toBeFalse();
});

test('user can delete their own project', function () {
    $project = Project::factory()->forUser($this->user)->create();
    expect($this->policy->delete($this->user, $project))->toBeTrue();
});

test('user cannot delete another users project', function () {
    $project = Project::factory()->forUser($this->other)->create();
    expect($this->policy->delete($this->user, $project))->toBeFalse();
});

test('user can archive their own active project', function () {
    $project = Project::factory()->forUser($this->user)->active()->create();
    expect($this->policy->archive($this->user, $project))->toBeTrue();
});

test('user cannot archive an already archived project', function () {
    $project = Project::factory()->forUser($this->user)->archived()->create();
    expect($this->policy->archive($this->user, $project))->toBeFalse();
});

test('user cannot archive another users project', function () {
    $project = Project::factory()->forUser($this->other)->active()->create();
    expect($this->policy->archive($this->user, $project))->toBeFalse();
});
