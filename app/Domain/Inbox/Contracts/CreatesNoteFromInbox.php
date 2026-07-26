<?php

namespace App\Domain\Inbox\Contracts;

use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Contract for creating a Note from an InboxItem.
 *
 * Implemented by EPIC-005 (Knowledge Base / Notes module). The stub used
 * in TASK-0009 allows TriageInboxItem to be fully tested without the
 * Notes module existing.
 */
interface CreatesNoteFromInbox
{
    /**
     * Create a Note entity from the given InboxItem's content.
     *
     * @return Model The newly created Note.
     */
    public function execute(User $user, InboxItem $inboxItem): Model;
}
