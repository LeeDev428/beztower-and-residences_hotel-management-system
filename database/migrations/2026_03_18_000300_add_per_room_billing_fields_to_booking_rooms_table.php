<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_rooms', function (Blueprint $table) {
            $table->decimal('additional_charge', 10, 2)
                ->default(0)
                ->after('manual_adjustment');

            $table->string('additional_charge_reason')
                ->nullable()
                ->after('additional_charge');

            $table->decimal('discount_amount', 10, 2)
                ->default(0)
                ->after('additional_charge_reason');

            $table->string('discount_type')
                ->nullable()
                ->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('booking_rooms', function (Blueprint $table) {
            $table->dropColumn([
                'additional_charge',
                'additional_charge_reason',
                'discount_amount',
                'discount_type',
            ]);
        });
    }
};
