<?php

namespace App\Models;

use App\Traits\HasSEO;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HomePage extends Model implements HasMedia
{
    use HasSEO, InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('hero_video')
            ->singleFile()
            ->useDisk('r2');

        $this->addMediaCollection('bento_gallery')
            ->useDisk('r2');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('poster')
            ->extractVideoFrameAtSecond(0)
            ->performOnCollections('hero_video');
    }

    protected $fillable = [
        'hero_preheading',
        'hero_title',
        'hero_subheading',
        'spotify_playlist_heading',
        'spotify_playlist_id',
        'show_spotify_playlist',
        'spotify_playlist_force_dark',
    ];

    protected $casts = [
        'show_spotify_playlist' => 'boolean',
        'spotify_playlist_force_dark' => 'boolean',
    ];

    public function getSeoDefaults(): array
    {
        return [
            'title' => 'Symbiosa - Accueil',
            'description' => "Collectif d'événementiel techno à Gembloux. Découvrez nos prochains événements et l'actualité de la scène.",
        ];
    }

    public function getSeoFallbacks(): array
    {
        return [
            'og_title' => ['seo_title'],
            'twitter_title' => ['seo_title'],
        ];
    }
}
