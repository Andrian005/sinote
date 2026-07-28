<?php

namespace App\Domain\Notification\Models;

use App\Domain\Notification\Enums\ReminderStatus;
use App\Domain\Notification\Enums\ReminderType;
use App\Domain\Shared\Models\User;
use Database\Factories\Domain\Notification\ReminderFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reminder extends Model
{
    /** @use HasFactory<ReminderFactory> */
    use HasFactory, HasUlids;

    protected static function newFactory(): ReminderFactory
    {
        return ReminderFactory::new();
    }

    protected $fillable = [
        'user_id',
        'remindable_id',
        'remindable_type',
        'reminder_type',
        'scheduled_at',
        'status',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'reminder_type' => ReminderType::class,
            'status' => ReminderStatus::class,
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function remindable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeScheduled(Builder $query): void
    {
        $query->where('status', ReminderStatus::Scheduled);
    }

    public function scopeSent(Builder $query): void
    {
        $query->where('status', ReminderStatus::Sent);
    }

    public function scopeCancelled(Builder $query): void
    {
        $query->where('status', ReminderStatus::Cancelled);
    }

    /** Scheduled reminders whose delivery time has arrived (scheduled_at <= now). */
    public function scopePendingDelivery(Builder $query): void
    {
        $query->where('status', ReminderStatus::Scheduled)
            ->where('scheduled_at', '<=', now());
    }
}
