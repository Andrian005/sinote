<?php

namespace App\Domain\Tasks\Enums;

/**
 * Task priority level. Default is Medium.
 * Sorted high → medium → low, with due_date ASC on the Dashboard.
 */
enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    /** Numeric sort weight — higher is more urgent. */
    public function weight(): int
    {
        return match ($this) {
            self::Low => 1,
            self::Medium => 2,
            self::High => 3,
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Low => 'bg-green-100 text-green-800',
            self::Medium => 'bg-yellow-100 text-yellow-800',
            self::High => 'bg-red-100 text-red-800',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Rendah',
            self::Medium => 'Sedang',
            self::High => 'Tinggi',
        };
    }
}
