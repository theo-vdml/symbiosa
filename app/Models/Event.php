<?php

namespace App\Models;

use App\Traits\HasSEO;
use App\Traits\InteractsWithFiles;
use App\Enums\PublicationStatus;
use App\Traits\HasPublication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @property array<int, array{question: string, answer: string}> $faq
 */
class Event extends Model
{
    use InteractsWithFiles;
    use HasPublication;
    use HasSEO;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'status',
        'published_at',
        'start_at',
        'end_at',
        'city',
        'country',
        'address',
        'body',
        'faq',
        'poster',
        'background',
        'ticketing_starts_at',
        'ticketing_ends_at',
        'ticket_email_content',
        'ticket_pdf_content',
        'stripe_metadata',
    ];

    protected $appends = ['min_price', 'date', 'start_time', 'end_time', 'is_ticketing_open', 'ticketing_status'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'faq' => 'array',
        'status' => PublicationStatus::class,
        'published_at' => 'datetime',
        'ticketing_starts_at' => 'datetime',
        'ticketing_ends_at' => 'datetime',
        'stripe_metadata' => 'array',
    ];

    protected function ticketingStatus(): Attribute
    {
        return Attribute::get(function () {
            $now = now();

            if ($this->ticketing_starts_at === null) {
                return 'none';
            }

            if ($this->ticketing_starts_at > $now) {
                return 'coming_soon';
            }

            if ($this->ticketing_ends_at === null) {
                return $now < $this->start_at ? 'open' : 'closed';
            }

            return $now <= $this->ticketing_ends_at ? 'open' : 'closed';
        });
    }

    protected function isTicketingOpen(): Attribute
    {
        return Attribute::get(fn() => $this->ticketing_status === 'open');
    }

    protected function date(): Attribute
    {
        return Attribute::get(fn() => $this->start_at?->copy()->startOfDay());
    }

    protected function startTime(): Attribute
    {
        return Attribute::get(fn() => $this->start_at?->format('H:i'));
    }

    protected function endTime(): Attribute
    {
        return Attribute::get(fn() => $this->end_at?->format('H:i'));
    }

    public function getMinPriceAttribute()
    {
        return $this->ticketTypes->flatMap->prices->min('price_in_euro');
    }

    public function fileAttributes(): array
    {
        return [
            'poster' => 'public',
            'background' => 'public',
        ];
    }

    public function sponsors()
    {
        return $this->belongsToMany(Sponsor::class)
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('event_sponsor.sort_order');
    }

    public function eventSponsors()
    {
        return $this->hasMany(EventSponsor::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function artists()
    {
        return $this->belongsToMany(Artist::class, 'artist_event')
            ->using(ArtistEvent::class)
            ->withPivot('performance_time', 'sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order', 'asc');
    }

    public function artistEvents()
    {
        return $this->hasMany(ArtistEvent::class);
    }

    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class)->orderBy('sort_order');
    }

    public function addons()
    {
        return $this->hasMany(EventAddon::class)->orderBy('sort_order');
    }

    public function checkouts()
    {
        return $this->hasMany(Checkout::class);
    }

    public function issuedTickets()
    {
        return $this->hasManyThrough(IssuedTicket::class, Checkout::class);
    }

    public function getAttendeesCountAttribute()
    {
        return $this->issuedTickets()->where('is_attendee', true)->count();
    }

    public function scopeUpcoming(Builder $query, bool $includeOngoing = false)
    {
        if ($includeOngoing) {
            return $query->where('end_at', '>=', now())
                ->orderBy('start_at', 'asc');
        }

        return $query->where('start_at', '>=', now())
            ->orderBy('start_at', 'asc');
    }

    public function scopeNotFinished(Builder $query)
    {
        return $query->where('end_at', '>=', now())
            ->orderBy('start_at', 'asc');
    }
}
