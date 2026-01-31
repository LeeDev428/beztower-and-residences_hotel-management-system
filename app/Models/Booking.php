<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
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
        'original_check_in_date'
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
        'rescheduled_at' => 'datetime'
    ];

    public function getFinalTotalAttribute()
    {
        return $this->total_amount 
             + $this->early_checkin_charge 
             + $this->late_checkout_charge 
             - $this->pwd_senior_discount 
             + $this->manual_adjustment;
    }

    public function canReschedule()
    {
        if (!$this->cancelled_at) return false;
        
        // Must reschedule within 1 week of cancellation
        $oneWeekFromCancellation = $this->cancelled_at->copy()->addWeek();
        if (now()->greaterThan($oneWeekFromCancellation)) return false;
        
        return true;
    }

    public function isValidRescheduleDate($newDate)
    {
        // New date must be within 1 month from original booking date
        $originalCheckIn = $this->original_check_in_date ?? $this->check_in_date;
        $maxRescheduleDate = $originalCheckIn->copy()->addMonth();
        
        return $newDate->lessThanOrEqualTo($maxRescheduleDate);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
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
