<?php

namespace App\Settings;

class HomepageSettings extends PageSettings
{
    public ?string $spotify_playlist_heading;

    public ?string $spotify_playlist_id;

    public bool $show_spotify_playlist;

    public static function group(): string
    {
        return 'homepage';
    }

    public function getSeoFallbacks(): array
    {
        return [
            'og_title' => ['seo_title'],
            'twitter_title' => ['seo_title'],
        ];
    }

    public function getSeoDefaults(): array
    {
        return [
            'title' => 'Symbiosa - Accueil',
            'description' => "Collectif d'événementiel techno à Gembloux. Découvrez nos prochains événements et l'actualité de la scène.",
        ];
    }
}
