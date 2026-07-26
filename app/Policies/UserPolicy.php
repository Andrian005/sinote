<?php

namespace App\Policies;

use App\Domain\Shared\Models\User;

class UserPolicy
{
    public function view(User $user, User $target): bool
    {
        return $user->id === $target->id;
    }

    public function update(User $user, User $target): bool
    {
        return $user->id === $target->id;
    }

    public function delete(User $user, User $target): bool
    {
        return $user->id === $target->id;
    }
}
