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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnprocessed(Builder $query): void
    {
        $query->where('status', InboxItemStatus::Unprocessed);
    }

    public function scopeProcessed(Builder $query): void
    {
        $query->where('status', InboxItemStatus::Processed);
    }

    public function scopeDiscarded(Builder $query): void
    {
        $query->where('status', InboxItemStatus::Discarded);
    }
}
