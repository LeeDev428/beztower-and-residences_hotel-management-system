<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class AutoCancelExpiredBookings extends Command
{
    protected $signature = 'bookings:auto-cancel-expired';
    protected $description = 'Cancel pending/confirmed bookings that have exceeded the 8-hour payment deadline without payment proof';

    public function handle(): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $expired */
        $expired = Booking::whereIn('status', ['pending', 'confirmed'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->whereDoesntHave('payments', function ($paymentQuery) {
                $paymentQuery->whereNotNull('proof_of_payment')
                    ->orWhereIn('payment_status', ['verified', 'completed']);
            })
            ->get();

        /** @var \App\Models\Booking $booking */
        foreach ($expired as $booking) {
            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'Automatically cancelled: payment proof not received within 8 hours.',
            ]);

            $booking->loadMissing(['rooms', 'room']);
            $roomsToRelease = $booking->rooms->isNotEmpty()
                ? $booking->rooms
                : collect([$booking->room])->filter();

            foreach ($roomsToRelease as $room) {
                $room->update(['status' => 'available']);
            }
        }

        $this->info("Cancelled {$expired->count()} expired booking(s) without payment proof.");
    }
}
