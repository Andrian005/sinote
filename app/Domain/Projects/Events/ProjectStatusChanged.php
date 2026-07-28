<?php

namespace App\Domain\Projects\Events;

use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a Project transitions to 'completed' or 'archived'.
 * Listened to by CancelRemindersOnProjectStatusChanged.
 */
class ProjectStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Project $project,
        public readonly ProjectStatus $newStatus,
    ) {}
}
