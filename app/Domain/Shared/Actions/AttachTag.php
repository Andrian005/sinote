<?php

namespace App\Domain\Shared\Actions;

use App\Domain\Shared\Models\Tag;
use Illuminate\Database\Eloquent\Model;

class AttachTag
{
    /**
     * Attach a tag to a taggable model (Task, Project, Note, Habit, etc).
     *
     * This operation is idempotent — calling it multiple times with the same
     * tag and model will not create duplicate entries in the taggables table.
     *
     * @param  Tag  $tag  The tag to attach
     * @param  Model  $taggable  The model to tag (must use MorphToMany 'tags' relation)
     */
    public function execute(Tag $tag, Model $taggable): void
    {
        // syncWithoutDetaching ensures idempotency — won't create duplicates
        $taggable->tags()->syncWithoutDetaching([$tag->id]);
    }
}
