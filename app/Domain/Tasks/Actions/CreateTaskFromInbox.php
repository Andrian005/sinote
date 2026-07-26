<?php

namespace App\Domain\Tasks\Actions;

use App\Domain\Inbox\Contracts\CreatesTaskFromInbox as Contract;
use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Implements CreatesTaskFromInbox. Bound in AppServiceProvider.
 *
 * Truncation rule: content ≤255 chars becomes the title (description null).
 * If longer, the first 255 chars become the title and full content the description.
 */
class CreateTaskFromInbox implements Contract
{
    public function __construct(private readonly CreateTask $createTask) {}

    public function execute(User $user, InboxItem $inboxItem): Model
    {
        $content = $inboxItem->content;

        $title = strlen($content) <= 255 ? $content : substr($content, 0, 255);
        $description = strlen($content) <= 255 ? null : $content;

        return $this->createTask->execute($user, [
            'title' => $title,
            'description' => $description,
        ]);
    }
}
