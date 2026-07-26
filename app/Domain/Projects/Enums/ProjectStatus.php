<?php

namespace App\Domain\Projects\Enums;

/**
 * Project lifecycle status.
 *
 * State machine:
 *   active ──────► completed
 *     │                │
 *     │                └──► (reopen) ──► active
 *     └────────────────────────────────► archived
 *
 * 'archived' is a final state — no transitions out.
 * 'completed' can also be triggered automatically when progress = 100%
 * via RecalculateProjectProgress.
 * See UpdateProjectStatus for the guard implementation.
 */
enum ProjectStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Archived = 'archived';

    /** Valid target statuses from this status. Used by UpdateProjectStatus. */
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
