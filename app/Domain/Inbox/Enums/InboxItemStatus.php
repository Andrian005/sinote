<?php

namespace App\Domain\Inbox\Enums;

/**
 * InboxItem lifecycle: unprocessed → processed | discarded.
 */
enum InboxItemStatus: string
{
    case Unprocessed = 'unprocessed';
    case Processed = 'processed';
    case Discarded = 'discarded';
}
