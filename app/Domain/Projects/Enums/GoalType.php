<?php

namespace App\Domain\Projects\Enums;

/**
 * Goal type — immutable after creation (FSD 3.1).
 *
 * time_bound: has a target_date; considered overdue if past due + not completed.
 * ongoing:    no deadline; represents continuous habits/directions.
 */
enum GoalType: string
{
    case TimeBound = 'time_bound';
    case Ongoing = 'ongoing';

    /** Label in Indonesian for display in the UI. */
    public function label(): string
    {
        return match ($this) {
            self::TimeBound => 'Berujung (ada tenggat)',
            self::Ongoing => 'Berkelanjutan',
        };
    }
}
