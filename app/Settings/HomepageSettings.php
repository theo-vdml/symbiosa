<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HomepageSettings extends Settings
{
    public ?string $spotify_playlist_heading;

    public ?string $spotify_playlist_id;

    public bool $show_spotify_playlist;

    public static function group(): string
    {
        return 'homepage';
    }
}
