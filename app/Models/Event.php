<?php

namespace App\Models;

use App\Traits\InteractsWithFiles;
use App\Enums\PublicationStatus;
use App\Traits\HasPublication;
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

    protected $casts = [
        'date' => 'date',
        'faq' => 'array',
        'status' => PublicationStatus::class,
        'published_at' => 'datetime',
    ];

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
}
