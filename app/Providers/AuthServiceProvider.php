<?php

namespace App\Providers;

use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\Tag;
use App\Domain\Shared\Models\User;
use App\Policies\InboxItemPolicy;
use App\Policies\TagPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        InboxItem::class => InboxItemPolicy::class,
        Tag::class => TagPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
