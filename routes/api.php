<?php

use Illuminate\Support\Facades\Route;

Route::get('/user', function () {
    return auth()->user();
})->middleware('auth:sanctum');
