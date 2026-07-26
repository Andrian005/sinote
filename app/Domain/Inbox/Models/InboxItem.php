<?php

namespace App\Domain\Inbox\Models;

use App\Domain\Inbox\Enums\InboxItemStatus;
use App\Domain\Shared\Models\User;
use Database\Factories\Domain\Inbox\InboxItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InboxItem extends Model
{
    /** @use HasFactory<InboxItemFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     * Required because the factory lives outside the default namespace.
     */
    protected static function newFactory(): InboxItemFactory
    {
        return InboxItemFactory::new();
    }

    protected $fillable = [
        'user_id',
        'content',
        'status',
        'converted_to_type',
        'converted_to_id',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => InboxItemStatus::class,
            'processed_at' => 'datetime',
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

    // -------------------------------------------------------------------------
    // Query Scopes
    // -------------------------------------------------------------------------

    /**
     * Scope query to unprocessed items only.
     */
    public function scopeUnprocessed(Builder $query): void
    {
        $query->where('status', InboxItemStatus::Unprocessed);
    }

    /**
     * Scope query to processed items only.
     */
    public function scopeProcessed(Builder $query): void
    {
        $query->where('status', InboxItemStatus::Processed);
    }

    /**
     * Scope query to discarded items only.
     */
    public function scopeDiscarded(Builder $query): void
    {
        $query->where('status', InboxItemStatus::Discarded);
    }

    // -------------------------------------------------------------------------
    // Conversion tracking note
    // -------------------------------------------------------------------------
    //
    // Fields `converted_to_type` and `converted_to_id` are NOT foreign keys
    // (Database Spec Bagian E, poin 2). They are informational only — allows
    // the target entity to be deleted without breaking InboxItem history.
    // The TriageInboxItem Action (TASK-0009) populates these fields during
    // conversion to Task/Note/Project.
}
