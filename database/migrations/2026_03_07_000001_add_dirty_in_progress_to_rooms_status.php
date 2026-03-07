<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE rooms MODIFY COLUMN status ENUM('available','occupied','dirty','in_progress','maintenance') DEFAULT 'available'");
    }

    public function down(): void
    {
        // Revert any dirty/in_progress rooms to available before removing the enum values
        DB::table('rooms')->whereIn('status', ['dirty', 'in_progress'])->update(['status' => 'available']);
        DB::statement("ALTER TABLE rooms MODIFY COLUMN status ENUM('available','occupied','maintenance') DEFAULT 'available'");
    }
};
