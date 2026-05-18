<?php

namespace App\Models;

use App\Contracts\Reservable;
use App\Enums\ReservableStatus;
use App\Traits\HasStock;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAddon extends Model implements Reservable
{
    use HasStock;

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'price',
        'capacity',
        'available_from',
        'available_until',
        'max_per_order',
        'sort_order',
        'price_in_euro',
    ];

    protected $casts = [
        'price' => 'integer',
        'capacity' => 'integer',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
    ];

    protected $appends = ['status', 'price_in_euro', 'available_stock'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    protected function status(): Attribute
    {
        return Attribute::get(function (): ReservableStatus {
            if ($this->available_from?->isFuture()) {
                return ReservableStatus::UPCOMING;
            }

            if ($this?->available_until?->isPast()) {
                return ReservableStatus::SOLD_OUT;
            }

            if ($this->capacity && $this->reserved_stock >= $this->capacity) {
                return ReservableStatus::SOLD_OUT;
            }

            return ReservableStatus::OPEN;
        });
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
