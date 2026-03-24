<?php

namespace App\Console\Commands;

use App\Mail\CheckinReminder;
use App\Models\AppSetting;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCheckinReminders extends Command
{
    protected $signature = 'bookings:send-checkin-reminders';

    protected $description = 'Send check-in reminder emails to guests arriving in the next 24 hours';

    public function handle()
    {
        $configuredCheckInTime = AppSetting::getValue('check_in_time', '14:00');
        $now = now();
        $windowStart = $now->copy()->addHours(24)->subMinutes(30);
        $windowEnd = $now->copy()->addHours(24)->addMinutes(30);

        $bookings = Booking::with(['guest', 'rooms.roomType', 'room.roomType'])
            ->whereIn('status', ['confirmed', 'rescheduled'])
            ->whereHas('payments', function ($query) {
                $query->whereIn('payment_status', ['verified', 'completed']);
            })
            ->get();

        $bookings = $bookings->filter(function (Booking $booking) use ($configuredCheckInTime, $windowStart, $windowEnd) {
            $checkInDate = optional($booking->check_in_date)?->toDateString();
            if (!$checkInDate) {
                return false;
            }

            $checkInDateTime = Carbon::parse($checkInDate . ' ' . $configuredCheckInTime);

            return $checkInDateTime->between($windowStart, $windowEnd);
        });

        if ($bookings->isEmpty()) {
            $this->info('No qualified check-in reminders to send in the 24-hour window.');
            return 0;
        }

        $sentCount = 0;

        foreach ($bookings as $booking) {
            if (!$booking->guest || !$booking->guest->email) {
                continue;
            }

            try {
                Mail::to($booking->guest->email)->send(new CheckinReminder($booking));
                $this->info("Sent check-in reminder to {$booking->guest->email}");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("Failed to send reminder to {$booking->guest->email}: " . $e->getMessage());
            }
        }

        $this->info("Successfully sent {$sentCount} check-in reminder(s).");
        return 0;
    }
}
