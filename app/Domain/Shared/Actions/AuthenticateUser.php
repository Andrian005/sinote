<?php

namespace App\Domain\Shared\Actions;

use App\Domain\Shared\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthenticateUser
{
    public function execute(string $email, string $password, bool $remember = false): ?User
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            Log::channel('security')->warning('Failed login attempt', [
                'email' => $email,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toIso8601String(),
            ]);

            return null;
        }

        Auth::login($user, $remember);

        return $user;
    }
}
