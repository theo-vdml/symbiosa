<?php

namespace App\Models;

use App\Contracts\Reservable;
use App\Enums\ReservableStatus;
use App\Traits\HasStock;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model implements Reservable
{
    use HasStock;

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
    ];

    protected $appends = ['status', 'available_stock'];

    // --- Relations ---

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(TicketPrice::class)->orderBy('sort_order');
    }

    public function reservations()
    {
        return $this->morphMany(Reservation::class, 'reservable');
    }

    public function issuedTickets()
    {
        return $this->morphMany(IssuedTicket::class, 'reservable');
    }

    protected static function booted()
    {
        static::deleting(function ($ticketType) {
            if ($ticketType->reservations()->exists() || $ticketType->issuedTickets()->exists()) {
                throw new \Exception("Impossible de supprimer ce type de billet car des réservations ou des billets y sont liés.");
            }
        });
    }

    // --- Attributes (Logic) ---


    protected function activePrice(): Attribute
    {
        return Attribute::get(fn(): mixed => $this->prices->first(function (TicketPrice $price): bool {

            $isTimeValid = $price->available_until === null
                || $price->available_until->isFuture();

            $isThresholdValid = $price->threshold === null
                || $this->reserved_stock < $price->threshold;

            return $isTimeValid && $isThresholdValid;
        }));
    }

    protected function status(): Attribute
    {
        return Attribute::get(function (): ReservableStatus {
            if ($this->available_from?->isFuture()) {
                return ReservableStatus::UPCOMING;
            }

            if ($this->available_stock !== null && $this->available_stock <= 0) {
                return ReservableStatus::SOLD_OUT;
            }

            if (!$this->active_price) {
                return ReservableStatus::SOLD_OUT;
            }

            return ReservableStatus::OPEN;
        });
    }
}
