<?php

namespace App\Domain\Tasks\Exceptions;

use App\Domain\Tasks\Enums\TaskStatus;
use RuntimeException;

/**
 * Thrown when an invalid Task status transition is attempted.
 * See TaskStatus::allowedTransitions() for the valid state machine.
 */
class InvalidTaskTransitionException extends RuntimeException
{
    public function __construct(TaskStatus $from, TaskStatus $to)
    {
        parent::__construct(
            "Cannot transition Task from '{$from->value}' to '{$to->value}'."
        );
    }
}
