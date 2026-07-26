<?php

use App\Domain\Projects\Enums\GoalStatus;
use App\Domain\Projects\Enums\GoalType;
use App\Domain\Projects\Models\Goal;
use App\Domain\Shared\Models\User;
use App\Livewire\Goals\GoalForm;
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

test('user dapat membuat Goal ongoing baru', function () {
    actingAs($this->user);

    Livewire::test(GoalForm::class)
        ->set('title', 'Rutin olahraga')
        ->set('goalType', 'ongoing')
        ->call('save');

    assertDatabaseHas('goals', [
        'user_id' => $this->user->id,
        'title' => 'Rutin olahraga',
        'goal_type' => 'ongoing',
    ]);
});

test('Goal baru selalu berstatus active', function () {
    actingAs($this->user);

    Livewire::test(GoalForm::class)
        ->set('title', 'Goal baru')
        ->set('goalType', 'ongoing')
        ->call('save');

    $goal = Goal::where('user_id', $this->user->id)->first();
    expect($goal->status)->toBe(GoalStatus::Active);
});

test('user dapat membuat Goal time_bound dengan target_date', function () {
    actingAs($this->user);

    $targetDate = now()->addMonths(3)->toDateString();

    Livewire::test(GoalForm::class)
        ->set('title', 'Lari 5K')
        ->set('goalType', 'time_bound')
        ->set('targetDate', $targetDate)
        ->call('save');

    assertDatabaseHas('goals', [
        'user_id' => $this->user->id,
        'goal_type' => 'time_bound',
    ]);

    $goal = Goal::where('user_id', $this->user->id)
        ->where('goal_type', 'time_bound')
        ->first();

    expect($goal)->not->toBeNull()
        ->and($goal->target_date->toDateString())->toBe($targetDate);
});

test('form direset setelah create berhasil', function () {
    actingAs($this->user);

    Livewire::test(GoalForm::class)
        ->set('title', 'Akan direset')
        ->set('goalType', 'ongoing')
        ->call('save')
        ->assertSet('title', '');
});

test('saved menjadi true setelah create berhasil', function () {
    actingAs($this->user);

    Livewire::test(GoalForm::class)
        ->set('title', 'Test flash')
        ->set('goalType', 'ongoing')
        ->call('save')
        ->assertSet('saved', true);
});

// ---------------------------------------------------------------------------
// Validasi
// ---------------------------------------------------------------------------

test('validasi error jika title kosong', function () {
    actingAs($this->user);

    Livewire::test(GoalForm::class)
        ->set('title', '')
        ->call('save')
        ->assertHasErrors(['title' => 'required']);
});

test('validasi error jika title lebih dari 255 karakter', function () {
    actingAs($this->user);

    Livewire::test(GoalForm::class)
        ->set('title', str_repeat('x', 256))
        ->call('save')
        ->assertHasErrors(['title' => 'max']);
});

// ---------------------------------------------------------------------------
// Edit — goal_type immutable
// ---------------------------------------------------------------------------

test('user dapat update title Goal miliknya', function () {
    $goal = Goal::factory()->forUser($this->user)->ongoing()->create(['title' => 'Judul lama']);

    actingAs($this->user);

    Livewire::test(GoalForm::class, ['goalId' => $goal->id])
        ->set('title', 'Judul baru')
        ->call('save');

    assertDatabaseHas('goals', ['id' => $goal->id, 'title' => 'Judul baru']);
});

test('goal_type tidak bisa diubah di edit mode (immutable)', function () {
    $goal = Goal::factory()->forUser($this->user)->ongoing()->create();

    actingAs($this->user);

    Livewire::test(GoalForm::class, ['goalId' => $goal->id])
        ->assertSet('isEditMode', true)
        ->set('goalType', 'time_bound')
        ->call('save');

    // goal_type tetap ongoing karena UpdateGoal strips it
    expect($goal->fresh()->goal_type)->toBe(GoalType::Ongoing);
});

test('user tidak dapat update Goal milik user lain', function () {
    $goal = Goal::factory()->forUser($this->other)->create(['title' => 'Punya orang lain']);

    actingAs($this->user);

    Livewire::test(GoalForm::class, ['goalId' => $goal->id])
        ->set('title', 'Dicuri')
        ->call('save');

    assertDatabaseHas('goals', ['id' => $goal->id, 'title' => 'Punya orang lain']);
});
