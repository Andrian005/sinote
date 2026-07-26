<?php

use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;
use App\Livewire\Dashboard\DashboardToday;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->other = User::factory()->create();
});

// ---------------------------------------------------------------------------
// Stats bar
// ---------------------------------------------------------------------------

test('stats menampilkan jumlah Task aktif yang benar', function () {
    Task::factory()->count(3)->forUser($this->user)->todo()->create();
    Task::factory()->count(2)->forUser($this->user)->inProgress()->create();
    Task::factory()->forUser($this->user)->done()->create(); // tidak dihitung

    actingAs($this->user);

    $stats = Livewire::test(DashboardToday::class)->get('stats');

    expect($stats['tasks'])->toBe(5);
});

test('stats menampilkan jumlah Project aktif yang benar', function () {
    Project::factory()->count(4)->forUser($this->user)->active()->create();
    Project::factory()->forUser($this->user)->archived()->create(); // tidak dihitung

    actingAs($this->user);

    $stats = Livewire::test(DashboardToday::class)->get('stats');

    expect($stats['projects'])->toBe(4);
});

test('stats menampilkan jumlah Inbox unprocessed yang benar', function () {
    InboxItem::factory()->count(3)->forUser($this->user)->unprocessed()->create();
    InboxItem::factory()->forUser($this->user)->processed()->create(); // tidak dihitung

    actingAs($this->user);

    $stats = Livewire::test(DashboardToday::class)->get('stats');

    expect($stats['inbox'])->toBe(3);
});

test('stats hanya menghitung data milik user yang login', function () {
    Task::factory()->count(5)->forUser($this->other)->todo()->create();
    Project::factory()->count(3)->forUser($this->other)->active()->create();

    actingAs($this->user);

    $stats = Livewire::test(DashboardToday::class)->get('stats');

    expect($stats['tasks'])->toBe(0)
        ->and($stats['projects'])->toBe(0);
});

// ---------------------------------------------------------------------------
// Today Tasks — visibility
// ---------------------------------------------------------------------------

test('Task overdue (past due_date + active) muncul di Today list', function () {
    Task::factory()->forUser($this->user)->todo()->overdue()->create();

    actingAs($this->user);

    $tasks = Livewire::test(DashboardToday::class)->get('todayTasks');

    expect($tasks)->toHaveCount(1);
});

test('Task due hari ini muncul di Today list', function () {
    Task::factory()->forUser($this->user)->todo()
        ->create(['due_date' => now()->toDateString()]);

    actingAs($this->user);

    $tasks = Livewire::test(DashboardToday::class)->get('todayTasks');

    expect($tasks)->toHaveCount(1);
});

test('Task due masa depan TIDAK muncul di Today list', function () {
    Task::factory()->forUser($this->user)->todo()
        ->create(['due_date' => now()->addDays(3)->toDateString()]);

    actingAs($this->user);

    $tasks = Livewire::test(DashboardToday::class)->get('todayTasks');

    expect($tasks)->toHaveCount(0);
});

test('Task tanpa due_date muncul di Today list', function () {
    Task::factory()->forUser($this->user)->todo()->create(['due_date' => null]);

    actingAs($this->user);

    $tasks = Livewire::test(DashboardToday::class)->get('todayTasks');

    expect($tasks)->toHaveCount(1);
});

test('Task done dan archived tidak muncul di Today list', function () {
    Task::factory()->forUser($this->user)->done()->create(['due_date' => now()->toDateString()]);
    Task::factory()->forUser($this->user)->archived()->create(['due_date' => now()->toDateString()]);

    actingAs($this->user);

    $tasks = Livewire::test(DashboardToday::class)->get('todayTasks');

    expect($tasks)->toHaveCount(0);
});

test('Task milik user lain tidak muncul di Today list', function () {
    Task::factory()->count(3)->forUser($this->other)->todo()->create(['due_date' => null]);

    actingAs($this->user);

    $tasks = Livewire::test(DashboardToday::class)->get('todayTasks');

    expect($tasks)->toHaveCount(0);
});

// ---------------------------------------------------------------------------
// Limit
// ---------------------------------------------------------------------------

test('Today list dibatasi maksimal 7 Task', function () {
    // 5 overdue + 5 null due_date = 10 total eligible, harus dipotong ke 7
    Task::factory()->count(5)->forUser($this->user)->todo()->overdue()->create();
    Task::factory()->count(5)->forUser($this->user)->todo()->create(['due_date' => null]);

    actingAs($this->user);

    $tasks = Livewire::test(DashboardToday::class)->get('todayTasks');

    expect($tasks)->toHaveCount(7);
});
