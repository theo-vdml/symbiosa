<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Inertia\Inertia;

class ArchiveController extends Controller
{
    public function index()
    {
        $events = Event::published()
            ->where('is_visible_in_archives', true)
            ->with(['genres', 'artists', 'media'])
            ->orderBy('start_at', 'desc')
            ->get()
            ->map(function (Event $event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'type' => $event->genres->first()?->name ?? 'Événement',
                    'genres' => $event->genres->pluck('name')->toArray(),
                    'isoDate' => $event->start_at?->toIso8601String(),
                    'location' => $event->city . ', ' . $event->country,
                    'image' => $event->poster_url ?? $event->background_url,
                    'lineup' => $event->artists->pluck('name')->toArray(),
                    'photoCount' => $event->getMedia('gallery')->count(),
                    'recapLink' => route('events.show', $event->slug),
                ];
            });

        return Inertia::render('Archives', [
            'events' => $events,
        ]);
    }
}
