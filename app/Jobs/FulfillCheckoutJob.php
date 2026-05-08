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
use Illuminate\Support\Str;

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

            // 1. Create IssuedTicket entries
            foreach ($this->checkout->reservations as $reservation) {
                $isAttendee = $reservation->reservable_type === 'App\Models\TicketType';

                for ($i = 0; $i < $reservation->quantity; $i++) {
                    $ticket = IssuedTicket::create([
                        'checkout_id' => $this->checkout->id,
                        'reservation_id' => $reservation->id,
                        'reservable_type' => $reservation->reservable_type,
                        'reservable_id' => $reservation->reservable_id,
                        'qr_code_token' => (string) Str::uuid(),
                        'is_attendee' => $isAttendee,
                        'name' => $reservation->reservable->name,
                        'price_paid' => $reservation->unit_price,
                    ]);

                    $issuedTickets[] = $ticket;
                }
            }

            // 2. Generate PDFs
            $pdf = $pdfService->generate($issuedTickets);

            // 3. Send Email
            Mail::to($this->checkout->customer_email)->send(
                new OrderTicketsMail($this->checkout, $event, $pdf)
            );
        });
    }
}
