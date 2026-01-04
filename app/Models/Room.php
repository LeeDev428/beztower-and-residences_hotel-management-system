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
        'status',
        'archived_at'
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    // Scope to get only active (non-archived) rooms
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    // Scope to get only archived rooms
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    // Method to archive a room
    public function archive()
    {
        $this->archived_at = now();
        $this->save();
    }

    // Method to restore an archived room
    public function restore()
    {
        $this->archived_at = null;
        $this->save();
    }

    // Check if room is archived
    public function isArchived()
    {
        return !is_null($this->archived_at);
    }

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
