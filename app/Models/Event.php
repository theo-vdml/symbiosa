<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property array<int, array{question: string, answer: string}> $faq
 */
class Event extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'faq' => 'array',
    ];

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
}
