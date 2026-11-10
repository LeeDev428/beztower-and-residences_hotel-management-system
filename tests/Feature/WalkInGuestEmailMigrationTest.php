<?php

use App\Models\Guest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

it('allows a walk-in guest to be saved without an email address', function () {
    Schema::disableForeignKeyConstraints();
    Schema::dropAllTables();
    Schema::enableForeignKeyConstraints();

    Schema::create('guests', function (Blueprint $table) {
        $table->id();
        $table->string('first_name');
        $table->string('last_name');
        $table->string('email');
        $table->string('phone');
        $table->string('country')->nullable();
        $table->text('address')->nullable();
        $table->string('id_photo')->nullable();
        $table->text('preferences')->nullable();
        $table->timestamps();
    });

    $migration = require database_path('migrations/2026_11_10_000001_make_guest_email_nullable.php');
    $migration->up();

    $guest = Guest::create([
        'first_name' => 'Juan',
        'last_name' => 'Dela Cruz',
        'phone' => '09123456789',
        'email' => null,
    ]);

    expect($guest->refresh()->email)->toBeNull();
    $this->assertDatabaseHas('guests', [
        'phone' => '09123456789',
        'email' => null,
    ]);
});
