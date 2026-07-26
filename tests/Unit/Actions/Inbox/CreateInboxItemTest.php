<?php

use App\Domain\Inbox\Actions\CreateInboxItem;
use App\Domain\Inbox\Enums\InboxItemStatus;
use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new CreateInboxItem;
});

test('creates a new InboxItem with the given content', function () {
    $item = $this->action->execute($this->user, 'Buy groceries');

    expect($item)->toBeInstanceOf(InboxItem::class)
        ->and($item->content)->toBe('Buy groceries');

    assertDatabaseHas('inbox_items', [
        'id' => $item->id,
        'content' => 'Buy groceries',
    ]);
});

test('new InboxItem always has status unprocessed', function () {
    $item = $this->action->execute($this->user, 'Some thought');

    expect($item->status)->toBe(InboxItemStatus::Unprocessed);
});

test('new InboxItem is associated with the correct user', function () {
    $item = $this->action->execute($this->user, 'My idea');

    expect($item->user_id)->toBe($this->user->id);
});

test('content is trimmed before persisting', function () {
    $item = $this->action->execute($this->user, '  padded content  ');

    expect($item->content)->toBe('padded content');

    assertDatabaseHas('inbox_items', [
        'id' => $item->id,
        'content' => 'padded content',
    ]);
});

test('processed_at is null on a newly created item', function () {
    $item = $this->action->execute($this->user, 'Fresh capture');

    expect($item->processed_at)->toBeNull();
});

test('different users can capture items independently', function () {
    $otherUser = User::factory()->create();

    $item1 = $this->action->execute($this->user, 'User 1 idea');
    $item2 = $this->action->execute($otherUser, 'User 2 idea');

    expect($item1->user_id)->toBe($this->user->id)
        ->and($item2->user_id)->toBe($otherUser->id)
        ->and($item1->id)->not->toBe($item2->id);
});
