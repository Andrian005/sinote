<?php

use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Models\Task;
use App\Livewire\Tasks\TaskForm;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->other = User::factory()->create();
});

// ---------------------------------------------------------------------------
// Create
// ---------------------------------------------------------------------------

test('user dapat membuat Task baru', function () {
    actingAs($this->user);

    Livewire::test(TaskForm::class)
        ->set('title', 'Belajar Livewire')
        ->call('save');

    assertDatabaseHas('tasks', [
        'user_id' => $this->user->id,
        'title' => 'Belajar Livewire',
    ]);
});

test('Task baru tersimpan dengan status todo', function () {
    actingAs($this->user);

    Livewire::test(TaskForm::class)
        ->set('title', 'Task baru')
        ->call('save');

    $task = Task::where('user_id', $this->user->id)->first();

    expect($task->status)->toBe(TaskStatus::Todo);
});

test('form direset setelah save berhasil', function () {
    actingAs($this->user);

    Livewire::test(TaskForm::class)
        ->set('title', 'Akan direset')
        ->call('save')
        ->assertSet('title', '');
});

test('saved flag menjadi true setelah create berhasil', function () {
    actingAs($this->user);

    Livewire::test(TaskForm::class)
        ->set('title', 'Test flash')
        ->call('save')
        ->assertSet('saved', true);
});

// ---------------------------------------------------------------------------
// Validasi
// ---------------------------------------------------------------------------

test('validasi error jika title kosong', function () {
    actingAs($this->user);

    Livewire::test(TaskForm::class)
        ->set('title', '')
        ->call('save')
        ->assertHasErrors(['title' => 'required']);
});

test('validasi error jika title melebihi 255 karakter', function () {
    actingAs($this->user);

    Livewire::test(TaskForm::class)
        ->set('title', str_repeat('x', 256))
        ->call('save')
        ->assertHasErrors(['title' => 'max']);
});

// ---------------------------------------------------------------------------
// Edit
// ---------------------------------------------------------------------------

test('user dapat update Task miliknya', function () {
    $task = Task::factory()->forUser($this->user)->withTitle('Judul lama')->create();

    actingAs($this->user);

    Livewire::test(TaskForm::class, ['taskId' => $task->id])
        ->set('title', 'Judul baru')
        ->call('save');

    assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Judul baru',
    ]);
});

test('user tidak dapat update Task milik user lain', function () {
    $task = Task::factory()->forUser($this->other)->withTitle('Punya orang lain')->create();

    actingAs($this->user);

    Livewire::test(TaskForm::class, ['taskId' => $task->id])
        ->set('title', 'Dicuri')
        ->call('save');

    // Judul tetap tidak berubah
    assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Punya orang lain',
    ]);
});
