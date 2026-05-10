<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::published()
            ->upcoming(includeOngoing: true)
            ->with('genres')
            ->get();

        return Inertia::render('Events/Index', [
            'events' => $events,
        ]);
    }
    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)
            ->with(['genres', 'sponsors', 'artists', 'artists.genres', 'ticketTypes.prices', 'seo'])
            ->firstOrFail();

        return Inertia::render('Events/Show', [
            'event' => $event,
            'seo' => [
                'title' => $event->seo?->title ?? $event->title,
                'description' => $event->seo?->description ?? $event->description,
                'keywords' => implode(', ', $event->seo?->keywords ?? []),
                'robots' => $event->seo?->robots ?? 'index, follow',
                'canonical_url' => $event->seo?->canonical_url,
                'og_title' => $event->seo?->og_title ?? $event->seo?->title ?? $event->title,
                'og_description' => $event->seo?->og_description ?? $event->seo?->description ?? $event->description,
                'og_image' => $event->seo?->og_image ?? $event->poster,
                'og_type' => $event->seo?->og_type ?? 'website',
                'twitter_card' => $event->seo?->twitter_card ?? 'summary_large_image',
                'twitter_title' => $event->seo?->twitter_title ?? $event->seo?->title ?? $event->title,
                'twitter_description' => $event->seo?->twitter_description ?? $event->seo?->description ?? $event->description,
                'twitter_image' => $event->seo?->twitter_image ?? $event->seo?->og_image ?? $event->poster,
                'json_ld' => $event->seo?->json_ld ? json_encode($event->seo->json_ld) : null,
            ]
        ]);
    }
    public function ticketing(string $slug)
    {
        $event = Event::where('slug', $slug)
            ->with('ticketTypes', 'ticketTypes.prices', 'addons')
            ->firstOrFail();

        if ($event->ticketing_status !== 'open') {
            return redirect()->route('events.show', $event->slug)
                ->with('error', 'La billetterie n\'est pas accessible pour le moment.');
        }

        return Inertia::render('Events/Ticketing', [
            'event' => $event,
        ]);
    }
}
