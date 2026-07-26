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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class)->withDefault(null);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function scopeTodo(Builder $query): void
    {
        $query->where('status', TaskStatus::Todo);
    }

    public function scopeInProgress(Builder $query): void
    {
        $query->where('status', TaskStatus::InProgress);
    }

    public function scopeDone(Builder $query): void
    {
        $query->where('status', TaskStatus::Done);
    }

    public function scopeArchived(Builder $query): void
    {
        $query->where('status', TaskStatus::Archived);
    }

    public function scopeActive(Builder $query): void
    {
        $query->whereIn('status', [TaskStatus::Todo, TaskStatus::InProgress]);
    }

    public function scopePending(Builder $query): void
    {
        $query->whereIn('status', [TaskStatus::Todo, TaskStatus::InProgress])
            ->whereNotNull('due_date');
    }

    public function scopeOverdue(Builder $query): void
    {
        $query->whereIn('status', [TaskStatus::Todo, TaskStatus::InProgress])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->toDateString());
    }
}
