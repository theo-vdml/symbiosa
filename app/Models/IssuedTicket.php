<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Str;
use Vinkla\Hashids\Facades\Hashids;

class IssuedTicket extends Model
{
    protected $fillable = [
        'event_id',
        'checkout_id',
        'reservable_type',
        'reservable_id',
        'ticket_price_id',
        'public_id',
        'checked_in_at',
        'price_paid',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'price_paid' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            $ticket->public_id = 'PENDING-' . Str::random(10);
        });

        static::created(function ($ticket) {
            $hash = Hashids::encode($ticket->id);

            $ticket->public_id = 'SY-' . substr($hash, 0, 3) . '-' . substr($hash, 3);
            $ticket->saveQuietly();
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function checkout(): BelongsTo
    {
        return $this->belongsTo(Checkout::class);
    }

    public function reservable(): MorphTo
    {
        return $this->morphTo();
    }

    public function ticketPrice(): BelongsTo
    {
        return $this->belongsTo(TicketPrice::class);
    }
}
