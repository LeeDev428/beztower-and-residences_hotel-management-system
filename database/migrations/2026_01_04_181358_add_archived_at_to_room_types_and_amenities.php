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
        Schema::table('room_types', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('max_guests');
        });
        
        Schema::table('amenities', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('icon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });
        
        Schema::table('amenities', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });
    }
};
