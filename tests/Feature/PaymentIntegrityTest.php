<?php

use App\Jobs\FulfillCheckoutJob;
use App\Models\Checkout;
use App\Models\Event;
use App\Models\IssuedTicket;
use App\Models\TicketPrice;
use App\Models\TicketType;
use App\Services\TicketPdfService;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\Support\FakeStripeClient;

beforeEach(function () {
    $this->withoutVite();
    $this->stripe = FakeStripeClient::install();
    config(['services.stripe.webhook.secret' => 'whsec_test']);
});

afterEach(fn () => FakeStripeClient::uninstall());

/**
 * A checkout with one reservation of 2 tickets on a fresh event.
 *
 * @param  array<string, mixed>  $attributes
 */
function makeReservedCheckout(array $attributes = [], int $capacity = 100): Checkout
{
    $event = makeOnSaleEvent();
    $type = TicketType::create(['event_id' => $event->id, 'name' => 'Standard', 'capacity' => $capacity]);
    $price = TicketPrice::create(['ticket_type_id' => $type->id, 'name' => 'Normal', 'price' => 1500]);

    $checkout = Checkout::create([
        'uuid' => (string) Str::uuid(),
        'event_id' => $event->id,
        'customer_name' => 'Jane Buyer',
        'customer_email' => 'jane@example.com',
        'expires_at' => now()->addMinutes(15),
        ...$attributes,
    ]);

    $checkout->reservations()->create([
        'reservable_type' => TicketType::class,
        'reservable_id' => $type->id,
        'ticket_price_id' => $price->id,
        'quantity' => 2,
        'unit_price' => 1500,
    ]);

    return $checkout;
}

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function stripeSession(string $id, string $status, array $overrides = []): array
{
    return [
        'id' => $id,
        'object' => 'checkout.session',
        'status' => $status,
        'payment_status' => $status === 'complete' ? 'paid' : 'unpaid',
        'url' => $status === 'open' ? "https://checkout.stripe.com/c/pay/{$id}" : null,
        ...$overrides,
    ];
}

/**
 * Sends a correctly signed webhook.
 *
 * @param  array<string, mixed>  $object
 */
function postStripeWebhook(string $type, array $object, string $secret = 'whsec_test')
{
    $payload = json_encode([
        'id' => 'evt_'.Str::random(10),
        'object' => 'event',
        'type' => $type,
        'data' => ['object' => $object],
    ]);
    $timestamp = time();
    $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", $secret);

    return test()->call('POST', '/webhooks/stripe', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
        'CONTENT_TYPE' => 'application/json',
    ], $payload);
}

/**
 * @return array<string, mixed>
 */
function paidSessionFor(Checkout $checkout, string $sessionId = 'cs_paid', array $overrides = []): array
{
    return stripeSession($sessionId, 'complete', [
        'client_reference_id' => $checkout->uuid,
        'metadata' => ['checkout_uuid' => $checkout->uuid],
        'payment_intent' => 'pi_1',
        'customer' => null,
        ...$overrides,
    ]);
}

function availableStock(Checkout $checkout): ?int
{
    return $checkout->reservations()->first()->reservable->fresh()->available_stock;
}

describe('Stock is held exactly as long as the order can be paid', function () {
    it('releases the stock of an expired checkout even if it still has a Stripe session', function () {
        $checkout = makeReservedCheckout(['stripe_session_id' => 'cs_stale'], capacity: 10);
        expect(availableStock($checkout))->toBe(8);

        $checkout->update(['expires_at' => now()->subMinute()]);

        expect(availableStock($checkout))->toBe(10)
            ->and($checkout->fresh()->status->value)->toBe('expired');
    });

    it('extends the checkout to the Stripe session expiry plus a grace period', function () {
        $checkout = makeReservedCheckout(['email_verified_at' => now()]);
        $this->stripe->respond('POST', '/v1/checkout/sessions', stripeSession('cs_new', 'open'));

        $this->post("/checkout/{$checkout->uuid}/start", ['name' => 'Jane Buyer', 'email' => 'jane@example.com']);

        $checkout->refresh();
        expect($checkout->stripe_session_id)->toBe('cs_new')
            ->and($checkout->expires_at->diffInMinutes(now(), true))->toBeGreaterThan(34)->toBeLessThanOrEqual(35);
    });
});

describe('Cancelling and resuming a payment', function () {
    it('expires the open Stripe session when the buyer cancels', function () {
        $checkout = makeReservedCheckout(['stripe_session_id' => 'cs_1']);
        $this->stripe
            ->respond('GET', '/v1/checkout/sessions/cs_1', stripeSession('cs_1', 'open'))
            ->respond('POST', '/v1/checkout/sessions/cs_1/expire', stripeSession('cs_1', 'expired'));

        $this->get("/checkout/{$checkout->uuid}/cancel")->assertRedirect(route('checkout.show', $checkout->uuid));

        expect($this->stripe->requested('POST', '/v1/checkout/sessions/cs_1/expire'))->toBeTrue()
            ->and($checkout->fresh()->stripe_session_id)->toBeNull();
    });

    it('sends the buyer to the success page when the cancelled session was in fact paid', function () {
        $checkout = makeReservedCheckout(['stripe_session_id' => 'cs_1']);
        $this->stripe->respond('GET', '/v1/checkout/sessions/cs_1', stripeSession('cs_1', 'complete'));

        $this->get("/checkout/{$checkout->uuid}/cancel")->assertRedirect(route('checkout.success', $checkout->uuid));

        expect($this->stripe->requested('POST', '/v1/checkout/sessions/cs_1/expire'))->toBeFalse()
            ->and($checkout->fresh()->stripe_session_id)->toBe('cs_1');
    });

    it('resumes an open session', function () {
        $checkout = makeReservedCheckout(['stripe_session_id' => 'cs_1']);
        $this->stripe->respond('GET', '/v1/checkout/sessions/cs_1', stripeSession('cs_1', 'open'));

        $this->get("/checkout/{$checkout->uuid}")->assertRedirect('https://checkout.stripe.com/c/pay/cs_1');
    });

    it('goes to the success page for a paid session', function () {
        $checkout = makeReservedCheckout(['stripe_session_id' => 'cs_1']);
        $this->stripe->respond('GET', '/v1/checkout/sessions/cs_1', stripeSession('cs_1', 'complete'));

        $this->get("/checkout/{$checkout->uuid}")->assertRedirect(route('checkout.success', $checkout->uuid));
    });

    it('clears an expired or unknown session instead of failing', function (string $kind) {
        $checkout = makeReservedCheckout(['stripe_session_id' => 'cs_1']);
        $kind === 'expired'
            ? $this->stripe->respond('GET', '/v1/checkout/sessions/cs_1', stripeSession('cs_1', 'expired'))
            : $this->stripe->respondMissing('GET', '/v1/checkout/sessions/cs_1');

        $this->get("/checkout/{$checkout->uuid}")->assertOk();

        expect($checkout->fresh()->stripe_session_id)->toBeNull();
    })->with(['expired', 'unknown']);

    it('sends an expired checkout back to the ticketing page after clearing its session', function () {
        $checkout = makeReservedCheckout(['stripe_session_id' => 'cs_1', 'expires_at' => now()->subMinute()]);
        $this->stripe->respond('GET', '/v1/checkout/sessions/cs_1', stripeSession('cs_1', 'expired'));

        $this->get("/checkout/{$checkout->uuid}")
            ->assertRedirect(route('events.ticketing', $checkout->event->slug));
    });
});

describe('Webhooks fulfil paid sessions exactly once', function () {
    beforeEach(fn () => Queue::fake());

    it('fulfils a paid checkout.session.completed', function () {
        $checkout = makeReservedCheckout(['stripe_session_id' => 'cs_paid']);

        postStripeWebhook('checkout.session.completed', paidSessionFor($checkout))->assertOk();

        expect($checkout->fresh())
            ->completed_at->not->toBeNull()
            ->stripe_payment_intent_id->toBe('pi_1');
        Queue::assertPushed(FulfillCheckoutJob::class, 1);
    });

    it('fulfils only once when the event is delivered twice', function () {
        $checkout = makeReservedCheckout(['stripe_session_id' => 'cs_paid']);

        postStripeWebhook('checkout.session.completed', paidSessionFor($checkout));
        postStripeWebhook('checkout.session.completed', paidSessionFor($checkout));

        Queue::assertPushed(FulfillCheckoutJob::class, 1);
    });

    it('ignores payment_intent.succeeded', function () {
        $checkout = makeReservedCheckout(['stripe_session_id' => 'cs_paid']);

        postStripeWebhook('payment_intent.succeeded', [
            'id' => 'pi_1',
            'object' => 'payment_intent',
            'metadata' => ['checkout_uuid' => $checkout->uuid],
        ])->assertOk();

        expect($checkout->fresh()->completed_at)->toBeNull();
        Queue::assertNothingPushed();
    });

    it('waits for async_payment_succeeded when the payment is still pending', function () {
        $checkout = makeReservedCheckout(['stripe_session_id' => 'cs_paid']);

        postStripeWebhook('checkout.session.completed', paidSessionFor($checkout, overrides: ['payment_status' => 'unpaid']));
        expect($checkout->fresh()->completed_at)->toBeNull();

        postStripeWebhook('checkout.session.async_payment_succeeded', paidSessionFor($checkout));
        expect($checkout->fresh()->completed_at)->not->toBeNull();
        Queue::assertPushed(FulfillCheckoutJob::class, 1);
    });

    it('still honours a paid session that is no longer the current one', function () {
        $checkout = makeReservedCheckout(['stripe_session_id' => null]);

        postStripeWebhook('checkout.session.completed', paidSessionFor($checkout, 'cs_old'));

        expect($checkout->fresh())
            ->completed_at->not->toBeNull()
            ->stripe_session_id->toBe('cs_old');
    });

    it('logs a critical error for a second payment on a paid order', function () {
        Log::spy();
        $checkout = makeReservedCheckout(['stripe_session_id' => 'cs_paid', 'completed_at' => now(), 'stripe_payment_intent_id' => 'pi_1']);

        postStripeWebhook('checkout.session.completed', paidSessionFor($checkout, 'cs_other', ['payment_intent' => 'pi_2']));

        Log::shouldHaveReceived('critical')->withArgs(fn ($message) => str_contains($message, 'duplicate payment'))->once();
        Queue::assertNothingPushed();
    });

    it('does not let an old session expiry clear the current session', function () {
        $checkout = makeReservedCheckout(['stripe_session_id' => 'cs_current']);

        postStripeWebhook('checkout.session.expired', stripeSession('cs_old', 'expired', ['client_reference_id' => $checkout->uuid]));
        expect($checkout->fresh()->stripe_session_id)->toBe('cs_current');

        postStripeWebhook('checkout.session.expired', stripeSession('cs_current', 'expired', ['client_reference_id' => $checkout->uuid]));
        expect($checkout->fresh()->stripe_session_id)->toBeNull();
    });

    it('rejects an unsigned or wrongly signed payload', function () {
        $checkout = makeReservedCheckout(['stripe_session_id' => 'cs_paid']);

        postStripeWebhook('checkout.session.completed', paidSessionFor($checkout), secret: 'whsec_wrong')->assertStatus(400);

        expect($checkout->fresh()->completed_at)->toBeNull();
    });
});

describe('Ticket issuance is idempotent', function () {
    beforeEach(function () {
        $pdf = Mockery::mock(PDF::class);
        $pdf->shouldReceive('output')->andReturn('%PDF-fake');
        $this->mock(TicketPdfService::class)->shouldReceive('generate')->andReturn($pdf);
    });

    it('issues one ticket per reserved unit and emails them', function () {
        Mail::fake();
        $checkout = makeReservedCheckout(['completed_at' => now()]);

        FulfillCheckoutJob::dispatchSync($checkout);

        expect(IssuedTicket::where('checkout_id', $checkout->id)->count())->toBe(2);
        Mail::assertSentCount(1);
    });

    it('does not issue tickets again when run twice', function () {
        Mail::fake();
        $checkout = makeReservedCheckout(['completed_at' => now()]);

        FulfillCheckoutJob::dispatchSync($checkout);
        FulfillCheckoutJob::dispatchSync($checkout);

        expect(IssuedTicket::where('checkout_id', $checkout->id)->count())->toBe(2);
    });

    it('keeps the tickets when the email fails, so a retry only re-sends it', function () {
        $checkout = makeReservedCheckout(['completed_at' => now()]);
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP down'));

        expect(fn () => (new FulfillCheckoutJob($checkout))->handle(app(TicketPdfService::class)))
            ->toThrow(RuntimeException::class, 'SMTP down');

        expect(IssuedTicket::where('checkout_id', $checkout->id)->count())->toBe(2);
    });

    it('raises a critical alert when every attempt failed', function () {
        Log::spy();
        $checkout = makeReservedCheckout(['completed_at' => now()]);

        (new FulfillCheckoutJob($checkout))->failed(new RuntimeException('SMTP down'));

        Log::shouldHaveReceived('critical')->withArgs(fn ($message, $context) => $context['checkout'] === $checkout->uuid)->once();
    });
});
