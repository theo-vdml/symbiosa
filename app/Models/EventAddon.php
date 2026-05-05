<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAddon extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'description',
        'price',
        'capacity',
        'sold_count',
        'reserved_count',
        'available_from',
        'available_until',
        'max_per_order',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'integer',
        'capacity' => 'integer',
        'sold_count' => 'integer',
        'reserved_count' => 'integer',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    protected function priceInEuro(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['price'] / 100,
            set: fn($value) => $value * 100,
        );
    }
}
