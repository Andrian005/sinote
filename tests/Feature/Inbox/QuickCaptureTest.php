<?php

use App\Domain\Inbox\Enums\InboxItemStatus;
use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;
use App\Livewire\Inbox\QuickCapture;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
});

// ---------------------------------------------------------------------------
// Happy path
// ---------------------------------------------------------------------------

test('user dapat membuat InboxItem via Quick Capture', function () {
    actingAs($this->user);

    Livewire::test(QuickCapture::class)
        ->set('content', 'Ide video baru tentang Livewire')
        ->call('save');

    assertDatabaseHas('inbox_items', [
        'user_id' => $this->user->id,
        'content' => 'Ide video baru tentang Livewire',
    ]);
});

test('InboxItem tersimpan dengan status unprocessed', function () {
    actingAs($this->user);

    Livewire::test(QuickCapture::class)
        ->set('content', 'Sebuah catatan penting')
        ->call('save');

    $item = InboxItem::where('user_id', $this->user->id)->first();

    expect($item->status)->toBe(InboxItemStatus::Unprocessed);
});

test('form direset setelah save berhasil', function () {
    actingAs($this->user);

    Livewire::test(QuickCapture::class)
        ->set('content', 'Isi sebelum save')
        ->call('save')
        ->assertSet('content', '');
});

test('flash saved menjadi true setelah save berhasil', function () {
    actingAs($this->user);

    Livewire::test(QuickCapture::class)
        ->set('content', 'Konten valid')
        ->call('save')
        ->assertSet('saved', true);
});

// ---------------------------------------------------------------------------
// Validasi
// ---------------------------------------------------------------------------

test('validasi error muncul jika content kosong', function () {
    actingAs($this->user);

    Livewire::test(QuickCapture::class)
        ->set('content', '')
        ->call('save')
        ->assertHasErrors(['content' => 'required']);

    assertDatabaseCount('inbox_items', 0);
});

test('validasi error muncul jika content hanya spasi', function () {
    actingAs($this->user);

    Livewire::test(QuickCapture::class)
        ->set('content', '   ')
        ->call('save')
        ->assertHasErrors(['content']);

    assertDatabaseCount('inbox_items', 0);
});

test('validasi error muncul jika content melebihi 5000 karakter', function () {
    actingAs($this->user);

    Livewire::test(QuickCapture::class)
        ->set('content', str_repeat('a', 5001))
        ->call('save')
        ->assertHasErrors(['content' => 'max']);

    assertDatabaseCount('inbox_items', 0);
});

test('content tepat 5000 karakter lolos validasi', function () {
    actingAs($this->user);

    Livewire::test(QuickCapture::class)
        ->set('content', str_repeat('a', 5000))
        ->call('save')
        ->assertHasNoErrors();

    assertDatabaseCount('inbox_items', 1);
});

// ---------------------------------------------------------------------------
// Isolasi user
// ---------------------------------------------------------------------------

test('InboxItem disimpan hanya untuk user yang sedang login', function () {
    $otherUser = User::factory()->create();

    actingAs($this->user);

    Livewire::test(QuickCapture::class)
        ->set('content', 'Hanya untuk user saya')
        ->call('save');

    // Item dimiliki user yang login
    assertDatabaseHas('inbox_items', ['user_id' => $this->user->id]);

    // Bukan milik user lain
    expect(
        InboxItem::where('user_id', $otherUser->id)->count()
    )->toBe(0);
});
