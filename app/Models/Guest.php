<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'address',
        'id_photo',
        'preferences'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getNameAttribute()
    {
        $firstName = trim((string) ($this->attributes['first_name'] ?? ''));
        $lastName = trim((string) ($this->attributes['last_name'] ?? ''));
        $combined = trim($firstName . ' ' . $lastName);

        if ($combined !== '') {
            return $combined;
        }

        return (string) ($this->attributes['name'] ?? 'Guest');
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }
}
