<?php

namespace App\Models;

use App\Traits\InteractsWithFiles;
use App\Enums\PublicationStatus;
use App\Traits\HasPublication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property array<int, array{question: string, answer: string}> $faq
 */
class Event extends Model
{
    use InteractsWithFiles;
    use HasPublication;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'status',
        'published_at',
        'date',
        'start_time',
        'end_time',
        'location',
        'address',
        'body',
        'faq',
        'poster',
        'background',
    ];

    protected $appends = ['min_price'];

    protected $casts = [
        'date' => 'date',
        'faq' => 'array',
        'status' => PublicationStatus::class,
        'published_at' => 'datetime',
    ];

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

    public function scopeUpcoming(Builder $query)
    {
        $query->where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc');
    }
}
