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
        'sort_order',
        'price_in_euro',
    ];

    protected $casts = [
        'price' => 'integer',
        'capacity' => 'integer',
        'sold_count' => 'integer',
        'reserved_count' => 'integer',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
    ];

    protected $appends = ['status', 'price_in_euro'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getStatusAttribute(): string
    {
        $isSoon = $this->available_from && $this->available_from->isFuture();
        $isSoldOut = ($this->capacity > 0 && $this->sold_count >= $this->capacity) ||
            ($this->available_until && $this->available_until->isPast());

        if ($isSoon) return 'soon';
        if ($isSoldOut) return 'sold_out';

        return 'available';
    }

    protected function priceInEuro(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => isset($attributes['price']) ? $attributes['price'] / 100 : 0,
            set: fn($value) => [
                'price' => (int) ($value * 100),
            ],
        );
    }
}
