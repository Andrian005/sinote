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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): MorphToMany
    {
        return $this->morphedByMany(
            Task::class,
            'taggable',
        );
    }

    public function projects(): MorphToMany
    {
        return $this->morphedByMany(
            Project::class,
            'taggable',
        );
    }

    public function notes(): MorphToMany
    {
        return $this->morphedByMany(
            Note::class,
            'taggable',
        );
    }

    public function habits(): MorphToMany
    {
        return $this->morphedByMany(
            Habit::class,
            'taggable',
        );
    }
}
