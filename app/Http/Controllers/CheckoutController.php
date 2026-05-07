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

        return redirect()->route('checkout.show', $checkout->uuid);
    }

    public function show(Checkout $checkout)
    {
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

        return Inertia::render('Checkout/Show', [
            'checkout' => $checkout->load('reservations.reservable.event'),
        ]);
    }

    public function checkout(Request $request, Checkout $checkout)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'accept_cgv' => 'accepted',
            'accept_rgpd' => 'accepted',
        ]);

        if ($checkout->expires_at->isPast() || $checkout->completed_at || $checkout->cancelled_at) {
            return redirect()->route('events.ticketing', $checkout->event->slug)
                ->with('error', 'La session a expiré.');
        }

        if ($checkout->stripe_session_id) {
            return redirect()->back()->with('error', 'Une session de paiement est déjà en cours.');
        }

        $checkout->update([
            'customer_name' => $request->name,
            'customer_email' => $request->email,
        ]);

        $lineItems = $this->getLineItems($checkout);

        $session = Session::create([
            'payment_method_types' => ['card', 'bancontact'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success', $checkout->uuid),
            'cancel_url' => route('checkout.cancel_payment', $checkout->uuid),
            'customer_email' => $checkout->customer_email,
            'client_reference_id' => $checkout->uuid,
            'expires_at' => now()->addMinutes(30)->timestamp,
            'metadata' => [
                'checkout_uuid' => $checkout->uuid,
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'checkout_uuid' => $checkout->uuid,
                ],
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
            'checkout' => $checkout->load('reservations.reservable.event'),
        ]);
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

    private function handleTicketReservation(Checkout $checkout, array $item)
    {
        $price = TicketPrice::with('ticketType')->findOrFail($item['priceId']);
        /** @var TicketType $ticketType */
        $ticketType = $price->ticketType;

        if ($price->status !== ReservableStatus::OPEN) {
            throw ValidationException::withMessages([
                'cart' => "Le tarif pour '{$ticketType->name}' a changé ou n'est plus disponible."
            ]);
        }

        if (!$ticketType->hasStockFor($item['qty'])) {
            throw ValidationException::withMessages([
                'cart' => "Il ne reste plus assez de places pour '{$ticketType->name}'."
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
