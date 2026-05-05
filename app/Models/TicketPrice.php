<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TicketPrice extends Model
{
    protected $fillable = [
        'ticket_type_id',
        'name',
        'price',
        'available_until',
        'threshold',
        'sort_order',
        'price_in_euro',
    ];

    protected $casts = [
        'available_until' => 'datetime',
        'price' => 'integer',
        'threshold' => 'integer',
    ];

    protected $appends = ['status', 'price_in_euro'];

    protected $hidden = ['ticketType'];

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    public function getStatusAttribute(): string
    {
        $type = $this->ticketType;

        if ($type->available_from && $type->available_from->isFuture()) {
            return 'soon';
        }

        if ($type->capacity > 0 && $type->sold_count >= $type->capacity) {
            return 'sold_out';
        }

        $activePrice = $type->activePrice;

        if (!$activePrice) {
            return 'sold_out';
        }

        if ($this->id === $activePrice->id) {
            return 'available';
        }

        return $this->sort_order < $activePrice->sort_order ? 'sold_out' : 'soon';
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
