<?php

namespace App\Domain\Shared\Actions;

use App\Domain\Shared\Models\Tag;
use Illuminate\Database\Eloquent\Model;

class DetachTag
{
    /**
     * Detach a tag from a taggable model.
     *
     * Removes the relationship between the tag and the model without deleting
     * either the tag or the model itself.
     *
     * @param  Tag  $tag  The tag to detach
     * @param  Model  $taggable  The model to untag
     */
    public function execute(Tag $tag, Model $taggable): void
    {
        $taggable->tags()->detach($tag->id);
    }
}
