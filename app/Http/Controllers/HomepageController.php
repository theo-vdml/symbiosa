<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Post;
use App\Settings\HomepageSettings;
use Inertia\Inertia;

class HomepageController extends Controller
{
    public function index(HomepageSettings $settings)
    {
        $posts = Post::published()
            ->with('category')
            ->latest('published_at')
            ->take(3)
            ->get();

        $upcomingEvent = Event::published()
            ->upcoming()
            ->with('genres')
            ->orderBy('date')
            ->first();

        return Inertia::render('Home', [
            'posts' => $posts,
            'upcomingEvent' => $upcomingEvent,
            'spotifyPlaylistHeading' => $settings->spotify_playlist_heading,
            'spotifyPlaylistId' => $settings->spotify_playlist_id,
            'showSpotifyPlaylist' => $settings->show_spotify_playlist
        ]);
    }
}
