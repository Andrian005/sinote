<?php

namespace App\Domain\Inbox\Exceptions;

use RuntimeException;

/**
 * Thrown when an operation requires an InboxItem to be unprocessed,
 * but the item has already been processed or discarded.
 */
class InboxItemAlreadyProcessedException extends RuntimeException
{
    public function __construct(string $status)
    {
        parent::__construct(
            "Cannot perform this operation: InboxItem is already '{$status}'."
        );
    }
}
