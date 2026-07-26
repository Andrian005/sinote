<?php

namespace App\Providers;

use App\Domain\Shared\Actions\AuthenticateUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::authenticateUsing(function (Request $request) {
            $action = new AuthenticateUser;

            return $action->execute($request->email, $request->password, false);
        });

        Fortify::loginView(fn () => view('layouts.guest', ['slot' => view('livewire.pages.auth.login')]));

        Fortify::registerView(fn () => view('layouts.guest', ['slot' => view('livewire.pages.auth.register')]));

        Fortify::requestPasswordResetLinkView(fn () => view('layouts.guest', ['slot' => view('livewire.pages.auth.forgot-password')]));

        Fortify::resetPasswordView(fn ($request) => view('layouts.guest', ['slot' => view('livewire.pages.auth.reset-password', ['request' => $request])]));

        Fortify::verifyEmailView(fn () => view('layouts.guest', ['slot' => view('livewire.pages.auth.verify-email')]));

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->getId());
        });
    }
}
