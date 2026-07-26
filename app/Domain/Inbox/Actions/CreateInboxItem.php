<?php

namespace App\Domain\Inbox\Actions;

use App\Domain\Inbox\Enums\InboxItemStatus;
use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;

class CreateInboxItem
{
    /**
     * Capture a new item into the user's Inbox.
     *
     * Content is trimmed before persisting so that leading/trailing
     * whitespace never reaches the database (FSD 1.1 — capture rules).
     * Status is always set to 'unprocessed' on creation; callers cannot
     * override this to maintain the Inbox lifecycle invariant.
     *
     * @param  User  $user  The authenticated user capturing the item.
     * @param  string  $content  Raw content string (trimmed internally).
     * @return InboxItem The newly created InboxItem.
     */
    public function execute(User $user, string $content): InboxItem
    {
        return InboxItem::create([
            'user_id' => $user->id,
            'content' => trim($content),
            'status' => InboxItemStatus::Unprocessed,
        ]);
    }
}
