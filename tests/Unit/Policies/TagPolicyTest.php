<?php

use App\Domain\Shared\Models\Tag;
use App\Domain\Shared\Models\User;
use App\Policies\TagPolicy;

beforeEach(function () {
    $this->policy = new TagPolicy;
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

test('user can view their own tag', function () {
    $tag = Tag::factory()->forUser($this->user)->create();

    expect($this->policy->view($this->user, $tag))->toBeTrue();
});

test('user cannot view another users tag', function () {
    $tag = Tag::factory()->forUser($this->otherUser)->create();

    expect($this->policy->view($this->user, $tag))->toBeFalse();
});

test('user can update their own tag', function () {
    $tag = Tag::factory()->forUser($this->user)->create();

    expect($this->policy->update($this->user, $tag))->toBeTrue();
});

test('user cannot update another users tag', function () {
    $tag = Tag::factory()->forUser($this->otherUser)->create();

    expect($this->policy->update($this->user, $tag))->toBeFalse();
});

test('user can delete their own tag', function () {
    $tag = Tag::factory()->forUser($this->user)->create();

    expect($this->policy->delete($this->user, $tag))->toBeTrue();
});

test('user cannot delete another users tag', function () {
    $tag = Tag::factory()->forUser($this->otherUser)->create();

    expect($this->policy->delete($this->user, $tag))->toBeFalse();
});
