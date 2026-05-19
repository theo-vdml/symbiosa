<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\HomePage;
use App\Models\Post;
use Inertia\Inertia;

class HomepageController extends Controller
{
    public function index()
    {
        $homePage = HomePage::first() ?? new HomePage();

        $posts = Post::published()
            ->with('category')
            ->latest('published_at')
            ->take(3)
            ->get();

        $upcomingEvent = Event::published()
            ->upcoming(includeOngoing: true)
            ->with('genres')
            ->first();

        return Inertia::render('Home', [
            'posts' => $posts,
            'upcomingEvent' => $upcomingEvent,
            'heroPreheading' => $homePage->hero_preheading,
            'heroTitle' => $homePage->hero_title,
            'heroSubheading' => $homePage->hero_subheading,
            'heroVideoUrl' => $homePage->getFirstMediaUrl('hero_video'),
            'heroPosterUrl' => $homePage->getFirstMediaUrl('hero_video', 'poster'),
            'spotifyPlaylistHeading' => $homePage->spotify_playlist_heading,
            'spotifyPlaylistId' => $homePage->spotify_playlist_id,
            'showSpotifyPlaylist' => $homePage->show_spotify_playlist,
            'spotifyPlaylistForceDark' => $homePage->spotify_playlist_force_dark,
            'seo' => $homePage->getSeoData(),
        ]);
    }
}
