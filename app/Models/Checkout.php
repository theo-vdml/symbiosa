<?php

namespace App\Models;

use App\Enums\CheckoutStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Checkout
 *
 * @property int $id
 * @property string $uuid
 * @property string $customer_email
 * @property string|null $customer_name
 * @property string|null $stripe_session_id
 * @property Carbon $expires_at
 * @property Carbon|null $completed_at
 * @property Carbon|null $cancelled_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read CheckoutStatus $status Statut calculé de la session
 * @property-read Collection|Reservation[] $reservations
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout holdingStock() Filtre les sessions qui bloquent encore du stock
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout isCompleted() Filtre les sessions payées
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout isCancelled() Filtre les sessions annulées manuellement
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout isPending() Filtre les sessions ni payées ni annulées, encore dans leur délai
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout isExpired() Filtre les sessions non payées, non annulées, dont le délai est passé
 * @method static \Illuminate\Database\Eloquent\Builder|Checkout withinExpirationWindow() Filtre les sessions dont le délai (expires_at) n'est pas passé
 */
class Checkout extends Model
{
    /** Minutes during which an email verification code can be used. */
    public const VERIFICATION_CODE_TTL = 15;

    /** Wrong codes allowed before the code is invalidated and must be re-sent. */
    public const VERIFICATION_MAX_ATTEMPTS = 5;

    /** Lifetime of a Stripe Checkout session (Stripe's minimum is 30 minutes). */
    public const STRIPE_SESSION_TTL = 30;

    /** Extra time stock stays held after the Stripe session expires, for late webhooks. */
    public const STRIPE_WEBHOOK_GRACE = 5;

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
        'email_verification_sent_at',
        'email_verification_attempts',
    ];

    /**
     * Never sent to the browser: the verification code would make email
     * verification pointless, and the Stripe ids are internal.
     */
    protected $hidden = [
        'email_verification_code',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'stripe_customer_id',
    ];

    protected $appends = [
        'verification_pending',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'email_verification_sent_at' => 'datetime',
        'email_verification_attempts' => 'integer',
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

    /**
     * Unpaid checkouts hold stock until expires_at only. When a Stripe session is
     * opened, expires_at is pushed to the session's own expiry (plus a grace
     * period), so stock is held exactly as long as the order can still be paid.
     */
    public function scopeIsPending(Builder $query)
    {
        return $query->whereNull('completed_at')
            ->whereNull('cancelled_at')
            ->withinExpirationWindow();
    }

    public function scopeIsExpired(Builder $query)
    {
        return $query->whereNull('completed_at')
            ->whereNull('cancelled_at')
            ->where('expires_at', '<=', now());
    }

    /**
     * Détermine si le checkout bloque actuellement des inventaires (billets, addons) :
     * payé, ou en attente et encore dans son délai.
     *
     * @return Builder
     */
    public function scopeHoldingStock(Builder $query)
    {
        return $query->where(function (Builder $q) {
            $q->isCompleted()->orWhere(fn ($sub) => $sub->isPending());
        });
    }

    // --- Accessors ---

    /**
     * Accessor pour le statut métier du Checkout.
     * Déduit dynamiquement l'état à partir des différents timestamps et de l'activité Stripe.
     */
    protected function status(): Attribute
    {
        return Attribute::get(function (): CheckoutStatus {
            if ($this->completed_at) {
                return CheckoutStatus::COMPLETED;
            }
            if ($this->cancelled_at) {
                return CheckoutStatus::CANCELLED;
            }

            if ($this->expires_at->isPast()) {
                return CheckoutStatus::EXPIRED;
            }

            if ($this->stripe_session_id) {
                return CheckoutStatus::PROCESSING;
            }

            return CheckoutStatus::PENDING;
        });
    }

    /**
     * A code was sent and is waiting to be entered (exposed instead of the code itself).
     */
    protected function verificationPending(): Attribute
    {
        return Attribute::get(fn (): bool => filled($this->email_verification_code) && ! $this->email_verified_at);
    }
}
