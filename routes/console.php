<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('bookings:auto-cancel-expired')->hourly();

// Schedule checkout reminders to be sent every day at 8:00 AM
Schedule::command('bookings:send-checkout-reminders')->dailyAt('08:00');

// Schedule check-in reminders 24 hours before arrival (daily run at 8:00 AM)
Schedule::command('bookings:send-checkin-reminders')->dailyAt('08:00');
