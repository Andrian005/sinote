<?php

namespace App\Policies;

use App\Domain\Shared\Models\User;

/**
 * Template Policy untuk entitas yang dimiliki user (Pola A).
 *
 * CARA PAKAI:
 * 1. Salin file ini, ubah nama class dan type-hint $entity sesuai entitas baru.
 * 2. Daftarkan di AuthServiceProvider::$policies.
 * 3. Hapus komentar template ini dari file hasil salinan.
 *
 * Pola inti: view/update/delete → $user->id === $entity->user_id
 * Tidak ada method lain yang dikembalikan true tanpa alasan eksplisit.
 *
 * Contoh penggunaan di Livewire:
 *   $this->authorize('update', $entity);
 *
 * @see docs/rules/LARAVEL_RULES.md § Policy
 */
class ExamplePolicy
{
    /**
     * Entitas hanya boleh dilihat oleh pemiliknya.
     *
     * @param  User  $user  User yang sedang login.
     * @param  object  $entity  Entitas dengan kolom user_id (Task, Project, Note, dst.)
     */
    public function view(User $user, object $entity): bool
    {
        return $user->id === $entity->user_id;
    }

    /**
     * Entitas hanya boleh diubah oleh pemiliknya.
     */
    public function update(User $user, object $entity): bool
    {
        return $user->id === $entity->user_id;
    }

    /**
     * Entitas hanya boleh dihapus oleh pemiliknya.
     */
    public function delete(User $user, object $entity): bool
    {
        return $user->id === $entity->user_id;
    }
}
