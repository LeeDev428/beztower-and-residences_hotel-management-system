<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\RoomController;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoomManagementController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\BookingManagementController;
use App\Http\Controllers\Admin\GuestManagementController;
use App\Http\Controllers\Admin\HousekeepingController;

// Customer Routes (Public - No Login Required)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
Route::get('/api/calendar/availability', [RoomController::class, 'getAvailability'])->name('calendar.availability');
Route::post('/booking/check-availability', [BookingController::class, 'checkAvailability'])->name('booking.checkAvailability');
Route::post('/booking/create', [BookingController::class, 'create'])->name('booking.create');
Route::get('/booking/payment/{reference}', [BookingController::class, 'payment'])->name('booking.payment');
Route::post('/booking/payment/{reference}', [BookingController::class, 'processPayment'])->name('booking.processPayment');
Route::get('/booking/confirmation/{reference}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
Route::get('/booking/download-pdf/{reference}', [BookingController::class, 'downloadPDF'])->name('booking.downloadPDF');

// Admin Routes (Protected - Login Required)
Route::prefix('admin')->group(function () {
    // Admin Authentication
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Protected Admin Routes
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // Room Management
        Route::prefix('rooms')->group(function () {
            Route::get('/', [RoomManagementController::class, 'index'])->name('admin.rooms.index');
            Route::get('/create', [RoomManagementController::class, 'create'])->name('admin.rooms.create')->middleware('role:admin,manager');
            Route::post('/', [RoomManagementController::class, 'store'])->name('admin.rooms.store')->middleware('role:admin,manager');
            Route::get('/{room}/edit', [RoomManagementController::class, 'edit'])->name('admin.rooms.edit')->middleware('role:admin,manager');
            Route::put('/{room}', [RoomManagementController::class, 'update'])->name('admin.rooms.update')->middleware('role:admin,manager');
            Route::delete('/{room}', [RoomManagementController::class, 'destroy'])->name('admin.rooms.destroy')->middleware('role:admin');
            Route::post('/{room}/restore', [RoomManagementController::class, 'restore'])->name('admin.rooms.restore')->middleware('role:admin');
            Route::post('/{room}/upload-photo', [RoomManagementController::class, 'uploadPhoto'])->name('admin.rooms.uploadPhoto')->middleware('role:admin,manager');
            Route::delete('/{room}/photos/{photo}', [RoomManagementController::class, 'deletePhoto'])->name('admin.rooms.deletePhoto')->middleware('role:admin,manager');
            Route::post('/{room}/block-dates', [RoomManagementController::class, 'blockDates'])->name('admin.rooms.blockDates')->middleware('role:admin,manager');
        });
        
        // Room Types Management
        Route::prefix('room-types')->group(function () {
            Route::get('/', [RoomTypeController::class, 'index'])->name('admin.room-types.index');
            Route::post('/', [RoomTypeController::class, 'store'])->name('admin.room-types.store')->middleware('role:admin,manager');
            Route::put('/{roomType}', [RoomTypeController::class, 'update'])->name('admin.room-types.update')->middleware('role:admin,manager');
            Route::delete('/{roomType}', [RoomTypeController::class, 'destroy'])->name('admin.room-types.destroy')->middleware('role:admin');
            Route::post('/{roomType}/restore', [RoomTypeController::class, 'restore'])->name('admin.room-types.restore')->middleware('role:admin');
        });
        
        // Amenities Management
        Route::prefix('amenities')->group(function () {
            Route::get('/', [AmenityController::class, 'index'])->name('admin.amenities.index');
            Route::post('/', [AmenityController::class, 'store'])->name('admin.amenities.store')->middleware('role:admin,manager');
            Route::put('/{amenity}', [AmenityController::class, 'update'])->name('admin.amenities.update')->middleware('role:admin,manager');
            Route::delete('/{amenity}', [AmenityController::class, 'destroy'])->name('admin.amenities.destroy')->middleware('role:admin');
            Route::post('/{amenity}/restore', [AmenityController::class, 'restore'])->name('admin.amenities.restore')->middleware('role:admin');
        });
        
        // Booking Management
        Route::prefix('bookings')->group(function () {
            Route::get('/', [BookingManagementController::class, 'index'])->name('admin.bookings.index');
            Route::get('/{booking}', [BookingManagementController::class, 'show'])->name('admin.bookings.show');
            Route::put('/{booking}/status', [BookingManagementController::class, 'updateStatus'])->name('admin.bookings.updateStatus');
        });
        
        // Guest Management
        Route::prefix('guests')->group(function () {
            Route::get('/', [GuestManagementController::class, 'index'])->name('admin.guests.index');
            Route::get('/{guest}', [GuestManagementController::class, 'show'])->name('admin.guests.show');
            Route::put('/{guest}', [GuestManagementController::class, 'update'])->name('admin.guests.update')->middleware('role:admin,manager');
        });
        
        // Housekeeping
        Route::prefix('housekeeping')->group(function () {
            Route::get('/', [HousekeepingController::class, 'index'])->name('admin.housekeeping.index');
            Route::put('/{housekeeping}', [HousekeepingController::class, 'update'])->name('admin.housekeeping.update');
            Route::post('/assign', [HousekeepingController::class, 'assign'])->name('admin.housekeeping.assign')->middleware('role:admin,manager');
        });

        // Payment Verification
        Route::prefix('payments')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('admin.payments.index');
            Route::get('/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('admin.payments.show');
            Route::post('/{payment}/verify', [\App\Http\Controllers\Admin\PaymentController::class, 'verify'])->name('admin.payments.verify')->middleware('role:admin,manager');
            Route::post('/{payment}/reject', [\App\Http\Controllers\Admin\PaymentController::class, 'reject'])->name('admin.payments.reject')->middleware('role:admin,manager');
        });

        // Reports
        Route::prefix('reports')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');
            Route::get('/revenue', [\App\Http\Controllers\Admin\ReportController::class, 'revenue'])->name('admin.reports.revenue')->middleware('role:admin,manager');
            Route::get('/occupancy', [\App\Http\Controllers\Admin\ReportController::class, 'occupancy'])->name('admin.reports.occupancy');
            Route::get('/export/{type}', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('admin.reports.export')->middleware('role:admin,manager');
        });

        // Users & Roles (Admin Only)
        Route::middleware('role:admin')->prefix('users')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
            Route::get('/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('admin.users.create');
            Route::post('/', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
            Route::get('/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('admin.users.edit');
            Route::put('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
            Route::delete('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
        });

        // Settings (Admin Only)
        Route::middleware('role:admin')->prefix('settings')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
            Route::put('/', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');
        });
    });
});

