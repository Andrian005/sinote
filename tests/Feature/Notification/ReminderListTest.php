<?php

use App\Domain\Notification\Models\Reminder;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;
use App\Livewire\Notification\ReminderList;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->other = User::factory()->create();
});

// ---------------------------------------------------------------------------
// Visibility — own reminders
// ---------------------------------------------------------------------------

test('user melihat reminder scheduled miliknya yang sudah tiba', function () {
    $task = Task::factory()->forUser($this->user)->create();

    Reminder::factory()->forUser($this->user)->forTask($task)->scheduled()->dueToday()->create();

    actingAs($this->user);

    $reminders = Livewire::test(ReminderList::class)->get('reminders');

    expect($reminders)->toHaveCount(1);
});

test('user tidak melihat reminder milik user lain', function () {
    $task = Task::factory()->forUser($this->other)->create();

    Reminder::factory()->forUser($this->other)->forTask($task)->scheduled()->dueToday()->create();

    actingAs($this->user);

    $reminders = Livewire::test(ReminderList::class)->get('reminders');

    expect($reminders)->toHaveCount(0);
});

// ---------------------------------------------------------------------------
// Status filtering — only scheduled pending delivery
// ---------------------------------------------------------------------------

test('reminder sent tidak muncul', function () {
    $task = Task::factory()->forUser($this->user)->create();

    Reminder::factory()->forUser($this->user)->forTask($task)->sent()
        ->create(['scheduled_at' => now()->subHour()]);

    actingAs($this->user);

    $reminders = Livewire::test(ReminderList::class)->get('reminders');

    expect($reminders)->toHaveCount(0);
});

test('reminder cancelled tidak muncul', function () {
    $task = Task::factory()->forUser($this->user)->create();

    Reminder::factory()->forUser($this->user)->forTask($task)->cancelled()
        ->create(['scheduled_at' => now()->subHour()]);

    actingAs($this->user);

    $reminders = Livewire::test(ReminderList::class)->get('reminders');

    expect($reminders)->toHaveCount(0);
});

test('reminder skipped tidak muncul', function () {
    $task = Task::factory()->forUser($this->user)->create();

    Reminder::factory()->forUser($this->user)->forTask($task)->skipped()
        ->create(['scheduled_at' => now()->subHour()]);

    actingAs($this->user);

    $reminders = Livewire::test(ReminderList::class)->get('reminders');

    expect($reminders)->toHaveCount(0);
});

// ---------------------------------------------------------------------------
// Time filtering — future reminders must not appear
// ---------------------------------------------------------------------------

test('reminder future (scheduled_at > now) tidak muncul', function () {
    $task = Task::factory()->forUser($this->user)->create();

    // scheduled_at = 2 days from now — not yet due
    Reminder::factory()->forUser($this->user)->forTask($task)->scheduled()
        ->create(['scheduled_at' => now()->addDays(2)]);

    actingAs($this->user);

    $reminders = Livewire::test(ReminderList::class)->get('reminders');

    expect($reminders)->toHaveCount(0);
});

// ---------------------------------------------------------------------------
// Empty state
// ---------------------------------------------------------------------------

test('empty state muncul jika tidak ada reminder aktif', function () {
    actingAs($this->user);

    Livewire::test(ReminderList::class)
        ->assertSee('Tidak ada reminder aktif saat ini.');
});

// ---------------------------------------------------------------------------
// Limit mode
// ---------------------------------------------------------------------------

test('limit mode membatasi jumlah reminder yang ditampilkan', function () {
    $task = Task::factory()->forUser($this->user)->create();

    // Create 5 pending reminders — all due now or in the past
    Reminder::factory()->count(5)->forUser($this->user)->forTask($task)->scheduled()
        ->create(['scheduled_at' => now()->subMinutes(10)]);

    actingAs($this->user);

    $reminders = Livewire::test(ReminderList::class, ['limit' => 3])->get('reminders');

    expect($reminders)->toHaveCount(3);
});
