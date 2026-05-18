<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeObject;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook.secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;
            case 'checkout.session.expired':
                $this->handleCheckoutSessionExpired($event->data->object);
                break;
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    private function handleCheckoutSessionCompleted(StripeObject $session)
    {
        $checkoutUuid = $session->metadata->checkout_uuid ?? $session->client_reference_id;

        if (!$checkoutUuid) {
            Log::error('Stripe Webhook: Missing checkout_uuid in session', ['session' => $session->id]);
            return;
        }

        // On vérifie le checkout_uuid ET l'id de stripe qui est déjà dans notre db pour être 100% sur
        $checkout = Checkout::where('uuid', $checkoutUuid)
            ->where('stripe_session_id', $session->id)
            ->first();

        if ($checkout) {
            $this->fulfillCheckout($checkout, [
                'stripe_payment_intent_id' => $session->payment_intent,
                'stripe_customer_id' => $session->customer,
            ]);
        } else {
            Log::warning('Stripe Webhook: Checkout not found for session completed', [
                'session' => $session->id,
                'uuid' => $checkoutUuid
            ]);
        }
    }

    private function handlePaymentIntentSucceeded(StripeObject $paymentIntent)
    {
        $checkoutUuid = $paymentIntent->metadata->checkout_uuid;

        if (!$checkoutUuid) {
            Log::error('Stripe Webhook: Missing checkout_uuid in payment intent', ['payment_intent' => $paymentIntent->id]);
            return;
        }

        $checkout = Checkout::where('uuid', $checkoutUuid)->first();

        if ($checkout) {
            $this->fulfillCheckout($checkout, [
                'stripe_payment_intent_id' => $paymentIntent->id,
                'stripe_customer_id' => $paymentIntent->customer,
            ]);
        }
    }

    private function fulfillCheckout(Checkout $checkout, array $stripeData)
    {
        if ($checkout->completed_at) {
            // Déjà complété, on met juste à jour les IDs au cas où
            $checkout->update($stripeData);
            return;
        }

        $checkout->update(array_merge([
            'completed_at' => now(),
        ], $stripeData));

        \App\Jobs\FulfillCheckoutJob::dispatch($checkout);
    }

    private function handleCheckoutSessionExpired(StripeObject $session)
    {
        $checkoutUuid = $session->metadata->checkout_uuid ?? $session->client_reference_id;

        if (!$checkoutUuid) {
            Log::error('Stripe Webhook: Missing checkout_uuid in expired session', ['session' => $session->id]);
            return;
        }

        $checkout = Checkout::where('uuid', $checkoutUuid)->first();

        if ($checkout && !$checkout->completed_at && !$checkout->cancelled_at) {
            $checkout->update([
                'stripe_session_id' => null,
            ]);
        }
    }
}
