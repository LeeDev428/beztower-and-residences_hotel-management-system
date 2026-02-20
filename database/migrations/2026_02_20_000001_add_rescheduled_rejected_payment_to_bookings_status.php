<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','confirmed','checked_in','checked_out','cancelled','rescheduled','rejected_payment') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert statuses that no longer exist in the original enum
        DB::statement("UPDATE bookings SET status = 'cancelled' WHERE status IN ('rescheduled','rejected_payment')");
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','confirmed','checked_in','checked_out','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
