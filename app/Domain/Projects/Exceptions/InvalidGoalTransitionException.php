<?php

namespace App\Domain\Projects\Exceptions;

use App\Domain\Projects\Enums\GoalStatus;
use RuntimeException;

class InvalidGoalTransitionException extends RuntimeException
{
    public function __construct(GoalStatus $from, GoalStatus $to)
    {
        parent::__construct(
            "Cannot transition Goal from '{$from->value}' to '{$to->value}'."
        );
    }
}
