<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class AutoCancelExpiredBookings extends Command
{
    protected $signature = 'bookings:auto-cancel-expired';
    protected $description = 'Cancel pending bookings that have exceeded the 8-hour payment deadline';

    public function handle(): void
    {
        $expired = Booking::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->whereDoesntHave('payments', fn ($q) => $q->whereIn('payment_status', ['verified', 'completed']))
            ->get();

        foreach ($expired as $booking) {
            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'Automatically cancelled: payment not received within 8 hours.',
            ]);

            if ($booking->room) {
                $booking->room->update(['status' => 'available']);
            }
        }

        $this->info("Cancelled {$expired->count()} expired booking(s).");
    }
}
