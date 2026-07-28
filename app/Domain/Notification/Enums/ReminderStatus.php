<?php

namespace App\Domain\Notification\Enums;

enum ReminderStatus: string
{
    case Scheduled = 'scheduled';
    case Sent = 'sent';
    case Cancelled = 'cancelled';
    case Skipped = 'skipped';

    /** Sent, Cancelled, and Skipped are final — no further transitions allowed. */
    public function isFinal(): bool
    {
        return $this !== self::Scheduled;
    }

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Terjadwal',
            self::Sent => 'Terkirim',
            self::Cancelled => 'Dibatalkan',
            self::Skipped => 'Dilewati',
        };
    }
}
