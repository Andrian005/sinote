{{--
    Legacy fallback login page.
    The active login flow is handled by livewire/pages/auth/login.blade.php
    via the Livewire Volt component (registered on route('login')).
    This file is kept only in case a non-Livewire route references it directly.
    It uses the guest layout to avoid full-HTML duplication.
--}}
<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-900">Masuk</h2>
        <p class="mt-1 text-sm text-gray-600">Selamat datang kembali! Silakan masuk ke akun Anda.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input
                name="email"
                id="email"
                type="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all outline-none"
                placeholder="nama@email.com"
            />
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input
                name="password"
                id="password"
                type="password"
                required
                autocomplete="current-password"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all outline-none"
                placeholder="••••••••"
            />
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label for="remember" class="flex items-center cursor-pointer">
                <input
                    name="remember"
                    id="remember"
                    type="checkbox"
                    class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0"
                />
                <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                    Lupa password?
                </a>
            @endif
        </div>

        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:ring-4 focus:ring-blue-100 transition-all duration-200 shadow-lg shadow-blue-500/30">
            Masuk
        </button>

        <div class="text-center">
            <span class="text-sm text-gray-600">Belum punya akun? </span>
            <a href="{{ route('register') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                Daftar sekarang
            </a>
        </div>
    </form>
</x-guest-layout>
