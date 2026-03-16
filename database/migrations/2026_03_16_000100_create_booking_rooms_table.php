<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->decimal('nightly_rate', 10, 2);
            $table->timestamps();

            $table->unique(['booking_id', 'room_id']);
            $table->index(['room_id', 'booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_rooms');
    }
};
