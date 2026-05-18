<?php

namespace App\Models;

use App\Enums\PublicationStatus;
use App\Traits\HasPublication;
use App\Traits\HasSEO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property array<int, array{question: string, answer: string}> $faq
 */
class Event extends Model implements HasMedia
{
    use HasPublication;
    use HasSEO;
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('poster')
            ->singleFile()
            ->withResponsiveImages()
            ->useDisk('r2');

        $this->addMediaCollection('background')
            ->singleFile()
            ->withResponsiveImages()
            ->useDisk('r2');

        $this->addMediaCollection('gallery')
            ->useDisk('r2');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(600)
            ->height(600)
            ->sharpen(10)
            ->nonQueued()
            ->performOnCollections('gallery');
    }

    protected $fillable = [
        'title',
        'description',
        'slug',
        'status',
        'is_visible_in_archives',
        'published_at',
        'start_at',
        'end_at',
        'city',
        'country',
        'address',
        'body',
        'faq',
        'minimum_age',
        'dress_code',
        'ticketing_starts_at',
        'ticketing_ends_at',
        'ticket_email_content',
        'ticket_pdf_content',
        'stripe_metadata',
    ];

    protected $appends = ['min_price', 'date', 'start_time', 'end_time', 'is_ticketing_open', 'ticketing_status', 'poster_url', 'background_url', 'background_responsive', 'gallery_urls'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'faq' => 'array',
        'status' => PublicationStatus::class,
        'published_at' => 'datetime',
        'is_visible_in_archives' => 'boolean',
        'ticketing_starts_at' => 'datetime',
        'ticketing_ends_at' => 'datetime',
        'stripe_metadata' => 'array',
    ];

    public function getPosterUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('poster');
    }

    public function getBackgroundUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('background');
    }

    public function getBackgroundResponsiveAttribute(): array
    {
        $media = $this->getFirstMedia('background');
        return $media ? [
            'src' => $media->getUrl(),
            'srcset' => $media->getSrcset(),
        ] : [];
    }

    public function getGalleryUrlsAttribute(): array
    {
        return $this->getMedia('gallery')->map(fn($media) => [
            'id' => $media->id,
            'url' => $media->getUrl(),
            'thumb' => $media->getUrl('thumb'),
            'responsive' => [
                'src' => $media->getUrl(),
                'srcset' => $media->getSrcset(),
            ],
        ])->toArray();
    }

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

    public function reservations()
    {
        return $this->hasManyThrough(Reservation::class, Checkout::class);
    }

    public function issuedTickets()
    {
        return $this->hasManyThrough(IssuedTicket::class, Checkout::class);
    }

    public function checkinLists()
    {
        return $this->hasMany(CheckinList::class);
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

    public function getSeoDefaults(): array
    {
        return [
            "title" => "Un évènement symbiosa",
            "twitter_card" => "summary_large_image",
        ];
    }

    public function getSeoFallbacks(): array
    {
        return [
            "title" => ["title", "slug"],
            "description" => ["description", "title"],
            "og_title" => ["title", "slug"],
            "og_description" => ["description", "title"],
            "og_image" => 'background_url',
        ];
    }
}
