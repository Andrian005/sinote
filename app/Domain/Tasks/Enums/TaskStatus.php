<?php

namespace App\Domain\Tasks\Enums;

/**
 * Task lifecycle status.
 *
 * State machine:
 *   todo ──────► in_progress ──────► done
 *    │                │                │
 *    │                │                └──► (reopen) ──► todo
 *    └────────────────┴──────────────────► archived
 *
 * 'archived' is a final state — no transitions out.
 * See UpdateTaskStatus for the guard implementation.
 */
enum TaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';
    case Archived = 'archived';

    /** Valid target statuses from this status. Used by UpdateTaskStatus. */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Todo => [self::InProgress, self::Archived],
            self::InProgress => [self::Todo, self::Done, self::Archived],
            self::Done => [self::Todo],
            self::Archived => [],
        };
    }

    public function isActive(): bool
    {
        return match ($this) {
            self::Todo, self::InProgress => true,
            self::Done, self::Archived => false,
        };
    }
}
