<?php

use App\Jobs\ScanDeadlines;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(ScanDeadlines::class)
    ->dailyAt('07:00')
    ->name('scan-deadlines')
    ->withoutOverlapping();
