<?php

use App\Domain\Notification\Models\Reminder;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;
use App\Livewire\Dashboard\DashboardToday;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->other = User::factory()->create();
});

test('stats bar menampilkan hitungan reminder aktif milik user', function () {
    $task = Task::factory()->forUser($this->user)->create();

    Reminder::factory()->count(2)->forUser($this->user)->forTask($task)->scheduled()
        ->create(['scheduled_at' => now()->subMinutes(5)]);

    actingAs($this->user);

    $count = Livewire::test(DashboardToday::class)->get('remindersCount');

    expect($count)->toBe(2);
});

test('reminder milik user lain tidak dihitung di stats', function () {
    $task = Task::factory()->forUser($this->other)->create();

    Reminder::factory()->count(3)->forUser($this->other)->forTask($task)->scheduled()
        ->create(['scheduled_at' => now()->subMinutes(5)]);

    actingAs($this->user);

    $count = Livewire::test(DashboardToday::class)->get('remindersCount');

    expect($count)->toBe(0);
});

test('reminder future tidak dihitung di stats', function () {
    $task = Task::factory()->forUser($this->user)->create();

    // scheduled_at 2 days ahead — not yet pending delivery
    Reminder::factory()->forUser($this->user)->forTask($task)->scheduled()
        ->create(['scheduled_at' => now()->addDays(2)]);

    actingAs($this->user);

    $count = Livewire::test(DashboardToday::class)->get('remindersCount');

    expect($count)->toBe(0);
});
