<?php

namespace App\Domain\Inbox\Actions;

use App\Domain\Inbox\Enums\InboxItemStatus;
use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;

class CreateInboxItem
{
    /**
     * Capture new item to Inbox. Content is trimmed, status always 'unprocessed'.
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
