<?php

namespace App\Http\Controllers;

use App\Jobs\ResendLostTicketsJob;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LostTicketsController extends Controller
{
    public function show()
    {
        $page = \App\Models\LostTicketPage::first() ?? new \App\Models\LostTicketPage();

        return Inertia::render('LostTickets', [
            'preheading' => $page->preheading ?? 'Support',
            'heading' => $page->heading ?? 'Retrouvez vos billets',
            'description' => $page->description ?? 'Entrez votre adresse e-mail ci-dessous pour recevoir vos billets associés à cette adresse pour des événements à venir.',
            'helpItems' => $page->help_items ?? [],
            'seo' => $page->getSeoData()
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        ResendLostTicketsJob::dispatch($request->email);

        return back()->with('status', "Si des billets sont associés à cette adresse pour des événements à venir, ils vous seront renvoyés d'ici 5 minutes. Si vous ne recevez rien, n'hésitez pas à nous contacter directement.");
    }
}
