<?php

namespace App\Jobs;

use App\Mail\OrderTicketsMail;
use App\Models\Checkout;
use App\Models\IssuedTicket;
use App\Services\TicketPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Issues the tickets of a paid checkout and emails them.
 *
 * Safe to run more than once: tickets are created only if none exist yet, so a
 * retry (e.g. after a mail failure) only re-sends the email.
 */
class FulfillCheckoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    /** @var array<int, int> seconds between attempts */
    public array $backoff = [10, 60, 300, 900];

    public function __construct(
        public Checkout $checkout
    ) {}

    public function handle(TicketPdfService $pdfService): void
    {
        DB::transaction(function () {
            // Row lock: two runs for the same checkout cannot both create tickets.
            $checkout = Checkout::whereKey($this->checkout->getKey())->lockForUpdate()->firstOrFail();

            if ($checkout->issuedTickets()->exists()) {
                return;
            }

            foreach ($checkout->reservations as $reservation) {
                for ($i = 0; $i < $reservation->quantity; $i++) {
                    IssuedTicket::create([
                        'event_id' => $checkout->event_id,
                        'checkout_id' => $checkout->id,
                        'reservable_type' => $reservation->reservable_type,
                        'reservable_id' => $reservation->reservable_id,
                        'ticket_price_id' => $reservation->ticket_price_id,
                        'price_paid' => $reservation->unit_price,
                    ]);
                }
            }
        });

        // Outside the transaction: a mail failure must not roll the tickets back.
        $checkout = $this->checkout->fresh(['event', 'issuedTickets.checkout', 'issuedTickets.reservable', 'issuedTickets.ticketPrice']);
        $event = $checkout->event;

        $pdf = $pdfService->generate($event, $checkout->issuedTickets);

        Mail::to($checkout->customer_email)->send(
            new OrderTicketsMail($checkout, $event, $pdf, $event->ticket_email_content)
        );
    }

    /**
     * All attempts failed: the buyer has paid but may not have received anything.
     */
    public function failed(?Throwable $exception): void
    {
        Log::critical('Ticket fulfilment failed: paid order without tickets sent', [
            'checkout' => $this->checkout->uuid,
            'error' => $exception?->getMessage(),
        ]);

        if ($exception) {
            report($exception);
        }
    }
}
