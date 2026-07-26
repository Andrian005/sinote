<?php

namespace App\Domain\Tasks\Enums;

/**
 * Task lifecycle status.
 *
 * State machine (FSD Modul 2.2):
 *
 *   todo ──────► in_progress ──────► done
 *    │                │                │
 *    │                │                └──► (reopen) ──► todo
 *    └────────────────┴──────────────────► archived
 *
 * 'archived' is a final state — no transitions out.
 * 'done' can be reopened to 'todo'.
 * See UpdateTaskStatus Action for guard implementation.
 */
enum TaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';
    case Archived = 'archived';

    /**
     * Returns the set of valid target statuses from this status.
     * Used by UpdateTaskStatus Action to enforce the state machine.
     *
     * @return array<TaskStatus>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Todo => [self::InProgress, self::Archived],
            self::InProgress => [self::Todo, self::Done, self::Archived],
            self::Done => [self::Todo], // reopen only
            self::Archived => [],       // final — no transitions out
        };
    }

    /**
     * Whether this status counts as "active" work (shown on Dashboard).
     */
    public function isActive(): bool
    {
        return match ($this) {
            self::Todo, self::InProgress => true,
            self::Done, self::Archived => false,
        };
    }
}
