<?php

namespace App\Domain\Inbox\Actions;

use App\Domain\Inbox\Enums\InboxItemStatus;
use App\Domain\Inbox\Exceptions\InboxItemAlreadyProcessedException;
use App\Domain\Inbox\Models\InboxItem;

class DiscardInboxItem
{
    /**
     * Discard an InboxItem. Only unprocessed items may be discarded.
     *
     * @throws InboxItemAlreadyProcessedException
     */
    public function execute(InboxItem $inboxItem): bool
    {
        if ($inboxItem->status !== InboxItemStatus::Unprocessed) {
            throw new InboxItemAlreadyProcessedException($inboxItem->status->value);
        }

        return $inboxItem->update([
            'status' => InboxItemStatus::Discarded,
            'processed_at' => now(),
        ]);
    }
}
