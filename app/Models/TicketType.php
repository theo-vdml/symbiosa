<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'description',
        'capacity',
        'sold_count',
        'reserved_count',
        'available_from',
        'max_per_order',
        'sort_order'
    ];

    protected $casts = [
        'available_from' => 'datetime',
        'capacity' => 'integer',
        'sold_count' => 'integer',
        'reserved_count' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(TicketPrice::class)->orderBy('sort_order');
    }

    public function getStatusAttribute(): string
    {
        if ($this->available_from && $this->available_from->isFuture()) {
            return 'soon';
        }

        if ($this->capacity > 0 && $this->sold_count >= $this->capacity) {
            return 'sold_out';
        }

        return $this->active_price ? 'available' : 'sold_out';
    }

    public function getActivePriceAttribute(): ?TicketPrice
    {
        return $this->prices->first(function ($price) {
            $isTimeValid = is_null($price->available_until) || $price->available_until->isFuture();
            $isThresholdValid = is_null($price->threshold) || $this->sold_count < $price->threshold;
            return $isTimeValid && $isThresholdValid;
        });
    }
}
