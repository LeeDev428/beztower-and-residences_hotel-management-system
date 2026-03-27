<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Run frequently to enforce the 8-hour payment deadline with minimal delay.
Schedule::command('bookings:auto-cancel-expired')->everyMinute();

// Schedule checkout reminders to be sent every day at 8:00 AM
Schedule::command('bookings:send-checkout-reminders')->dailyAt('08:00');

// Schedule check-in reminders hourly so each booking gets a reminder near exactly 24 hours before check-in.
Schedule::command('bookings:send-checkin-reminders')->hourly();
