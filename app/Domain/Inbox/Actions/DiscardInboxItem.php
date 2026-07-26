<?php

namespace App\Domain\Inbox\Actions;

use App\Domain\Inbox\Enums\InboxItemStatus;
use App\Domain\Inbox\Exceptions\InboxItemAlreadyProcessedException;
use App\Domain\Inbox\Models\InboxItem;

class DiscardInboxItem
{
    /**
     * Mark an InboxItem as discarded.
     *
     * Discarding is only allowed on unprocessed items. Attempting to
     * discard an already-processed or already-discarded item throws
     * InboxItemAlreadyProcessedException to preserve lifecycle integrity.
     *
     * @param  InboxItem  $inboxItem  The item to discard.
     * @return bool True on successful update.
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
