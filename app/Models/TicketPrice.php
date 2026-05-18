<?php

namespace App\Models;

use App\Enums\ReservableStatus;
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

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function issuedTickets()
    {
        return $this->hasMany(IssuedTicket::class);
    }

    protected static function booted()
    {
        static::deleting(function ($ticketPrice) {
            if ($ticketPrice->reservations()->exists() || $ticketPrice->issuedTickets()->exists()) {
                throw new \Exception("Impossible de supprimer ce prix car des réservations ou des billets y sont liés.");
            }
        });
    }

    protected function status(): Attribute
    {
        return Attribute::get(function (): ReservableStatus {
            if ($this->ticketType->status !== ReservableStatus::OPEN) {
                return $this->ticketType->status;
            }

            $activePrice = $this->ticketType->activePrice;

            if ($activePrice === null) {
                return ReservableStatus::SOLD_OUT;
            }

            if ($activePrice->id === $this->id) {
                return ReservableStatus::OPEN;
            }

            if ($this->sort_order > $activePrice->sort_order) {
                return ReservableStatus::UPCOMING;
            }

            return ReservableStatus::SOLD_OUT;
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
