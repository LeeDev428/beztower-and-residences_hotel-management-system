<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\RoomController;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoomManagementController;
use App\Http\Controllers\Admin\BookingManagementController;
use App\Http\Controllers\Admin\GuestManagementController;
use App\Http\Controllers\Admin\HousekeepingController;

// Customer Routes (Public - No Login Required)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
Route::post('/booking/check-availability', [BookingController::class, 'checkAvailability'])->name('booking.checkAvailability');
Route::post('/booking/create', [BookingController::class, 'create'])->name('booking.create');
Route::get('/booking/payment/{reference}', [BookingController::class, 'payment'])->name('booking.payment');
Route::post('/booking/payment/{reference}', [BookingController::class, 'processPayment'])->name('booking.processPayment');
Route::get('/booking/confirmation/{reference}', [BookingController::class, 'confirmation'])->name('booking.confirmation');

// Admin Routes (Protected - Login Required)
Route::prefix('admin')->group(function () {
    // Admin Authentication
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Protected Admin Routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // Room Management
        Route::get('/rooms', [RoomManagementController::class, 'index'])->name('admin.rooms.index');
        Route::get('/rooms/create', [RoomManagementController::class, 'create'])->name('admin.rooms.create');
        Route::post('/rooms', [RoomManagementController::class, 'store'])->name('admin.rooms.store');
        Route::get('/rooms/{room}/edit', [RoomManagementController::class, 'edit'])->name('admin.rooms.edit');
        Route::put('/rooms/{room}', [RoomManagementController::class, 'update'])->name('admin.rooms.update');
        Route::delete('/rooms/{room}', [RoomManagementController::class, 'destroy'])->name('admin.rooms.destroy');
        
        // Booking Management
        Route::get('/bookings', [BookingManagementController::class, 'index'])->name('admin.bookings.index');
        Route::get('/bookings/{booking}', [BookingManagementController::class, 'show'])->name('admin.bookings.show');
        Route::put('/bookings/{booking}/status', [BookingManagementController::class, 'updateStatus'])->name('admin.bookings.updateStatus');
        
        // Guest Management
        Route::get('/guests', [GuestManagementController::class, 'index'])->name('admin.guests.index');
        Route::get('/guests/{guest}', [GuestManagementController::class, 'show'])->name('admin.guests.show');
        
        // Housekeeping
        Route::get('/housekeeping', [HousekeepingController::class, 'index'])->name('admin.housekeeping.index');
        Route::put('/housekeeping/{housekeeping}', [HousekeepingController::class, 'update'])->name('admin.housekeeping.update');
    });
});

