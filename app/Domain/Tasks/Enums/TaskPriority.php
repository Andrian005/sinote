<?php

namespace App\Domain\Tasks\Enums;

/**
 * Task priority level.
 *
 * Used for sorting on the Dashboard (high → medium → low, due_date ASC).
 * Default is Medium (FSD Modul 2.1, Database Spec A.5).
 */
enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    /**
     * Numeric sort weight — higher is more urgent.
     * Useful for raw ORDER BY without DB-level enum ordering.
     */
    public function weight(): int
    {
        return match ($this) {
            self::Low => 1,
            self::Medium => 2,
            self::High => 3,
        };
    }

    /**
     * Tailwind CSS color class for the priority badge in the UI.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Low => 'bg-green-100 text-green-800',
            self::Medium => 'bg-yellow-100 text-yellow-800',
            self::High => 'bg-red-100 text-red-800',
        };
    }

    /**
     * Label in Indonesian for display in the UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Low => 'Rendah',
            self::Medium => 'Sedang',
            self::High => 'Tinggi',
        };
    }
}
