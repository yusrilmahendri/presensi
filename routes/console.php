<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Scheduled Tasks
Schedule::command('attendance:send-checkin-reminder')
    ->dailyAt('08:30')
    ->description('Send check-in reminders to employees');

Schedule::command('attendance:send-checkout-reminder')
    ->everyFiveMinutes()
    ->between('14:00', '18:00')
    ->description('Send check-out reminders before shift ends');

Schedule::command('attendance:send-weekly-summary')
    ->weeklyOn(1, '08:00') // Every Monday at 8 AM
    ->description('Send weekly attendance summary to admins');

Schedule::command('attendance:send-upcoming-leave-notification')
    ->dailyAt('17:00')
    ->description('Send notification for leaves starting tomorrow');

