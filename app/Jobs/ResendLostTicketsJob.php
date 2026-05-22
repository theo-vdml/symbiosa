<?php

namespace App\Jobs;

use App\Mail\ResentLostTicketsMail;
use App\Models\Checkout;
use App\Services\TicketPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ResendLostTicketsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $email
    ) {}

    /**
     * Execute the job.
     */
    public function handle(TicketPdfService $pdfService): void
    {
        $checkouts = Checkout::query()
            ->where('customer_email', $this->email)
            ->isCompleted()
            ->whereHas('event', function ($query) {
                $query->notFinished();
            })
            ->with(['event', 'issuedTickets'])
            ->get();

        foreach ($checkouts as $checkout) {
            /** @var Checkout $checkout */
            $event = $checkout->event;
            $issuedTickets = $checkout->issuedTickets;

            if ($issuedTickets->isEmpty()) {
                continue;
            }

            $pdf = $pdfService->generate($event, $issuedTickets);

            Mail::to($checkout->customer_email)->send(
                new ResentLostTicketsMail($checkout, $event, $pdf)
            );
        }
    }
}
