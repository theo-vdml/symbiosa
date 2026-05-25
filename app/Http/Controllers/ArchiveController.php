<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Inertia\Inertia;

class ArchiveController extends Controller
{
    public function index()
    {
        $page = \App\Models\ArchivePage::first() ?? new \App\Models\ArchivePage();
        $events = Event::published()
            ->archived()
            ->with(['media'])
            ->get();

        return Inertia::render('Archives', [
            'preheading' => $page->preheading ?? 'Archives',
            'heading' => $page->heading ?? 'Nos Archives',
            'description' => $page->description ?? "",
            'events' => $events,
            'seo' => $page->getSeoData(),
        ]);
    }
}
