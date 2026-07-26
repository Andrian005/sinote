<?php

namespace App\Providers;

use App\Domain\Tasks\Events\TaskCompleted;
use App\Listeners\UpdateProjectProgress;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        TaskCompleted::class => [
            UpdateProjectProgress::class,
        ],
    ];
}
