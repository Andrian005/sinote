<?php

namespace App\Domain\Tasks\Models;

use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Models\Tag;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Enums\TaskPriority;
use App\Domain\Tasks\Enums\TaskStatus;
use Database\Factories\Domain\Tasks\TaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     * Required because the factory lives outside the default namespace.
     */
    protected static function newFactory(): TaskFactory
    {
        return TaskFactory::new();
    }

    protected $fillable = [
        'user_id',
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'due_date' => 'date',
            'completed_at' => 'datetime',
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

    /**
     * Optional Project association.
     * withDefault(null) ensures the relation returns null (not an empty Model)
     * when project_id is null.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class)->withDefault(null);
    }

    /**
     * Tags attached to this Task (polymorphic many-to-many).
     * Relation is active once the taggables table and Tag model are available
     * (already done in EPIC-001).
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    // -------------------------------------------------------------------------
    // Query Scopes
    // -------------------------------------------------------------------------

    /** Tasks with status 'todo'. */
    public function scopeTodo(Builder $query): void
    {
        $query->where('status', TaskStatus::Todo);
    }

    /** Tasks with status 'in_progress'. */
    public function scopeInProgress(Builder $query): void
    {
        $query->where('status', TaskStatus::InProgress);
    }

    /** Tasks with status 'done'. */
    public function scopeDone(Builder $query): void
    {
        $query->where('status', TaskStatus::Done);
    }

    /** Tasks with status 'archived'. */
    public function scopeArchived(Builder $query): void
    {
        $query->where('status', TaskStatus::Archived);
    }

    /**
     * Active tasks: todo + in_progress.
     * Default scope for Dashboard and TaskList component.
     */
    public function scopeActive(Builder $query): void
    {
        $query->whereIn('status', [TaskStatus::Todo, TaskStatus::InProgress]);
    }

    /**
     * Pending tasks: active tasks that have a due date set.
     * Useful for Deadline Reminder module (EPIC-006).
     */
    public function scopePending(Builder $query): void
    {
        $query->whereIn('status', [TaskStatus::Todo, TaskStatus::InProgress])
            ->whereNotNull('due_date');
    }

    /**
     * Overdue tasks: pending tasks whose due_date is in the past.
     */
    public function scopeOverdue(Builder $query): void
    {
        $query->whereIn('status', [TaskStatus::Todo, TaskStatus::InProgress])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->toDateString());
    }
}
