<?php

use App\Enums\PublicationStatus;
use App\Mail\CheckoutEmailVerificationMail;
use App\Models\Checkout;
use App\Models\Event;
use App\Models\EventAddon;
use App\Models\TicketPrice;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutVite();
});

/**
 * @return array{type: TicketType, price: TicketPrice}
 */
function makeTicketTypeWithPrice(Event $event, int $capacity = 100, ?int $maxPerOrder = null): array
{
    $type = TicketType::create([
        'event_id' => $event->id,
        'name' => 'Standard',
        'capacity' => $capacity,
        'max_per_order' => $maxPerOrder,
    ]);

    $price = TicketPrice::create(['ticket_type_id' => $type->id, 'name' => 'Normal', 'price' => 1500]);

    return ['type' => $type, 'price' => $price];
}

function makePendingCheckout(Event $event, array $attributes = []): Checkout
{
    return Checkout::create([
        'uuid' => (string) Str::uuid(),
        'event_id' => $event->id,
        'expires_at' => now()->addMinutes(15),
        ...$attributes,
    ]);
}

function ticketLine(TicketType $type, TicketPrice $price, int $qty): array
{
    return ['type' => 'ticket', 'id' => $type->id, 'priceId' => $price->id, 'qty' => $qty];
}

describe('S2 — the verification code never reaches the browser', function () {
    it('hides the code and the Stripe ids from the checkout page', function () {
        $checkout = makePendingCheckout(makeOnSaleEvent(), [
            'customer_email' => 'jane@example.com',
            'email_verification_code' => '123456',
            'email_verification_sent_at' => now(),
            'stripe_payment_intent_id' => 'pi_secret',
        ]);

        $this->get("/checkout/{$checkout->uuid}")
            ->assertOk()
            ->assertDontSee('123456')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Checkout/Show')
                ->missing('checkout.email_verification_code')
                ->missing('checkout.stripe_payment_intent_id')
                ->where('checkout.verification_pending', true));
    });

    it('hides the code from the success page', function () {
        $checkout = makePendingCheckout(makeOnSaleEvent(), ['email_verification_code' => '654321']);

        $this->get("/checkout/{$checkout->uuid}/success")
            ->assertOk()
            ->assertDontSee('654321')
            ->assertInertia(fn (Assert $page) => $page->missing('checkout.email_verification_code'));
    });
});

describe('S11 — draft and scheduled events stay private', function () {
    it('returns 404 to visitors for a draft or scheduled event', function () {
        $draft = makeEvent();
        $scheduled = makeEvent(['status' => PublicationStatus::Published, 'published_at' => now()->addDay()]);

        $this->get("/events/{$draft->slug}")->assertNotFound();
        $this->get("/events/{$scheduled->slug}")->assertNotFound();
    });

    it('shows a published event', function () {
        $this->get('/events/'.makeOnSaleEvent()->slug)->assertOk();
    });

    it('lets a logged-in admin preview a draft', function () {
        $this->actingAs(User::factory()->create())
            ->get('/events/'.makeEvent()->slug)
            ->assertOk();
    });

    it('never opens the ticketing of a draft, even for admins', function () {
        $draft = makeEvent(['ticketing_starts_at' => now()->subDay()]);

        $this->actingAs(User::factory()->create())
            ->get("/events/{$draft->slug}/ticketing")
            ->assertNotFound();
    });
});

describe('S3 — the cart is validated against the event', function () {
    it('books valid tickets', function () {
        $event = makeOnSaleEvent();
        ['type' => $type, 'price' => $price] = makeTicketTypeWithPrice($event);

        $response = $this->post("/events/{$event->slug}/checkout", ['items' => [ticketLine($type, $price, 2)]]);

        $checkout = Checkout::sole();
        $response->assertRedirect(route('checkout.show', $checkout->uuid));
        expect($checkout->reservations()->sole())
            ->quantity->toBe(2)
            ->unit_price->toBe(1500)
            ->ticket_price_id->toBe($price->id);
    });

    it('refuses a price that belongs to another event', function () {
        $event = makeOnSaleEvent();
        ['type' => $otherType, 'price' => $otherPrice] = makeTicketTypeWithPrice(makeOnSaleEvent());

        $this->from("/events/{$event->slug}/ticketing")
            ->post("/events/{$event->slug}/checkout", ['items' => [ticketLine($otherType, $otherPrice, 1)]])
            ->assertSessionHas('error', 'Un article de votre panier n\'est pas disponible pour cet événement.');

        expect(Checkout::count())->toBe(0);
    });

    it('refuses an addon that belongs to another event', function () {
        $event = makeOnSaleEvent();
        $otherAddon = EventAddon::create(['event_id' => makeOnSaleEvent()->id, 'name' => 'Vestiaire', 'price' => 200]);

        $this->post("/events/{$event->slug}/checkout", ['items' => [['type' => 'addon', 'id' => $otherAddon->id, 'qty' => 1]]])
            ->assertSessionHas('error', 'Un article de votre panier n\'est pas disponible pour cet événement.');

        expect(Checkout::count())->toBe(0);
    });

    it('refuses a draft event', function () {
        $draft = makeEvent(['ticketing_starts_at' => now()->subDay()]);
        ['type' => $type, 'price' => $price] = makeTicketTypeWithPrice($draft);

        $this->post("/events/{$draft->slug}/checkout", ['items' => [ticketLine($type, $price, 1)]])
            ->assertSessionHas('error', 'La billetterie n\'est pas accessible pour le moment.');

        expect(Checkout::count())->toBe(0);
    });

    it('refuses an event whose ticketing is closed', function () {
        $event = makeOnSaleEvent(['ticketing_starts_at' => now()->addDay()]);
        ['type' => $type, 'price' => $price] = makeTicketTypeWithPrice($event);

        $this->post("/events/{$event->slug}/checkout", ['items' => [ticketLine($type, $price, 1)]])
            ->assertSessionHas('error', 'La billetterie n\'est pas accessible pour le moment.');

        expect(Checkout::count())->toBe(0);
    });

    it('enforces max_per_order on the total quantity of a ticket type', function () {
        $event = makeOnSaleEvent();
        ['type' => $type, 'price' => $price] = makeTicketTypeWithPrice($event, maxPerOrder: 4);

        $this->post("/events/{$event->slug}/checkout", ['items' => [ticketLine($type, $price, 3), ticketLine($type, $price, 2)]])
            ->assertSessionHas('error', "Vous pouvez commander au maximum 4 × 'Standard' par commande.");

        expect(Checkout::count())->toBe(0);
    });

    it('applies the default limit of 10 when max_per_order is not set', function () {
        $event = makeOnSaleEvent();
        ['type' => $type, 'price' => $price] = makeTicketTypeWithPrice($event);

        $this->post("/events/{$event->slug}/checkout", ['items' => [ticketLine($type, $price, 11)]])
            ->assertSessionHas('error', "Vous pouvez commander au maximum 10 × 'Standard' par commande.");
    });

    it('checks stock on the total quantity of a ticket type', function () {
        $event = makeOnSaleEvent();
        ['type' => $type, 'price' => $price] = makeTicketTypeWithPrice($event, capacity: 3);

        $this->post("/events/{$event->slug}/checkout", ['items' => [ticketLine($type, $price, 2), ticketLine($type, $price, 2)]])
            ->assertSessionHas('error', "Il ne reste plus assez de places pour 'Standard'.");

        expect(Checkout::count())->toBe(0);
    });
});

describe('S10 — admin-only event fields are not serialized', function () {
    it('strips them from public pages but keeps them for admin forms', function () {
        $event = makeOnSaleEvent([
            'stripe_metadata' => ['accounting_code' => 'SECRET-42'],
            'ticket_email_content' => '<p>internal email</p>',
            'ticket_pdf_content' => '<p>internal pdf</p>',
        ]);

        $this->get("/events/{$event->slug}")
            ->assertOk()
            ->assertDontSee('SECRET-42')
            ->assertInertia(fn (Assert $page) => $page
                ->missing('event.stripe_metadata')
                ->missing('event.ticket_email_content')
                ->missing('event.ticket_pdf_content'));

        // Filament fills its edit forms with attributesToArray().
        expect($event->fresh()->attributesToArray())
            ->toHaveKeys(['stripe_metadata', 'ticket_email_content', 'ticket_pdf_content']);
    });
});

describe('S8 — the email verification code is hardened', function () {
    beforeEach(fn () => Mail::fake());

    it('sends a 6-digit code', function () {
        $checkout = makePendingCheckout(makeOnSaleEvent());

        $this->post("/checkout/{$checkout->uuid}/send-verification", ['email' => 'jane@example.com', 'name' => 'Jane']);

        Mail::assertSent(CheckoutEmailVerificationMail::class, fn ($mail) => preg_match('/^\d{6}$/', $mail->code) === 1);
        expect($checkout->fresh())
            ->email_verification_sent_at->not->toBeNull()
            ->email_verification_attempts->toBe(0);
    });

    it('verifies the right code and clears it', function () {
        $checkout = makePendingCheckout(makeOnSaleEvent(), ['email_verification_code' => '123456', 'email_verification_sent_at' => now()]);

        $this->post("/checkout/{$checkout->uuid}/verify", ['code' => '123456'])->assertSessionHasNoErrors();

        expect($checkout->fresh())
            ->email_verified_at->not->toBeNull()
            ->email_verification_code->toBeNull();
    });

    it('invalidates the code after 5 wrong attempts', function () {
        $checkout = makePendingCheckout(makeOnSaleEvent(), ['email_verification_code' => '123456', 'email_verification_sent_at' => now()]);

        foreach (range(1, 5) as $_) {
            $this->post("/checkout/{$checkout->uuid}/verify", ['code' => '000000'])->assertSessionHasErrors(['code' => 'Code invalide.']);
        }

        $this->post("/checkout/{$checkout->uuid}/verify", ['code' => '123456'])
            ->assertSessionHasErrors(['code' => 'Ce code a expiré ou a été trop de fois mal saisi. Demandez un nouveau code.']);

        expect($checkout->fresh())
            ->email_verified_at->toBeNull()
            ->email_verification_code->toBeNull();
    });

    it('rejects a code older than 15 minutes', function () {
        $checkout = makePendingCheckout(makeOnSaleEvent(), [
            'email_verification_code' => '123456',
            'email_verification_sent_at' => now()->subMinutes(16),
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->post("/checkout/{$checkout->uuid}/verify", ['code' => '123456'])
            ->assertSessionHasErrors('code');

        expect($checkout->fresh()->email_verified_at)->toBeNull();
    });

    it('verifies through the email link and drops the code from the URL', function () {
        $checkout = makePendingCheckout(makeOnSaleEvent(), ['email_verification_code' => '123456', 'email_verification_sent_at' => now()]);

        $this->get("/checkout/{$checkout->uuid}?verification_code=123456")
            ->assertRedirect(route('checkout.show', $checkout->uuid));

        expect($checkout->fresh()->email_verified_at)->not->toBeNull();
    });

    it('does not send a code for a completed checkout', function () {
        $checkout = makePendingCheckout(makeOnSaleEvent(), ['completed_at' => now()]);

        $this->post("/checkout/{$checkout->uuid}/send-verification", ['email' => 'jane@example.com', 'name' => 'Jane'])
            ->assertSessionHas('error', 'Cette commande ne peut plus être modifiée.');

        Mail::assertNothingSent();
    });
});

describe('S6 — checkout endpoints are rate limited', function () {
    it('limits verification emails to 3 per checkout every 10 minutes', function () {
        Mail::fake();
        $checkout = makePendingCheckout(makeOnSaleEvent());
        $send = fn () => $this->post("/checkout/{$checkout->uuid}/send-verification", ['email' => 'jane@example.com', 'name' => 'Jane']);

        foreach (range(1, 3) as $_) {
            $send()->assertSessionHas('message', 'Code de vérification envoyé !');
        }

        $send()->assertSessionHas('error', 'Trop d\'emails envoyés. Patientez quelques minutes avant de redemander un code.');
        Mail::assertSentCount(3);
    });

    it('limits lost-ticket requests to 3 per email per hour', function () {
        Queue::fake();

        foreach (range(1, 3) as $_) {
            $this->post('/lost-tickets', ['email' => 'jane@example.com'])->assertSessionHasNoErrors();
        }

        $this->post('/lost-tickets', ['email' => 'JANE@example.com'])
            ->assertSessionHasErrors(['email' => 'Trop de demandes. Réessayez plus tard.']);
    });

    it('limits checkout creation to 10 per minute per IP', function () {
        $event = makeOnSaleEvent();
        ['type' => $type, 'price' => $price] = makeTicketTypeWithPrice($event, capacity: 1000);
        $book = fn () => $this->post("/events/{$event->slug}/checkout", ['items' => [ticketLine($type, $price, 1)]]);

        foreach (range(1, 10) as $_) {
            $book()->assertSessionMissing('error');
        }

        $book()->assertSessionHas('error', 'Trop de tentatives. Réessayez dans une minute.');
        expect(Checkout::count())->toBe(10);
    });
});
