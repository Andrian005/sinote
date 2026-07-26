<?php

namespace App\Domain\Tasks\Actions;

use App\Domain\Inbox\Contracts\CreatesTaskFromInbox as Contract;
use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Model;

/**
 * Concrete implementation of the CreatesTaskFromInbox contract.
 *
 * Bound in AppServiceProvider so that TriageInboxItem (EPIC-002) resolves
 * this implementation automatically via the Laravel container.
 *
 * Title truncation rule: InboxItem content is used as Task title if ≤255
 * characters. If longer, the first 255 characters become the title and the
 * full content is stored as the Task description (FSD 2.1 — title max 255).
 */
class CreateTaskFromInbox implements Contract
{
    public function __construct(private readonly CreateTask $createTask) {}

    public function execute(User $user, InboxItem $inboxItem): Model
    {
        $content = $inboxItem->content;

        if (strlen($content) <= 255) {
            $title = $content;
            $description = null;
        } else {
            $title = substr($content, 0, 255);
            $description = $content;
        }

        return $this->createTask->execute($user, [
            'title' => $title,
            'description' => $description,
        ]);
    }
}
