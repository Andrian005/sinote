<?php

use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Goal;
use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Models\User;
use App\Livewire\Projects\ProjectForm;
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

test('user dapat membuat Project baru', function () {
    actingAs($this->user);

    Livewire::test(ProjectForm::class)
        ->set('title', 'Buat aplikasi catatan')
        ->call('save');

    assertDatabaseHas('projects', [
        'user_id' => $this->user->id,
        'title' => 'Buat aplikasi catatan',
    ]);
});

test('Project baru berstatus active dan progress 0', function () {
    actingAs($this->user);

    Livewire::test(ProjectForm::class)
        ->set('title', 'Project baru')
        ->call('save');

    $project = Project::where('user_id', $this->user->id)->first();
    expect($project->status)->toBe(ProjectStatus::Active)
        ->and($project->progress)->toBe(0);
});

test('form direset setelah create berhasil', function () {
    actingAs($this->user);

    Livewire::test(ProjectForm::class)
        ->set('title', 'Akan direset')
        ->call('save')
        ->assertSet('title', '');
});

test('saved menjadi true setelah create berhasil', function () {
    actingAs($this->user);

    Livewire::test(ProjectForm::class)
        ->set('title', 'Test flash')
        ->call('save')
        ->assertSet('saved', true);
});

// ---------------------------------------------------------------------------
// Validasi
// ---------------------------------------------------------------------------

test('validasi error jika title kosong', function () {
    actingAs($this->user);

    Livewire::test(ProjectForm::class)
        ->set('title', '')
        ->call('save')
        ->assertHasErrors(['title' => 'required']);
});

test('validasi error jika title lebih dari 255 karakter', function () {
    actingAs($this->user);

    Livewire::test(ProjectForm::class)
        ->set('title', str_repeat('x', 256))
        ->call('save')
        ->assertHasErrors(['title' => 'max']);
});

// ---------------------------------------------------------------------------
// Goal attachment
// ---------------------------------------------------------------------------

test('user dapat attach Goal miliknya ke Project', function () {
    $goal = Goal::factory()->forUser($this->user)->active()->create();

    actingAs($this->user);

    Livewire::test(ProjectForm::class)
        ->set('title', 'Project dengan goal')
        ->set('goalId', $goal->id)
        ->call('save');

    assertDatabaseHas('projects', ['user_id' => $this->user->id, 'goal_id' => $goal->id]);
});

// ---------------------------------------------------------------------------
// Edit
// ---------------------------------------------------------------------------

test('user dapat update Project miliknya', function () {
    $project = Project::factory()->forUser($this->user)->withTitle('Judul lama')->create();

    actingAs($this->user);

    Livewire::test(ProjectForm::class, ['projectId' => $project->id])
        ->set('title', 'Judul baru')
        ->call('save');

    assertDatabaseHas('projects', ['id' => $project->id, 'title' => 'Judul baru']);
});

test('user tidak dapat update Project milik user lain', function () {
    $project = Project::factory()->forUser($this->other)->withTitle('Punya orang lain')->create();

    actingAs($this->user);

    Livewire::test(ProjectForm::class, ['projectId' => $project->id])
        ->set('title', 'Dicuri')
        ->call('save');

    assertDatabaseHas('projects', ['id' => $project->id, 'title' => 'Punya orang lain']);
});
