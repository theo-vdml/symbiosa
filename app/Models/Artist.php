<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Artist extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('portrait')
            ->singleFile()
            ->useDisk('r2');
    }

    protected $fillable = [
        'name',
        'website',
        'biography',
    ];

    protected $appends = [
        'portrait_url',
    ];

    public function getPortraitUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('portrait');
    }

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
