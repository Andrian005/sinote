<?php

use App\Domain\Inbox\Contracts\CreatesNoteFromInbox;
use App\Domain\Inbox\Contracts\CreatesTaskFromInbox;
use App\Domain\Inbox\Enums\InboxItemStatus;
use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;
use App\Livewire\Inbox\InboxList;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

// ---------------------------------------------------------------------------
// List / visibility
// ---------------------------------------------------------------------------

test('user dapat melihat daftar InboxItem unprocessed miliknya', function () {
    $items = InboxItem::factory()->count(3)->forUser($this->user)->unprocessed()->create();

    actingAs($this->user);

    $component = Livewire::test(InboxList::class);
    $inboxItems = $component->get('inboxItems');

    expect($inboxItems)->toHaveCount(3);
});

test('user tidak dapat melihat InboxItem milik user lain', function () {
    InboxItem::factory()->count(3)->forUser($this->otherUser)->unprocessed()->create();
    InboxItem::factory()->count(2)->forUser($this->user)->unprocessed()->create();

    actingAs($this->user);

    $inboxItems = Livewire::test(InboxList::class)->get('inboxItems');

    expect($inboxItems)->toHaveCount(2);
});

test('InboxItem yang sudah processed tidak muncul di list', function () {
    InboxItem::factory()->forUser($this->user)->unprocessed()->create();
    InboxItem::factory()->forUser($this->user)->processed()->create();
    InboxItem::factory()->forUser($this->user)->discarded()->create();

    actingAs($this->user);

    $inboxItems = Livewire::test(InboxList::class)->get('inboxItems');

    // Hanya yang unprocessed yang tampil
    expect($inboxItems)->toHaveCount(1);
});

test('list diurutkan terbaru di atas', function () {
    $first = InboxItem::factory()->forUser($this->user)->unprocessed()->create(['created_at' => now()->subHour()]);
    $second = InboxItem::factory()->forUser($this->user)->unprocessed()->create(['created_at' => now()]);

    actingAs($this->user);

    $inboxItems = Livewire::test(InboxList::class)->get('inboxItems');

    expect($inboxItems->first()->id)->toBe($second->id);
});

// ---------------------------------------------------------------------------
// Discard
// ---------------------------------------------------------------------------

test('user dapat discard InboxItem miliknya', function () {
    $item = InboxItem::factory()->forUser($this->user)->unprocessed()->create();

    actingAs($this->user);

    Livewire::test(InboxList::class)
        ->call('discard', $item->id);

    expect($item->fresh()->status)->toBe(InboxItemStatus::Discarded);
});

test('flash muncul setelah discard berhasil', function () {
    $item = InboxItem::factory()->forUser($this->user)->unprocessed()->create();

    actingAs($this->user);

    Livewire::test(InboxList::class)
        ->call('discard', $item->id)
        ->assertSet('flash', 'Item berhasil dihapus dari Inbox.');
});

test('user tidak dapat discard InboxItem milik user lain', function () {
    $item = InboxItem::factory()->forUser($this->otherUser)->unprocessed()->create();

    actingAs($this->user);

    Livewire::test(InboxList::class)
        ->call('discard', $item->id);

    // Status tidak berubah
    expect($item->fresh()->status)->toBe(InboxItemStatus::Unprocessed);
});

test('flash error muncul ketika discard InboxItem milik user lain', function () {
    $item = InboxItem::factory()->forUser($this->otherUser)->unprocessed()->create();

    actingAs($this->user);

    Livewire::test(InboxList::class)
        ->call('discard', $item->id)
        ->assertSet('flashIsError', true);
});

// ---------------------------------------------------------------------------
// Triage — via mock contracts
// ---------------------------------------------------------------------------

/**
 * Helper: bind mock contracts into the container for triage tests.
 * Returns a fake Model (Mockery mock) that acts as the created entity.
 */
function bindTriageMocks(?string $returnId = 'fake-id-001'): Model
{
    $fakeEntity = Mockery::mock(Model::class);
    $fakeEntity->shouldReceive('getKey')->andReturn($returnId);

    $mockCreateTask = Mockery::mock(CreatesTaskFromInbox::class);
    $mockCreateTask->shouldReceive('execute')->andReturn($fakeEntity);

    $mockCreateNote = Mockery::mock(CreatesNoteFromInbox::class);
    $mockCreateNote->shouldReceive('execute')->andReturn($fakeEntity);

    app()->instance(CreatesTaskFromInbox::class, $mockCreateTask);
    app()->instance(CreatesNoteFromInbox::class, $mockCreateNote);

    return $fakeEntity;
}

test('user dapat mentriage InboxItem ke Task', function () {
    bindTriageMocks('task-fake-001');

    $item = InboxItem::factory()->forUser($this->user)->unprocessed()->create();

    actingAs($this->user);

    Livewire::test(InboxList::class)
        ->call('triage', $item->id, 'task');

    expect($item->fresh()->status)->toBe(InboxItemStatus::Processed)
        ->and($item->fresh()->converted_to_type)->toBe('task');
});

test('user dapat mentriage InboxItem ke Note', function () {
    bindTriageMocks('note-fake-001');

    $item = InboxItem::factory()->forUser($this->user)->unprocessed()->create();

    actingAs($this->user);

    Livewire::test(InboxList::class)
        ->call('triage', $item->id, 'note');

    expect($item->fresh()->status)->toBe(InboxItemStatus::Processed)
        ->and($item->fresh()->converted_to_type)->toBe('note');
});

test('flash muncul setelah triage berhasil', function () {
    bindTriageMocks();

    $item = InboxItem::factory()->forUser($this->user)->unprocessed()->create();

    actingAs($this->user);

    Livewire::test(InboxList::class)
        ->call('triage', $item->id, 'task')
        ->assertSet('flash', 'Item berhasil dikonversi menjadi Task.');
});

test('user tidak dapat mentriage InboxItem milik user lain', function () {
    bindTriageMocks();

    $item = InboxItem::factory()->forUser($this->otherUser)->unprocessed()->create();

    actingAs($this->user);

    Livewire::test(InboxList::class)
        ->call('triage', $item->id, 'task');

    // Status tidak berubah
    expect($item->fresh()->status)->toBe(InboxItemStatus::Unprocessed);
});

test('flash error muncul ketika triage InboxItem milik user lain', function () {
    bindTriageMocks();

    $item = InboxItem::factory()->forUser($this->otherUser)->unprocessed()->create();

    actingAs($this->user);

    Livewire::test(InboxList::class)
        ->call('triage', $item->id, 'task')
        ->assertSet('flashIsError', true);
});

// ---------------------------------------------------------------------------
// Pagination
// ---------------------------------------------------------------------------

test('pagination bekerja dengan lebih dari 10 item', function () {
    InboxItem::factory()->count(15)->forUser($this->user)->unprocessed()->create();

    actingAs($this->user);

    $inboxItems = Livewire::test(InboxList::class)->get('inboxItems');

    // Halaman pertama hanya 10 item
    expect($inboxItems)->toHaveCount(10)
        ->and($inboxItems->total())->toBe(15);
});
