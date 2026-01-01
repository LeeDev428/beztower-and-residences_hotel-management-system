<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule checkout reminders to be sent every day at 8:00 AM
Schedule::command('bookings:send-checkout-reminders')->dailyAt('08:00');
