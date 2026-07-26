<?php

namespace App\Domain\Shared\Actions;

use App\Domain\Shared\Models\Tag;

class CreateTag
{
    /**
     * Create a tag or return existing one for the user.
     * Names are normalized to lowercase — duplicates are never created.
     */
    public function execute(string $userId, string $name): Tag
    {
        $normalizedName = strtolower(trim($name));

        return Tag::firstOrCreate(
            ['user_id' => $userId, 'name' => $normalizedName],
            ['user_id' => $userId, 'name' => $normalizedName],
        );
    }
}
