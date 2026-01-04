<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'archived_at'
    ];

    protected $casts = [
        'archived_at' => 'datetime'
    ];

    // Scope to get only active (non-archived) amenities
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    // Scope to get only archived amenities
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    // Method to archive an amenity
    public function archive()
    {
        $this->archived_at = now();
        $this->save();
    }

    // Method to restore an archived amenity
    public function restore()
    {
        $this->archived_at = null;
        $this->save();
    }

    // Check if amenity is archived
    public function isArchived()
    {
        return !is_null($this->archived_at);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_amenities');
    }
}
