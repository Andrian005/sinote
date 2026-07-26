<?php

use App\Domain\Projects\Enums\GoalStatus;
use App\Domain\Projects\Models\Goal;
use App\Domain\Shared\Models\User;
use App\Livewire\Goals\GoalList;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->other = User::factory()->create();
});

// ---------------------------------------------------------------------------
// Visibility
// ---------------------------------------------------------------------------

test('user melihat Goal miliknya dengan filter active', function () {
    Goal::factory()->count(3)->forUser($this->user)->active()->create();

    actingAs($this->user);

    expect(Livewire::test(GoalList::class)->get('goals'))->toHaveCount(3);
});

test('user tidak melihat Goal milik user lain', function () {
    Goal::factory()->count(4)->forUser($this->other)->active()->create();
    Goal::factory()->count(2)->forUser($this->user)->active()->create();

    actingAs($this->user);

    expect(Livewire::test(GoalList::class)->get('goals'))->toHaveCount(2);
});

test('Goal completed dan archived tidak muncul di filter active', function () {
    Goal::factory()->forUser($this->user)->active()->create();
    Goal::factory()->forUser($this->user)->completed()->create();
    Goal::factory()->forUser($this->user)->archived()->create();

    actingAs($this->user);

    expect(Livewire::test(GoalList::class, ['filter' => 'active'])->get('goals'))->toHaveCount(1);
});

// ---------------------------------------------------------------------------
// updateStatus
// ---------------------------------------------------------------------------

test('user dapat menyelesaikan Goal aktif miliknya', function () {
    $goal = Goal::factory()->forUser($this->user)->active()->create();

    actingAs($this->user);

    Livewire::test(GoalList::class)->call('updateStatus', $goal->id, 'completed');

    expect($goal->fresh()->status)->toBe(GoalStatus::Completed);
});

test('user dapat reopen Goal completed miliknya', function () {
    $goal = Goal::factory()->forUser($this->user)->completed()->create();

    actingAs($this->user);

    Livewire::test(GoalList::class, ['filter' => 'completed'])
        ->call('updateStatus', $goal->id, 'active');

    expect($goal->fresh()->status)->toBe(GoalStatus::Active);
});

test('flash sukses muncul setelah updateStatus berhasil', function () {
    $goal = Goal::factory()->forUser($this->user)->active()->create();

    actingAs($this->user);

    Livewire::test(GoalList::class)
        ->call('updateStatus', $goal->id, 'completed')
        ->assertSet('flash', 'Status goal berhasil diperbarui.');
});

// ---------------------------------------------------------------------------
// archive
// ---------------------------------------------------------------------------

test('user dapat arsipkan Goal aktif miliknya', function () {
    $goal = Goal::factory()->forUser($this->user)->active()->create();

    actingAs($this->user);

    Livewire::test(GoalList::class)->call('archive', $goal->id);

    expect($goal->fresh()->status)->toBe(GoalStatus::Archived);
});

test('flash sukses muncul setelah archive berhasil', function () {
    $goal = Goal::factory()->forUser($this->user)->active()->create();

    actingAs($this->user);

    Livewire::test(GoalList::class)
        ->call('archive', $goal->id)
        ->assertSet('flash', 'Goal berhasil diarsipkan.');
});

// ---------------------------------------------------------------------------
// Authorization
// ---------------------------------------------------------------------------

test('user tidak dapat update status Goal milik user lain', function () {
    $goal = Goal::factory()->forUser($this->other)->active()->create();

    actingAs($this->user);

    Livewire::test(GoalList::class)->call('updateStatus', $goal->id, 'completed');

    expect($goal->fresh()->status)->toBe(GoalStatus::Active);
});

test('flash error muncul ketika update status Goal milik user lain', function () {
    $goal = Goal::factory()->forUser($this->other)->active()->create();

    actingAs($this->user);

    Livewire::test(GoalList::class)
        ->call('updateStatus', $goal->id, 'completed')
        ->assertSet('flashIsError', true);
});

test('user tidak dapat arsipkan Goal milik user lain', function () {
    $goal = Goal::factory()->forUser($this->other)->active()->create();

    actingAs($this->user);

    Livewire::test(GoalList::class)->call('archive', $goal->id);

    expect($goal->fresh()->status)->toBe(GoalStatus::Active);
});

// ---------------------------------------------------------------------------
// Pagination
// ---------------------------------------------------------------------------

test('pagination bekerja dengan lebih dari 10 goal', function () {
    Goal::factory()->count(15)->forUser($this->user)->active()->create();

    actingAs($this->user);

    $goals = Livewire::test(GoalList::class)->get('goals');

    expect($goals)->toHaveCount(10)->and($goals->total())->toBe(15);
});
