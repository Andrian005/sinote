<?php

namespace App\Domain\Projects\Exceptions;

use App\Domain\Projects\Enums\ProjectStatus;
use RuntimeException;

class InvalidProjectTransitionException extends RuntimeException
{
    public function __construct(ProjectStatus $from, ProjectStatus $to)
    {
        parent::__construct(
            "Cannot transition Project from '{$from->value}' to '{$to->value}'."
        );
    }
}
