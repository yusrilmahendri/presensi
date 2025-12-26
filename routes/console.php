<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Scheduled Tasks

// Send reminder 5-10 minutes before shift starts
Schedule::command('attendance:send-upcoming-checkin-reminder')
    ->everyMinute()
    ->description('Send reminder 5-10 minutes before shift starts');

// Send reminder after shift has started
Schedule::command('attendance:send-checkin-reminder')
    ->dailyAt('08:30')
    ->description('Send check-in reminders to employees');

// Send check-out reminder before shift ends
Schedule::command('attendance:send-checkout-reminder')
    ->everyFiveMinutes()
    ->between('14:00', '18:00')
    ->description('Send check-out reminders before shift ends');

// Send weekly summary to admins
Schedule::command('attendance:send-weekly-summary')
    ->weeklyOn(1, '08:00') // Every Monday at 8 AM
    ->description('Send weekly attendance summary to admins');

// Send notification for upcoming leaves
Schedule::command('attendance:send-upcoming-leave-notification')
    ->dailyAt('17:00')
    ->description('Send notification for leaves starting tomorrow');

