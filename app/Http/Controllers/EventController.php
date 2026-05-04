<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::published()
            ->upcoming()
            ->with('genres')
            ->orderBy('date')
            ->get();

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

    public function ticketing(string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $ticketTypes = [
            [
                'id' => 1,
                'name' => 'Pass Standard',
                'description' => 'Accès standard à l\'événement à prix réduit.',
                'prices' => [
                    ['id' => 1, 'name' => 'Early Bird', 'price' => 25, 'active' => false],
                    ['id' => 2, 'name' => 'Prévente', 'price' => 35, 'active' => true],
                    ['id' => 3, 'name' => 'Regular', 'price' => 50, 'active' => false],
                ],
            ],
            [
                'id' => 2,
                'name' => 'Pass VIP',
                'description' => 'Accès VIP avec espace réservé et bar dédié.',
                'prices' => [
                    ['id' => 4, 'name' => 'Regular', 'price' => 75, 'active' => true],
                ],
            ],
            [
                'id' => 3,
                'name' => 'Pack Groupe (4 personnes)',
                'description' => 'Venez à 4 et payez moins cher.',
                'prices' => [
                    ['id' => 5, 'name' => 'Regular', 'price' => 120, 'active' => true],
                ],
            ],
        ];

        return Inertia::render('Events/Ticketing', [
            'event' => $event,
            'ticketTypes' => $ticketTypes,
        ]);
    }
}
