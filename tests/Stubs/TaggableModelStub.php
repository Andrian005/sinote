<?php

namespace Tests\Stubs;

use App\Domain\Shared\Models\Tag;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Stub model for testing polymorphic tagging.
 *
 * This model doesn't require a real table — tests will use it
 * to verify the tagging relationships work correctly.
 */
class TaggableModelStub extends Model
{
    use HasUlids;

    protected $table = 'taggable_stubs';

    protected $fillable = ['user_id', 'name'];

    public function tags(): MorphToMany
    {
        return $this->morphToMany(
            Tag::class,
            'taggable'
        );
    }
}
