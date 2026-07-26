<?php

use App\Domain\Inbox\Actions\DiscardInboxItem;
use App\Domain\Inbox\Enums\InboxItemStatus;
use App\Domain\Inbox\Exceptions\InboxItemAlreadyProcessedException;
use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->action = new DiscardInboxItem;
});

test('can discard an unprocessed InboxItem', function () {
    $item = InboxItem::factory()->forUser($this->user)->unprocessed()->create();

    $result = $this->action->execute($item);

    expect($result)->toBeTrue();
});

test('status changes to discarded after discard', function () {
    $item = InboxItem::factory()->forUser($this->user)->unprocessed()->create();

    $this->action->execute($item);

    expect($item->fresh()->status)->toBe(InboxItemStatus::Discarded);

    assertDatabaseHas('inbox_items', [
        'id' => $item->id,
        'status' => InboxItemStatus::Discarded->value,
    ]);
});

test('processed_at is set after discard', function () {
    $item = InboxItem::factory()->forUser($this->user)->unprocessed()->create();

    $this->action->execute($item);

    expect($item->fresh()->processed_at)->not->toBeNull();
});

test('throws exception when discarding an already processed item', function () {
    $item = InboxItem::factory()->forUser($this->user)->processed()->create();

    expect(fn () => $this->action->execute($item))
        ->toThrow(InboxItemAlreadyProcessedException::class);
});

test('throws exception when discarding an already discarded item', function () {
    $item = InboxItem::factory()->forUser($this->user)->discarded()->create();

    expect(fn () => $this->action->execute($item))
        ->toThrow(InboxItemAlreadyProcessedException::class);
});

test('exception message contains the current status', function () {
    $item = InboxItem::factory()->forUser($this->user)->processed()->create();

    expect(fn () => $this->action->execute($item))
        ->toThrow(
            InboxItemAlreadyProcessedException::class,
            'processed'
        );
});
