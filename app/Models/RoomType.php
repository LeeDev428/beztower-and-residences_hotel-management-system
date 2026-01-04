<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'base_price',
        'discount_percentage',
        'max_guests',
        'archived_at'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'archived_at' => 'datetime'
    ];

    // Scope to get only active (non-archived) room types
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    // Scope to get only archived room types
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    // Method to archive a room type
    public function archive()
    {
        $this->archived_at = now();
        $this->save();
    }

    // Method to restore an archived room type
    public function restore()
    {
        $this->archived_at = null;
        $this->save();
    }

    // Check if room type is archived
    public function isArchived()
    {
        return !is_null($this->archived_at);
    }

    // Get discounted price
    public function getDiscountedPriceAttribute()
    {
        if ($this->discount_percentage > 0) {
            return $this->base_price * (1 - ($this->discount_percentage / 100));
        }
        return $this->base_price;
    }

    // Get discount amount
    public function getDiscountAmountAttribute()
    {
        if ($this->discount_percentage > 0) {
            return $this->base_price * ($this->discount_percentage / 100);
        }
        return 0;
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
