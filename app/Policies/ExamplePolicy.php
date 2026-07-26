<?php

namespace App\Policies;

use App\Domain\Shared\Models\User;

/**
 * Template Policy untuk entitas yang dimiliki user (Pola A).
 * Salin file ini, ganti nama class dan type-hint $entity, lalu hapus komentar ini.
 *
 * Pola inti: view/update/delete → $user->id === $entity->user_id
 */
class ExamplePolicy
{
    public function view(User $user, object $entity): bool
    {
        return $user->id === $entity->user_id;
    }

    public function update(User $user, object $entity): bool
    {
        return $user->id === $entity->user_id;
    }

    public function delete(User $user, object $entity): bool
    {
        return $user->id === $entity->user_id;
    }
}
