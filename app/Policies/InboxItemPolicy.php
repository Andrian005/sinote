<?php

namespace App\Policies;

use App\Domain\Inbox\Enums\InboxItemStatus;
use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;

class InboxItemPolicy
{
    /**
     * Determine if the user can list their own InboxItems.
     * Used as a gate for index queries — actual filtering via user_id scope.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view a specific InboxItem.
     */
    public function view(User $user, InboxItem $inboxItem): bool
    {
        return $user->id === $inboxItem->user_id;
    }

    /**
     * Determine if the user can create a new InboxItem.
     * Always allowed — any authenticated user can capture.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update an InboxItem.
     * Only the owner can edit, and only while the item is unprocessed.
     */
    public function update(User $user, InboxItem $inboxItem): bool
    {
        return $user->id === $inboxItem->user_id
            && $inboxItem->status === InboxItemStatus::Unprocessed;
    }

    /**
     * Determine if the user can delete an InboxItem.
     */
    public function delete(User $user, InboxItem $inboxItem): bool
    {
        return $user->id === $inboxItem->user_id;
    }

    /**
     * Determine if the user can triage an InboxItem.
     * Triage is only possible on unprocessed items owned by the user.
     */
    public function triage(User $user, InboxItem $inboxItem): bool
    {
        return $user->id === $inboxItem->user_id
            && $inboxItem->status === InboxItemStatus::Unprocessed;
    }
}
