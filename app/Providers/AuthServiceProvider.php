<?php

namespace App\Providers;

use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Projects\Models\Goal;
use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Models\Tag;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;
use App\Policies\GoalPolicy;
use App\Policies\InboxItemPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TagPolicy;
use App\Policies\TaskPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Goal::class => GoalPolicy::class,
        InboxItem::class => InboxItemPolicy::class,
        Project::class => ProjectPolicy::class,
        Tag::class => TagPolicy::class,
        Task::class => TaskPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
