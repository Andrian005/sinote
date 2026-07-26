<?php

namespace App\Policies;

use App\Domain\Shared\Models\Tag;
use App\Domain\Shared\Models\User;

class TagPolicy
{
    /**
     * Determine if the user can view the tag.
     */
    public function view(User $user, Tag $tag): bool
    {
        return $user->id === $tag->user_id;
    }

    /**
     * Determine if the user can update the tag.
     */
    public function update(User $user, Tag $tag): bool
    {
        return $user->id === $tag->user_id;
    }

    /**
     * Determine if the user can delete the tag.
     */
    public function delete(User $user, Tag $tag): bool
    {
        return $user->id === $tag->user_id;
    }
}
