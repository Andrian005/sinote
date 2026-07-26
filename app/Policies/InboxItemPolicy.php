<?php

namespace App\Policies;

use App\Domain\Inbox\Enums\InboxItemStatus;
use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;

class InboxItemPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, InboxItem $inboxItem): bool
    {
        return $user->id === $inboxItem->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /** Only unprocessed items may be edited. */
    public function update(User $user, InboxItem $inboxItem): bool
    {
        return $user->id === $inboxItem->user_id
            && $inboxItem->status === InboxItemStatus::Unprocessed;
    }

    public function delete(User $user, InboxItem $inboxItem): bool
    {
        return $user->id === $inboxItem->user_id;
    }

    /** Only unprocessed items may be triaged. */
    public function triage(User $user, InboxItem $inboxItem): bool
    {
        return $user->id === $inboxItem->user_id
            && $inboxItem->status === InboxItemStatus::Unprocessed;
    }
}
