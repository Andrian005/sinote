<?php

namespace App\Domain\Projects\Enums;

/**
 * Project lifecycle status.
 *
 * State machine (FSD 3.2):
 *
 *   active ──────► completed
 *     │                │
 *     │                └──► (reopen) ──► active
 *     └────────────────────────────────► archived
 *
 * 'archived' is a final state — no transitions out.
 * 'completed' can also be triggered automatically when progress reaches 100%
 * via RecalculateProjectProgress Action.
 */
enum ProjectStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Archived = 'archived';

    /**
     * Returns valid target statuses from this status.
     * Used by UpdateProjectStatus Action.
     *
     * @return array<ProjectStatus>
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
