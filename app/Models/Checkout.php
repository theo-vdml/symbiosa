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
 * @property string|null $last_stripe_intent_id
 * @property \Illuminate\Support\Carbon|null $last_stripe_intent_created_at
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read \App\Enums\CheckoutStatus $status Statut calculé de la session
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reservation[] $reservations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PaymentAttempt[] $paymentAttempts
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout holdingStock() Filtre les sessions qui bloquent encore du stock
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout isCompleted() Filtre les sessions payées
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout isCancelled() Filtre les sessions annulées manuellement
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout isPending() Filtre les sessions ni payées ni annulées
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout withinExpirationWindow() Filtre les sessions dont le timer de base est valide
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout hasRecentStripeActivity() Filtre les sessions avec une activité Stripe < 20min
 */
class Checkout extends Model
{
    /**
     * @var int Délai de grâce accordé après expiration si un paiement Stripe est en cours.
     */
    protected const int STRIPE_GRACE_PERIOD_MINUTES = 20;

    protected $fillable = [
        'uuid',
        'customer_email',
        'customer_name',
        'last_stripe_intent_id',
        'last_stripe_intent_created_at',
        'expires_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'last_stripe_intent_created_at' => 'datetime',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // --- Relations ---

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function paymentAttempts()
    {
        return $this->hasMany(PaymentAttempt::class);
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

    public function scopeIsPending(Builder $query)
    {
        return $query->whereNull('completed_at')->whereNull('cancelled_at');
    }

    public function scopeWithinExpirationWindow(Builder $query)
    {
        return $query->where('expires_at', '>', now());
    }

    public function scopeHasRecentStripeActivity(Builder $query)
    {
        return $query->whereNotNull('last_stripe_intent_created_at')
            ->where('last_stripe_intent_created_at', '>', now()->subMinutes(self::STRIPE_GRACE_PERIOD_MINUTES));
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
            $q->isCompleted()
                ->orWhere(function (Builder $sub) {
                    $sub->isPending()
                        ->where(function (Builder $subSub) {
                            $subSub->withinExpirationWindow()
                                ->orWhere->hasRecentStripeActivity();
                        });
                });
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

            if ($this->last_stripe_intent_id) {
                $isRecent = $this?->last_stripe_intent_created_at
                    ->gt(now()->subMinutes(self::STRIPE_GRACE_PERIOD_MINUTES));

                if ($isRecent) return CheckoutStatus::PROCESSING;

                return CheckoutStatus::STUCK;
            }

            if ($this?->expires_at->isPast()) {
                return CheckoutStatus::EXPIRED;
            }

            return CheckoutStatus::PENDING;
        });
    }
}
