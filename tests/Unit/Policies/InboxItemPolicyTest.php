<?php

use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;
use App\Policies\InboxItemPolicy;

beforeEach(function () {
    $this->policy = new InboxItemPolicy;
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

// ---------------------------------------------------------------------------
// viewAny
// ---------------------------------------------------------------------------

test('viewAny always returns true for authenticated user', function () {
    expect($this->policy->viewAny($this->user))->toBeTrue();
});

// ---------------------------------------------------------------------------
// view
// ---------------------------------------------------------------------------

test('user can view their own InboxItem', function () {
    $item = InboxItem::factory()->forUser($this->user)->create();

    expect($this->policy->view($this->user, $item))->toBeTrue();
});

test('user cannot view another users InboxItem', function () {
    $item = InboxItem::factory()->forUser($this->otherUser)->create();

    expect($this->policy->view($this->user, $item))->toBeFalse();
});

// ---------------------------------------------------------------------------
// create
// ---------------------------------------------------------------------------

test('create always returns true for authenticated user', function () {
    expect($this->policy->create($this->user))->toBeTrue();
});

// ---------------------------------------------------------------------------
// update
// ---------------------------------------------------------------------------

test('user can update their own unprocessed InboxItem', function () {
    $item = InboxItem::factory()->forUser($this->user)->unprocessed()->create();

    expect($this->policy->update($this->user, $item))->toBeTrue();
});

test('user cannot update their own processed InboxItem', function () {
    $item = InboxItem::factory()->forUser($this->user)->processed()->create();

    expect($this->policy->update($this->user, $item))->toBeFalse();
});

test('user cannot update their own discarded InboxItem', function () {
    $item = InboxItem::factory()->forUser($this->user)->discarded()->create();

    expect($this->policy->update($this->user, $item))->toBeFalse();
});

test('user cannot update another users InboxItem', function () {
    $item = InboxItem::factory()->forUser($this->otherUser)->unprocessed()->create();

    expect($this->policy->update($this->user, $item))->toBeFalse();
});

// ---------------------------------------------------------------------------
// delete
// ---------------------------------------------------------------------------

test('user can delete their own InboxItem', function () {
    $item = InboxItem::factory()->forUser($this->user)->create();

    expect($this->policy->delete($this->user, $item))->toBeTrue();
});

test('user cannot delete another users InboxItem', function () {
    $item = InboxItem::factory()->forUser($this->otherUser)->create();

    expect($this->policy->delete($this->user, $item))->toBeFalse();
});

// ---------------------------------------------------------------------------
// triage
// ---------------------------------------------------------------------------

test('user can triage their own unprocessed InboxItem', function () {
    $item = InboxItem::factory()->forUser($this->user)->unprocessed()->create();

    expect($this->policy->triage($this->user, $item))->toBeTrue();
});

test('user cannot triage their own processed InboxItem', function () {
    $item = InboxItem::factory()->forUser($this->user)->processed()->create();

    expect($this->policy->triage($this->user, $item))->toBeFalse();
});

test('user cannot triage their own discarded InboxItem', function () {
    $item = InboxItem::factory()->forUser($this->user)->discarded()->create();

    expect($this->policy->triage($this->user, $item))->toBeFalse();
});

test('user cannot triage another users InboxItem', function () {
    $item = InboxItem::factory()->forUser($this->otherUser)->unprocessed()->create();

    expect($this->policy->triage($this->user, $item))->toBeFalse();
});
