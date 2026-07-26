<?php

namespace Tests\Fixtures;

use App\Domain\Shared\Models\Tag;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Minimal taggable model for unit testing AttachTag / DetachTag actions.
 *
 * Reuses the `users` table (always available in the test database) as a
 * backing store — no extra migration required.  This fixture exists only
 * in the test namespace and is never loaded in production.
 *
 * Declares the morphToMany('tags') relationship that AttachTag / DetachTag
 * expect on their $taggable argument.
 */
class FakeTaggable extends Model
{
    use HasUlids;

    /** Re-use the users table that is always present in the test DB. */
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public $timestamps = true;

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
