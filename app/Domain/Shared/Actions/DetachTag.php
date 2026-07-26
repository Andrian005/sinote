<?php

namespace App\Domain\Shared\Actions;

use App\Domain\Shared\Models\Tag;
use Illuminate\Database\Eloquent\Model;

class DetachTag
{
    public function execute(Tag $tag, Model $taggable): void
    {
        $taggable->tags()->detach($tag->id);
    }
}
