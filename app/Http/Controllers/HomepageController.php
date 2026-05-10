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
            ->upcoming(includeOngoing: true)
            ->with('genres')
            ->first();

        return Inertia::render('Home', [
            'posts' => $posts,
            'upcomingEvent' => $upcomingEvent,
            'spotifyPlaylistHeading' => $settings->spotify_playlist_heading,
            'spotifyPlaylistId' => $settings->spotify_playlist_id,
            'showSpotifyPlaylist' => $settings->show_spotify_playlist,
            'seo' => [
                'title' => $settings->seo_title,
                'description' => $settings->seo_description,
                'keywords' => implode(', ', $settings->seo_keywords ?? []),
                'robots' => $settings->seo_robots,
                'canonical_url' => $settings->seo_canonical_url,
                'og_title' => $settings->seo_og_title,
                'og_description' => $settings->seo_og_description,
                'og_image' => $settings->seo_og_image,
                'og_type' => $settings->seo_og_type,
                'twitter_card' => $settings->seo_twitter_card,
                'twitter_title' => $settings->seo_twitter_title,
                'twitter_description' => $settings->seo_twitter_description,
                'twitter_image' => $settings->seo_twitter_image,
                'json_ld' => $settings->seo_json_ld ? json_encode($settings->seo_json_ld) : null,
            ]
        ]);
    }
}
