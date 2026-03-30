<?php

namespace App\Console\Commands;

use App\Support\BookingAutoCancelService;
use Illuminate\Console\Command;

class AutoCancelExpiredBookings extends Command
{
    protected $signature = 'bookings:auto-cancel-expired';
    protected $description = 'Cancel pending/confirmed bookings that have exceeded the 8-hour payment deadline without payment proof';

    public function handle(): void
    {
        $cancelledCount = app(BookingAutoCancelService::class)->cancelExpiredWithoutProof();

        $this->info("Cancelled {$cancelledCount} expired booking(s) without payment proof.");
    }
}
