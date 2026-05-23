<?php

namespace App\Http\Controllers;

use App\Enums\ReservableStatus;
use App\Models\Checkout;
use App\Models\Event;
use App\Models\EventAddon;
use App\Models\TicketPrice;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Mail\CheckoutEmailVerificationMail;
use App\Services\TicketPdfService;
use App\Enums\CheckoutStatus;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Stripe\Checkout\Session;
use Str;

class CheckoutController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.qty' => 'required|integer|min:1|max:99',
            'items.*.type' => 'required|in:ticket,addon',
            'items.*.id' => 'required|integer',
            'items.*.priceId' => 'nullable|required_if:items.*.type,ticket|integer',
        ]);

        try {
            $checkout = DB::transaction(function () use ($request, $event) {

                $checkout = Checkout::create([
                    'uuid' => (string) Str::uuid(),
                    'expires_at' => now()->addMinutes(15),
                    'event_id' => $event->id,
                ]);

                foreach ($request->items as $item) {
                    if ($item['type'] === 'ticket') {
                        $this->handleTicketReservation($checkout, $item);
                    } else {
                        $this->handleAddonReservation($checkout, $item);
                    }
                }

                return $checkout;
            });
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', collect($e->errors())->flatten()->first());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la réservation.');
        }

        return redirect()->route('checkout.show', $checkout->uuid);
    }

    public function show(Checkout $checkout, Request $request)
    {
        if ($request->has('reset_verification')) {
            $checkout->update([
                'email_verification_code' => null,
                'email_verified_at' => null,
            ]);
            return redirect()->route('checkout.show', $checkout->uuid);
        }

        if ($request->has('verification_code')) {
            if ((string)$checkout->email_verification_code === (string)$request->verification_code) {
                $checkout->update([
                    'email_verified_at' => now(),
                ]);
                return redirect()->route('checkout.show', $checkout->uuid)->with('message', 'Email vérifié avec succès !');
            }
        }

        if ($checkout->completed_at) {
            // Le checkout est déjà complété, rediriger vers la page de succès
            return redirect()->route('checkout.success', $checkout->uuid);
        } else if ($checkout->stripe_session_id) {
            // Une session de paiement est en cours, vérifier son statut auprès de Stripe
            $session = Session::retrieve($checkout->stripe_session_id);
            if ($session) {
                return redirect()->to($session->url);
            } else {
                // La session Stripe n'existe plus, réinitialiser le checkout pour permettre une nouvelle tentative
                $checkout->update([
                    'stripe_session_id' => null,
                ]);
            }
        } else if ($checkout->expires_at->isPast()) {
            // La session a expiré, rediriger vers la page de sélection des billets avec un message d'erreur
            $event = $checkout->event;
            $slug = $event?->slug;

            return redirect()->route($slug ? 'events.ticketing' : 'events.index', $slug ? ['slug' => $slug] : [])
                ->with('error', 'Votre session a expiré. Veuillez recommencer votre sélection.');
        }

        $legalPages = \App\Models\LegalPage::requiresAcceptance()->get();

        return Inertia::render('Checkout/Show', [
            'checkout' => $checkout->load('reservations.reservable.event'),
            'legalPages' => $legalPages,
        ]);
    }

    public function checkout(Request $request, Checkout $checkout)
    {
        $legalPagesRequiringAcceptance = \App\Models\LegalPage::requiresAcceptance()->with('latestVersion')->get();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ];

        foreach ($legalPagesRequiringAcceptance as $page) {
            $rules['accept_' . str_replace('-', '_', $page->slug)] = 'accepted';
        }

        $request->validate($rules);

        if (!$checkout->email_verified_at || $checkout->customer_email !== $request->email) {
            return redirect()->back()->with('error', 'Veuillez vérifier votre adresse email avant de continuer.');
        }

        if ($checkout->expires_at->isPast() || $checkout->completed_at || $checkout->cancelled_at) {
            return redirect()->route('events.ticketing', $checkout->event->slug)
                ->with('error', 'La session a expiré.');
        }

        if ($checkout->stripe_session_id) {
            return redirect()->back()->with('error', 'Une session de paiement est déjà en cours.');
        }

        $acceptedVersions = [];
        foreach ($legalPagesRequiringAcceptance as $page) {
            $acceptedVersions[] = [
                'legal_page_id' => $page->id,
                'slug' => $page->slug,
                'version_number' => $page->latestVersion?->version_number,
            ];
        }

        $checkout->update([
            'customer_name' => $request->name,
            'customer_email' => $request->email,
            'accepted_legal_pages' => $acceptedVersions,
        ]);

        $lineItems = $this->getLineItems($checkout);

        $event = $checkout->event;
        $eventMetadata = $event->stripe_metadata ?? [];

        $session = Session::create([
            'payment_method_types' => ['card', 'bancontact'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success', $checkout->uuid),
            'cancel_url' => route('checkout.cancel_payment', $checkout->uuid),
            'customer_email' => $checkout->customer_email,
            'client_reference_id' => $checkout->uuid,
            'expires_at' => now()->addMinutes(30)->timestamp,
            'metadata' => array_merge([
                'checkout_uuid' => $checkout->uuid,
            ], $eventMetadata),
            'payment_intent_data' => [
                'metadata' => array_merge([
                    'checkout_uuid' => $checkout->uuid,
                ], $eventMetadata),
            ],
        ]);

        $checkout->update([
            'stripe_session_id' => $session->id,
        ]);

        return Inertia::location($session->url);
    }

    public function success(Checkout $checkout)
    {
        return Inertia::render('Checkout/Success', [
            'checkout' => $checkout->load('reservations.reservable.event')->append('status'),
        ]);
    }

    public function downloadTickets(Checkout $checkout, TicketPdfService $pdfService)
    {
        if ($checkout->status !== CheckoutStatus::COMPLETED) {
            abort(403, 'Cette commande n\'est pas encore confirmée.');
        }

        $checkout->load(['issuedTickets.ticketPrice.ticketType', 'event']);

        $pdf = $pdfService->generate($checkout->event, $checkout->issuedTickets);

        return $pdf->download("tickets-{$checkout->uuid}.pdf");
    }

    public function cancel_payment(Checkout $checkout)
    {
        if (!$checkout->cancelled_at) {
            $checkout->update([
                'stripe_session_id' => null,
            ]);
        }

        return redirect()->route('checkout.show', $checkout->uuid)
            ->with('error', 'Le paiement a été annulé. Vous pouvez réessayer de payer ou revenir à la sélection des billets.');
    }

    public function sendVerificationEmail(Request $request, Checkout $checkout)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'required|string|max:255',
        ]);

        $code = str_pad((string)rand(1, 999999), 6, '0', STR_PAD_LEFT);

        $checkout->update([
            'customer_email' => $request->email,
            'customer_name' => $request->name,
            'email_verification_code' => (string)$code,
            'email_verified_at' => null,
        ]);

        $verificationUrl = route('checkout.show', [
            'checkout' => $checkout->uuid,
            'verification_code' => $code
        ]);

        Mail::to($request->email)->send(new CheckoutEmailVerificationMail($checkout, $code, $verificationUrl));

        return redirect()->back()->with('message', 'Code de vérification envoyé !');
    }

    public function verifyEmail(Request $request, Checkout $checkout)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $checkout = $checkout->fresh();

        if ($checkout->email_verification_code && (string)$checkout->email_verification_code === (string)$request->code) {
            $checkout->update([
                'email_verified_at' => now(),
            ]);

            return redirect()->back();
        }

        return redirect()->back()->withErrors(['code' => 'Code invalide.']);
    }

    private function handleTicketReservation(Checkout $checkout, array $item)
    {
        $price = TicketPrice::with('ticketType')->findOrFail($item['priceId']);
        /** @var TicketType $ticketType */
        $ticketType = $price->ticketType;

        if (!$ticketType->hasStockFor($item['qty'])) {
            throw ValidationException::withMessages([
                'cart' => "Il ne reste plus assez de places pour '{$ticketType->name}'."
            ]);
        }

        if ($price->status !== ReservableStatus::OPEN) {
            throw ValidationException::withMessages([
                'cart' => "Le tarif pour '{$ticketType->name} - {$price->name}' a changé ou n'est plus disponible."
            ]);
        }


        $checkout->reservations()->create([
            'reservable_id' => $ticketType->id,
            'reservable_type' => TicketType::class,
            'ticket_price_id' => $price->id,
            'quantity' => $item['qty'],
            'unit_price' => $price->price,
        ]);
    }

    private function handleAddonReservation(Checkout $checkout, array $item)
    {
        $addon = EventAddon::findOrFail($item['id']);

        if ($addon->status !== ReservableStatus::OPEN) {
            throw ValidationException::withMessages([
                'cart' => "L'addon '{$addon->name}' n'est plus disponible."
            ]);
        }

        if (!$addon->hasStockFor($item['qty'])) {
            throw ValidationException::withMessages([
                'cart' => "Il ne reste plus assez de stock pour l'addon '{$addon->name}'."
            ]);
        }

        $checkout->reservations()->create([
            'reservable_id' => $addon->id,
            'reservable_type' => EventAddon::class,
            'quantity' => $item['qty'],
            'unit_price' => $addon->price,
        ]);
    }

    private function getLineItems(Checkout $checkout)
    {
        $lineItems = [];

        foreach ($checkout->reservations as $reservation) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $reservation->reservable->name,
                    ],
                    'unit_amount' => (int) $reservation->unit_price,
                ],
                'quantity' => $reservation->quantity,
            ];
        }

        return $lineItems;
    }
}
