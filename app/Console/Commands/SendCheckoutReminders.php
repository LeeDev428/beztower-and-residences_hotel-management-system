<?php

namespace App\Console\Commands;

use App\Mail\CheckoutReminder;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCheckoutReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-checkout-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send checkout reminder emails to guests checking out today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();
        
        // Get all bookings with checkout date = today and status = checked_in
        $bookings = Booking::with('guest')
            ->where('check_out_date', $today)
            ->where('status', 'checked_in')
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No checkouts scheduled for today.');
            return 0;
        }

        $sentCount = 0;
        
        foreach ($bookings as $booking) {
            try {
                Mail::to($booking->guest->email)->send(new CheckoutReminder($booking));
                $this->info("Sent checkout reminder to {$booking->guest->email}");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("Failed to send reminder to {$booking->guest->email}: " . $e->getMessage());
            }
        }

        $this->info("Successfully sent {$sentCount} checkout reminder(s).");
        return 0;
    }
}
