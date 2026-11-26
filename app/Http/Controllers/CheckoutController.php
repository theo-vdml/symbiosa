<?php

namespace App\Http\Controllers;

use App\Enums\CheckoutStatus;
use App\Enums\ReservableStatus;
use App\Mail\CheckoutEmailVerificationMail;
use App\Models\Checkout;
use App\Models\Event;
use App\Models\EventAddon;
use App\Models\LegalPage;
use App\Models\TicketPrice;
use App\Models\TicketType;
use App\Services\TicketPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Str;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    /**
     * Default per-order cap when a ticket type or addon has none (matches the ticketing UI).
     */
    private const DEFAULT_MAX_PER_ORDER = 10;

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.qty' => 'required|integer|min:1|max:99',
            'items.*.type' => 'required|in:ticket,addon',
            'items.*.id' => 'required|integer',
            'items.*.priceId' => 'nullable|required_if:items.*.type,ticket|integer',
        ]);

        $isPublished = Event::published()->whereKey($event->getKey())->exists();

        if (! $isPublished || ! $event->is_ticketing_open) {
            return redirect()->back()->with('error', 'La billetterie n\'est pas accessible pour le moment.');
        }

        try {
            $checkout = DB::transaction(function () use ($request, $event) {
                // Serialize reservations per event: a concurrent checkout waits here until this
                // one commits, then sees its reservations in the stock count (no overselling).
                // Must be the first query of the transaction so the stock reads happen after it.
                Event::whereKey($event->getKey())->lockForUpdate()->first();

                $checkout = Checkout::create([
                    'uuid' => (string) Str::uuid(),
                    'expires_at' => now()->addMinutes(15),
                    'event_id' => $event->id,
                ]);

                $tickets = collect($request->items)->where('type', 'ticket');
                $addons = collect($request->items)->where('type', 'addon');

                $this->reserveTickets($checkout, $event, $tickets->all());
                $this->reserveAddons($checkout, $event, $addons->all());

                return $checkout;
            });
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', collect($e->errors())->flatten()->first());
        } catch (\Exception $e) {
            report($e);

            return redirect()->back()->with('error', 'Une erreur est survenue lors de la réservation.');
        }

        return redirect()->route('checkout.show', $checkout->uuid);
    }

    public function show(Checkout $checkout, Request $request)
    {
        if ($request->has('reset_verification')) {
            $checkout->update([
                'email_verification_code' => null,
                'email_verification_sent_at' => null,
                'email_verification_attempts' => 0,
                'email_verified_at' => null,
            ]);

            return redirect()->route('checkout.show', $checkout->uuid);
        }

        // Link from the verification email; redirect so the code does not stay in the URL.
        if ($request->filled('verification_code')) {
            $error = $this->attemptEmailVerification($checkout, (string) $request->query('verification_code'));

            return redirect()->route('checkout.show', $checkout->uuid)
                ->with($error ? 'error' : 'message', $error ?? 'Email vérifié avec succès !');
        }

        if ($checkout->completed_at) {
            // Le checkout est déjà complété, rediriger vers la page de succès
            return redirect()->route('checkout.success', $checkout->uuid);
        } elseif ($checkout->stripe_session_id) {
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
        } elseif ($checkout->expires_at->isPast()) {
            // La session a expiré, rediriger vers la page de sélection des billets avec un message d'erreur
            $event = $checkout->event;
            $slug = $event?->slug;

            return redirect()->route($slug ? 'events.ticketing' : 'events.index', $slug ? ['slug' => $slug] : [])
                ->with('error', 'Votre session a expiré. Veuillez recommencer votre sélection.');
        }

        $legalPages = LegalPage::requiresAcceptance()->get();

        return Inertia::render('Checkout/Show', [
            'checkout' => $checkout->load('reservations.reservable.event'),
            'legalPages' => $legalPages,
        ]);
    }

    public function checkout(Request $request, Checkout $checkout)
    {
        $legalPagesRequiringAcceptance = LegalPage::requiresAcceptance()->with('latestVersion')->get();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ];

        foreach ($legalPagesRequiringAcceptance as $page) {
            $rules['accept_'.str_replace('-', '_', $page->slug)] = 'accepted';
        }

        $request->validate($rules);

        if (! $checkout->email_verified_at || $checkout->customer_email !== $request->email) {
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

        $stripeExpiresAt = now()->addMinutes(Checkout::STRIPE_SESSION_TTL);

        $session = Session::create([
            'payment_method_types' => ['card', 'bancontact'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success', $checkout->uuid),
            'cancel_url' => route('checkout.cancel_payment', $checkout->uuid),
            'customer_email' => $checkout->customer_email,
            'client_reference_id' => $checkout->uuid,
            'expires_at' => $stripeExpiresAt->timestamp,
            'metadata' => array_merge([
                'checkout_uuid' => $checkout->uuid,
            ], $eventMetadata),
            'payment_intent_data' => [
                'metadata' => array_merge([
                    'checkout_uuid' => $checkout->uuid,
                ], $eventMetadata),
            ],
        ]);

        // Hold the stock for as long as this session can be paid.
        $checkout->update([
            'stripe_session_id' => $session->id,
            'expires_at' => $stripeExpiresAt->addMinutes(Checkout::STRIPE_WEBHOOK_GRACE),
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
        if (! $checkout->cancelled_at) {
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

        if ($checkout->completed_at || $checkout->cancelled_at || $checkout->stripe_session_id || $checkout->expires_at->isPast()) {
            return redirect()->back()->with('error', 'Cette commande ne peut plus être modifiée.');
        }

        $code = sprintf('%06d', random_int(0, 999999));

        $checkout->update([
            'customer_email' => $request->email,
            'customer_name' => $request->name,
            'email_verification_code' => $code,
            'email_verification_sent_at' => now(),
            'email_verification_attempts' => 0,
            'email_verified_at' => null,
        ]);

        $verificationUrl = route('checkout.show', [
            'checkout' => $checkout->uuid,
            'verification_code' => $code,
        ]);

        Mail::to($request->email)->send(new CheckoutEmailVerificationMail($checkout, $code, $verificationUrl));

        return redirect()->back()->with('message', 'Code de vérification envoyé !');
    }

    public function verifyEmail(Request $request, Checkout $checkout)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $error = $this->attemptEmailVerification($checkout, (string) $request->code);

        if ($error) {
            // Once the code is invalidated the page goes back to the email step, where
            // field errors of the code step are not shown: flash the message instead.
            return $checkout->fresh()->verification_pending
                ? redirect()->back()->withErrors(['code' => $error])
                : redirect()->back()->with('error', $error);
        }

        return redirect()->back();
    }

    /**
     * Checks a verification code, counting the attempt.
     *
     * @return string|null An error message, or null when the email is now verified.
     */
    private function attemptEmailVerification(Checkout $checkout, string $code): ?string
    {
        // Row lock: concurrent guesses are counted one after the other, so the
        // attempt limit cannot be exceeded.
        return DB::transaction(function () use ($checkout, $code) {
            $checkout = Checkout::whereKey($checkout->getKey())->lockForUpdate()->firstOrFail();

            if ($checkout->email_verified_at) {
                return null;
            }

            if (blank($checkout->email_verification_code)) {
                return 'Aucun code en attente. Demandez un nouveau code.';
            }

            $expired = $checkout->email_verification_sent_at === null
                || $checkout->email_verification_sent_at->addMinutes(Checkout::VERIFICATION_CODE_TTL)->isPast();

            if ($expired || $checkout->email_verification_attempts >= Checkout::VERIFICATION_MAX_ATTEMPTS) {
                $checkout->update(['email_verification_code' => null]);

                return 'Ce code a expiré ou a été trop de fois mal saisi. Demandez un nouveau code.';
            }

            $checkout->increment('email_verification_attempts');

            if (! hash_equals((string) $checkout->email_verification_code, trim($code))) {
                return 'Code invalide.';
            }

            $checkout->update([
                'email_verified_at' => now(),
                'email_verification_code' => null,
            ]);

            return null;
        });
    }

    /**
     * @param  array<int, array{qty: int, priceId: int}>  $items
     */
    private function reserveTickets(Checkout $checkout, Event $event, array $items): void
    {
        if ($items === []) {
            return;
        }

        // Only prices of this event's ticket types can be booked.
        $prices = TicketPrice::with('ticketType')
            ->whereIn('id', array_column($items, 'priceId'))
            ->whereHas('ticketType', fn ($query) => $query->where('event_id', $event->id))
            ->get()
            ->keyBy('id');

        $quantitiesByType = [];

        foreach ($items as $item) {
            $price = $prices->get($item['priceId']) ?? throw $this->unavailableItem();
            $ticketType = $price->ticketType;

            if ($price->status !== ReservableStatus::OPEN) {
                throw ValidationException::withMessages([
                    'cart' => "Le tarif pour '{$ticketType->name} - {$price->name}' a changé ou n'est plus disponible.",
                ]);
            }

            $quantitiesByType[$ticketType->id] = ($quantitiesByType[$ticketType->id] ?? 0) + $item['qty'];
        }

        // Stock and per-order limits apply to the total quantity of each ticket type.
        foreach ($quantitiesByType as $ticketTypeId => $quantity) {
            $ticketType = $prices->firstWhere('ticket_type_id', $ticketTypeId)->ticketType;
            $this->ensureQuantityAllowed($ticketType->name, $quantity, $ticketType->max_per_order, $ticketType->hasStockFor($quantity));
        }

        foreach ($items as $item) {
            $price = $prices->get($item['priceId']);

            $checkout->reservations()->create([
                'reservable_id' => $price->ticket_type_id,
                'reservable_type' => TicketType::class,
                'ticket_price_id' => $price->id,
                'quantity' => $item['qty'],
                'unit_price' => $price->price,
            ]);
        }
    }

    /**
     * @param  array<int, array{qty: int, id: int}>  $items
     */
    private function reserveAddons(Checkout $checkout, Event $event, array $items): void
    {
        if ($items === []) {
            return;
        }

        // Only this event's addons can be booked.
        $addons = $event->addons()->whereIn('event_addons.id', array_column($items, 'id'))->get()->keyBy('id');

        $quantities = [];

        foreach ($items as $item) {
            $addon = $addons->get($item['id']) ?? throw $this->unavailableItem();

            if ($addon->status !== ReservableStatus::OPEN) {
                throw ValidationException::withMessages([
                    'cart' => "L'addon '{$addon->name}' n'est plus disponible.",
                ]);
            }

            $quantities[$addon->id] = ($quantities[$addon->id] ?? 0) + $item['qty'];
        }

        foreach ($quantities as $addonId => $quantity) {
            $addon = $addons->get($addonId);
            $this->ensureQuantityAllowed($addon->name, $quantity, $addon->max_per_order, $addon->hasStockFor($quantity));

            $checkout->reservations()->create([
                'reservable_id' => $addon->id,
                'reservable_type' => EventAddon::class,
                'quantity' => $quantity,
                'unit_price' => $addon->price,
            ]);
        }
    }

    private function ensureQuantityAllowed(string $name, int $quantity, ?int $maxPerOrder, bool $hasStock): void
    {
        $max = $maxPerOrder ?: self::DEFAULT_MAX_PER_ORDER;

        if ($quantity > $max) {
            throw ValidationException::withMessages([
                'cart' => "Vous pouvez commander au maximum {$max} × '{$name}' par commande.",
            ]);
        }

        if (! $hasStock) {
            throw ValidationException::withMessages([
                'cart' => "Il ne reste plus assez de places pour '{$name}'.",
            ]);
        }
    }

    private function unavailableItem(): ValidationException
    {
        return ValidationException::withMessages([
            'cart' => 'Un article de votre panier n\'est pas disponible pour cet événement.',
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
