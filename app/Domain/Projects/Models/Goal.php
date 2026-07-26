<?php

namespace App\Domain\Projects\Models;

use App\Domain\Projects\Enums\GoalStatus;
use App\Domain\Projects\Enums\GoalType;
use App\Domain\Shared\Models\User;
use Database\Factories\Domain\Projects\GoalFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goal extends Model
{
    /** @use HasFactory<GoalFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    protected static function newFactory(): GoalFactory
    {
        return GoalFactory::new();
    }

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'goal_type',
        'status',
        'target_date',
    ];

    protected function casts(): array
    {
        return [
            'goal_type' => GoalType::class,
            'status' => GoalStatus::class,
            'target_date' => 'date',
            'deleted_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): void
    {
        $query->where('status', GoalStatus::Active);
    }

    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', GoalStatus::Completed);
    }

    public function scopeArchived(Builder $query): void
    {
        $query->where('status', GoalStatus::Archived);
    }

    public function scopeTimeBound(Builder $query): void
    {
        $query->where('goal_type', GoalType::TimeBound);
    }

    public function scopeOngoing(Builder $query): void
    {
        $query->where('goal_type', GoalType::Ongoing);
    }

    /**
     * Overdue time-bound goals: active + past target_date.
     */
    public function scopeOverdue(Builder $query): void
    {
        $query->where('status', GoalStatus::Active)
            ->where('goal_type', GoalType::TimeBound)
            ->whereNotNull('target_date')
            ->where('target_date', '<', now()->toDateString());
    }
}
