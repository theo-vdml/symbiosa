<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('genres')->orderBy('date')->get();

        return Inertia::render('Events/Index', [
            'events' => $events,
        ]);
    }

    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)->with('genres', 'sponsors', 'artists', 'artists.genres')->firstOrFail();

        // return compact('event');

        return Inertia::render('Events/Show', [
            'event' => $event,
        ]);
    }
}
