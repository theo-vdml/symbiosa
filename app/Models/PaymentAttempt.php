<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAttempt extends Model
{
    protected $fillable = [
        'checkout_id',
        'stripe_intent_id',
        'status',
        'message',
    ];

    // --- Relations ---

    public function checkout()
    {
        return $this->belongsTo(Checkout::class);
    }
}
