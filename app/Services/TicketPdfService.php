<?php

namespace App\Services;

use App\Models\IssuedTicket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class TicketPdfService
{
    /**
     * Génère un PDF unique contenant un ou plusieurs tickets.
     *
     * @param IssuedTicket|iterable $tickets
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generate(IssuedTicket|iterable $tickets)
    {
        if ($tickets instanceof IssuedTicket) {
            $tickets = $tickets->newCollection([$tickets]);
        } elseif (!($tickets instanceof EloquentCollection)) {
            $tickets = new EloquentCollection($tickets);
        }

        $tickets->loadMissing(['checkout.event', 'reservable']);

        $firstTicket = $tickets->first();

        if (!$firstTicket) {
            throw new \Exception("Aucun ticket fourni pour la génération du PDF.");
        }

        $backgroundImage = null;
        $event = $firstTicket->checkout->event;

        if ($event->background) {
            $path = \Illuminate\Support\Facades\Storage::disk('public')->path($event->background);
            if (file_exists($path)) {
                $backgroundImage = $path;
            }
        }

        return Pdf::loadView('pdfs.ticket', [
            'tickets'  => $tickets,
            'event'    => $firstTicket->checkout->event,
            'checkout' => $firstTicket->checkout,
            'backgroundImage' => $backgroundImage,
        ])->setPaper('a4', 'portrait');
    }
}
