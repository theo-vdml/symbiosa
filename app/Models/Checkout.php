<?php

namespace App\Models;

use App\Enums\CheckoutStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * App\Models\Checkout
 *
 * @property int $id
 * @property string $uuid
 * @property string $customer_email
 * @property string|null $customer_name
 * @property string|null $stripe_session_id
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read \App\Enums\CheckoutStatus $status Statut calculé de la session
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reservation[] $reservations
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout holdingStock() Filtre les sessions qui bloquent encore du stock
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout isCompleted() Filtre les sessions payées
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout isCancelled() Filtre les sessions annulées manuellement
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout isPending() Filtre les sessions ni payées ni annulées
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout isExpired() Filtre les sessions expirées (non payées, non annulées, et dont la date d'expiration est passée)
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout withinExpirationWindow() Filtre les sessions dont le timer de base est valide
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout hasRecentStripeActivity() Filtre les sessions avec une activité Stripe < 20min
 */
class Checkout extends Model
{
    protected $fillable = [
        'uuid',
        'event_id',
        'customer_email',
        'customer_name',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'stripe_customer_id',
        'accepted_legal_pages',
        'expires_at',
        'completed_at',
        'cancelled_at',
        'email_verified_at',
        'email_verification_code',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'accepted_legal_pages' => 'array',
    ];

    // --- Relations ---

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function issuedTickets()
    {
        return $this->hasMany(IssuedTicket::class);
    }

    // --- Scopes ---

    public function scopeIsCompleted(Builder $query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopeIsCancelled(Builder $query)
    {
        return $query->whereNotNull('cancelled_at');
    }

    public function scopeWithinExpirationWindow(Builder $query)
    {
        return $query->where('expires_at', '>', now());
    }

    public function scopeHasRecentStripeActivity(Builder $query)
    {
        return $query->whereNotNull('stripe_session_id');
    }

    public function scopeIsPending(Builder $query)
    {
        return $query->whereNull('completed_at')
            ->whereNull('cancelled_at')
            ->where(function (Builder $sub) {
                $sub->withinExpirationWindow()
                    ->orWhere(fn($sub2) => $sub2->hasRecentStripeActivity());
            });
    }

    public function scopeIsExpired(Builder $query)
    {
        return $query->whereNull('completed_at')
            ->whereNull('cancelled_at')
            ->whereNot(function (Builder $sub) {
                $sub->withinExpirationWindow()
                    ->orWhere(fn($sub2) => $sub2->hasRecentStripeActivity());
            });
    }

    /**
     * Détermine si le checkout bloque actuellement des inventaires (billets, addons).
     * Combine la validité temporelle et l'activité des intents Stripe.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHoldingStock(Builder $query)
    {
        return $query->where(function (Builder $q) {
            $q->isCompleted()->orWhere(fn($sub) => $sub->isPending());
        });
    }

    // --- Accessors ---

    /**
     * Accessor pour le statut métier du Checkout.
     * Déduit dynamiquement l'état à partir des différents timestamps et de l'activité Stripe.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function status(): Attribute
    {
        return Attribute::get(function (): CheckoutStatus {
            if ($this->completed_at) return CheckoutStatus::COMPLETED;
            if ($this->cancelled_at) return CheckoutStatus::CANCELLED;

            if ($this->stripe_session_id) {
                return CheckoutStatus::PROCESSING;
            }

            if ($this?->expires_at->isPast()) {
                return CheckoutStatus::EXPIRED;
            }

            return CheckoutStatus::PENDING;
        });
    }
}
