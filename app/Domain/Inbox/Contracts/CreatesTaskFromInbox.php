<?php

namespace App\Domain\Inbox\Contracts;

use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Contract for creating a Task from an InboxItem.
 *
 * Implemented by EPIC-003 (Tasks module). The stub used in TASK-0009
 * allows TriageInboxItem to be fully tested without Task module existing.
 */
interface CreatesTaskFromInbox
{
    /**
     * Create a Task entity from the given InboxItem's content.
     *
     * @return Model The newly created Task.
     */
    public function execute(User $user, InboxItem $inboxItem): Model;
}
