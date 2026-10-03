<?php

namespace App\Support;

use App\Mail\BookingExpired;
use App\Models\ActivityLog;
use App\Models\Booking;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingAutoCancelService
{
    public const CANCELLATION_REASON = 'Automatically declined: payment proof was not received within 8 hours.';

    public function cancelExpiredWithoutProofIfDue(int $cooldownSeconds = 60): int
    {
        $cacheKey = 'bookings:auto-cancel:running';

        if ($cooldownSeconds > 0 && !Cache::add($cacheKey, true, now()->addSeconds(max($cooldownSeconds, 60)))) {
            return 0;
        }

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
                $this->applySubmittedPaymentFilter($paymentQuery);
            })
            ->select('id')
            ->chunkById(100, function ($bookings) use (&$cancelledCount) {
                foreach ($bookings as $candidate) {
                    if ($this->cancelBookingIfExpired((int) $candidate->id)) {
                        $cancelledCount++;
                    }
                }
            });

        return $cancelledCount;
    }

    /**
     * Atomically cancel one booking if its payment window has expired.
     * Payment submission also locks this row, so both operations cannot win.
     */
    public function cancelBookingIfExpired(int $bookingId): ?Booking
    {
        $cancelledBooking = DB::transaction(function () use ($bookingId) {
            $booking = Booking::query()
                ->whereKey($bookingId)
                ->lockForUpdate()
                ->first();

            if (!$booking || !$this->cancelLockedBookingIfExpired($booking)) {
                return null;
            }

            return $booking;
        }, 3);

        if ($cancelledBooking) {
            $this->notifyExpiredBooking($cancelledBooking);
        }

        return $cancelledBooking;
    }

    /**
     * The caller must hold a database lock on the booking row.
     */
    public function cancelLockedBookingIfExpired(Booking $booking): bool
    {
        if (!in_array((string) $booking->status, ['pending', 'confirmed'], true)) {
            return false;
        }

        if (!$booking->expires_at || $booking->expires_at->isFuture()) {
            return false;
        }

        if ($booking->payments()->where(function ($paymentQuery) {
            $this->applySubmittedPaymentFilter($paymentQuery);
        })->exists()) {
            return false;
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => self::CANCELLATION_REASON,
        ]);

        $this->releaseRooms($booking);
        $this->recordAutomaticCancellation($booking);

        return true;
    }

    public function notifyExpiredBooking(Booking $booking): void
    {
        $booking->loadMissing(['guest', 'room.roomType', 'rooms.roomType']);
        $guestEmail = trim((string) optional($booking->guest)->email);

        if ($guestEmail === '') {
            return;
        }

        try {
            Mail::to($guestEmail)->send(new BookingExpired($booking));
        } catch (\Throwable $e) {
            Log::error('Failed to send automatic booking-expiration email.', [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function applySubmittedPaymentFilter($query): void
    {
        $query->whereNotNull('proof_of_payment')
            ->orWhereIn('payment_status', ['verified', 'completed']);
    }

    private function releaseRooms(Booking $booking): void
    {
        $booking->loadMissing(['rooms', 'room']);

        $rooms = $booking->rooms->isNotEmpty()
            ? $booking->rooms
            : collect([$booking->room])->filter();

        foreach ($rooms as $room) {
            $hasCheckedInOccupant = Booking::query()
                ->where('id', '!=', $booking->id)
                ->where('status', 'checked_in')
                ->where(function ($query) use ($room) {
                    $query->where('room_id', $room->id)
                        ->orWhereHas('rooms', function ($roomsQuery) use ($room) {
                            $roomsQuery->where('rooms.id', $room->id);
                        });
                })
                ->exists();

            if ($hasCheckedInOccupant && $room->status !== 'occupied') {
                $room->update(['status' => 'occupied']);
            } elseif (!$hasCheckedInOccupant && $room->status === 'occupied') {
                $room->update(['status' => 'available']);
            }
        }
    }

    private function recordAutomaticCancellation(Booking $booking): void
    {
        try {
            ActivityLog::create([
                'user_id' => null,
                'action' => 'booking_auto_decline',
                'model_type' => Booking::class,
                'model_id' => $booking->id,
                'description' => 'Automatically declined booking #' . $booking->booking_reference . ' after the 8-hour payment deadline.',
                'changes' => [
                    'status' => 'cancelled',
                    'expires_at' => optional($booking->expires_at)?->toIso8601String(),
                    'reason' => self::CANCELLATION_REASON,
                ],
                'ip_address' => null,
                'user_agent' => 'Laravel Scheduler',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Unable to record automatic booking cancellation.', [
                'booking_id' => $booking->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
