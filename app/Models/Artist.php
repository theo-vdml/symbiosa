<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    protected $fillable = [
        'name',
        'thumbnail',
        'website',
        'biography',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'artist_event')
            ->using(ArtistEvent::class)
            ->withPivot('performance_time', 'sort_order')
            ->withTimestamps();
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'artist_genre');
    }
}
