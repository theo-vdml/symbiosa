<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'checkout_id',
        'reservable_id',
        'reservable_type',
        'quantity',
        'unit_price',
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
}
