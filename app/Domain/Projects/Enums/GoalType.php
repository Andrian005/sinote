<?php

namespace App\Domain\Projects\Enums;

/**
 * Goal type — immutable after creation.
 * time_bound goals require a target_date; ongoing goals have no deadline.
 */
enum GoalType: string
{
    case TimeBound = 'time_bound';
    case Ongoing = 'ongoing';

    public function label(): string
    {
        return match ($this) {
            self::TimeBound => 'Berujung (ada tenggat)',
            self::Ongoing => 'Berkelanjutan',
        };
    }
}
