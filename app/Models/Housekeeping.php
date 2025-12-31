<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Housekeeping extends Model
{
    protected $fillable = [
        'room_id',
        'status',
        'assigned_to',
        'notes',
        'last_cleaned_at'
    ];

    protected $casts = [
        'last_cleaned_at' => 'datetime'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
