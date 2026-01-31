<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Early check-in / Late checkout
            $table->integer('early_checkin_hours')->default(0)->after('total_amount');
            $table->decimal('early_checkin_charge', 10, 2)->default(0)->after('early_checkin_hours');
            $table->integer('late_checkout_hours')->default(0)->after('early_checkin_charge');
            $table->decimal('late_checkout_charge', 10, 2)->default(0)->after('late_checkout_hours');
            
            // PWD/Senior Citizen discount
            $table->boolean('has_pwd_senior')->default(false)->after('late_checkout_charge');
            $table->integer('pwd_senior_count')->default(0)->after('has_pwd_senior');
            $table->decimal('pwd_senior_discount', 10, 2)->default(0)->after('pwd_senior_count');
            
            // Manual adjustments
            $table->decimal('manual_adjustment', 10, 2)->default(0)->after('pwd_senior_discount');
            $table->string('adjustment_reason')->nullable()->after('manual_adjustment');
            
            // Cancellation fields
            $table->timestamp('cancelled_at')->nullable()->after('adjustment_reason');
            $table->string('cancellation_reason')->nullable()->after('cancelled_at');
            $table->enum('refund_status', ['unpaid', 'partially_paid', 'paid'])->default('unpaid')->after('cancellation_reason');
            $table->timestamp('rescheduled_at')->nullable()->after('refund_status');
            $table->date('original_check_in_date')->nullable()->after('rescheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'early_checkin_hours', 'early_checkin_charge',
                'late_checkout_hours', 'late_checkout_charge',
                'has_pwd_senior', 'pwd_senior_count', 'pwd_senior_discount',
                'manual_adjustment', 'adjustment_reason',
                'cancelled_at', 'cancellation_reason', 'refund_status',
                'rescheduled_at', 'original_check_in_date'
            ]);
        });
    }
};
