<?php

namespace App\Support;

use App\Models\Booking;
use Illuminate\Support\Facades\Cache;

class BookingAutoCancelService
{
    public function cancelExpiredWithoutProofIfDue(int $cooldownSeconds = 60): int
    {
        $cacheKey = 'bookings:auto-cancel:last-run-at';
        $now = now()->timestamp;
        $lastRun = (int) Cache::get($cacheKey, 0);

        if ($cooldownSeconds > 0 && ($now - $lastRun) < $cooldownSeconds) {
            return 0;
        }

        Cache::put($cacheKey, $now, now()->addSeconds(max($cooldownSeconds, 60)));

        return $this->cancelExpiredWithoutProof();
    }

    public function cancelExpiredWithoutProof(): int
    {
        $cancelledCount = 0;

        Booking::query()
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->whereDoesntHave('payments', function ($paymentQuery) {
                $paymentQuery->where(function ($nestedQuery) {
                    $nestedQuery->whereNotNull('proof_of_payment')
                        ->orWhereIn('payment_status', ['verified', 'completed']);
                });
            })
            ->with(['rooms', 'room'])
            ->chunkById(100, function ($bookings) use (&$cancelledCount) {
                foreach ($bookings as $booking) {
                    $updated = Booking::query()
                        ->whereKey($booking->id)
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->update([
                            'status' => 'cancelled',
                            'cancellation_reason' => 'Automatically cancelled: payment proof not received within 8 hours.',
                        ]);

                    if ($updated === 0) {
                        continue;
                    }

                    $roomsToRelease = $booking->rooms->isNotEmpty()
                        ? $booking->rooms
                        : collect([$booking->room])->filter();

                    foreach ($roomsToRelease as $room) {
                        $room->update(['status' => 'available']);
                    }

                    $cancelledCount++;
                }
            });

        return $cancelledCount;
    }
}
