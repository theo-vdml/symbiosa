<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class IssuedTicket extends Model
{
    protected $fillable = [
        'checkout_id',
        'reservation_id',
        'reservable_type',
        'reservable_id',
        'qr_code_token',
        'scanned_at',
        'is_attendee',
        'name',
        'price_paid',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'is_attendee' => 'boolean',
        'price_paid' => 'integer',
    ];

    public function checkout(): BelongsTo
    {
        return $this->belongsTo(Checkout::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function reservable(): MorphTo
    {
        return $this->morphTo();
    }
}
