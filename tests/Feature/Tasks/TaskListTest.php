<?php

use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Models\Task;
use App\Livewire\Tasks\TaskList;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->other = User::factory()->create();
});

// ---------------------------------------------------------------------------
// Visibility
// ---------------------------------------------------------------------------

test('user melihat Task miliknya dengan filter active', function () {
    Task::factory()->count(3)->forUser($this->user)->todo()->create();

    actingAs($this->user);

    $tasks = Livewire::test(TaskList::class)->get('tasks');

    expect($tasks)->toHaveCount(3);
});

test('user tidak melihat Task milik user lain', function () {
    Task::factory()->count(4)->forUser($this->other)->todo()->create();
    Task::factory()->count(2)->forUser($this->user)->todo()->create();

    actingAs($this->user);

    $tasks = Livewire::test(TaskList::class)->get('tasks');

    expect($tasks)->toHaveCount(2);
});

test('Task done dan archived tidak muncul di filter active', function () {
    Task::factory()->forUser($this->user)->todo()->create();
    Task::factory()->forUser($this->user)->done()->create();
    Task::factory()->forUser($this->user)->archived()->create();

    actingAs($this->user);

    $tasks = Livewire::test(TaskList::class, ['filter' => 'active'])->get('tasks');

    expect($tasks)->toHaveCount(1);
});

// ---------------------------------------------------------------------------
// Status transitions
// ---------------------------------------------------------------------------

test('user dapat ubah status todo → in_progress', function () {
    $task = Task::factory()->forUser($this->user)->todo()->create();

    actingAs($this->user);

    Livewire::test(TaskList::class)
        ->call('updateStatus', $task->id, 'in_progress');

    expect($task->fresh()->status)->toBe(TaskStatus::InProgress);
});

test('user dapat ubah status in_progress → done', function () {
    $task = Task::factory()->forUser($this->user)->inProgress()->create();

    actingAs($this->user);

    Livewire::test(TaskList::class)
        ->call('updateStatus', $task->id, 'done');

    expect($task->fresh()->status)->toBe(TaskStatus::Done);
});

test('user dapat reopen done → todo', function () {
    $task = Task::factory()->forUser($this->user)->done()->create();

    actingAs($this->user);

    Livewire::test(TaskList::class)
        ->call('updateStatus', $task->id, 'todo');

    expect($task->fresh()->status)->toBe(TaskStatus::Todo);
});

test('user dapat arsipkan Task', function () {
    $task = Task::factory()->forUser($this->user)->todo()->create();

    actingAs($this->user);

    Livewire::test(TaskList::class)
        ->call('archive', $task->id);

    expect($task->fresh()->status)->toBe(TaskStatus::Archived);
});

// ---------------------------------------------------------------------------
// Authorization
// ---------------------------------------------------------------------------

test('user tidak dapat update status Task milik user lain', function () {
    $task = Task::factory()->forUser($this->other)->todo()->create();

    actingAs($this->user);

    Livewire::test(TaskList::class)
        ->call('updateStatus', $task->id, 'in_progress');

    // Status tidak berubah
    expect($task->fresh()->status)->toBe(TaskStatus::Todo);
});

test('flash error muncul ketika update status Task milik user lain', function () {
    $task = Task::factory()->forUser($this->other)->todo()->create();

    actingAs($this->user);

    Livewire::test(TaskList::class)
        ->call('updateStatus', $task->id, 'in_progress')
        ->assertSet('flashIsError', true);
});

// ---------------------------------------------------------------------------
// Flash
// ---------------------------------------------------------------------------

test('flash sukses muncul setelah update status berhasil', function () {
    $task = Task::factory()->forUser($this->user)->todo()->create();

    actingAs($this->user);

    Livewire::test(TaskList::class)
        ->call('updateStatus', $task->id, 'in_progress')
        ->assertSet('flash', 'Status task berhasil diperbarui.');
});

test('flash sukses muncul setelah arsip berhasil', function () {
    $task = Task::factory()->forUser($this->user)->todo()->create();

    actingAs($this->user);

    Livewire::test(TaskList::class)
        ->call('archive', $task->id)
        ->assertSet('flash', 'Task berhasil diarsipkan.');
});

// ---------------------------------------------------------------------------
// Pagination
// ---------------------------------------------------------------------------

test('pagination bekerja dengan lebih dari 15 item', function () {
    Task::factory()->count(20)->forUser($this->user)->todo()->create();

    actingAs($this->user);

    $tasks = Livewire::test(TaskList::class)->get('tasks');

    expect($tasks)->toHaveCount(15)
        ->and($tasks->total())->toBe(20);
});

// ---------------------------------------------------------------------------
// Widget mode (limit)
// ---------------------------------------------------------------------------

test('widget mode membatasi jumlah task yang ditampilkan', function () {
    Task::factory()->count(10)->forUser($this->user)->todo()->create();

    actingAs($this->user);

    $tasks = Livewire::test(TaskList::class, ['limit' => 5])->get('tasks');

    expect($tasks)->toHaveCount(5);
});
