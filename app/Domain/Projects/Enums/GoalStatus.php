<?php

namespace App\Domain\Projects\Enums;

/**
 * Goal lifecycle status.
 *
 * State machine:
 *   active ──────► completed
 *     │                │
 *     │                └──► (reopen) ──► active
 *     └────────────────────────────────► archived
 *
 * 'archived' is a final state — no transitions out.
 * See UpdateGoalStatus for the guard implementation.
 */
enum GoalStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Archived = 'archived';

    /** Valid target statuses from this status. Used by UpdateGoalStatus. */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Active => [self::Completed, self::Archived],
            self::Completed => [self::Active],
            self::Archived => [],
        };
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }
}
