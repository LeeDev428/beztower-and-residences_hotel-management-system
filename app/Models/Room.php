<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_type_id',
        'room_number',
        'floor',
        'description',
        'status'
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'room_amenities');
    }

    public function photos()
    {
        return $this->hasMany(RoomPhoto::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function housekeeping()
    {
        return $this->hasOne(Housekeeping::class)->latest();
    }

    public function blockDates()
    {
        return $this->hasMany(BlockDate::class);
    }
}
