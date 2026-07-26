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
    /**
     * Accepted target types for triage (FSD Modul 1 — Triage Rules).
     */
    private const VALID_TYPES = ['task', 'note'];

    public function __construct(
        private readonly CreatesTaskFromInbox $createTask,
        private readonly CreatesNoteFromInbox $createNote,
    ) {}

    /**
     * Convert an unprocessed InboxItem into a Task or Note.
     *
     * Steps (FSD 1.2 — Triage):
     *  1. Guard: item must be unprocessed.
     *  2. Delegate creation to the injected contract implementation.
     *  3. Stamp the InboxItem as processed with a back-reference to the
     *     created entity (informational only — not a FK, per Database Spec E.2).
     *
     * @param  User  $user  The owner performing the triage.
     * @param  InboxItem  $inboxItem  The item being triaged.
     * @param  string  $targetType  'task' or 'note'.
     * @return Model The entity (Task or Note) that was created.
     *
     * @throws InboxItemAlreadyProcessedException If item is not unprocessed.
     * @throws InvalidArgumentException If targetType is not supported.
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
