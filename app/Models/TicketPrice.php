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
        'sort_order'
    ];

    protected $casts = [
        'available_until' => 'datetime',
        'price' => 'integer', // Stocké en centimes
        'threshold' => 'integer',
    ];

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    protected function priceInEuro(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['price'] / 100,
            set: fn($value) => $value * 100,
        );
    }
}
