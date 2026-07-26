<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| Dapat diakses tanpa login. Auth routes (login, register, dst.)
| ditangani oleh Laravel Fortify — tidak didefinisikan di sini.
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
| Seluruh route di dalam group ini memerlukan user sudah login.
| Tambahkan semua route modul di sini — jangan definisikan route
| authenticated di luar group ini.
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/today', function () {
        return view('dashboard');
    });
});
