<?php

namespace App\Domain\Shared\Models;

use App\Domain\Habits\Models\Habit;
use App\Domain\KnowledgeBase\Models\Note;
use App\Domain\Projects\Models\Project;
use App\Domain\Tasks\Models\Task;
use Database\Factories\Domain\Shared\TagFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'user_id',
        'name',
    ];

    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * All Tasks tagged with this Tag.
     *
     * The related model class is referenced as a string to avoid a hard
     * dependency on modules that may not exist yet. When the Task module is
     * built (EPIC-003) this will resolve correctly — no change needed here.
     */
    public function tasks(): MorphToMany
    {
        return $this->morphedByMany(
            Task::class,
            'taggable',
        );
    }

    /**
     * All Projects tagged with this Tag.
     */
    public function projects(): MorphToMany
    {
        return $this->morphedByMany(
            Project::class,
            'taggable',
        );
    }

    /**
     * All Notes tagged with this Tag.
     */
    public function notes(): MorphToMany
    {
        return $this->morphedByMany(
            Note::class,
            'taggable',
        );
    }

    /**
     * All Habits tagged with this Tag.
     */
    public function habits(): MorphToMany
    {
        return $this->morphedByMany(
            Habit::class,
            'taggable',
        );
    }

    // -------------------------------------------------------------------------
    // Normalisation note
    // -------------------------------------------------------------------------
    //
    // Tag names are always stored as lowercase. Normalisation is the
    // responsibility of the CreateTag Action — not enforced here via a mutator
    // so that the Action remains the single source of business logic (CORE_RULES § 2).
    // This comment serves as the in-code documentation required by TASK-0005 AC.
}
