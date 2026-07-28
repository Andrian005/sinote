<?php

namespace App\Domain\Notification\Models;

use App\Domain\Shared\Models\User;
use Database\Factories\Domain\Notification\NotificationPreferenceFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    /** @use HasFactory<NotificationPreferenceFactory> */
    use HasFactory, HasUlids;

    protected static function newFactory(): NotificationPreferenceFactory
    {
        return NotificationPreferenceFactory::new();
    }

    protected $fillable = [
        'user_id',
        'deadline_reminder_enabled',
        'habit_reminder_enabled',
        'habit_reminder_time',
        'review_ritual_enabled',
    ];

    protected function casts(): array
    {
        return [
            'deadline_reminder_enabled' => 'boolean',
            'habit_reminder_enabled' => 'boolean',
            'review_ritual_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
