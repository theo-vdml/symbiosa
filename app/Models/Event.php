<?php

namespace App\Models;

use App\Traits\InteractsWithFiles;
use Illuminate\Database\Eloquent\Model;

/**
 * @property array<int, array{question: string, answer: string}> $faq
 */
class Event extends Model
{
    use InteractsWithFiles;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'faq' => 'array',
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
