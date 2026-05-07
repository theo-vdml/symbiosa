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

        $checkout = Checkout::where('uuid', $checkoutUuid)->first();

        if ($checkout) {
            $checkout->update([
                'completed_at' => now(),
                'stripe_session_id' => $session->id,
            ]);
        }
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
