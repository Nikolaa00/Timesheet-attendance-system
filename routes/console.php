<?php

use App\Http\Services\AttendanceService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('attendance:auto-checkout', function () {
    app(AttendanceService::class)->autoCheckOut();
    $this->info('Auto check-out completed.');
})->purpose('Check out users checked in for more than 8 hours');

Schedule::command('attendance:auto-checkout')->everyTenMinutes();
