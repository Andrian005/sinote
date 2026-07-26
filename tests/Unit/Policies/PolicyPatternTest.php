<?php

use App\Domain\Shared\Models\User;
use App\Policies\ExamplePolicy;
use App\Policies\UserPolicy;

/**
 * Unit test untuk memverifikasi pola Policy standar proyek.
 *
 * Dua pola yang diuji:
 *   - Pola A (ExamplePolicy): $user->id === $entity->user_id  — untuk semua entitas owned
 *   - Pola B (UserPolicy):    $user->id === $target->id       — khusus User-on-self
 *
 * @see docs/rules/LARAVEL_RULES.md § Policy
 */

// ---------------------------------------------------------------------------
// Pola A — ExamplePolicy (template untuk Task, Project, Note, dst.)
// ---------------------------------------------------------------------------

describe('ExamplePolicy — Pola A (entity->user_id)', function () {
    beforeEach(function () {
        $this->policy = new ExamplePolicy;
        $this->owner = User::factory()->create();
        $this->other = User::factory()->create();

        // Entitas dummy dengan user_id — mensimulasikan Task/Project/Note/dll.
        $this->ownedEntity = (object) ['user_id' => $this->owner->id];
        $this->alienEntity = (object) ['user_id' => $this->other->id];
    });

    it('mengizinkan owner untuk view entitasnya sendiri', function () {
        expect($this->policy->view($this->owner, $this->ownedEntity))->toBeTrue();
    });

    it('menolak user lain untuk view entitas yang bukan miliknya', function () {
        expect($this->policy->view($this->other, $this->ownedEntity))->toBeFalse();
    });

    it('mengizinkan owner untuk update entitasnya sendiri', function () {
        expect($this->policy->update($this->owner, $this->ownedEntity))->toBeTrue();
    });

    it('menolak user lain untuk update entitas yang bukan miliknya', function () {
        expect($this->policy->update($this->other, $this->ownedEntity))->toBeFalse();
    });

    it('mengizinkan owner untuk delete entitasnya sendiri', function () {
        expect($this->policy->delete($this->owner, $this->ownedEntity))->toBeTrue();
    });

    it('menolak user lain untuk delete entitas yang bukan miliknya', function () {
        expect($this->policy->delete($this->other, $this->ownedEntity))->toBeFalse();
    });

    it('menolak jika user_id entitas adalah null', function () {
        $nullEntity = (object) ['user_id' => null];
        expect($this->policy->view($this->owner, $nullEntity))->toBeFalse();
        expect($this->policy->update($this->owner, $nullEntity))->toBeFalse();
        expect($this->policy->delete($this->owner, $nullEntity))->toBeFalse();
    });
});

// ---------------------------------------------------------------------------
// Pola B — UserPolicy (User-on-self)
// ---------------------------------------------------------------------------

describe('UserPolicy — Pola B (user self-ownership)', function () {
    beforeEach(function () {
        $this->policy = new UserPolicy;
        $this->userA = User::factory()->create();
        $this->userB = User::factory()->create();
    });

    it('mengizinkan user untuk view profilnya sendiri', function () {
        expect($this->policy->view($this->userA, $this->userA))->toBeTrue();
    });

    it('menolak user untuk view profil user lain', function () {
        expect($this->policy->view($this->userA, $this->userB))->toBeFalse();
    });

    it('mengizinkan user untuk update profilnya sendiri', function () {
        expect($this->policy->update($this->userA, $this->userA))->toBeTrue();
    });

    it('menolak user untuk update profil user lain', function () {
        expect($this->policy->update($this->userA, $this->userB))->toBeFalse();
    });

    it('mengizinkan user untuk delete akunnya sendiri', function () {
        expect($this->policy->delete($this->userA, $this->userA))->toBeTrue();
    });

    it('menolak user untuk delete akun user lain', function () {
        expect($this->policy->delete($this->userA, $this->userB))->toBeFalse();
    });
});
