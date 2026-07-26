<?php

namespace App\Domain\Inbox\Contracts;

use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;
use Illuminate\Database\Eloquent\Model;

interface CreatesTaskFromInbox
{
    public function execute(User $user, InboxItem $inboxItem): Model;
}
