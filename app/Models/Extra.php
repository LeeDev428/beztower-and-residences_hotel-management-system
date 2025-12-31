<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_extras')
                    ->withPivot('quantity', 'price_at_booking')
                    ->withTimestamps();
    }
}
