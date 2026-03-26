<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class Booking extends Model
{
    protected static ?array $bookingRoomPivotColumnsCache = null;

    protected $fillable = [
        'booking_reference',
        'guest_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'number_of_guests',
        'total_nights',
        'subtotal',
        'extras_total',
        'tax_amount',
        'total_amount',
        'payment_option',
        'status',
        'special_requests',
        // Additional charges
        'early_checkin_hours',
        'early_checkin_charge',
        'late_checkout_hours',
        'late_checkout_charge',
        // PWD/Senior
        'has_pwd_senior',
        'pwd_senior_count',
        'pwd_senior_discount',
        // Manual adjustments
        'manual_adjustment',
        'adjustment_reason',
        // Cancellation
        'cancelled_at',
        'cancellation_reason',
        'refund_status',
        'rescheduled_at',
        'original_check_in_date',
        'expires_at',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'original_check_in_date' => 'date',
        'subtotal' => 'decimal:2',
        'extras_total' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'early_checkin_charge' => 'decimal:2',
        'late_checkout_charge' => 'decimal:2',
        'pwd_senior_discount' => 'decimal:2',
        'manual_adjustment' => 'decimal:2',
        'has_pwd_senior' => 'boolean',
        'cancelled_at' => 'datetime',
        'rescheduled_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function getFinalTotalAttribute()
    {
        return $this->total_amount 
             + $this->early_checkin_charge 
             + $this->late_checkout_charge 
             - $this->pwd_senior_discount 
             + $this->manual_adjustment;
    }

    public static function applyActiveReservationFilter($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'checked_in', 'rescheduled']);
    }

    public static function applyDateConflictWindow($query, string $checkInDate, string $checkOutDate)
    {
        $requestedCheckIn = Carbon::parse($checkInDate)->startOfDay();
        $requestedCheckOut = Carbon::parse($checkOutDate)->startOfDay();

        if ($requestedCheckOut->lessThanOrEqualTo($requestedCheckIn)) {
            $requestedCheckOut = $requestedCheckIn->copy()->addDay();
        }

        $normalizedCheckOutDate = $requestedCheckOut->toDateString();

        return $query->where(function ($overlapQuery) use ($checkInDate, $normalizedCheckOutDate) {
            $overlapQuery
                ->where(function ($rangeQuery) use ($checkInDate, $normalizedCheckOutDate) {
                    $rangeQuery
                        ->where('check_in_date', '<', $normalizedCheckOutDate)
                        ->whereRaw(
                            "(CASE WHEN check_out_date <= check_in_date THEN DATE_ADD(check_out_date, INTERVAL 1 DAY) ELSE check_out_date END) > ?",
                            [$checkInDate]
                        );
                })
                ->orWhere(function ($lateCheckoutQuery) use ($checkInDate) {
                    // Late checkout blocks same-day turnover until checkout extension is resolved.
                    $lateCheckoutQuery->whereDate('check_out_date', $checkInDate)
                        ->where('late_checkout_hours', '>', 0)
                        ->whereIn('status', ['confirmed', 'checked_in', 'rescheduled']);
                });
        });
    }

    public function canReschedule()
    {
        return !in_array($this->status, ['checked_in', 'checked_out'], true);
    }

    public function isValidRescheduleDate($newDate)
    {
        $baseCheckIn = \Carbon\Carbon::parse($this->original_check_in_date ?? $this->check_in_date)->startOfDay();
        $requestedDate = \Carbon\Carbon::parse($newDate)->startOfDay();

        return $requestedDate->gt($baseCheckIn)
            && $requestedDate->lessThanOrEqualTo($baseCheckIn->copy()->addDays(14));
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function rooms()
    {
        $pivotColumns = $this->getBookingRoomPivotColumns();

        return $this->belongsToMany(Room::class, 'booking_rooms')
            ->withPivot($pivotColumns)
            ->withTimestamps();
    }

    protected function getBookingRoomPivotColumns(): array
    {
        if (self::$bookingRoomPivotColumnsCache !== null) {
            return self::$bookingRoomPivotColumnsCache;
        }

        $preferredColumns = [
            'nightly_rate',
            'manual_adjustment',
            'additional_charge',
            'additional_charge_reason',
            'discount_amount',
            'discount_type',
        ];

        try {
            if (!Schema::hasTable('booking_rooms')) {
                self::$bookingRoomPivotColumnsCache = [];
                return self::$bookingRoomPivotColumnsCache;
            }

            $existingColumns = Schema::getColumnListing('booking_rooms');
            $availableColumns = array_values(array_filter(
                $preferredColumns,
                fn ($column) => in_array($column, $existingColumns, true)
            ));

            self::$bookingRoomPivotColumnsCache = $availableColumns;

            return self::$bookingRoomPivotColumnsCache;
        } catch (\Throwable $e) {
            self::$bookingRoomPivotColumnsCache = [];
            return self::$bookingRoomPivotColumnsCache;
        }
    }

    public function roomType()
    {
        return $this->hasOneThrough(RoomType::class, Room::class, 'id', 'id', 'room_id', 'room_type_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function extras()
    {
        return $this->belongsToMany(Extra::class, 'booking_extras')
                    ->withPivot('quantity', 'price_at_booking')
                    ->withTimestamps();
    }
}
