<?php

use App\Mail\BookingExpired;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomType;
use App\Support\BookingAutoCancelService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Schema::disableForeignKeyConstraints();
    Schema::dropAllTables();
    Schema::enableForeignKeyConstraints();

    Schema::create('room_types', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('base_price', 10, 2)->default(0);
        $table->decimal('discount_percentage', 5, 2)->default(0);
        $table->integer('max_guests')->default(2);
        $table->timestamp('archived_at')->nullable();
        $table->timestamps();
    });

    Schema::create('rooms', function (Blueprint $table) {
        $table->id();
        $table->foreignId('room_type_id');
        $table->string('room_number');
        $table->integer('floor')->default(2);
        $table->text('description')->nullable();
        $table->decimal('discount_percentage', 5, 2)->default(0);
        $table->string('status')->default('available');
        $table->timestamp('archived_at')->nullable();
        $table->timestamps();
    });

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

    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->string('booking_reference')->unique();
        $table->foreignId('guest_id');
        $table->foreignId('room_id');
        $table->date('check_in_date');
        $table->date('check_out_date');
        $table->integer('number_of_guests')->default(1);
        $table->integer('total_nights')->default(1);
        $table->decimal('subtotal', 10, 2)->default(1000);
        $table->decimal('extras_total', 10, 2)->default(0);
        $table->decimal('tax_amount', 10, 2)->default(0);
        $table->decimal('total_amount', 10, 2)->default(1000);
        $table->string('payment_option')->default('down_payment');
        $table->string('status')->default('pending');
        $table->timestamp('expires_at')->nullable();
        $table->text('special_requests')->nullable();
        $table->integer('early_checkin_hours')->default(0);
        $table->decimal('early_checkin_charge', 10, 2)->default(0);
        $table->integer('late_checkout_hours')->default(0);
        $table->decimal('late_checkout_charge', 10, 2)->default(0);
        $table->boolean('has_pwd_senior')->default(false);
        $table->integer('pwd_senior_count')->default(0);
        $table->decimal('pwd_senior_discount', 10, 2)->default(0);
        $table->decimal('manual_adjustment', 10, 2)->default(0);
        $table->string('adjustment_reason')->nullable();
        $table->json('adjustment_items')->nullable();
        $table->timestamp('cancelled_at')->nullable();
        $table->string('cancellation_reason')->nullable();
        $table->string('refund_status')->default('unpaid');
        $table->timestamp('rescheduled_at')->nullable();
        $table->date('original_check_in_date')->nullable();
        $table->timestamps();
    });

    Schema::create('booking_rooms', function (Blueprint $table) {
        $table->id();
        $table->foreignId('booking_id');
        $table->foreignId('room_id');
        $table->decimal('nightly_rate', 10, 2)->default(1000);
        $table->decimal('manual_adjustment', 10, 2)->default(0);
        $table->decimal('additional_charge', 10, 2)->default(0);
        $table->string('additional_charge_reason')->nullable();
        $table->decimal('discount_amount', 10, 2)->default(0);
        $table->string('discount_type')->nullable();
        $table->timestamps();
    });

    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('booking_id');
        $table->string('payment_type')->default('down_payment');
        $table->string('payment_method')->default('gcash');
        $table->string('payment_reference')->nullable();
        $table->decimal('amount', 10, 2)->default(300);
        $table->decimal('percentage', 5, 2)->nullable();
        $table->string('payment_status')->default('pending');
        $table->timestamp('payment_date')->nullable();
        $table->string('proof_of_payment')->nullable();
        $table->text('payment_notes')->nullable();
        $table->timestamp('verified_at')->nullable();
        $table->unsignedBigInteger('verified_by')->nullable();
        $table->timestamps();
    });

    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('action');
        $table->string('model_type')->nullable();
        $table->unsignedBigInteger('model_id')->nullable();
        $table->text('description');
        $table->json('changes')->nullable();
        $table->string('ip_address')->nullable();
        $table->string('user_agent')->nullable();
        $table->timestamps();
    });

    Mail::fake();
});

function createExpiringBooking(array $overrides = []): Booking
{
    $roomType = RoomType::create([
        'name' => 'Standard Room',
        'description' => 'Test room',
        'base_price' => 1000,
        'max_guests' => 2,
    ]);

    $room = Room::create([
        'room_type_id' => $roomType->id,
        'room_number' => (string) random_int(100, 999),
        'floor' => 2,
        'status' => 'available',
    ]);

    $guest = Guest::create([
        'first_name' => 'Test',
        'last_name' => 'Guest',
        'email' => 'guest'.random_int(1000, 9999).'@example.com',
        'phone' => '09171234567',
    ]);

    $booking = Booking::create(array_merge([
        'booking_reference' => 'BEZ-'.strtoupper(bin2hex(random_bytes(4))),
        'guest_id' => $guest->id,
        'room_id' => $room->id,
        'check_in_date' => now()->addDay()->toDateString(),
        'check_out_date' => now()->addDays(2)->toDateString(),
        'number_of_guests' => 1,
        'total_nights' => 1,
        'subtotal' => 1000,
        'extras_total' => 0,
        'tax_amount' => 107.14,
        'total_amount' => 1000,
        'payment_option' => 'down_payment',
        'status' => 'pending',
        'expires_at' => now()->subMinute(),
    ], $overrides));

    $booking->rooms()->attach($room->id, ['nightly_rate' => 1000]);

    return $booking;
}

test('an unpaid reservation is automatically declined after eight hours', function () {
    $booking = createExpiringBooking();

    $count = app(BookingAutoCancelService::class)->cancelExpiredWithoutProof();

    expect($count)->toBe(1);
    $booking->refresh();
    expect($booking->status)->toBe('cancelled')
        ->and($booking->cancelled_at)->not->toBeNull()
        ->and($booking->cancellation_reason)->toBe(BookingAutoCancelService::CANCELLATION_REASON);

    $this->assertDatabaseHas('activity_logs', [
        'action' => 'booking_auto_decline',
        'model_id' => $booking->id,
    ]);
    Mail::assertSent(BookingExpired::class, 1);
});

test('a reservation remains active before its payment deadline', function () {
    $booking = createExpiringBooking(['expires_at' => now()->addMinute()]);

    expect(app(BookingAutoCancelService::class)->cancelExpiredWithoutProof())->toBe(0);
    expect($booking->fresh()->status)->toBe('pending');
    Mail::assertNothingSent();
});

test('submitted payment proof stops automatic decline while verification is pending', function () {
    $booking = createExpiringBooking();
    Payment::create([
        'booking_id' => $booking->id,
        'payment_type' => 'down_payment',
        'payment_method' => 'gcash',
        'payment_reference' => 'PAY123',
        'amount' => 300,
        'percentage' => 30,
        'payment_status' => 'pending',
        'proof_of_payment' => 'payments/proofs/test.jpg',
        'payment_date' => now(),
    ]);

    expect(app(BookingAutoCancelService::class)->cancelExpiredWithoutProof())->toBe(0);
    expect($booking->fresh()->status)->toBe('pending');
    Mail::assertNothingSent();
});

test('a verified payment without an uploaded proof also stops automatic decline', function () {
    $booking = createExpiringBooking();
    Payment::create([
        'booking_id' => $booking->id,
        'payment_type' => 'down_payment',
        'payment_method' => 'cash',
        'payment_reference' => 'DESK123',
        'amount' => 300,
        'percentage' => 30,
        'payment_status' => 'verified',
        'payment_date' => now(),
    ]);

    expect(app(BookingAutoCancelService::class)->cancelExpiredWithoutProof())->toBe(0);
    expect($booking->fresh()->status)->toBe('pending');
});

test('automatic decline is idempotent', function () {
    $booking = createExpiringBooking();
    $service = app(BookingAutoCancelService::class);

    expect($service->cancelExpiredWithoutProof())->toBe(1)
        ->and($service->cancelExpiredWithoutProof())->toBe(0)
        ->and($booking->fresh()->status)->toBe('cancelled');

    Mail::assertSent(BookingExpired::class, 1);
    $this->assertDatabaseCount('activity_logs', 1);
});

test('payment proof cannot be uploaded after the deadline', function () {
    Storage::fake('public');
    $booking = createExpiringBooking();

    $response = $this->post(route('booking.processPayment', $booking->booking_reference), [
        'payment_method' => 'GCash',
        'payment_reference' => 'PAY123456',
        'proof_of_payment' => UploadedFile::fake()->image('proof.jpg'),
    ]);

    $response->assertRedirect(route('booking.payment', $booking->booking_reference));
    $response->assertSessionHas('error');
    expect($booking->fresh()->status)->toBe('cancelled');
    $this->assertDatabaseCount('payments', 0);
    expect(Storage::disk('public')->allFiles('payments/proofs'))->toBeEmpty();
});
