<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('guests')
            ->whereNull('email')
            ->orderBy('id')
            ->eachById(function ($guest) {
                DB::table('guests')
                    ->where('id', $guest->id)
                    ->update(['email' => 'guest-'.$guest->id.'@placeholder.invalid']);
            });

        Schema::table('guests', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }
};
