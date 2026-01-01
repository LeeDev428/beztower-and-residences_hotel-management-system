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
        'status',
        'special_requests'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'subtotal' => 'decimal:2',
        'extras_total' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

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
