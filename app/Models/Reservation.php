<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'checkout_id',
        'reservable_id',
        'reservable_type',
        'ticket_price_id',
        'quantity',
        'unit_price',
    ];

    protected $casts = [
        'unit_price' => 'integer',
    ];

    // --- Relations ---

    public function checkout()
    {
        return $this->belongsTo(Checkout::class);
    }

    public function reservable()
    {
        return $this->morphTo();
    }

    public function ticketPrice()
    {
        return $this->belongsTo(TicketPrice::class);
    }
}
