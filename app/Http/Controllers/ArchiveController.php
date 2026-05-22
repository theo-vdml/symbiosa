<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Inertia\Inertia;

class ArchiveController extends Controller
{
    public function index()
    {
        $events = Event::published()
            ->archived()
            ->with(['media'])
            ->get();

        return Inertia::render('Archives', [
            'events' => $events,
        ]);
    }
}
