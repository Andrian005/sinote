<?php

namespace App\Domain\Projects\Enums;

/**
 * Goal lifecycle status.
 *
 * State machine (FSD 3.1):
 *
 *   active ──────► completed
 *     │                │
 *     │                └──► (reopen) ──► active
 *     └────────────────────────────────► archived
 *
 * 'archived' is a final state — no transitions out.
 */
enum GoalStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Archived = 'archived';

    /**
     * Returns valid target statuses from this status.
     * Used by UpdateGoalStatus Action.
     *
     * @return array<GoalStatus>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Active => [self::Completed, self::Archived],
            self::Completed => [self::Active], // reopen only
            self::Archived => [],              // final
        };
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }
}
