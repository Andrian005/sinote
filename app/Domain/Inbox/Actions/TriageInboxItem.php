<?php

namespace App\Domain\Inbox\Actions;

use App\Domain\Inbox\Contracts\CreatesNoteFromInbox;
use App\Domain\Inbox\Contracts\CreatesTaskFromInbox;
use App\Domain\Inbox\Enums\InboxItemStatus;
use App\Domain\Inbox\Exceptions\InboxItemAlreadyProcessedException;
use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class TriageInboxItem
{
    private const VALID_TYPES = ['task', 'note'];

    public function __construct(
        private readonly CreatesTaskFromInbox $createTask,
        private readonly CreatesNoteFromInbox $createNote,
    ) {}

    /**
     * Convert an unprocessed InboxItem into a Task or Note.
     *
     * Back-reference fields (converted_to_type, converted_to_id) are
     * informational only — not foreign keys, so the target entity can be
     * deleted without breaking InboxItem history.
     *
     * @throws InboxItemAlreadyProcessedException
     * @throws InvalidArgumentException
     */
    public function execute(User $user, InboxItem $inboxItem, string $targetType): Model
    {
        if ($inboxItem->status !== InboxItemStatus::Unprocessed) {
            throw new InboxItemAlreadyProcessedException($inboxItem->status->value);
        }

        if (! in_array($targetType, self::VALID_TYPES, strict: true)) {
            throw new InvalidArgumentException(
                "Unsupported triage target type: '{$targetType}'. Expected one of: "
                .implode(', ', self::VALID_TYPES).'.'
            );
        }

        $created = match ($targetType) {
            'task' => $this->createTask->execute($user, $inboxItem),
            'note' => $this->createNote->execute($user, $inboxItem),
        };

        $inboxItem->update([
            'status' => InboxItemStatus::Processed,
            'converted_to_type' => $targetType,
            'converted_to_id' => $created->getKey(),
            'processed_at' => now(),
        ]);

        return $created;
    }
}
