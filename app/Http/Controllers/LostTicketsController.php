<?php

namespace App\Http\Controllers;

use App\Jobs\ResendLostTicketsJob;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LostTicketsController extends Controller
{
    public function show()
    {
        return Inertia::render('LostTickets', [
            'seo' => \App\Services\SeoProcessor::make([
                'title' => 'Billets perdus',
                'description' => 'Récupérez vos billets pour les évènements à venir.',
            ])
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
