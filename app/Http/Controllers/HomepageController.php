<?php

namespace App\Http\Controllers;

use App\Settings\HomepageSettings;
use Inertia\Inertia;

class HomepageController extends Controller
{
    public function index(HomepageSettings $settings)
    {
        return Inertia::render('Home', [
            'spotifyPlaylistHeading' => $settings->spotify_playlist_heading,
            'spotifyPlaylistId' => $settings->spotify_playlist_id,
            'showSpotifyPlaylist' => $settings->show_spotify_playlist
        ]);
    }
}
