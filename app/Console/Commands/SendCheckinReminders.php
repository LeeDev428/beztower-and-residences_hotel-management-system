<?php

namespace App\Console\Commands;

use App\Mail\CheckinReminder;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCheckinReminders extends Command
{
    protected $signature = 'bookings:send-checkin-reminders';

    protected $description = 'Send check-in reminder emails to guests arriving in the next 24 hours';

    public function handle()
    {
        $tomorrow = now()->addDay()->toDateString();

        $bookings = Booking::with(['guest', 'rooms.roomType', 'room.roomType'])
            ->whereDate('check_in_date', $tomorrow)
            ->whereIn('status', ['confirmed'])
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No check-ins scheduled for tomorrow.');
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
