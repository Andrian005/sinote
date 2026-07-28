<?php

namespace App\Providers;

use App\Domain\Inbox\Contracts\CreatesTaskFromInbox;
use App\Domain\Shared\Actions\AuthenticateUser;
use App\Domain\Shared\Models\User;
use App\Domain\Shared\Observers\UserObserver;
use App\Domain\Tasks\Actions\CreateTaskFromInbox as CreateTaskFromInboxAction;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind CreatesTaskFromInbox contract to its concrete implementation.
        // Enables TriageInboxItem (EPIC-002) to create real Tasks without
        // knowing about the Tasks domain (loose coupling via interface).
        $this->app->bind(CreatesTaskFromInbox::class, CreateTaskFromInboxAction::class);
    }

    public function boot(): void
    {
        User::observe(UserObserver::class);

        Fortify::authenticateUsing(function (Request $request) {
            $action = new AuthenticateUser;

            return $action->execute($request->email, $request->password, false);
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->getId());
        });
    }
}
