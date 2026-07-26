<?php

namespace App\Domain\Shared\Actions;

use App\Domain\Shared\Models\Tag;

class CreateTag
{
    /**
     * Create a new tag or return existing tag with the same name (case-insensitive).
     *
     * Tag names are always normalized to lowercase. If a tag with the same
     * name (case-insensitive) already exists for the user, that tag is returned
     * instead of creating a duplicate.
     *
     * @param  string  $userId  ULID of the user creating the tag
     * @param  string  $name  Tag name (will be normalized to lowercase)
     * @return Tag The created or existing tag
     */
    public function execute(string $userId, string $name): Tag
    {
        $normalizedName = strtolower(trim($name));

        return Tag::firstOrCreate(
            [
                'user_id' => $userId,
                'name' => $normalizedName,
            ],
            [
                'user_id' => $userId,
                'name' => $normalizedName,
            ]
        );
    }
}
