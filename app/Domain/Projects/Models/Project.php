<?php

namespace App\Domain\Projects\Models;

use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;
use Database\Factories\Domain\Projects\ProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    protected static function newFactory(): ProjectFactory
    {
        return ProjectFactory::new();
    }

    protected $fillable = [
        'user_id',
        'goal_id',
        'title',
        'description',
        'status',
        'progress',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'progress' => 'integer',
            'due_date' => 'date',
            'deleted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class)->withDefault(null);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', ProjectStatus::Active);
    }

    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', ProjectStatus::Completed);
    }

    public function scopeArchived(Builder $query): void
    {
        $query->where('status', ProjectStatus::Archived);
    }

    public function scopeOverdue(Builder $query): void
    {
        $query->where('status', ProjectStatus::Active)
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->toDateString());
    }
}
