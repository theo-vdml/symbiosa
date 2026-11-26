<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventsPage;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        $page = EventsPage::first() ?? new EventsPage;
        $events = Event::published()
            ->upcoming(includeOngoing: true)
            ->with('genres')
            ->get();

        return Inertia::render('Events/Index', [
            'preheading' => $page->preheading ?? 'Calendrier',
            'heading' => $page->heading ?? 'Événements à venir',
            'description' => $page->description ?? '',
            'events' => $events,
            'seo' => $page->getSeoData(),
        ]);
    }

    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)
            // Drafts and scheduled events stay private; logged-in admins can still preview them.
            ->when(! auth()->check(), fn ($query) => $query->published())
            ->with(['genres', 'sponsors', 'artists', 'artists.genres', 'ticketTypes.prices', 'seo'])
            ->firstOrFail();

        return Inertia::render('Events/Show', [
            'event' => $event,
            'seo' => $event->getSeoData(),
        ]);
    }

    public function ticketing(string $slug)
    {
        $event = Event::published()
            ->where('slug', $slug)
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
