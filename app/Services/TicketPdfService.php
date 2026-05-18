<?php

namespace App\Services;

use App\Models\Event;
use App\Models\IssuedTicket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Log;

class TicketPdfService
{
    /**
     * Génère un PDF unique contenant un ou plusieurs tickets.
     *
     * @param IssuedTicket|iterable $tickets
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generate(Event $event, IssuedTicket|iterable $tickets)
    {
        if ($tickets instanceof IssuedTicket) {
            $tickets = $tickets->newCollection([$tickets]);
        } elseif (!($tickets instanceof EloquentCollection)) {
            $tickets = new EloquentCollection($tickets);
        }

        if ($tickets->isEmpty()) {
            throw new \Exception("Aucun ticket fourni pour la génération du PDF.");
        }

        $backgroundImage = null;

        if ($event->background_url) {
            $backgroundImage = $event->background_url;
        }

        Log::info('Bg: ' . ($backgroundImage ?? 'none'));

        return Pdf::loadView('pdfs.ticket', [
            'tickets'  => $tickets,
            'event'    => $event,
            'backgroundImage' => $backgroundImage,
            'customContent' => $event->ticket_pdf_content,
        ])->setPaper('a4', 'portrait');
    }
}
