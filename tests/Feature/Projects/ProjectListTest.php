<?php

use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Models\User;
use App\Livewire\Projects\ProjectList;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->other = User::factory()->create();
});

// ---------------------------------------------------------------------------
// Visibility
// ---------------------------------------------------------------------------

test('user melihat Project miliknya dengan filter active', function () {
    Project::factory()->count(3)->forUser($this->user)->active()->create();

    actingAs($this->user);

    expect(Livewire::test(ProjectList::class)->get('projects'))->toHaveCount(3);
});

test('user tidak melihat Project milik user lain', function () {
    Project::factory()->count(4)->forUser($this->other)->active()->create();
    Project::factory()->count(2)->forUser($this->user)->active()->create();

    actingAs($this->user);

    expect(Livewire::test(ProjectList::class)->get('projects'))->toHaveCount(2);
});

test('Project completed dan archived tidak muncul di filter active', function () {
    Project::factory()->forUser($this->user)->active()->create();
    Project::factory()->forUser($this->user)->completed()->create();
    Project::factory()->forUser($this->user)->archived()->create();

    actingAs($this->user);

    expect(Livewire::test(ProjectList::class, ['filter' => 'active'])->get('projects'))->toHaveCount(1);
});

// ---------------------------------------------------------------------------
// updateStatus
// ---------------------------------------------------------------------------

test('user dapat menyelesaikan Project aktif miliknya', function () {
    $project = Project::factory()->forUser($this->user)->active()->create();

    actingAs($this->user);

    Livewire::test(ProjectList::class)->call('updateStatus', $project->id, 'completed');

    expect($project->fresh()->status)->toBe(ProjectStatus::Completed);
});

test('user dapat reopen Project completed miliknya', function () {
    $project = Project::factory()->forUser($this->user)->completed()->create();

    actingAs($this->user);

    Livewire::test(ProjectList::class, ['filter' => 'completed'])
        ->call('updateStatus', $project->id, 'active');

    expect($project->fresh()->status)->toBe(ProjectStatus::Active);
});

test('flash sukses muncul setelah updateStatus berhasil', function () {
    $project = Project::factory()->forUser($this->user)->active()->create();

    actingAs($this->user);

    Livewire::test(ProjectList::class)
        ->call('updateStatus', $project->id, 'completed')
        ->assertSet('flash', 'Status project berhasil diperbarui.');
});

// ---------------------------------------------------------------------------
// archive
// ---------------------------------------------------------------------------

test('user dapat arsipkan Project aktif miliknya', function () {
    $project = Project::factory()->forUser($this->user)->active()->create();

    actingAs($this->user);

    Livewire::test(ProjectList::class)->call('archive', $project->id);

    expect($project->fresh()->status)->toBe(ProjectStatus::Archived);
});

test('flash sukses muncul setelah archive berhasil', function () {
    $project = Project::factory()->forUser($this->user)->active()->create();

    actingAs($this->user);

    Livewire::test(ProjectList::class)
        ->call('archive', $project->id)
        ->assertSet('flash', 'Project berhasil diarsipkan.');
});

// ---------------------------------------------------------------------------
// Authorization
// ---------------------------------------------------------------------------

test('user tidak dapat update status Project milik user lain', function () {
    $project = Project::factory()->forUser($this->other)->active()->create();

    actingAs($this->user);

    Livewire::test(ProjectList::class)->call('updateStatus', $project->id, 'completed');

    expect($project->fresh()->status)->toBe(ProjectStatus::Active);
});

test('flash error muncul ketika update status Project milik user lain', function () {
    $project = Project::factory()->forUser($this->other)->active()->create();

    actingAs($this->user);

    Livewire::test(ProjectList::class)
        ->call('updateStatus', $project->id, 'completed')
        ->assertSet('flashIsError', true);
});

test('user tidak dapat arsipkan Project milik user lain', function () {
    $project = Project::factory()->forUser($this->other)->active()->create();

    actingAs($this->user);

    Livewire::test(ProjectList::class)->call('archive', $project->id);

    expect($project->fresh()->status)->toBe(ProjectStatus::Active);
});

// ---------------------------------------------------------------------------
// Pagination & widget mode
// ---------------------------------------------------------------------------

test('pagination bekerja dengan lebih dari 10 project', function () {
    Project::factory()->count(15)->forUser($this->user)->active()->create();

    actingAs($this->user);

    $projects = Livewire::test(ProjectList::class)->get('projects');

    expect($projects)->toHaveCount(10)->and($projects->total())->toBe(15);
});

test('widget mode membatasi jumlah project yang ditampilkan', function () {
    Project::factory()->count(10)->forUser($this->user)->active()->create();

    actingAs($this->user);

    $projects = Livewire::test(ProjectList::class, ['limit' => 3])->get('projects');

    expect($projects)->toHaveCount(3);
});
