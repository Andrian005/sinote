<?php

namespace App\Domain\Inbox\Enums;

/**
 * InboxItem lifecycle status.
 *
 * Unprocessed → (triage action) → Processed or Discarded.
 * See FSD Module 1 § Business Rules for transition rules.
 */
enum InboxItemStatus: string
{
    case Unprocessed = 'unprocessed';
    case Processed = 'processed';
    case Discarded = 'discarded';
}
