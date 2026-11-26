<?php

namespace App\Http\Controllers;

use App\Models\ArchivePage;
use App\Models\Event;
use Inertia\Inertia;

class ArchiveController extends Controller
{
    public function index()
    {
        $page = ArchivePage::first() ?? new ArchivePage;
        $events = Event::published()
            ->archived()
            ->with(['media'])
            ->get();

        return Inertia::render('Archives', [
            'preheading' => $page->preheading ?? 'Archives',
            'heading' => $page->heading ?? 'Nos Archives',
            'description' => $page->description ?? '',
            'events' => $events,
            'seo' => $page->getSeoData(),
        ]);
    }
}
