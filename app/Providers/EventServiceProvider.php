<?php

namespace App\Providers;

use App\Domain\Projects\Events\ProjectStatusChanged;
use App\Domain\Tasks\Events\TaskCompleted;
use App\Listeners\CancelRemindersOnProjectStatusChanged;
use App\Listeners\CancelRemindersOnTaskCompleted;
use App\Listeners\UpdateProjectProgress;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TaskCompleted::class => [
            UpdateProjectProgress::class,
            CancelRemindersOnTaskCompleted::class,
        ],
        ProjectStatusChanged::class => [
            CancelRemindersOnProjectStatusChanged::class,
        ],
    ];
}
