<?php

namespace App\Jobs;

use App\Mail\OrderTicketsMail;
use App\Models\Checkout;
use App\Models\IssuedTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\TicketPdfService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FulfillCheckoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Checkout $checkout
    ) {}

    /**
     * Execute the job.
     */
    public function handle(TicketPdfService $pdfService): void
    {
        DB::transaction(function () use ($pdfService) {
            $this->checkout->load(['reservations.reservable', 'event']);
            $event = $this->checkout->event;

            $issuedTickets = [];

            foreach ($this->checkout->reservations as $reservation) {

                for ($i = 0; $i < $reservation->quantity; $i++) {
                    $ticket = IssuedTicket::create([
                        'event_id' => $event->id,
                        'checkout_id' => $this->checkout->id,
                        'reservable_type' => $reservation->reservable_type,
                        'reservable_id' => $reservation->reservable_id,
                        'ticket_price_id' => $reservation->ticket_price_id,
                        'price_paid' => $reservation->unit_price,
                    ]);

                    $issuedTickets[] = $ticket;
                }
            }

            $pdf = $pdfService->generate($event, $issuedTickets);

            Mail::to($this->checkout->customer_email)->send(
                new OrderTicketsMail($this->checkout, $event, $pdf, $event->ticket_email_content)
            );
        });
    }
}
