<?php

namespace App\Domain\Shared\Actions;

use App\Domain\Shared\Models\Tag;
use Illuminate\Database\Eloquent\Model;

class AttachTag
{
    /**
     * Attach a tag to a taggable model.
     * Idempotent — calling multiple times with the same tag will not create duplicates.
     */
    public function execute(Tag $tag, Model $taggable): void
    {
        $taggable->tags()->syncWithoutDetaching([$tag->id]);
    }
}
