<?php

namespace App\Http\Controllers;

use App\Jobs\FulfillCheckoutJob;
use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeObject;
use Stripe\Webhook;

/**
 * Orders are fulfilled from Checkout Session events only:
 * - checkout.session.completed with payment_status "paid" (cards, Bancontact)
 * - checkout.session.async_payment_succeeded (delayed payment methods)
 *
 * payment_intent.succeeded is deliberately ignored: it duplicates the events
 * above for the same payment and used to race with them.
 */
class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                (string) config('services.stripe.webhook.secret'),
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $session = $event->data->object;

        match ($event->type) {
            'checkout.session.completed' => $session->payment_status === 'paid'
                ? $this->fulfill($session)
                : Log::info('Stripe Webhook: session completed, payment pending', ['session' => $session->id]),
            'checkout.session.async_payment_succeeded' => $this->fulfill($session),
            'checkout.session.async_payment_failed', 'checkout.session.expired' => $this->releaseSession($session),
            default => null,
        };

        return response()->json(['status' => 'success']);
    }

    /**
     * Marks the checkout as paid and queues ticket issuance, exactly once.
     */
    private function fulfill(StripeObject $session): void
    {
        $uuid = $this->checkoutUuid($session);

        $checkout = DB::transaction(function () use ($session, $uuid) {
            // Row lock: concurrent deliveries for the same checkout wait here, then see
            // completed_at and stop, so tickets are never issued twice.
            $checkout = $uuid ? Checkout::where('uuid', $uuid)->lockForUpdate()->first() : null;

            if (! $checkout) {
                return null;
            }

            if ($checkout->completed_at) {
                if ($checkout->stripe_payment_intent_id && $checkout->stripe_payment_intent_id !== $session->payment_intent) {
                    // A second, different payment for an order that is already paid.
                    Log::critical('Stripe Webhook: duplicate payment for a completed checkout, refund needed', [
                        'checkout' => $checkout->uuid,
                        'session' => $session->id,
                        'payment_intent' => $session->payment_intent,
                    ]);
                }

                return null;
            }

            if ($checkout->stripe_session_id !== $session->id) {
                // e.g. the buyer cancelled while the payment was going through. The money
                // was taken, so the order is honoured anyway.
                Log::warning('Stripe Webhook: paid session is not the current one, fulfilling anyway', [
                    'checkout' => $checkout->uuid,
                    'session' => $session->id,
                    'current_session' => $checkout->stripe_session_id,
                ]);
            }

            $checkout->update([
                'completed_at' => now(),
                'stripe_session_id' => $session->id,
                'stripe_payment_intent_id' => $session->payment_intent,
                'stripe_customer_id' => $session->customer,
            ]);

            return $checkout;
        });

        if ($checkout) {
            FulfillCheckoutJob::dispatch($checkout);

            return;
        }

        if (! $uuid || ! Checkout::where('uuid', $uuid)->exists()) {
            Log::critical('Stripe Webhook: payment received for an unknown checkout, refund needed', [
                'session' => $session->id,
                'uuid' => $uuid,
                'payment_intent' => $session->payment_intent,
            ]);
        }
    }

    /**
     * The session can no longer be paid: forget it so the buyer can try again.
     */
    private function releaseSession(StripeObject $session): void
    {
        // Only the current session: an old session expiring must not clear a newer one.
        Checkout::where('uuid', $this->checkoutUuid($session))
            ->where('stripe_session_id', $session->id)
            ->whereNull('completed_at')
            ->update(['stripe_session_id' => null]);
    }

    private function checkoutUuid(StripeObject $session): ?string
    {
        return $session->metadata->checkout_uuid ?? $session->client_reference_id ?? null;
    }
}
