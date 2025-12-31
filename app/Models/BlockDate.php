<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockDate extends Model
{
    protected $fillable = [
        'room_id',
        'start_date',
        'end_date',
        'reason',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
