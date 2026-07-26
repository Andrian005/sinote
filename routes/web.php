<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

/*
|--------------------------------------------------------------------------
| Auth Routes (Guest Only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('livewire.pages.auth.register');
    })->name('register');

    Route::get('/forgot-password', function () {
        return view('livewire.pages.auth.forgot-password');
    })->name('password.request');

    Route::get('/reset-password/{token}', function ($token) {
        return view('livewire.pages.auth.reset-password', ['token' => $token]);
    })->name('password.reset');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard / Today View
    Route::get('/today', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile Management
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    // Inbox Routes (EPIC-002)
    Route::get('/inbox', function () {
        return view('livewire.pages.inbox.index');
    })->name('inbox.index');

    // Tasks Routes (EPIC-003)
    Route::get('/tasks', function () {
        return view('livewire.pages.tasks.index');
    })->name('tasks.index');

    // Projects Routes (EPIC-004)
    Route::prefix('projects')->name('projects.')->group(function () {
        // Will be implemented in EPIC-004
    });

    // Goals Routes (EPIC-004)
    Route::prefix('goals')->name('goals.')->group(function () {
        // Will be implemented in EPIC-004
    });

    // Notes Routes (EPIC-005)
    Route::prefix('notes')->name('notes.')->group(function () {
        // Will be implemented in EPIC-005
    });

    // Tags Routes (Shared)
    Route::prefix('tags')->name('tags.')->group(function () {
        // Tag management routes
    });
});
